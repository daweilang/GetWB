<?php

namespace App\Http\Controllers\Admin;


use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Libraries\Contracts\GetComment;
use App\Models\Weibo;
// use App\Models\Wb_comment_job;
use Symfony\Component\DomCrawler\Crawler;
use Storage;

class TestController extends Controller
{
    
    /**
     * 测试微博抓取
     */
    public function exampleTest()
    {

    	$getContent = new GetComment("4051287207495926");
    	
    	$content = Storage::get("wbHtml/$getContent->gid/comment_1");

    	$crawler = new Crawler();
    	$crawler->addHtmlContent($content);
    	//iterate again
    	foreach ($crawler->filterXPath('//div[@class="list_li S_line1 clearfix"]') as $i => $node) {	
    		$c = new Crawler($node);
   			$c->addHtmlContent($node);
    		var_dump($c->filterXPath('//div[@class="WB_face W_fl"]')->filter('a')->attr('href'));
    		echo "<br>";
    	}
    	
//     	$nodeValues = $crawler->filterXPath('//div[@class="list_li S_line1 clearfix"]')->each(function (Crawler $node, $i) {
//     		return $node->text();
//     	});

    	$crawler->filterXPath('//div[@class="list_li S_line1 clearfix"]')->each(function (Crawler $row) {
    		
//     		$comment_id = $row->filterXPath('//div[@class="list_li S_line1 clearfix"]')->filter('div')->attr('comment_id');
//     		var_dump($comment_id);
//     		var_dump($row->filterXPath('//div[@class="WB_face W_fl"]')->filter('a')->attr('href'));
//     		var_dump($row->filterXPath('//div[@class="WB_face W_fl"]')->filter('a>img')->extract(array('alt', 'usercard')));
//     		$user_name = $row->filterXPath('//div[@class="WB_text"]')->filter('a')->text();
//     		var_dump($user_name);
//     		$text = trim($row->filterXPath('//div[@class="WB_text"]')->text());
//     		var_dump($text);
    		var_dump(mb_substr($text,mb_strlen($user_name."：",'UTF-8'), null, 'UTF-8'));
// var_dump($row->filterXPath('//div[@class="media_box"]'));
// 			if($row->filterXPath('//div[@class="WB_media_wrap clearfix"]')->getNode(0)){
//     			echo $row->filterXPath('//div[@class="media_box"]')->filter('ul>li>img')->attr('src');
//     			echo "<br>";
// 			}
//     		if($row->filterXPath('//div[@class="WB_media_wrap clearfix"]'->attr('style'))){
//     			echo "Aaaaa";
//     			var_dump($row->filterXPath('//div[@class="WB_media_wrap clearfix"]')->filter('div>ul>li>img')->attr('href'));
//     		}
    	});
//     	var_dump($nodeValues);
    	exit;
    }
    	
}
