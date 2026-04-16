<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\orders;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * احصل على جميع فواتير المستخدم
     */
    public function getUserInvoices(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $invoices = Invoice::where('user_id', $user->id)
                ->with('order')
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $invoices,
            ]);
        } catch (\Exception $e) {
            \Log::error('خطأ في جلب الفواتير: ' . $e->getMessage());
            return response()->json(['error' => 'خطأ في جلب الفواتير'], 500);
        }
    }

    /**
     * احصل على فاتورة محددة
     */
    public function getInvoice($invoiceId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $invoice = Invoice::where('id', $invoiceId)
                ->where('user_id', $user->id)
                ->with(['order', 'user'])
                ->first();

            if (!$invoice) {
                return response()->json(['error' => 'الفاتورة غير موجودة'], 404);
            }

            // تحديث حالة المشاهدة
            if ($invoice->status === 'sent') {
                $invoice->updateStatus('viewed');
            }

            return response()->json([
                'success' => true,
                'data' => $invoice,
            ]);
        } catch (\Exception $e) {
            \Log::error('خطأ في جلب الفاتورة: ' . $e->getMessage());
            return response()->json(['error' => 'خطأ في جلب الفاتورة'], 500);
        }
    }

    /**
     * تحميل ملف الفاتورة PDF
     */
    public function downloadInvoice($invoiceId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $invoice = Invoice::where('id', $invoiceId)
                ->where('user_id', $user->id)
                ->first();

            if (!$invoice) {
                return response()->json(['error' => 'الفاتورة غير موجودة'], 404);
            }

            $pdfPath = $this->invoiceService->getInvoicePDF($invoice);
            
            if (!$pdfPath || !file_exists($pdfPath)) {
                return response()->json(['error' => 'ملف الفاتورة غير موجود'], 404);
            }

            return response()->download($pdfPath, $invoice->invoice_number . '.pdf');
        } catch (\Exception $e) {
            \Log::error('خطأ في تحميل الفاتورة: ' . $e->getMessage());
            return response()->json(['error' => 'خطأ في تحميل الفاتورة'], 500);
        }
    }

    /**
     * إعادة إرسال الفاتورة
     */
    public function resendInvoice($invoiceId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $invoice = Invoice::where('id', $invoiceId)
                ->where('user_id', $user->id)
                ->first();

            if (!$invoice) {
                return response()->json(['error' => 'الفاتورة غير موجودة'], 404);
            }

            $this->invoiceService->resendInvoice($invoice);

            return response()->json([
                'success' => true,
                'message' => 'تم إعادة إرسال الفاتورة بنجاح',
            ]);
        } catch (\Exception $e) {
            \Log::error('خطأ في إعادة إرسال الفاتورة: ' . $e->getMessage());
            return response()->json(['error' => 'خطأ في إعادة إرسال الفاتورة'], 500);
        }
    }

    /**
     * احصل على إحصائيات الفواتير
     */
    public function getInvoiceStatistics()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $statistics = [
                'total_invoices' => Invoice::where('user_id', $user->id)->count(),
                'paid_invoices' => Invoice::where('user_id', $user->id)->where('status', 'paid')->count(),
                'pending_invoices' => Invoice::where('user_id', $user->id)->where('status', 'pending')->count(),
                'sent_invoices' => Invoice::where('user_id', $user->id)->where('status', 'sent')->count(),
                'total_amount' => Invoice::where('user_id', $user->id)->sum('total_amount'),
                'paid_amount' => Invoice::where('user_id', $user->id)->where('status', 'paid')->sum('total_amount'),
            ];

            return response()->json([
                'success' => true,
                'data' => $statistics,
            ]);
        } catch (\Exception $e) {
            \Log::error('خطأ في جلب إحصائيات الفواتير: ' . $e->getMessage());
            return response()->json(['error' => 'خطأ في جلب الإحصائيات'], 500);
        }
    }
}
