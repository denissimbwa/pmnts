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
        Schema::create('provider_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id');
            $table->unsignedBigInteger('admin_id');
            $table->float('amount');
            $table->string('currency');
            $table->string('tx_value_type');    // cash - wallet - provider
            $table->string('flow_type');        // inflow - outflow
            $table->string('source_table');         // priovider - ref_ceter
            $table->string('source_column');
            $table->string('source_id');
            $table->string('detail1');
            $table->string('detail2');
            $table->string('detail3');
            $table->string('detail4');
            $table->string('detail5');
            $table->string('status')->default('active');
            $table->string('approve_status')->default('active');
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
        Schema::dropIfExists('provider_logs');
    }
};
