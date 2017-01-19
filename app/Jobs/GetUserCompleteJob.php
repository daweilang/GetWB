<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Libraries\Contracts\GetUserInfo;
use App\Libraries\Contracts\GetUserComplete;

class GetUserCompleteJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    protected $usercard;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($usercard)
    {
        //
        $this->usercard = $usercard;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	###分析用户微博页的业务逻辑
	    $getContent = new GetUserComplete($this->usercard);	    	
	    $getContent->GetUserInfoProcess();
   		return true;
    }
    
}
