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
			$table->bigInteger('comment_id');
			$table->bigInteger('mid')->comment('微博id');
			$table->string('usercard','30')->comment('拼成微博主页');
			$table->bigInteger('uid')->comment('评论用户id');
			$table->bigInteger('oid')->comment('微博所属用户id');
			$table->string('username','30')->comment('用户姓名');
			$table->string('content','255')->comment('评论内容');
			$table->bigInteger('reply_comment_id')->comment('评论回复id');
			$table->timestamp('wb_created')->default("0000-00-00 00:00:00")->comment('评论发布时间');
			$table->string('comment_pic_url', 255)->comment('评论图片');
			$table->string('comment_pic_md5', 100)->comment('评论图片MD5');
			$table->timestamps();
			$table->primary('comment_id');
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
