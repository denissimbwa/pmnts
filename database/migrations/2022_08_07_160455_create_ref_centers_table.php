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
        Schema::create('ref_centers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');  //group creator
            $table->unsignedBigInteger('admin_id');  //ID of afficer hadnling
            $table->unsignedBigInteger('provider_id');  //ID of serving station
            $table->string('center_code')->unique();      // code for users to request self addition
            $table->string('center_type')->default('pool');      // stage - pool - sacco - organization - advancepay - group etc
            $table->string('currency');
            $table->string('name');
            $table->string('mobile');
            $table->string('mobile2');
            $table->string('id_ref1');
            $table->string('id_ref2');
            $table->string('country_code')->default('256');   // Inherited from the user who registerd this center
            $table->string('address');
            $table->string('address_gps');
            $table->string('image_center1');
            $table->string('image_center2');
            $table->string('image_center3');
            $table->string('param1');       // param 1,2,3,... are names of items to eb registered by members, blank ones won't apear for user to signup
            $table->string('param2');
            $table->string('param3');
            $table->string('param4');
            $table->string('param5');
            $table->string('image1');
            $table->string('image2');
            $table->string('image3');
            $table->string('image4');
            $table->string('image5');
            $table->string('active_status')->default('inactive');
            $table->string('provider_acc_number')->default('all');  //When restrciting requests to a particular provider
            $table->string('other');    //any other detail
            $table->string('service_id')->default('none');    // ID of request_type_id for credit. This is to help validation at credit request

            $table->float('credit_limit')->default('0');   // Credit limit to affect the whole center

            $table->date('advance_start_date')->default('1111-11-11');   // start date for advance pay
            $table->date('advance_end_date')->default('1111-11-11');   // start date for advance pay
            $table->float('advance_limit_rate')->default('0');   // Percentage of the advance payment made
            $table->float('interest_rate')->default('5');   // Interest rate to overide default interest


            $table->float('credit_limit_edit')->default('0');   // Credit limit to affect the whole center
            $table->date('advance_start_date_edit')->default('1111-11-11');   // start date for advance pay
            $table->date('advance_end_date_edit')->default('1111-11-11');   // start date for advance pay
            $table->float('advance_limit_rate_edit')->default('0');   // Percentage of the advance payment made
            $table->float('interest_rate_edit')->default('5');   // Interest rate to overide default interest
            $table->string('provider_acc_number_edit');  //for advance pay groups
            $table->string('other_edit');    //any other detail
            $table->string('provider_id_edit');  //for advance pay groups
            $table->string('service_id_edit');    // ID of request_type_id for credit. This is to help validation at credit request
            $table->string('active_status_edit');
            $table->string('approve_status');
            $table->string('approve_action');
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
        Schema::dropIfExists('ref_centers');
    }
};
