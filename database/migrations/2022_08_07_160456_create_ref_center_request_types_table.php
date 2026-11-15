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
        Schema::create('ref_center_request_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');  // Who created the collection
            $table->unsignedBigInteger('ref_center_id');
            $table->unsignedBigInteger('tx_service_cat_ID');  //ID got form the requests table
            $table->string('tx_service_cat');     // credit -advance - discount   - release   - collection
            $table->string('tx_type');    //collection    type of transaction
            $table->integer('amount')->default('0');      //amount to be collected
            $table->string('name');    //name branded to the service
            $table->string('description',700);    //naration detailing the service description
            $table->string('param1');
            $table->string('param2');
            $table->integer('interval')->default(0);      // Interval period between consecutive collections Every aftger 'days' 
            $table->string('active_status')->default('inactive');
            $table->string('image1');       //Any images if necessarey especially for campaigns
            $table->string('image2');       //Any images if necessarey especially for campaigns
            $table->string('image3');       //Any images if necessarey especially for campaigns
            $table->timestamp('start_date')->default('2000-01-01');   // start date for advance pay
            $table->timestamp('end_date')->default('2000-01-01');   // start date for advance pay

            $table->integer('amount_edit');      //amount to be collected
            $table->string('name_edit');    //name branded to the service
            $table->string('description_edit',700);    //naration detailing the service description
            $table->string('param1_edit');
            $table->string('param2_edit');
            $table->string('interval_edit')->default('daily');      //daily - MON-SUN - 1-28
            $table->string('image1_edit');       //Any images if necessarey especially for campaigns
            $table->string('image2_edit');       //Any images if necessarey especially for campaigns
            $table->string('image3_edit');       //Any images if necessarey especially for campaigns
            $table->timestamp('start_date_edit')->default('2000-01-01');   // start date for advance pay
            $table->timestamp('end_date_edit')->default('2000-01-01');   // start date for advance pay
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
        Schema::dropIfExists('ref_center_request_types');
    }
};
