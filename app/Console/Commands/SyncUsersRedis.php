<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;
use Storage;
use Illuminate\Support\Facades\Redis as Redis;
use Illuminate\Database\QueryException;


class SyncUsersRedis extends Command
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
    	
    	$redis = Redis::connection("user");
    	
    	$offset = 0;
    	$limit = 100000;
    	$is_not_end = true;
    	while ($is_not_end){
    		
    		Storage::delete($table);
    		//该语句不能远程执行
    		try {
	    		$row = DB::statement("SELECT uid,status FROM $table limit $limit offset $offset INTO OUTFILE '$dataFile' FIELDS TERMINATED BY ','");
    		}
    		catch (QueryException $e){
    			$this->error("该程序不能远程执行");
    			exit;
    		}
    		
    		$this->info("$limit,$offset");
    		
	    	//写入文件减轻数据库压力
	    	$lines = file($dataFile);
	    	if(empty($lines)){
	    		$is_not_end = false;
	    		continue;
	    	}
	    	$redis->pipeline(function ($pipe) use ($lines) {
	    		foreach ($lines as $line_num => $line) {
	    			$arr = explode(",",$line);
	    			//set会重新值
	    			$pipe->setnx("uid:".$arr[0], trim($arr[1]));
	    		}
	    	});
			unset($lines);
	    	$offset += $limit;
    	}
    }
    
}
