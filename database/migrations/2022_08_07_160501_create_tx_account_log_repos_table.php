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
        Schema::create('tx_account_log_repos', function (Blueprint $table) {
            $table->id();
            $table->string('tx_id');
            $table->string('acc_number');
            $table->string('user_id');
            $table->float('raw_amount')->default('0');
            $table->string('currency');
            $table->string('cash_amount');
            $table->string('tx_type');  //deposite - withdraw
            $table->string('slip_no');
            $table->string('tx_status');
            $table->timestamp('act_date')->useCurrent();
            $table->timestamp('act_time')->useCurrent();
            $table->string('tx_channel')->default('WALLET');
            $table->string('currency_source')->default('UGX');
            $table->string('currency_destination')->default('UGX');
            $table->string('tx_rate')->default('1');
            $table->string('tx_value_type');    // cash - wallet - provider - api - kind
            $table->string('tx_service_cat');   //collection - credit - discount - advance   
            $table->string('tx_id_parent');   // used to group related transactions with the first original transaction number
            $table->string('profit_flag')->default('');   // profit -   normal
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
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tx_account_log_repos');
    }
};
