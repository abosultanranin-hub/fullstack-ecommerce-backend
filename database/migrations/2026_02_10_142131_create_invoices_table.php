<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // رقم الفاتورة
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('subtotal', 10, 2); // المبلغ قبل الضرائب
            $table->decimal('tax_amount', 10, 2)->default(0); // مبلغ الضرائب
            $table->decimal('shipping_amount', 10, 2)->default(0); // تكاليف الشحن
            $table->decimal('discount_amount', 10, 2)->default(0); // مبلغ الخصم
            $table->decimal('total_amount', 10, 2); // المبلغ الإجمالي
            $table->string('status')->default('pending'); // pending, sent, viewed, paid
            $table->string('currency')->default('USD');
            $table->string('payment_method')->nullable(); // طريقة الدفع
            $table->text('notes')->nullable(); // ملاحظات
            $table->string('pdf_path')->nullable(); // مسار الـ PDF المشفر
            $table->timestamp('sent_at')->nullable(); // وقت الإرسال
            $table->timestamp('viewed_at')->nullable(); // وقت العرض
            $table->timestamp('paid_at')->nullable(); // وقت الدفع
            $table->timestamps();
            
            // إضافة indexes للبحث السريع
            $table->index('invoice_number');
            $table->index('order_id');
            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
