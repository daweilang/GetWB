<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Libraries\Contracts\GetUserCompleteWB;
use App\Models\Wb_complete;

class GetCompleteWBJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    protected $uid;
    protected $userinfo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($uid)
    {
        //
        $this->uid = $uid;
        $this->userinfo = Wb_complete::find($uid);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {		
    	$weibos = new GetUserCompleteWB($this->userinfo);
    	$weibos->getUserWeibos();
	
   		$this->userinfo->status = 1;
   		$this->userinfo->save();
    }
    
    
    /**
     * 处理失败任务
     *
     * @return void
     */
    public function failed()
    {
    	//失败任务的状态
    	$this->userinfo->status=2;
    	$this->userinfo->save();
    }
    
}
