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
        Schema::create('ref_center_request_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ref_center_id');    //group ID
            $table->unsignedBigInteger('request_type_id');      // service ID
            $table->unsignedBigInteger('user_id');      //registered by
            $table->integer('amount');      //amount to be collected
            $table->string('flow_type');    //collection    type of transaction
            $table->string('schedule_type')->default('daily');      //daily - MON-SUN - 1-28
            $table->string('active_status')->default('active');
            $table->string('acc_number');       //recepient account number - this is the group account number

            $table->string('acc_number_edit');
            $table->string('active_status_edit');
            $table->string('approve_status');
            $table->string('approve_action');
            $table->string('approve_ref_id');
            $table->string('approve_narration');
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
        Schema::dropIfExists('ref_center_request_schedules');
    }
};
