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
        Schema::create('sys_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('tx_delay_interval');   //perriond between concecutive transaction of the same account 
            $table->integer('approval_count')->default(2);  // approvals count for new input to take effect
            $table->float('wd_max');
            $table->float('dp_max');
            $table->float('wd_charge');
            $table->float('dp_charge');
            $table->float('interest_rate');
            $table->float('max_group_size');
            $table->float('min_group_size');
            $table->float('penalty_rate');

            $table->integer('tx_delay_interval_edit');   //perriond between concecutive transaction of the same account 
            $table->integer('approval_count_edit');
            $table->float('wd_max_edit');
            $table->float('dp_max_edit');
            $table->float('wd_charge_edit');
            $table->float('dp_charge_edit');
            $table->float('interest_rate_edit');
            $table->float('max_group_size_edit');
            $table->float('min_group_size_edit');
            $table->float('penalty_rate_edit');
            
            $table->string('active_status')->default('inactive');
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
        Schema::dropIfExists('sys_settings');
    }
};
