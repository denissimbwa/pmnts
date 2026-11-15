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
        Schema::create('account_users', function (Blueprint $table) {
            $table->id();
            $table->string('source_table');
            $table->string('source_column');
            $table->string('source_id');    
            $table->string('acc_type');     //center - provider - acc_summary
            $table->string('role');
            $table->string('status')->default('inactive');
            $table->string('admin_id');
            $table->unsignedBigInteger('user_id');
            $table->string('approve_status');
            $table->string('approve_action');
            $table->string('approve_ref_id');
            $table->integer('approve_count')->default('2');     // Atleast two peaple approve account user status change if users are more than 2
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
        Schema::dropIfExists('account_users');
    }
};
