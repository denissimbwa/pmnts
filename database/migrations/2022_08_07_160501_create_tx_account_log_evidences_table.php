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
        Schema::create('tx_account_log_evidences', function (Blueprint $table) {
            $table->id();
            $table->string('tx_id');
            $table->string('ref_id');
            $table->string('received_by');
            $table->string('paid_by');
            $table->float('amount')->default('0');
            $table->string('status')->default('active');
            $table->string('image1');
            $table->string('image2');
            $table->string('image3');
            $table->string('approve_status')->default('pending');
            $table->string('approve_action');
            $table->string('approve_ref_id');
            $table->integer('approve_count')->default('1');
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
        Schema::dropIfExists('tx_account_log_evidences');
    }
};
