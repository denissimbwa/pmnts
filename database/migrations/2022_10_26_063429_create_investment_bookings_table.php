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
        Schema::create('investment_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('provider_id');
            $table->string('acc_number');
            $table->string('user_id');
            $table->string('status')->default('new');   //new , selected, closed
            $table->string('narration')->default('n/a');
            $table->string('amount')->default('0');
            $table->string('status_edit')->default('selected');   //selected, closed
            $table->string('approve_status');
            $table->string('approve_action')->default('approve');
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
        Schema::dropIfExists('investment_bookings');
    }
};
