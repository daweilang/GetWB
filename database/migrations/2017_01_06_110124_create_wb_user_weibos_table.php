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
    		$table->string('code','20')->comment('短域名');
    		$table->integer('comment')->comment('评论');
    		$table->integer('like')->comment('攒');
    		$table->integer('repost')->comment('转发统计');
    		$table->string('wb_content','255')->comment('微博内容');
    		$table->timestamp('wb_created')->default("0000-00-00 00:00:00")->comment('微博发布时间');
    		$table->string('wb_pic_url', 255)->comment('微博图片');
    		$table->timestamps();
    		$table->unique('mid');
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
