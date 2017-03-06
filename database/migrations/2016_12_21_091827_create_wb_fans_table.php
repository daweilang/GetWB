<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWbFansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	//
    	Schema::create('wb_fans', function (Blueprint $table) {
    		$table->engine = 'MyISAM';
    		$table->bigIncrements('id');
    		$table->bigInteger('uid')->comment('微博用户id');
    		$table->bigInteger('oid')->comment('微博fans');
    		$table->tinyInteger('status')->default('1')->comment('关注状态');
    		$table->unique(['uid','oid'], 'wb_fans_unique');
    	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::drop('wb_fans');
    }
}
