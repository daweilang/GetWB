<?php
/**
 * 设置抓取微博赞队列的队列
 */
namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Wb_like_job;
use App\Libraries\Contracts\GetLike;

class GetLikeContentJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    protected $wb_like_job;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Wb_like_job $like_page)
    {
        //
        $this->wb_like_job = $like_page;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	$getLikeJob = new GetLike($this->wb_like_job->mid);
    	$content = $getLikeJob->getLikeHtml($this->wb_like_job->j_like_page);
    	$getLikeJob->explainLikePage($content);
    	
    	//抓取完成后的状态
    	$this->wb_like_job->j_status = '2';
    	$this->wb_like_job->save();
    }
    
    /**
     * 处理失败任务
     *
     * @return void
     */
    public function failed()
    {
    	//失败任务的状态
    	$this->wb_like_job->j_status = '4';
    	$this->wb_like_job->save();
    }
    
}
