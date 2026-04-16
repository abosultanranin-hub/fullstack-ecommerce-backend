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
        Schema::table('cart_apis', function (Blueprint $table) {
            //


            $table->timestamp('abandoned_at')->nullable();
            $table->boolean('abandoned_email_sent')->default(false);
            $table->boolean('is_checked_out')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_apis', function (Blueprint $table) {
            $table->dropColumn([
                'abandoned_at',
                'abandoned_email_sent',
                'is_checked_out',
            ]);
        });
    }
};
