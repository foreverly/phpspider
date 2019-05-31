<?php
require_once __DIR__ . '/../../autoloader.php';

use phpspider\core\requests;
use phpspider\core\selector;

error_reporting(-1);
ini_set('display_errors', 1);

$configs = array(
    'name' => '美团-酒店',
    //'tasknum' => 8,
    'log_show' => true,
    'save_running_state' => false,
    'domains' => array(
        'hotel.meituan.com'
    ),
    'scan_urls' => array(
        "http://hotel.meituan.com/ganzhou",
    ),
    'list_url_regexes' => array(
    ),
    'content_url_regexes' => array(
        // "http://m.52mnw.cn/photo/\d+.html",
        "https://hotel.meituan.com/\d+/?ci=2019-05-31&co=2019-06-01",
    ),
    // 采集间隔
    'interval' => rand(500,2000),
    // 随机浏览器类型
    'user_agent' => array(
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.157 Safari/537.36",
    )
);


$request = new requests();

$request->set_header("Accept", "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3");
$request->set_header("Accept-Encoding", "gzip, deflate, br");
$request->set_header("Accept-Language", "zh-CN,zh;q=0.9");
$request->set_header("Cache-Control", "max-age=0");
$request->set_header("Connection", "keep-alive");
$request->set_header("Host", "hotel.meituan.com");
$request->set_header("Upgrade-Insecure-Requests", "1");
$request->set_useragent($configs['user_agent']);
$request->set_cookie("Cookie", "__mta=46895640.1559269654258.1559293316547.1559293323752.36; uuid=7bb807e080de4adc81f9.1559269605.1.0.0; _lxsdk_cuid=16b0bb5fd74c8-07fc3fe4a0ec09-353166-1fa400-16b0bb5fd74c8; ci=217; rvct=217; hotel_city_id=217; hotel_city_info=%7B%22id%22%3A217%2C%22name%22%3A%22%E8%B5%A3%E5%B7%9E%22%2C%22pinyin%22%3A%22ganzhou%22%7D; IJSESSIONID=17fs62818jgyw1c5y28kdv9ye8; iuuid=120CF8186E132BB2A0A7104BD2B2EAE6F76F9699AEFB94B71C4A348ECA629930; cityname=%E8%B5%A3%E5%B7%9E; _lxsdk=120CF8186E132BB2A0A7104BD2B2EAE6F76F9699AEFB94B71C4A348ECA629930; __mta=46895640.1559269654258.1559291345243.1559291936426.32; _lxsdk_s=16b0cff68f2-f57-f3a-b84%7C%7C20");

$token = "7dpH1shhUVzMtEYa4m0px0DxLEtRbwiNBVrcW23I7Y5yBomlIrOnY1mXgCXZ2EeJkinHIf3kD1hV5kattyV6Q+BupH09HlS9rF70WzolWeeg9pcLVMwIxPR77oYlNLsh+ii9TYArYdeaPvR4U6WwJw==";
$base_url = "https://ihotel.meituan.com/hbsearch/HotelSearch";
$fields = [
    'utm_medium' => 'pc',
    'version_name' => '999.9',
    'cateId' => '20',
    'attr_28' => '129',
    'uuid' => '120CF8186E132BB2A0A7104BD2B2EAE6F76F9699AEFB94B71C4A348ECA629930%401559273219688',
    'cityId' => 217,
    'offset' => 20,
    'limit' => 20,
    'startDay' => '20190531',
    'endDay' => '20190531',
    'q' => '',
    'sort' => 'default',
    'X-FOR-WITH' => $token
];

// 入口
$html  = $request->get("https://hotel.meituan.com/ganzhou/");
$total = selector::select($html, '//*[@id="app"]/section/div/div[1]/div[2]/div/ul/li[9]/a/text()');
$page  = 1;
$size  = selector::select($html, '//*[@id="list-view"]/div[1]/article[last()]/div[2]/h3/a/em');

handlePage($page, $size);

while ($page < $total) {
    $page++;
    handlePage($page, $size);
}



/**
 * 获取某页的数据
 */
function handlePage($page, $size)
{
    $url = "https://hotel.meituan.com/ganzhou/" . ($page > 1 ? "pn".$page : '');
    echo $url, '<br/>';
    $request = new requests();
    $html = $request->get($url);
    sleep(1);

    for ($i=0; $i < $size; $i++) { 

        $title = selector::select($html, '//*[@id="list-view"]/div[1]/article['.($i+1).']/div[2]/h3/a/text()');
        $img   = selector::select($html, '//*[@id="list-view"]/div[1]/article['.($i+1).']/div[1]/a/img');
        $turl  = selector::select($html, '//*[@id="list-view"]/div[1]/article['.($i+1).']/div[2]/div/div[3]/a//@href');

        // 获取详情页
        $dhtml = $request->get($turl);
        $phone = selector::select($dhtml, '//*[@id="poiDetail"]/div/div/div[4]/div/div[2]/div/div[1]/dd/span');
        // 图片名称
        $save_name = trim($title) . "#" . trim($phone);    
        // 图片存入文件夹
        $res = GrabImage($img, "./data/", $save_name);
        if (!$res) {
            // var_dump($re)
            print_r($html);exit;
        }
        echo "第".($i+1)."条获取完毕，文件名:{$res}", '<br/>';
    }

    return true;
}

/*
 *@$url string 远程图片地址
 *@$dir string 目录，可选 ，默认当前目录（相对路径）
 *@$filename string 新文件名，可选
 */
function GrabImage($url, $dir = '', $filename = ''){
    if(empty($url)){
        return false;
    }

    $ext = strrchr($url, '.');

    if(!in_array($ext, ['.gif', '.jpg', '.bmp', '.png'])){
        echo "{$ext}格式不支持！";
        return false;
    }

    //为空就当前目录
    if(empty($dir)){
        $dir = './';
    }

    $dir = realpath($dir);
    //目录+文件
    $filename = $dir . (empty($filename) ? '/'.time().$ext : '/'.$filename . $ext);

    if (!file_exists($filename)) {
        //开始捕捉 
        ob_start(); 
        @readfile($url); 
        $img = ob_get_contents(); 
        ob_end_clean(); 
        $size = strlen($img); 
        $fp2 = @fopen($filename , "a"); 
        fwrite($fp2, $img); 
        fclose($fp2); 
    }        

    return $filename; 
} 