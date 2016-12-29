<?php

/**
 *  GetFans.php 抓取粉丝
 * @copyright			(C) daweilang
 * @license				https://github.com/daweilang/
 *
 */


namespace App\Libraries\Contracts;

use App\Models\Wb_user;
use App\Models\Wb_fans;
use App\Models\Wb_fans_job;

use App\Libraries\Classes\WeiboContent;
use Symfony\Component\DomCrawler\Crawler;

use Storage;


class GetFans
{	
	private $uid;
	private $fansFile;
	private $followFile;
	private $user;
	
	public function __construct($uid)
	{
		$this->uid = $uid;
		$this->user = Wb_user::where('uid', $uid)->first();
		$this->fansFile = "wbUserHtml/$uid/fans";
		$this->followFile = "wbUserHtml/$uid/follow";
	}
	

	/**
	 * 根据uid获得fans和follow
	 */
	public function getFansJob()
	{
		###如果用户信息不全先补全信息
		if(!$this->user->exists || $this->user->status == '0'){
			$getContent = new GetUserInfo($this->uid);
			$this->user = $getContent->GetUserInfoProcess();
		}
		
		//插入表数据，代表抓取数据设置
		$job = Wb_fans_job::create( [ 'uid' => $this->uid, 'f_status' => '0', ]);
		
		//粉丝
		Storage::delete($this->fansFile);
		$page_total = 20;
		if($this->user->fans < 200){
			$page_total = ceil($this->user->fans/10);
		}
		for($page=1;$page<=$page_total;$page++){
			//将粉丝数据写入一个文件进行分析
			$this->getFansHtml($page);
			sleep("1");
		}
		$this->explainFansPage();	
		
		//关注
		Storage::delete($this->followFile);
		$page_total = 20;
		if($this->user->follow < 200){
			$page_total = ceil($this->user->follow/10);
		}
		for($page=1;$page<=$page_total;$page++){
			$this->getFansHtml($page,'follow');
			sleep("1");
		}
		$this->explainFansPage('', 'follow');
		
		//获得抓取的粉丝数和关注数
		$job->f_fans_total = Wb_fans::where('uid', $this->uid)->count();
		$job->f_follow_total = Wb_fans::where('oid', $this->uid)->count();
		$job->f_status = '1';
		$job->save();
	}
	
	
	
	/**
	 * 抓取评论页面写入文件
	 */
	public function getFansHtml($page, $type = 'fans')
	{
		//评论页地址
		http://weibo.cn/2803301701/fans
		if($type == 'fans'){
			$fansUrl = sprintf(config('weibo.WeiboUser.userFans'), $this->uid);
			$file = $this->fansFile;
		}
		elseif($type = 'follow'){
			$fansUrl = sprintf(config('weibo.WeiboUser.userFollow'), $this->uid);
			$file = $this->followFile;
		}

		$wb = new WeiboContent();
		//抓取
		$html = $wb->getFansPage($fansUrl, ['mp'=>20, 'page'=>$page], config('weibo.CookieFileCn.sina'), config('weibo.CookieFileCn.curl'));
		if(empty($html)){
			throw new \Exception("无法获取粉丝列表，请检查");
		}
		$crawler = new Crawler();
		$crawler->addHtmlContent($html);
		
		//fans页面和follow页面的结构不同分开处理
		if($type == 'fans'){
			$div = $crawler->filterXPath('//div[@class="c"]')->first()->html();
		}
		elseif($type = 'follow'){
			$div = $crawler->filter('body')->html();
		}
		Storage::append($file, $div);
		if(!Storage::exists($file)){
			throw new \Exception("无法储存微博评论页面");
		}
		return $div;
	}
	
	
	/**
	 * 获得评论的html分析
	 * @param $commentHtml 评论的html
	 * @param unknown $file 评论储存的html页面
	 */
	public function explainFansPage($html='', $type = 'fans')
	{
		if($type == 'fans'){
			$file = $this->fansFile;
		}
		elseif($type == 'follow'){
			$file = $this->followFile;
		}
		
		if(empty($html) && Storage::exists($file)){
			//该页面应该是html
			$html = Storage::get($file);
		}
		else{
			throw new \Exception("粉丝列表为空，请检查");
		}
		$crawler = new Crawler();
		$crawler->addHtmlContent($html);
		$crawler->filter('table')->each(function (Crawler $row) use ($type) {		
			//获得uid
			//注意，关注列表中，已关注用户回忽略
			$uidUrl = $row->filter('td')->last()->filter('a')->last()->attr('href');
			if ($uidUrl && preg_match('/uid=(\d+)/', $uidUrl, $m)){
				$uid = $m[1];
			}
			else{
				return ;
			}
			
			$tmp = explode("/", $row->filter('td')->first()->filter('a')->attr('href'));
			$usercard = $tmp[count($tmp)-1];
			$photo_url = $row->filter('td')->first()->filter('a>img')->attr('src');
			$username = $row->filter('td')->last()->filter('a')->text();
			$fans = 0;
			if(preg_match('/<br>粉丝(\d+)人<br>/', $row->filter('td')->last()->html(), $m)){
				$fans = $m[1];
			}			
			$userInfo = [
							'uid'=>$uid, 
							'usercard' => $usercard,
							'username' => $username,
							'photo_url' => $photo_url,		
							'fans' => $fans,		
			];
			//储存用户信息不用区分粉丝或关注
			$this->saveWbUser($userInfo);
			
			if($type == 'fans'){
				$this->saveWbFans($uid);
			}
			elseif($type == 'follow'){
				$this->saveWbFollow($uid);
			}
			
		});
	}
	
	
	private function saveWbUser($userInfo)
	{
		$user = Wb_user::firstOrNew(['uid' => $userInfo['uid']]);
		//更新时不必改动项
		if(!$user->exists){
			$user->uid = $userInfo['uid'];
		}
		$user->fans = $userInfo['fans'];
		$user->usercard = $userInfo['usercard'];
		$user->username = $userInfo['username'];
		$user->photo_url = $userInfo['photo_url'];
		$user->save();
	}

	
	/**
	 * 保存粉丝关系，uid是被关注者
	 * @param unknown $oid 粉丝id
	 */
	private function saveWbFans($oid)
	{
		$user = Wb_fans::firstOrNew(['uid' => $this->uid, 'oid'=>$oid]);
		//更新时不必改动项
		if($user->exists){
			$user->status = 1;
		}
		else{
			$user->uid = $this->uid;
			$user->oid = $oid;
			$user->status = 1;
		}
		$user->save();
	}

	/**
	 * 保存关注关系，uid是关注者
	 * @param unknown $uid 关注的id
	 */
	private function saveWbFollow($uid)
	{
		$user = Wb_fans::firstOrNew(['uid' => $uid, 'oid'=>$this->uid]);
		//更新时不必改动项
		if($user->exists){
			$user->status = 1;
		}
		else{
			$user->uid = $uid;
			$user->oid = $this->uid;
			$user->status = 1;
		}
		$user->save();
	}
}
