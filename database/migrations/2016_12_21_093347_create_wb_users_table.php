<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWbUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
	public function up()
	{
		//
		Schema::create('wb_users', function (Blueprint $table) {
			$table->bigInteger('uid')->comment('微博用户id');
			$table->string('username','30')->comment('用户姓名');
			$table->string('usercard','20')->comment('用户英文id');
			$table->integer('fans')->comment('fans统计');
			$table->integer('follow')->comment('关注统计');
			$table->integer('weibos')->comment('发微博数');
			$table->string('type','150')->comment('微博类型');
			$table->string('male','5')->comment('性别');
			$table->string('intro','255')->comment('微博介绍');
			$table->string('place','100')->comment('位置');
			$table->string('photo_url', 100)->comment('头像');
			$table->tinyInteger('status')->comment('获取用户状态，0，添加; 1,获得用户信息');
			$table->timestamps();
			$table->primary('uid');
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
