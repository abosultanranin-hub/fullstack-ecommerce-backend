<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
          //  $table->foreignId('store_id')->constrained('stores');
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('number')->unique();
            $table->enum('status', ['pending', 'processing', 'delivering', 'completed', 'cancelled'])
                ->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])
                ->default('pending');
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
