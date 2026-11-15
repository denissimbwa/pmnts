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
        Schema::create('tx_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('ref_center_id');     //optional group ID for accounts that belong to centers
            $table->unsignedBigInteger('provider_id');     //for provider accounts that belong to providers
            $table->string('user_uuid');
            $table->string('acc_name');
            $table->string('acc_description')->default('user end');  //special description of account
            $table->string('tx_value_type')->default('wallet');  //cash, wallet, api , provider
            $table->string('tx_channel')->default('wallet');    // wallet - transit - cash  -  api  -  exchange -  credit  -  mmoney - bank
            $table->string('acc_category')->default('user');  //group, provider, user, admin
            $table->string('acc_number')->unique();
            $table->string('currency')->default('UGX');
            $table->double('amount')->default('0');
            $table->integer('profit_dependants')->default('0');
            $table->string('invite_code')->unique();
            $table->string('active_status')->default('active'); 
            $table->string('wd_charges')->default('0'); //[range=>'1000-100,500-300', 'amount'=>3000]
            $table->string('wd_api_fee')->default('0'); //[range=>'1000-100,500-300', 'amount'=>3000]
            $table->double('wd_max_day')->default('0'); 
            $table->string('dp_charges')->default('0'); //[range=>'1000-100,500-300', 'amount'=>3000]
            $table->string('dp_api_fee')->default('0'); //[range=>'1000-100,500-300', 'amount'=>3000]
            $table->double('dp_max_day')->default('0'); 

            $table->string('wd_charges_edit')->default('0');
            $table->double('wd_max_day_edit')->default('0');
            $table->string('wd_api_fee_edit')->default('0');
            $table->string('dp_charges_edit')->default('0');
            $table->double('dp_max_day_edit')->default('0');
            $table->string('dp_api_fee_edit')->default('0');
            $table->string('active_status_edit'); 
            $table->string('approve_status')->default('active');
            $table->string('approve_action')->default('approve'); 
            $table->string('approve_ref_id');
            $table->string('approve_narration');
            $table->integer('approve_count')->default('3');
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
        Schema::dropIfExists('tx_accounts');
    }
};
