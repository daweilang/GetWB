<?php

namespace App\Http\Controllers\Admin;

use Log;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Libraries\Contracts\GetLike;
use App\Models\Weibo;
use Symfony\Component\DomCrawler\Crawler;
use Storage;
use Mockery\Expectation;

class TestController extends Controller
{
    
    /**
     * 测试微博抓取
     */
    public function exampleTest()
    {
    	Log::info('Showing user profile for user: ');
//     	throw new \Exception("无法获取粉丝列表，请检查");
    	$getContent = new GetLike("4059409665160775");
    	$getContent->explainLikePage('', "wbHtml/$getContent->mid/like_1");
    	
    }
    
    
    /**
     * 测试微博抓取
     */
    public function exampleCrawler()
    {
    
    	$html = <<<'HTML'
<!DOCTYPE html>
<html>
        <body>
        <p class="message">Hello World!</p>
        <p class="msg">Hello Crawler!</p>
        </body>
</html>
HTML;
    	$crawler = new Crawler($html);
//     	$tag = $crawler->filterXPath('//body/p')->text();
    	
    	echo $crawler->filterXPath('//body/p')->attr('class');
    	echo $crawler->filterXPath('//body/p')->last()->text();
    	echo "<br>";
    	var_dump($crawler->filterXPath('//body')->html());
    	
    	
    	foreach ($crawler->filterXPath('//body/p') as $i => $node) {
    		$c = new Crawler($node);
    		echo $c->filter('p')->text();
    		echo "<br>";
    	}
    	$nodeValues = $crawler->filterXPath('//body/p')->each(function (Crawler $node, $i) {
    	    	return $node->text();
    	});
    }
    	
}
