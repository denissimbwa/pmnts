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
        Schema::create('ref_center_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ref_center_id');      
            $table->unsignedBigInteger('provider_id');      //ID of credit giving provider
            $table->unsignedBigInteger('request_type_id');  //ID got form the requests table
            $table->string('request_type');     // credit -advance - discount   - release   - collection
            $table->unsignedBigInteger('user_id');  // Who initiated the request
            $table->unsignedBigInteger('admin_id'); //Who completed teh request
            $table->string('member_id');    //ID of member from center
            $table->string('tx_id')->unique();    //Transaction ID to reflect in accoutns log if transaction came form provider side
            $table->string('request_code');         //verification code to eb sent via SMS or otherwise
            $table->string('currency');
            $table->string('flow_type');        // inflow - outflow
            $table->double('requested_amount')->default('0');
            $table->double('offered_amount')->default('0');
            $table->double('taken_amount')->default('0');
            $table->double('payback_amount')->default('0');
            $table->double('paid_amount')->default('0');    //amount confirmed after API response
            $table->double('balance')->default('0');
            $table->double('limit')->default('0');       //prevailing user's limit
            $table->double('interest')->default('0');    //prevailing interest amount
            $table->integer('duration_days')->default('1');     //payback period
            $table->integer('grace_period')->default('1');
            $table->string('acc_number_funding');          //Prevailing investor's account to collect profit
//            $table->string('acc_number_profit');           //Prevailing dependant account it is by default the investor's account
            $table->string('acc_number_provider')->default('all');  //When restricting requests to a particular provider
            $table->string('acc_number_agent')->default('');  //agent account number for profit share
            $table->string('acc_number_center')->default('');  //center account number for profit share
            $table->string('acc_number_office')->default('');  //office  account number for profit share
            $table->string('tx_value_type');    // cash - wallet - provider
            $table->string('request_levels');       // 1- single entity , 2-accountability is in request log
            $table->string('country_code');
            $table->string('request_service_id');         // The id of the request types available for the group in ref_center_request_types
            $table->timestamp('schedule_date')->default('2000-01-01');
            $table->string('detail1');
            $table->string('detail2');
            $table->string('detail3');
            $table->string('detail4');
            $table->string('detail5');
            $table->string('active_status')->default('new');     // new  -   processing   -   complete    -   closed  -  failed 


            $table->string('active_status_edit');
            $table->string('approve_status')->default('active');
            $table->string('approve_action');   
            $table->string('approve_ref_id');
            $table->string('approve_narration');
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
        Schema::dropIfExists('ref_center_requests');
    }
};
