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
    		$table->integer('fans')->comment('已统计的fans');
    		$table->integer('follow')->comment('已统计的关注');
    		$table->integer('weibos')->comment('已统计的微博');
    		$table->tinyInteger('status')->comment('获取用户状态');
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
