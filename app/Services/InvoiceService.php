<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\orders;
use App\Mail\InvoiceMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Exception;
use Illuminate\Support\Str;

class InvoiceService
{
    /**
     * إنشاء فاتورة جديدة من طلب
     */
    public function createInvoice(orders $order, array $data = []): Invoice
    {
        try {
            $invoiceData = [
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'subtotal' => $data['subtotal'] ?? $this->calculateOrderSubtotal($order),
                'tax_amount' => $data['tax_amount'] ?? 0,
                'shipping_amount' => $data['shipping_amount'] ?? 0,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'currency' => $data['currency'] ?? 'USD',
                'payment_method' => $order->payment_method ?? 'online',
                'notes' => $data['notes'] ?? null,
                'status' => 'pending',
            ];

            // حساب المبلغ الإجمالي
            $invoiceData['total_amount'] = $invoiceData['subtotal'] 
                + $invoiceData['tax_amount'] 
                + $invoiceData['shipping_amount'] 
                - $invoiceData['discount_amount'];

            return Invoice::create($invoiceData);
        } catch (Exception $e) {
            \Log::error('خطأ في إنشاء الفاتورة: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * حساب المبلغ الأساسي للطلب
     */
    private function calculateOrderSubtotal(orders $order): float
    {
        $subtotal = 0;
        if ($order->orderItems) {
            foreach ($order->orderItems as $item) {
                $subtotal += ($item->price * $item->quantity);
            }
        }
        return $subtotal;
    }

    /**
     * إنشاء PDF للفاتورة
     */
    public function generatePDF(Invoice $invoice): string
    {
        try {
            // التحقق من البيانات الأساسية
            if (!$invoice->order || !$invoice->user) {
                throw new Exception('بيانات الفاتورة غير كاملة');
            }

            // إنشاء المسار الآمن للملف
            $fileName = 'invoice_' . $invoice->invoice_number . '_' . Str::random(8) . '.pdf';
            $filePath = 'invoices/' . date('Y/m') . '/';
            
            // التأكد من وجود المجلد
            if (!Storage::disk('local')->exists($filePath)) {
                Storage::disk('local')->makeDirectory($filePath, 0755, true);
            }

            // إنشاء PDF
            $pdf = Pdf::loadView('invoices.template', [
                'invoice' => $invoice,
                'order' => $invoice->order,
                'user' => $invoice->user,
                'orderItems' => $invoice->order->orderItems ?? [],
            ]);

            // تعيين خيارات PDF
            $pdf->setPaper('a4');
            $pdf->setOption('margin-top', 10);
            $pdf->setOption('margin-bottom', 10);

            // حفظ الملف
            $fullPath = $filePath . $fileName;
            Storage::disk('local')->put($fullPath, $pdf->output());

            // حفظ المسار النسبي في قاعدة البيانات (بدون تشفير لتجنب تجاوز طول العمود)
            $invoice->update(['pdf_path' => $fullPath]);

            return Storage::disk('local')->path($fullPath);
        } catch (Exception $e) {
            \Log::error('خطأ في إنشاء PDF الفاتورة: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * إرسال الفاتورة بالبريد الإلكتروني
     */
    public function sendInvoiceEmail(Invoice $invoice): bool
    {
        try {
            // التحقق من البريد الإلكتروني
            if (!$invoice->user || !$invoice->user->email) {
                \Log::error('❌ لم يتم العثور على بريد المستخدم للفاتورة: ' . $invoice->invoice_number);
                $invoice->update(['status' => 'failed']);
                throw new Exception('لم يتم العثور على بريد المستخدم');
            }

            \Log::info('🔄 بدء عملية إرسال البريد للعميل: ' . $invoice->user->email);
            \Log::info('   - رقم الفاتورة: ' . $invoice->invoice_number);
            \Log::info('   - رقم الطلب: ' . $invoice->order->number);

            // التحقق من وجود ملف PDF
            if (!$invoice->pdf_path) {
                \Log::warning('⚠️ لا يوجد pdf_path، سيتم إنشاء PDF جديد');
                $pdfPath = $this->generatePDF($invoice);
            } else {
                // استخدام المسار المباشر
                $pdfPath = Storage::disk('local')->path($invoice->pdf_path);
                if (!file_exists($pdfPath)) {
                    \Log::warning('⚠️ ملف PDF غير موجود، سيتم إنشاء جديد: ' . $pdfPath);
                    $pdfPath = $this->generatePDF($invoice);
                }
            }

            // التحقق من وجود الملف
            if (!$pdfPath || !file_exists($pdfPath)) {
                $errorMsg = 'ملف الفاتورة غير موجود: ' . ($pdfPath ?? 'null');
                \Log::error('❌ ' . $errorMsg);
                $invoice->update(['status' => 'failed']);
                throw new Exception($errorMsg);
            }

            \Log::info('✅ تم العثور على ملف PDF');
            \Log::info('   - الحجم: ' . filesize($pdfPath) . ' بايت');
            \Log::info('   - المسار: ' . $pdfPath);

            // إرسال البريد فوراً
            \Log::info('📧 إرسال البريد للعميل: ' . $invoice->user->email);
            Mail::to($invoice->user->email)->send(new InvoiceMail($invoice, $pdfPath));

            // تحديث حالة الفاتورة إلى "sent"
            $invoice->update([
                'status' => 'sent',
                'sent_at' => now()
            ]);

            \Log::info('✅ تم إرسال البريد بنجاح');
            \Log::info('   - رقم الفاتورة: ' . $invoice->invoice_number);
            \Log::info('   - الحالة: sent');
            return true;
        } catch (Exception $e) {
            \Log::error('❌ خطأ في إرسال الفاتورة: ' . $e->getMessage());
            \Log::error('   - الفاتورة: ' . $invoice->invoice_number);
            \Log::error('   - التفاصيل: ' . $e->getTraceAsString());
            
            // تحديث الحالة إلى "failed"
            $invoice->update(['status' => 'failed']);
            
            throw $e;
        }
    }

    /**
     * إرسال فاتورة فورية (PDF + البريد الإلكتروني)
     */
    public function createAndSendInvoice(orders $order, array $data = []): bool
    {
        try {
            \Log::info('');
            \Log::info('════════════════════════════════════════════════');
            \Log::info('🚀 بدء عملية إنشاء الفاتورة الكاملة');
            \Log::info('════════════════════════════════════════════════');
            \Log::info('معرف الطلب: ' . $order->id);
            \Log::info('رقم الطلب: ' . $order->number);
            \Log::info('معرف المستخدم: ' . $order->user_id);
            \Log::info('حالة الدفع: ' . $order->payment_status);

            // التحقق من الطلب
            if (!$order || !$order->id) {
                throw new Exception('الطلب غير صحيح أو معرف الطلب مفقود');
            }

            // التحقق من المستخدم
            if (!$order->user) {
                throw new Exception('المستخدم غير موجود للطلب: ' . $order->id);
            }

            // التحقق من عدم وجود فاتورة سابقة للطلب
            $existingInvoice = Invoice::where('order_id', $order->id)->first();
            if ($existingInvoice) {
                \Log::warning('⚠️ توجد فاتورة سابقة للطلب: ' . $order->id);
                \Log::warning('   - رقم الفاتورة السابق: ' . $existingInvoice->invoice_number);
                return false;
            }

            // ════════════════════════════════════════════════
            // الخطوة 1: إنشاء سجل الفاتورة في قاعدة البيانات
            // ════════════════════════════════════════════════
            \Log::info('');
            \Log::info('📝 الخطوة 1: إنشاء سجل الفاتورة في قاعدة البيانات');
            \Log::info('─────────────────────────────────────────────────');
            
            $invoice = $this->createInvoice($order, $data);
            
            if (!$invoice || !$invoice->id) {
                throw new Exception('فشل إنشاء سجل الفاتورة في قاعدة البيانات');
            }
            
            \Log::info('✅ تم إنشاء الفاتورة بنجاح');
            \Log::info('   - معرف الفاتورة: ' . $invoice->id);
            \Log::info('   - رقم الفاتورة: ' . $invoice->invoice_number);
            \Log::info('   - المبلغ الأساسي: ' . $invoice->subtotal);
            \Log::info('   - المبلغ الإجمالي: ' . $invoice->total_amount);
            \Log::info('   - الحالة: ' . $invoice->status);

            // ════════════════════════════════════════════════
            // الخطوة 2: إنشاء ملف PDF
            // ════════════════════════════════════════════════
            \Log::info('');
            \Log::info('📄 الخطوة 2: إنشاء ملف PDF');
            \Log::info('─────────────────────────────────────────────────');
            
            try {
                $pdfPath = $this->generatePDF($invoice);
                
                if (!$pdfPath || !file_exists($pdfPath)) {
                    throw new Exception('فشل إنشاء ملف PDF أو الملف غير موجود');
                }
                
                \Log::info('✅ تم إنشاء ملف PDF بنجاح');
                \Log::info('   - المسار: ' . $pdfPath);
                \Log::info('   - الحجم: ' . (file_exists($pdfPath) ? filesize($pdfPath) : 0) . ' بايت');
            } catch (\Exception $e) {
                \Log::error('❌ فشل إنشاء ملف PDF: ' . $e->getMessage());
                $invoice->update(['status' => 'pdf_failed']);
                throw $e;
            }

            // ════════════════════════════════════════════════
            // الخطوة 3: إرسال البريد الإلكتروني
            // ════════════════════════════════════════════════
            \Log::info('');
            \Log::info('📧 الخطوة 3: إرسال البريد الإلكتروني');
            \Log::info('─────────────────────────────────────────────────');
            
            try {
                $emailSent = $this->sendInvoiceEmail($invoice);
                
                if ($emailSent) {
                    \Log::info('✅ تم إرسال البريد بنجاح');
                    \Log::info('   - البريد: ' . $invoice->user->email);
                    \Log::info('   - الحالة: sent');
                } else {
                    \Log::warning('⚠️ لم يتم إرسال البريد');
                    \Log::warning('   - البريد: ' . $invoice->user->email);
                }
            } catch (\Exception $e) {
                \Log::error('❌ خطأ في إرسال البريد: ' . $e->getMessage());
                // لا نرمي الاستثناء هنا - نسمح بإتمام الطلب حتى لو فشل البريد
            }

            // ════════════════════════════════════════════════
            // انتهاء العملية بنجاح
            // ════════════════════════════════════════════════
            \Log::info('');
            \Log::info('════════════════════════════════════════════════');
            \Log::info('✅ انتهت عملية إنشاء الفاتورة بنجاح!');
            \Log::info('════════════════════════════════════════════════');
            \Log::info('الملخص:');
            \Log::info('  - رقم الفاتورة: ' . $invoice->invoice_number);
            \Log::info('  - رقم الطلب: ' . $order->number);
            \Log::info('  - المبلغ: $' . $invoice->total_amount);
            \Log::info('  - البريد: ' . $invoice->user->email);
            \Log::info('  - الحالة: ' . $invoice->status);
            \Log::info('════════════════════════════════════════════════');
            \Log::info('');
            
            return true;
            
        } catch (Exception $e) {
            \Log::error('');
            \Log::error('════════════════════════════════════════════════');
            \Log::error('❌ فشلت عملية إنشاء الفاتورة!');
            \Log::error('════════════════════════════════════════════════');
            \Log::error('الخطأ: ' . $e->getMessage());
            \Log::error('الطلب: ' . $order->number);
            \Log::error('السبب التفصيلي:');
            \Log::error($e->getTraceAsString());
            \Log::error('════════════════════════════════════════════════');
            \Log::error('');
            
            throw $e;
        }
    }

    /**
     * الحصول على الفاتورة بشكل آمن
     */
    public function getInvoicePDF(Invoice $invoice): ?string
    {
        try {
            if (!$invoice->pdf_path) {
                return null;
            }

            // المسار المباشر
            $fullPath = Storage::disk('local')->path($invoice->pdf_path);

            if (file_exists($fullPath)) {
                return $fullPath;
            }

            return null;
        } catch (Exception $e) {
            \Log::error('خطأ في الوصول إلى الفاتورة: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * حذف فاتورة وملفها بشكل آمن
     */
    public function deleteInvoice(Invoice $invoice): bool
    {
        try {
            if ($invoice->pdf_path) {
                if (Storage::disk('local')->exists($invoice->pdf_path)) {
                    Storage::disk('local')->delete($invoice->pdf_path);
                }
            }

            $invoice->delete();
            \Log::info('تم حذف الفاتورة: ' . $invoice->invoice_number);
            return true;
        } catch (Exception $e) {
            \Log::error('خطأ في حذف الفاتورة: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * إعادة إرسال فاتورة
     */
    public function resendInvoice(Invoice $invoice): bool
    {
        try {
            if (!$invoice->pdf_path) {
                $this->generatePDF($invoice);
            }

            $this->sendInvoiceEmail($invoice);
            return true;
        } catch (Exception $e) {
            \Log::error('خطأ في إعادة إرسال الفاتورة: ' . $e->getMessage());
            return false;
        }
    }
}
