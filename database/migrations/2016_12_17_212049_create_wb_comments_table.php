<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWbCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	//
    	Schema::create('wb_comments', function (Blueprint $table) {
    		$table->string('comment_id', '30');
    		$table->string('gid', '30')->comment('评论所属组，对应wb_comment_gid');
    		$table->string('wb_face','200')->comment('微博个人主页');
    		$table->string('wb_usercard','50')->comment('微博个人id');
    		$table->string('wb_username','30')->comment('用户姓名');
    		$table->string('wb_content','255')->comment('评论内容');
    		$table->string('wb_reply_username','50')->comment('评论回复用户');
    		
    		$table->timestamp('wb_created')->default("0000-00-00 00:00:00")->comment('评论发布时间');
    		$table->string('wb_comment_pic_url', 255)->comment('评论图片');
    		$table->string('wb_comment_pic_md5', 100)->comment('评论图片MD5');
    		
    		$table->timestamps();
    		$table->unique('comment_id');
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
