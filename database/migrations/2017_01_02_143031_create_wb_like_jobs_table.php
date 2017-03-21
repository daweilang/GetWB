<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWbLikeJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	//
    	Schema::create('wb_like_jobs', function (Blueprint $table) {
    		$table->increments('j_id');
    		$table->bigInteger('mid')->comment('微博id');
    		$table->string('model','30')->comment('任务对应的model');
    		$table->integer('j_page')->unsigned()->default(0)->comment('页号');
    		$table->integer('j_total')->unsigned()->default(0)->comment('该页统计');
    		$table->tinyInteger('j_status')->unsigned()->default(0)->comment('执行状态，错误码');
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
        //
    }
}
