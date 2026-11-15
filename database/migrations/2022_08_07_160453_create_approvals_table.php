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
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type');      // either request or response
            $table->string('source_table');
            $table->string('source_column');
            $table->string('source_id');
            $table->string('request')->default('N/A');
            $table->string('status')->default('active');
            $table->string('approve_scope')->default('admin');      // admin    -   group   -   user    -   provider
            $table->string('ref_center_id');      // center ID optional , to help identify applrovals related to an individual
            $table->string('approve_status')->default('new');
            $table->string('approve_action');
            $table->string('approve_ref_id');
            $table->string('approve_narration');
            $table->integer('approve_count')->default('2');
            $table->unsignedBigInteger('user_id'); //foreign key
            $table->unsignedBigInteger('approver_id'); //foreign key
            $table->string('approver_role');    // user access rank required to approve the request
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
        Schema::dropIfExists('approvals');
    }
};
