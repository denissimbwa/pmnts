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
        Schema::create('user_access', function (Blueprint $table) {
            $table->id();
            $table->string('admin_id');
            $table->unsignedBigInteger('user_id');  //ID for the user invited
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('mobile');   //For the user invited
            $table->string('country_code')->default('256');   //For the user invited
            $table->string('email');    //For the user invited
            $table->string('access_ref_name');        //Name of the section the user will access e.g Back-offce, sacco name, group name, fule station name, etc
            $table->string('access_ref_id');        //actual ID for the accessed area
            $table->string('access_ref_id_type');   //acc_number - user_id 
            $table->string('access_scope');     //group - provider - office
            $table->string('access_table');     //ref_centers - provider - office
            $table->string('role')->default('member');  //viewer,admin,owner
            $table->string('acceptance')->default('pending');
            $table->string('active_status')->default('inactive');

            $table->string('active_status_edit');
            $table->string('role_eidt');
            $table->string('approve_status');
            $table->string('approve_action');
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
        Schema::dropIfExists('user_access');
    }
};
