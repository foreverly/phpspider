<?php
require __DIR__ . '/../../vendor/autoload.php';

use QL\QueryList;
// use QL\Ext\PhantomJs;

error_reporting(-1);
ini_set('display_errors', 1);

// $ql = QueryList::getInstance();
// // 安装时需要设置PhantomJS二进制文件路径
// $ql->use(PhantomJs::class,'/usr/local/bin/phantomjs');
// //or Custom function name
// $ql->use(PhantomJs::class,'/usr/local/bin/phantomjs','browser');

$url = "https://hotel.meituan.com/ganzhou/";
// $html  = $request->get("https://hotel.meituan.com/ganzhou/");
// $total = selector::select($html, '//*[@id="app"]/section/div/div[1]/div[2]/div/ul/li[9]/a/text()');
// $page  = 1;
// $size  = selector::select($html, '//*[@id="list-view"]/div[1]/article[last()]/div[2]/h3/a/em');

$data = QueryList::get($url)
	->rules([
		// 每页大小
		'size' => array("#list-view > div.poi-results > article.poi-item.poi-item-active > div.info-wrapper > h3 > a > em", "text"),
		// 总页数
		'total' => array("#app > section > div > div.content-view > div.list-page-view > div > ul > li:nth-child(10) > a", "text")
	])
	->query()
	->getData();

var_dump($data);exit;
// public function qulist($url, $size, $total){

// 	for ($i=0; $i < $total; $i++) {
// 		$page = $i + 1;
// 		$url .= "pn{$page}";
// 		$data = QueryList::get($url)
// 		    // 设置采集规则
// 		    ->rules([
// 		        // 爬取图片地址
// 		        "title"=>array(".board-wrapper dd img.board-img","data-src"),
// 		        // 爬取电影名
// 		        "name"=>array(".board-wrapper dd .movie-item-info .name","html"),
// 		        // 爬取电影主演信息
// 		        "star"=>array(".board-wrapper dd .movie-item-info .star","html"),
// 		        // 爬取上映时间
// 		        "releasetime"=>array(".board-wrapper dd .movie-item-info .releasetime","html"),
// 		    ])
// 	    	->query()
// 	    	->getData();

// 	    $excel_array = $data->all();
// 	}

//     // var_dump($excel_array);exit;
// }