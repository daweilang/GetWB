<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWbSetjobLogsTable extends Migration
{
    /**
     *  设置队列日志，队列设置时间，类型，状态等
     *
     * @return void
     */
    public function up()
    {
    	//
    	Schema::create('wb_setjob_logs', function (Blueprint $table) {
 //     	$table->engine = 'MyISAM';
    		$table->increments('id');
    		$table->string('type', '15')->comment('类型, comment:评论；like:赞；owner:博主');
    		$table->bigInteger('object_id')->comment('该队列所属类型对应id，评论和赞对应mid，博主对应oid');
    		$table->tinyInteger('status')->unsigned()->default(0)->comment('统计状态，0，初始化；1，队列完成；2，队列失败');
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
