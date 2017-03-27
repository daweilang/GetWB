<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

class RetryWBException extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retrywb {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '设置重新抓取任务';
    
    

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
    	$type = $this->argument('type');
    	$table = static::getJobPageModel($type);
//     	$row = DB::table($table)->select(DB::raw('mid , max(j_page) as j_page'))->where('j_page', '>', '1000')->groupBy('mid')->take(3)->get();
    	$row = DB::table($table)->select(DB::raw('mid , max(j_page) as j_page'))->groupBy('mid')->get();
    	foreach($row as $v){
    		if($v->mid)
    		{
    			$getModel = "\App\Libraries\Contracts\Get".ucfirst($type);
   			
    			//必须使用orm生成的数组数据
    			$model = "\App\Models\\" . $getModel::getJobPageModel();
    			$jobPage= $model::where(['mid'=>$v->mid, 'j_page'=>$v->j_page])->first();
    			
    			$this->info('mid : '.$v->mid.' , j_page : '.$v->j_page);
    			
    			$retryJob = new $getModel($jobPage->mid, $jobPage->model);
    			//设置任务
    			$retryJob->setQueueClass("Get".ucfirst($type)."ContentJob", $jobPage);
//     			sleep(1);
    		}
    	}
    }
    
    
    
    /**
     * 本模块使用的pageModel
     * @return string
     */
    protected static function getJobPageModel($type)
    {
    	if(!in_array($type, ['like', 'comment', 'forward'])){
    		throw new RuntimeException('参数错误.');
    	}
    	return 'wb_'.$type.'_jobs';
    }
    
}
