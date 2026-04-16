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
            // إضافة أعمدة جديدة لتتبع حالة الفاتورة بشكل أفضل
            if (!Schema::hasColumn('invoices', 'queued_at')) {
                $table->datetime('queued_at')->nullable()->comment('وقت إضافة البريد إلى Queue');
            }
            
            if (!Schema::hasColumn('invoices', 'failed_reason')) {
                $table->longText('failed_reason')->nullable()->comment('سبب فشل البريد أو الفاتورة');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'queued_at')) {
                $table->dropColumn('queued_at');
            }
            
            if (Schema::hasColumn('invoices', 'failed_reason')) {
                $table->dropColumn('failed_reason');
            }
        });
    }
};
