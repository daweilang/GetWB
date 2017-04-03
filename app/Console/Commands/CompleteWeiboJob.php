<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Libraries\Classes\SetJobLog;

// use App\Libraries\Contracts\GetLike;
// use App\Libraries\Contracts\GetComment;
// use App\Libraries\Contracts\GetForward;

use App\Models\Wb_user_weibo;


class CompleteWeiboJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comwbjob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '设置完整的抓取微博任务';
    
    
    /**
     * 该条微博对象
     * @var object
     */
    protected $weibo = '';
    
    
    /**
     * 该条微博使用的model
     * @var object
     */
    private $model = 'Wb_user_weibo';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
    	parent::__construct();
    	
    	/**
    	 * 定时任务获取一条微博数据
    	 * @var unknown $weibo
    	 */
    	$this->weibo = Wb_user_weibo::where('status', 1)->take(10)->get();
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
					
    	if($this->weibo){

    		//先将数据设置状态
    		foreach($this->weibo as $v){
	    		$v->status = -2;
	    		$v->save();
    		}
    		
    		foreach($this->weibo as $v){
    			
		    	if($v->mid)
		    	{	 
		    		//设置抓取范围
		    		$array = ['forward', 'comment', 'like'];
		    		foreach($array as $type){
		    			$totalName = $type."_total";
		    			if($v->$totalName > 0){
		    				$this->SetTypeJob($type, $v->mid);
		    			}
		    		}
		    		
		    		$v->status = 2;
		    		$v->save();
		    		
		    		sleep(1);
	    		}
    		}
    	}
    }
    
    
    /**
     * 封装设置任务
     */
    protected function SetTypeJob($type, $mid)
    {
    	$job_log = new SetJobLog();
    	$job_log->createLog(['type'=>$type, 'object_id'=>$mid, 'status'=>0,]);
    	$thisModel = "\App\Libraries\Contracts\Get".ucfirst($type);
    	$likeJob = new $thisModel($mid, $this->model);
    	$likeJob->setJob();
    	$job_log->updateLog(['status'=>1]);
    }
}
