<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 微博点赞
 * @author daweilang
 *
 */

class CreateWbLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	//
    	Schema::create('wb_likes', function (Blueprint $table) {
    		$table->bigInteger('mid')->comment('微博id');
    		$table->bigInteger('oid')->comment('微博所属用户id');
    		$table->bigInteger('uid')->comment('点赞用户id');
    		$table->unique(['mid','uid'], 'wb_like_unique');
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
