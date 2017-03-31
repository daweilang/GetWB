<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;
use Storage;
use Illuminate\Support\Facades\Redis as Redis;

class SetUsersRedis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '录入用户数据到redis';
    
    

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
    	parent::__construct();
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	$table =  "wb_users";
    	
    	//写入同名文件
    	$dataFile = storage_path("app/$table");
    	
    	$offset = 0;
    	$limit = 100000;
    	$is_not_end = true;
    	while ($is_not_end){
    		print "$limit,$offset\n";
    		Storage::delete($table);
	    	$row = DB::statement("SELECT uid,status FROM $table limit $limit offset $offset INTO OUTFILE '$dataFile' FIELDS TERMINATED BY ','");
	    	//写入文件减轻数据库压力
	    	$lines = file($dataFile);
	    	if(empty($lines)){
	    		$is_not_end = false;
	    		continue;
	    	}
	    	Redis::pipeline(function ($pipe) use ($lines) {
	    		foreach ($lines as $line_num => $line) {
	    			$arr = explode(",",$line);
	    			//set会重新值
	    			$pipe->setnx($arr[0], trim($arr[1]));
	    		}
	    	});
			unset($lines);
	    	$offset += $limit;
    	}
    }
    
}
