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
        Schema::create('ref_center_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ref_center_id');      // stage - pool - sacco - organization - etc
            $table->unsignedBigInteger('user_id');  //registered by
            $table->unsignedBigInteger('owner_id');  //Account owner by whome the request is made
            $table->unsignedBigInteger('provider_id');  //ID of serving station
            $table->string('name');
            $table->string('mobile');
            $table->string('country_code','4')->default('256');
            $table->string('member_id');    //What uniquely identifies the member at that center 
            $table->string('address');
            $table->string('acc_number');   //filled in when member accepts the group
            $table->float('credit_limit')->default('0');    //credit limit if applicable
            $table->string('role')->default('member');      //member admin owner
            $table->string('ref_id');       // elephone of persone who benefits from this user
            $table->string('user_id_ref');  // ID for martketeer or reference person
            $table->string('acc_number_ref');  // ID for martketeer or reference person
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
            $table->string('active_status')->default('inactive');  // activated after approval by minimum count
            $table->float('collection_total')->default('0');
            $table->float('collection_taken')->default('0');
            $table->float('collection_balance')->default('0');
            $table->float('credit_total')->default('0');
            $table->float('credit_taken')->default('0');
            $table->float('credit_balance')->default('0');
            $table->float('discount_total')->default('0');
            $table->float('discount_taken')->default('0');
            $table->float('discount_balance')->default('0');
            $table->string('restricted_services');   // service IDs which user is deactivated from coma separated as _10_, _1_

            $table->float('credit_limit_edit')->default('0');
            $table->string('acc_number_edit');
            $table->string('active_status_edit');
            $table->string('role_edit');
            $table->string('approve_status')->default('active');
            $table->string('approve_action'); 
            $table->string('approve_ref_id');
            $table->string('approve_narration');
            $table->integer('approve_count')->default('2');
            $table->unique(['member_id', 'ref_center_id']);         //  Member ID to be unique per center
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
        Schema::dropIfExists('ref_center_members');
    }
};
