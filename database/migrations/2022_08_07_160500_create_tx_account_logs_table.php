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
        Schema::create('tx_account_logs', function (Blueprint $table) {
            $table->id();
            $table->string('tx_id')->unique();
            $table->string('acc_number');   // account number owning this deposit or withdrawal
            $table->string('source_account');   // optional account number funding this transaction especially staged withdrawals
            $table->string('dest_account');   // optional account number receiving this transaction especially staged withdrawals
            $table->string('acc_number_funding');   // optional account number to indicate provider in tx log and also identify transactions for provider that are under credit account
            $table->string('acc_number_api')->default('');
            $table->string('user_id');
            $table->string('ref_id');   // Transacting user's reference ID or number - platenumber, NIN, mobile, stage No.( member_id) can be null
            $table->double('raw_amount')->default('0');
            $table->string('currency');
            $table->double('cash_amount')->default('0');
            $table->string('tx_type');  //deposite - withdraw
            $table->string('tx_type_transit')->default('none');  //deposite - withdraw the final action once transit is approved
            $table->string('slip_no');
            $table->string('tx_status');    // approved or pending , this 
            $table->timestamp('act_date')->useCurrent();
            $table->timestamp('act_time')->useCurrent();
            $table->string('tx_channel')->default('wallet');        // wallet - transit - cash  -  api  -  exchange -  credit  -  mmoney - bank
            $table->string('currency_source')->default('UGX');
            $table->string('currency_destination')->default('UGX');
            $table->string('tx_rate')->default('1');
            $table->string('tx_value_type');    // cash - wallet - provider  -   api
            $table->string('tx_service_cat');   //collection - credit - discount - advance   
            $table->string('tx_service_cat_ID');   //collection - credit - discount - advance   - campaign (request type)
            $table->string('tx_owner_type');   //user  - office - group - provider
            $table->string('tx_owner_id');   // ref_center_id - user_id - admin_id  - provider_id
            $table->string('tx_id_parent');   // used to group related transactions with the first original transaction number
            $table->string('tx_flag')->default('normal');   // profit -   normal - transit - transfer  - exchange - repaid  - taken        ( A remark that categorises the transaction )
            $table->double('cash_amount_transit')->default('0');
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
            $table->string('transit_status')->default('none');
            $table->string('approve_status');
            $table->string('approve_action');   //
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
        Schema::dropIfExists('tx_account_logs');
    }
};
