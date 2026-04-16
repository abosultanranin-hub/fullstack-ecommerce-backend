<?php
// database/migrations/2024_01_01_000000_create_cart_apicookies_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartApicookiesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart_apicookies', function (Blueprint $table) {
            $table->id();
            
            // معرف الجلسة (لربط عناصر السلة بنفس العميل)
            $table->string('session_id', 100);
            
            // معرف المنتج
            $table->unsignedBigInteger('product_id');
            
            // الكمية
            $table->integer('quantity')->default(1);
            
            // بيانات المنتج الكاملة (تخزين كـ JSON)
            $table->json('product_data')->nullable();
            
            // الخيارات الإضافية (لون، حجم، إلخ)
            $table->json('options')->nullable();
            
            // التواريخ
            $table->timestamps();
            
            // الفهارس للأداء
            $table->index('session_id');
            $table->index('product_id');
            $table->index(['session_id', 'product_id']);
            $table->index(['session_id', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_apicookies');
    }
}