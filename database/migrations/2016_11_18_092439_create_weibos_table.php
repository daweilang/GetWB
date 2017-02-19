<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeibosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    	Schema::create('weibos', function (Blueprint $table) {
    		$table->increments('id');
    		$table->bigInteger('mid')->comment('微博id');
    		$table->string('wb_name', '150')->comment('微博标题');
    		$table->string('code','30')->comment('短域名');
    		$table->bigInteger('uid')->comment('微博用户id');
    		$table->string('wb_url');
    		$table->string('title', '255');    		
    		$table->integer('comment_page')->unsigned()->default(0);
    		$table->integer('comment_total')->unsigned()->default(0);
    		$table->integer('like_page')->unsigned()->default(0);
    		$table->integer('like_total')->unsigned()->default(0);
    		$table->tinyInteger('status')->unsigned()->default(0);
    		$table->timestamps();
    		$table->timestamp('comment_up');
    	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    	Schema::drop('weibos');
    }
}
