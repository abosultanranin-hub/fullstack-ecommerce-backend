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
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'invoice_number')) {
                $table->string('invoice_number')->unique();
            }
            if (!Schema::hasColumn('invoices', 'order_id')) {
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            }
            if (!Schema::hasColumn('invoices', 'user_id')) {
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('invoices', 'subtotal')) {
                $table->decimal('subtotal', 10, 2);
            }
            if (!Schema::hasColumn('invoices', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('invoices', 'shipping_amount')) {
                $table->decimal('shipping_amount', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('invoices', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('invoices', 'total_amount')) {
                $table->decimal('total_amount', 10, 2);
            }
            if (!Schema::hasColumn('invoices', 'status')) {
                $table->string('status')->default('pending');
            }
            if (!Schema::hasColumn('invoices', 'currency')) {
                $table->string('currency')->default('USD');
            }
            if (!Schema::hasColumn('invoices', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'pdf_path')) {
                $table->string('pdf_path')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'sent_at')) {
                $table->timestamp('sent_at')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'viewed_at')) {
                $table->timestamp('viewed_at')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'paid_at')) {
                $table->timestamp('paid_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // لا نحذف الأعمدة لأنها قد تكون موجودة بالفعل
        });
    }
};
