<?php
/**
 * 由于微博评论页过多，设置抓取评论任务队列时间较长
 * 设计设置抓取微博评论队列的队列
 * 队列的任务是根据评论页数设置抓取队列
 */
namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Weibo;
use App\Libraries\Contracts\GetComment;

class SetCommentJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    protected $gid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($gid)
    {
        //
        $this->gid = $gid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	$commentJob = new GetComment($this->gid);
    	$commentJob = $commentJob->setCommentJob();
    }
    
}
