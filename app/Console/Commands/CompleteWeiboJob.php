<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Libraries\Classes\SetJobLog;
use App\Libraries\Contracts\GetLike;
use App\Libraries\Contracts\GetComment;


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
    	$this->weibo = Wb_user_weibo::where(    			
    			function ($query) {
    				$query->where('status','=', 1)->orWhere('status', '=', -2);
    			}
    	)->first();
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
					
    	if($this->weibo){
    		
    		/**
    		 * 设置任务
    		 */
    		$this->weibo->status = -2;
    		$this->weibo->save();
    		
    		/**
    		 * 跟抓取的mid获得赞列表
    		 */
	    	if($this->weibo->mid)
	    	{ 			
	    		/**
	   			 * 设置抓取微博的赞任务
	   			 */
	   			if($this->weibo->like_total>0){
	    			$job_log = new SetJobLog();
	    			$job_log->createLog(['type'=>'like','object_id'=>$this->weibo->mid,'status'=>0,]);
	    		
	    			$likeJob = new GetLike($this->weibo->mid, $this->model);
	    			$likeJob->setJob();
	   				$job_log->updateLog(['status'=>1]);
	    		}

	    		/**
	   			 * 设置抓取微博的评论任务
	   			 */
	   			if($this->weibo->comment_total>0){
	    			$job_log = new SetJobLog();
	    			$job_log->createLog(['type'=>'comment','object_id'=>$this->weibo->mid,'status'=>0,]);
	    		
	    			$likeJob = new GetComment($this->weibo->mid, $this->model);
	    			$likeJob->setJob();
	   				$job_log->updateLog(['status'=>1]);
	    		}
	    		
	    		$this->weibo->status = 2;
	    		$this->weibo->save();

    		}
    	}
    }
}
