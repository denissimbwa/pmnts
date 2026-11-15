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
        Schema::create('tx_account_profit_holders', function (Blueprint $table) {
            $table->id();
            $table->string('acc_number_giver');
            $table->string('user_id');
            $table->string('acc_number_receiver');
            $table->float('rate')->default('0');
            $table->string('active_status')->default('inactive');

//            $table->string('acc_number_giver_edit');
//            $table->string('acc_number_receiver_edit');
            $table->float('rate_edit')->default('0');
            $table->string('active_status_edit');
            $table->string('approve_status');
            $table->string('approve_action');   //
            $table->string('approve_ref_id');
            $table->string('approve_narration');
            $table->integer('approve_count')->default('2');
//            $table->unique(array('acc_number_giver','acc_number_receiver') );
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
        Schema::dropIfExists('tx_account_profit_holders');
    }
};
