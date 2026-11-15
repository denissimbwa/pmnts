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
        Schema::create('client', function (Blueprint $table) {
            $table->id();
            $table->string('client_id');
            $table->string('name');
            $table->string('mobile');
            $table->string('address');
            $table->string('website');
            $table->string('call_back_url');
            $table->string('user_id');
            $table->string('access_code');
            $table->double('tx_fee_dp')->default("0");
            $table->double('tx_fee_wd')->default("0");
            $table->string('amount');
            $table->string('tx_count');
            $table->string('active_status')->default("active");
            $table->string('open_time')->default("06:00");
            $table->string('close_time')->default("18:00");
            
            $table->string('image');
            $table->string('param1');
            $table->string('param2');
            $table->string('param3');
            $table->string('param4');
            $table->string('param5');
            $table->string('param6');
            $table->string('param7');
            $table->string('param8');
            $table->string('param9');
            $table->string('param10');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client');
    }
};
