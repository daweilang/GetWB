<?php

namespace App\Jobs;

use Log;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Weibo;
use App\Libraries\Classes\SetJobLog;
use App\Libraries\Contracts\GetWeiboInfo;
use function GuzzleHttp\json_decode;

class GetWeiboJob extends Job implements ShouldQueue
{
	use InteractsWithQueue, SerializesModels;

	protected $weibo;

	//执行延时
	public $delay;

	//任务名，true或false
	private $jobName = FALSE;
	
	private $model = 'Weibo';

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct(Weibo $weibo)
	{
		//
		$this->weibo = $weibo;

		//获得全局延时时间设置
		if(empty($this->delay)){
			$this->delay = config('queue.delay');
		}
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		//只有微博状态为未分析时候才有效
		if(in_array($this->weibo->status, [0, 2])){

			###分析微博页的业务逻辑
			$getContent = new GetWeiboInfo($this->weibo);
			//获得微博页面内容
			$content = $getContent->getWeiboHtml();
			//分析微博的内容录入数据
			$getContent->explainWeibo($content);
			
			$array = json_decode($this->weibo->wb_scope, true);
			if(is_array($array)){
				foreach($array as $type){
					$totalName = $type."_total";
					if($this->weibo->$totalName > 0){
						$this->SetTypeJob($type);
					}
				}
			}
			return true;
		}
		else{
			return ;
		}
	}

	
	/**
	 * 处理失败任务
	 * @return void
	 */
	public function failed()
	{
		$this->weibo->status = -1;
		$this->weibo->save();
		Log::info('无法获得微博信息', ['Job'=>'GetWeibJob']);
	}
	
	
	/**
	 * 封装设置任务
	 */
	protected function SetTypeJob($type)
	{
		$job_log = new SetJobLog();
		$job_log->createLog(['type'=>$type, 'object_id'=>$this->weibo->mid, 'status'=>0,]);
		$thisModel = "\App\Libraries\Contracts\Get".ucfirst($type);
		$likeJob = new $thisModel($this->weibo->mid, $this->model);
		$likeJob->setJob();
		$job_log->updateLog(['status'=>1]);
	}
	
}
