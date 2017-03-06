<?php

/**
 *  GetCompleteWB.php 抓取用户所有微博数据
 *  使用com接口抓取
 * @copyright		(C) daweilang
 * @license			https://github.com/daweilang/
 *
 */


namespace App\Libraries\Contracts;


use App\Models\Wb_complete;
use App\Models\Wb_complete_job;
use App\Models\Wb_user_weibo;

use App\Jobs\GetCompleteWBJob;
use App\Libraries\Classes\WeiboContent;
use App\Libraries\Classes\TraitGetConfig;

use Symfony\Component\DomCrawler\Crawler;

use Storage;


class GetCompleteWB
{	
	
	use TraitGetConfig;
	
	/**
	 * 取得的微博用户信息
	 * @var unknown
	 */
	private $userinfo;
	
	/**
	 * 该条微博用户id
	 */
	public $uid;
	
	/**
	 * 设置抓取任务时抓取的页号
	 */
	private $getPage;
	
	/**
	 * 设置队列延时时间设置
	 * 默认由TraitGetConfig设置
	 * @var integer
	 */
	public $delay = 3;
	
	
	public function __construct(Wb_complete $userinfo)
	{
		/**
		 * 获得全局延时时间设置
		 */
		$this->getQueueConf();
		
		$this->userinfo = $userinfo;
		
		$this->uid = $this->userinfo->uid;
		
	}
	
	
	/**
	 * 根据微博数，设置抓取微博任务
	 */
	public function setJob($jobName='', $page = '1')
	{
		//complete任务表登记任务
		//@var \App\Models\Wb_complete_job $complete_job
		$complete_job = Wb_complete_job::create( [ 'uid' => $this->uid, 'j_complete_page' => $page,]);
					
		// 设置任务 $like_job 数据表储存了需要抓取数据的来源
		if(empty($jobName)){
			$job = (new GetCompleteWBJob($complete_job))->delay($this->delay);
		}
		else{
			$job = (new GetCompleteWBJob($complete_job))->onQueue($jobName)->delay($this->delay);
		}
		dispatch($job);
	}
	
	
	/**
	 * 抓取评论页面写入文件
	 */
	public function getHtml($page)
	{
		$this->getPage = $page;
		
		//使用微博接口获得数据
		//用户每页请求三次
		$wb = new WeiboContent();
		$file = "wbUserHtml/{$this->uid}/weibos_$page";
		Storage::delete($file);
		
		$html = '';
		foreach(config('weibo.WeiboInfo.weibosUrl') as $v){
			$thisUrl = sprintf($v, $this->userinfo->domain, $page, $this->userinfo->page_id, $page);
			//抓取
			$content = $wb->getWBHtml($thisUrl, config('weibo.CookieFile.weibo'), config('weibo.CookieFile.curl'));
			
			$array = json_decode($content, true);
			if(!is_array($array) || $array['code'] !== '100000'){
				Storage::put("wbUserHtml/$this->uid/error_$page", $content);
				throw new \Exception("无法获取接口，请检查获取结果");
			}
			$html .= $array['data'];
		}
		Storage::put($file, $html);
		if(!Storage::exists($file)){
			throw new \Exception("无法储存页面");
		}
		return $html;
	}
	
	
	/**
	 * 获得评论的html分析
	 * @param $commentHtml 评论的html
	 * @param unknown $file 评论储存的html页面
	 */
	public function explainPage($html, $file='')
	{
		if(empty($html)){
			if(Storage::exists($file)){
				//该页面应该是html
				$html = Storage::get($file);
			}
			else{
				throw new \Exception("微博列表为空，请检查");
			}
		}
		$crawler = new Crawler();
		$crawler->addHtmlContent($html);
		
		//微博接口返回的样式有 class="WB_cardwrap WB_feed_type S_bg2 WB_feed_vipcover "和class="WB_cardwrap WB_feed_type S_bg2 "两种
		$page_total = 0;
		$crawler->filterXPath('//div[contains(@action-type, "feed_list_item")]')->each(function (Crawler $row) use (&$page_total) {
			
// 			//获得weibo码
			$mid = $row->filter('div')->attr('mid');
			if(empty($mid)) return false;		
			
			$href = $row->filterXPath('//div[@class="WB_from S_txt2"]')->filter('a')->attr('href');
			// /1778181861/EwKLqhewY?from=page_1005051778181861_profile&wvr=6&mod=weibotime
			if(preg_match('/\/'.$this->uid.'\/(\w+)\?from/', $href, $m)){
				$code = $m[1];
			}
			else{
				//不是博主微博或取不到微博code
				return false;
			}
			
			$time = $row->filterXPath('//div[@class="WB_from S_txt2"]')->filter('a')->attr('date');
			$wb_created = date("Y-m-d H:i:s", substr($time, 0, 10));
			
			$action = [];
			
			$row->filterXPath('//div[@class="WB_handle"]')->filter('li')->each(function (Crawler $row) use ( &$action ){
				$action[$row->filter('a')->attr('action-type')] = $row->filter('a')->filter('em')->last()->text();
			});
			
			$wb = Wb_user_weibo::firstOrNew(['mid' => $mid]);
			//更新时不必改动项
			if(!$wb->exists){
				$wb->mid = $mid;
			}
			$wb->uid = $this->uid;
			$wb->code = $code;
			$wb->comment_total = $action['fl_comment'];
			$wb->like_total = $action['fl_like'];
			$wb->forward_total = $action['fl_forward'];
			$wb->wb_created = $wb_created;
			$wb->save();
			
			//由于存在数据丢失情况，记录每页统计数
			$page_total++;
		});
		
		sleep(1);
		
		$last_page_text = '';
		if($crawler->filterXPath('//div[@class="W_pages"]')->count()){
			$last_page_text = $crawler->filterXPath('//div[@class="W_pages"]')->filter('a')->last()->text();
		}
		//判断是否有下一页，有下一页则建立下一页任务
		if($last_page_text == '下一页'){
			$this->setJob("", $this->getPage+1);
		}
		else{
			//没有最后一页是尾页，停止设置抓取
			$this->userinfo->status=4;
			$this->userinfo->save();
		}
		
		return $page_total;
	}
	
}
