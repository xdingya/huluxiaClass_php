<?php
/**
 * File：example.php
 * Author: 小丁(@xdingya)
 * Site：xding.top
 * Date: 2025/1/21
 *
 **/

// 自动签到例子
header('Content-Type: text/html;charset=utf-8');
include('require_config.php');
include('php/Common.class.php');
include('php/Huluxia_Api.class.php');
global $config;

$hlx = new Huluxia_Api($config);
var_dump($hlx->categoryTosign());
