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
        Schema::create('request_types', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('request_type');     // credit -advance - discount   - release   - collection
            $table->string('name');
            $table->string('description');
            $table->float('limit');
            $table->float('interest')->default('10');
            $table->string('currency')->default('UGX');
            $table->integer('duration_days')->default('1');
            $table->integer('grace_period')->default('0');
            $table->float('penalty')->default('0');     //
            $table->string('active_status')->default('active');
            $table->string('access_level')->default('office');      // office    -  client

            $table->string('request_type_edit');     // credit -advance - discount   - release   - collection
            $table->string('name_edit');
            $table->string('description_edit');
            $table->float('limit_edit');
            $table->float('interest_edit');
            $table->integer('duration_days_edit');
            $table->integer('grace_period_edit');
            $table->string('active_status_edit')->default('active');
            $table->string('access_level_edit');

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
        Schema::dropIfExists('request_types');
    }
};
