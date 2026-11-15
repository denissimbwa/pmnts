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
        Schema::create('tx_api_refs', function (Blueprint $table) {
            $table->id();
            $table->string('tx_id')->unique();
            $table->string('ref_id');
            $table->string('user_id');
            $table->string('client_id');
            $table->string('call_back_url');
            $table->string('ref_amount');
            $table->string('api_channel');
            $table->string('request_body');
            $table->string('response_body');
            $table->string('status');
            $table->string('remark');
            $table->string('pay_id');
            $table->string('source');
            $table->string('destination');
            $table->string('channel');
            $table->string('active_status');
            $table->string('amount_source');
            $table->string('amount_cash');          // the actual +ve or -ve amount logging the client
            
            $table->string('source_account');
            $table->string('dest_account');
            $table->string('acc_number_funding');   // optional account number to indicate provider in tx log and also identify transactions for provider that are under credit account
            $table->string('acc_number_office');   // To make replica of wallet internal transactions
            $table->string('tx_status')->default('new');    //new - pending -success - failed - canceled
            $table->double('tx_fee')->default('0');
            $table->double('tx_cash_back')->default('0');
            $table->string('tx_type');      //deposit - withdraw
            $table->string('api_id');
            $table->string('api_ref_body');
            $table->double('api_fee')->default('0');
            $table->double('api_amount')->default('0');
            $table->string('api_errormsg')->default('');
            $table->timestamp('act_time')->useCurrent();
            $table->string('processsing_time');
            $table->string('tx_channel')->default('wallet');    //  wallet - transit - cash  -  api  -  exchange -  credit  -  mmoney - bank
            $table->string('sync_status')->default('no');
            $table->string('currency_source')->default('UGX');
            $table->string('currency_destination')->default('UGX');
            $table->string('tx_rate')->default('1');
            $table->string('tx_value_type');    // cash - wallet - provider -   api
            $table->string('tx_service_cat');   //collection - credit - discount - advance   - campaign (request type)
            $table->string('tx_service_cat_ID');   //collection - credit - discount - advance   - campaign (request type)
            $table->string('tx_owner_type');   //user  - office - group
            $table->string('tx_owner_id');   // ref_center_id - user_id - admin_id
            $table->string('slip_no');   // same as API ref ID combined with slip number as slip_no-tx_id 
            $table->string('request_service_id');         // The id of the request types available for the group in ref_center_request_types
            
            $table->string('recovery_flag')->default('no');   //
            $table->double('recovered_amount')->default('0');   //
            $table->string('investment_flag')->default('no');   // yes - no  shows whether a deposit was an investment made
            $table->string('investment_id');   //

            $table->string('bank_acc_name');   //
            $table->string('bank_name');   //
            $table->string('bank_branch');   //
            $table->string('bank_acc_number');   //
            
            $table->string('image1');   //
            $table->string('image2');   //
            $table->string('param1');   //extra parameter1 E.g scope-group scope-provider
            $table->string('param2');   //extra value 1
            $table->string('param3');   //extra parameter 2
            $table->string('param4');   //extra value 2
            $table->string('param5');
            $table->string('param6');
            $table->string('param7');
            $table->string('param8');
            $table->string('param9');
            $table->string('param10');
            $table->string('approve_status')->default('active');
            $table->string('approve_action')->default('approve'); 
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
        Schema::dropIfExists('tx_api_ref');
    }
};
