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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->default('');
            $table->string('user_uuid');
            $table->string('user_id');
            $table->string('country_code','4')->default('256');
            $table->string('mobile','100');
            $table->text('password');
            $table->string('verification_code');
            $table->string('verification_status')->default('no');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('active_status');
            $table->string('user_rank')->default('client');    //super admin,back admin, client
            $table->integer('login_count');
            $table->string('access_tocken');
            $table->string('active_status_edit');
            $table->string('user_rank_edit')->default('client');
            $table->string('approve_status');
            $table->string('approve_action');
            $table->string('approve_ref_id');
            $table->integer('approve_count')->default('2');
            $table->unique(['mobile', 'country_code']);         //  Member ID to be unique per center
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
