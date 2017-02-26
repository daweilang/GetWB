<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWbCompletesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	//
    	Schema::create('wb_completes', function (Blueprint $table) {
    		$table->bigInteger('uid')->comment('微博用户id');
    		$table->string('username','30')->comment('用户姓名');
    		$table->string('usercard','20')->comment('用户英文id');
    		$table->integer('fans')->comment('粉丝');
    		$table->integer('follow')->comment('关注');
    		$table->integer('weibos')->comment('微博数');
    		$table->integer('domain')->comment('用户微博接口使用');
    		$table->string('page_id','30')->comment('用户微博接口使用id');
    		$table->tinyInteger('status')->comment('获取用户信息状态： 0,未获取；1，已抓取；2，重新抓取；3，设置抓取全部微博任务；4，任务设置完成；-1，抓取用户信息失败；-3，设置抓取微博任务失败');
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
