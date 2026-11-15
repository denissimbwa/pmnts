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
        Schema::create('api_activity', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('tx_id');
            $table->string('client_id');
            $table->string('ref_id');
            $table->string('call_back_url');
            $table->string('ref_amount');
            $table->string('api_channel');
            $table->string('api_amount');
            $table->string('api_ref_body');
            $table->double('tx_fee')->default('0');
            $table->string('tx_type');
            $table->string('tx_channel');
            $table->string('request_body');
            $table->string('response_body');
            $table->string('status');
            $table->string('tx_status');
            $table->string('remark');
            $table->string('act_time');
            $table->string('pay_id');
            $table->string('source');
            $table->string('destination');
            $table->string('channel');
            $table->string('processsing_time');
            $table->string('active_status');
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
        Schema::dropIfExists('api_activity');
    }
};
