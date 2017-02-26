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
    		$table->bigInteger('mid')->comment('微博id');
    		$table->string('model','30')->comment('任务对应的model');
    		$table->integer('j_comment_page')->unsigned()->default(0)->comment('页号');
    		$table->integer('j_comment_total')->unsigned()->default(0)->comment('该页统计');
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
