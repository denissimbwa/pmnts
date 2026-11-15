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
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');  //provider creator
            $table->string('currency');
            $table->string('name');
            $table->string('mobile');
            $table->string('country_code')->default('256');   // Inherited from the user who registerd this venter
            $table->string('address');
            $table->string('bank_name');
            $table->string('banK_acc_number');
            $table->string('banK_acc_name');
            $table->string('bank_branch');
            $table->string('service_id');
            $table->string('acc_number_investor');
            $table->string('image');
            $table->string('interest_business')->default(2);      //percentage of the interest taken by platform
            $table->string('interest_agent')->default(1);      //percentage of the interest taken by agent
            $table->string('interest_center_head')->default(1);      //percentage of the interest taken by cahirman or group admin
            $table->integer('active_status');
            $table->string('active_status_invest')->default('inactive');      // available     -  closed    -   inactive
            $table->string('open_time')->default('06:00');  //Begining of active hours
            $table->string('close_time')->default('18:00');  //End of active hours

            $table->string('currency_edit');
            $table->string('name_edit');
            $table->string('mobile_edit');
            $table->string('address_edit');
            $table->string('bank_name_edit');
            $table->string('banK_acc_number_edit');
            $table->string('banK_acc_name_edit');
            $table->string('bank_branch_edit');
            $table->string('service_id_edit');  // business line ID
            $table->string('acc_number_investor_edit');
            $table->string('image_edit');
            $table->string('interest_business_edit')->default(2);      //percentage of the interest taken by platform
            $table->string('interest_agent_edit')->default(1);      //percentage of the interest taken by agent
            $table->string('interest_center_head_edit')->default(1);      //percentage of the interest taken by cahirman or group admin
            $table->integer('active_status_edit');
            $table->string('active_status_invest_edit')->default('available');      // available     -  closed
            $table->string('open_time_edit')->default('06:00');  //Begining of active hours
            $table->string('close_time_edit')->default('18:00');  //End of active hours
            $table->string('approve_status');
            $table->string('approve_action');   //
            $table->string('approve_ref_id');
            $table->string('approve_narration');   //
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
        Schema::dropIfExists('providers');
    }
};
