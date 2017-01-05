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
    		//微博id，对应wb_mid
    		$table->bigInteger('mid');
    		//页号
    		$table->integer('j_like_page')->unsigned()->default(0);
    		//改页评论数
    		$table->integer('j_like_total')->unsigned()->default(0);
    		//执行状态，错误码
    		$table->tinyInteger('j_status')->unsigned()->default(0);
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
