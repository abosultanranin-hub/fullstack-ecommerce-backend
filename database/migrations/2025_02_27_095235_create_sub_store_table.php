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
        Schema::create('sub_store', function (Blueprint $table) {
            $table->id();
            $table->string('name');        // إضافة عمود name
            $table->string('location');    // عمود الموقع
            $table->integer('capacity');   // عمود السعة
            $table->text('description')->nullable();  // إضافة عمود description
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // إضافة حقل user_id كـ foreign key
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_store');
    }
};
