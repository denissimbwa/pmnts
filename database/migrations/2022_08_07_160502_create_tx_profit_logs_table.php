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
        Schema::create('tx_profit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ref_center_request_id');   // optional but enables tracking which trasaction brought the profit
            $table->string('acc_number');   // receipient account
            $table->string('acc_number_ref');   // service account
            $table->float('amount')->default('0');
            $table->string('sync_status');   // whether is has been transafered to the account loig
            $table->string('tx_id_ref');
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
        Schema::dropIfExists('tx_profit_logs');
    }
};
