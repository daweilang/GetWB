<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWbForwardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::create('wb_forwards', function (Blueprint $table) {
    		$table->bigInteger('forward_id');
    		$table->bigInteger('mid')->comment('微博id');
    		$table->string('usercard','30')->comment('拼成微博主页');
    		$table->bigInteger('uid')->comment('转发用户id');
    		$table->bigInteger('oid')->comment('微博所属用户id');
    		$table->string('username','30')->comment('用户姓名');
    		$table->string('content','255')->comment('转发内容');
    		$table->timestamp('wb_created')->default("0000-00-00 00:00:00")->comment('评论发布时间');
    		$table->string('forward_pic_url', 255)->comment('评论图片');
    		$table->string('forward_pic_md5', 100)->comment('评论图片MD5');
    		$table->timestamps();
    		$table->primary('forward_id');
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
