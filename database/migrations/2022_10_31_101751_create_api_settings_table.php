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
        Schema::create('api_settings', function (Blueprint $table) {
            $table->id();
            $table->string('currency');
            $table->string('acc_number');
            $table->string('wd_charges');
            $table->string('dp_charges');
            $table->string('acc_number_profit');
            $table->string('acc_number_charge');
            $table->float('USD_rate')->default('0.00027');
            $table->float('KSH_rate')->default('0.034');
            $table->float('RWF_rate')->default('0.29');
            $table->float('UGX_rate')->default('1');

            $table->string('currency_edit');
            $table->string('acc_number_edit');
            $table->string('wd_charges_edit');
            $table->string('dp_charges_edit');
            $table->string('acc_number_profit_edit');
            $table->string('acc_number_charge_edit');
            $table->float('USD_rate_edit');
            $table->float('KSH_rate_edit');
            $table->float('RWF_rate_edit');
            $table->float('UGX_rate_edit');
            $table->string('approve_status');
            $table->string('approve_action')->default('edit');
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
        Schema::dropIfExists('api_settings');
    }
};
