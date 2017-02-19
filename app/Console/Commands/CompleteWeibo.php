<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Libraries\Contracts\CompleteWeiboInfo;

use App\Models\Wb_user_weibo;

class CompleteWeibo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comwbinfo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '补全微博';
    
    
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
    				$query->where('status', '=', 0)->orWhere('status', '=', -1);
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
    		$this->weibo->status = -1;
    		$this->weibo->save();
	    	
    		/**
    		 * 抓取com微博页面补全信息
    		 */
    		$weiboCom = new CompleteWeiboInfo($this->weibo);
    		$weiboCom->Process();
    		
    		/**
    		 * 判断抓取是否成功
    		 */
	    	if($this->weibo->mid)
	    	{ 			
	    		$this->weibo->status = 1;    		
    			$this->weibo->save();
	    	}
    	}
    }
}
