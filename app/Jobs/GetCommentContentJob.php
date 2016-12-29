<?php
/**
 * 设置抓取微博评论队列的队列
 */
namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Wb_comment_job;
use App\Libraries\Contracts\GetComment;

class GetCommentContentJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    protected $wb_comment_job;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Wb_comment_job $comment_page)
    {
        //
        $this->wb_comment_job = $comment_page;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	$getCommentJob = new GetComment($this->wb_comment_job->gid);
    	$content = $getCommentJob->getCommentHtml($this->wb_comment_job->j_comment_page);
    	$getCommentJob->explainCommentPage($content);
    	
    	//抓取完成后的状态
    	$this->wb_comment_job->j_status = '2';
    	$this->wb_comment_job->save();
    }
    
    /**
     * 处理失败任务
     *
     * @return void
     */
    public function failed()
    {
    	//失败任务的状态
    	$this->wb_comment_job->j_status = '4';
    	$this->wb_comment_job->save();
    }
    
}
