<?php
/**
 * 设置抓取粉丝任务
 * 由于微博限制只能抓取200个粉丝，200个关注，
 * 所以不必设置抓取任务SetFansJob
 */
namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Libraries\Contracts\GetFans;

class GetFansJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    protected $uid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($uid)
    {
        //
        $this->uid = $uid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	$fansJob = new GetFans($this->uid);
    	$fansJob->getFansJob();
    }
    
}
