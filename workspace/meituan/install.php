<?php
require_once __DIR__ . '/../../autoloader.php';

use QL\QueryList;
use QL\Ext\PhantomJs;

error_reporting(-1);
ini_set('display_errors', 1);

$ql = QueryList::getInstance();
// 安装时需要设置PhantomJS二进制文件路径
$ql->use(PhantomJs::class,'/usr/local/bin/phantomjs');
//or Custom function name
$ql->use(PhantomJs::class,'/usr/local/bin/phantomjs','browser');