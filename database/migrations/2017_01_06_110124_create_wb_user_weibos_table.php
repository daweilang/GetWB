<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWbUserWeibosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	//
    	Schema::create('wb_user_weibos', function (Blueprint $table) {
    		$table->bigIncrements('id');
    		$table->bigInteger('mid')->comment('微博id');
    		$table->bigInteger('uid')->comment('微博用户id');
    		$table->string('code','30')->comment('短域名');
    		$table->integer('comment_total')->unsigned()->default(0)->comment('评论');
    		$table->integer('like_total')->unsigned()->default(0)->comment('赞');
    		$table->integer('forward_total')->unsigned()->default(0)->comment('转发统计');
    		$table->string('title','255')->comment('微博内容');
    		$table->timestamp('wb_created')->default("0000-00-00 00:00:00")->comment('微博发布时间');
    		$table->string('wb_pic_url', 255)->comment('微博图片');
    		$table->tinyInteger('status')->comment('状态, 0, 未获取；1，补全信息；2，设置任务；3，重新抓取；-1，补全信息失败；-2，设置抓取任务失败');
    		$table->timestamps();
    		$table->unique('code');
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
