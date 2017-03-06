<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWbFansJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	//
    	Schema::create('wb_fans_jobs', function (Blueprint $table) {
//     		$table->engine = 'MyISAM';
    		$table->increments('id');
    		$table->bigInteger('uid')->comment('微博用户id');
    		$table->integer('f_fans_total')->unsigned()->default(0)->comment('统计的粉丝数');
    		$table->integer('f_follow_total')->unsigned()->default(0)->comment('统计的关注用户数');
    		$table->tinyInteger('f_status')->unsigned()->default(0)->comment('统计状态，0，初始化；1，统计完成；2重新统计');
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
        Schema::drop('wb_fans_jobs');
    }
}
