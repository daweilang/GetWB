<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Libraries\Contracts\GetUserInfo;

class GetUserInfoJob extends Job implements ShouldQueue
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
    		###分析微博页的业务逻辑
	    	$getContent = new GetUserInfo($this->usercard);	    	
	    	$getContent->GetUserInfoProcess();
   			return true;
    }
    
}
