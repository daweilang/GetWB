<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeiboTable extends Migration
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
    		$table->integer('wb_userid');
    		$table->string('wb_title', '150');
    		$table->string('wb_url');
    		$table->integer('wb_comment_page')->unsigned()->default(0);
    		$table->integer('wb_comment_total')->unsigned()->default(0);
    		$table->tinyInteger('wb_status')->unsigned()->default(0);
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
        //
    }
}
