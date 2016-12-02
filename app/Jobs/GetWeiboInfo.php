<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Weibo;
use App\Libraries\Contracts\GetWeiboContent;

class GetWeiboInfo extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    protected $weibo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Weibo $weibo)
    {
        //
        $this->weibo = $weibo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	$url = $this->weibo->wb_url;
    	$getContent = new GetWeiboContent();
    	$content = $getContent->getWeiboHtml($url);
    }
}
