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
        // 1. Table for tracking IPs for account sharing prevention
        Schema::create('login_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ip');
            $table->timestamp('created_at')->useCurrent();
        });

        // 2. Table for blocked countries
        Schema::create('blocked_countries', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 3)->unique();
            $table->timestamps();
        });

        // 3. Table for blocked email domains
        Schema::create('blocked_email_domains', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->unique();
            $table->timestamps();
        });

        // 4. Table for blocked IPs (Brute force protection)
        Schema::create('blocked_ips', function (Blueprint $table) {
            $table->id();
            $table->string('ip')->unique();
            $table->timestamp('blocked_until');
            $table->timestamps();
        });

        // 5. Table for security logs
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // e.g., 'failed_login', 'suspicious_login', 'blocked_ip'
            $table->string('ip')->nullable();
            $table->text('details')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });

        // 6. Add last_country and last_login to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_country')->nullable();
            $table->timestamp('last_login')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_country', 'last_login']);
        });
        Schema::dropIfExists('security_logs');
        Schema::dropIfExists('blocked_ips');
        Schema::dropIfExists('blocked_email_domains');
        Schema::dropIfExists('blocked_countries');
        Schema::dropIfExists('login_sessions');
    }
};
