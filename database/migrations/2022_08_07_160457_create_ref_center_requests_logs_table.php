<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ref_center_requests_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ref_center_id');      
            $table->unsignedBigInteger('ref_center_request_id');
            $table->unsignedBigInteger('request_type_id');
            $table->unsignedBigInteger('slip_no');          //tx slip number for payment made at tx_acc_log
            $table->unsignedBigInteger('admin_id');         //who processed the payment
            $table->unsignedBigInteger('member_id');        //who paid
            $table->string('request_code');             //code used for verification - optional
            $table->double('amount');        // +ve and -ve amounts
            $table->string('flow_type');     //inflow - outflow
            $table->string('currency');
            $table->string('tx_id_ref');    //reflect the id of the serving source table  - tx_account_logs , provider_logs
            $table->string('tx_id_ref_source_table');    //reflect the serving source table
            $table->string('tx_flag1');         //
            $table->string('request_service_id');         // The id of the request types available for the group in ref_center_request_types
            $table->string('tx_value_type');    // cash - wallet - provider
            $table->string('sync_status');       // 1- single entity , 2-accountability is in request log
            $table->string('approve_status');
            $table->string('approve_action');
            $table->string('approve_ref_id');
            $table->integer('approve_count')->default('2');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ref_center_requests_logs');
    }
};
