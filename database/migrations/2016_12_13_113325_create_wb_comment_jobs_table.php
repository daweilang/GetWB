<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWbCommentJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::create('wb_comment_jobs', function (Blueprint $table) {
    		$table->increments('j_id');
    		//微博id，对应mid
    		$table->bigInteger('mid');
    		//页号
    		$table->integer('j_comment_page')->unsigned()->default(0);
    		//改页评论数
    		$table->integer('j_comment_total')->unsigned()->default(0);
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
    	Schema::drop('wb_comment_jobs');
    }
}
