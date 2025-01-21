<?php
/**
 * File：require_config.php
 * Author: 小丁(@xdingya)
 * Site：xding.top
 * Date: 2025/1/21
 *
 **/

//所有用[]包裹起来的是你需要修改的
global $config;
$config = array(
    "phone"=> "[手机号]",
    "password" => "[密码]",       //密码需要 MD5 加密后的，例如 f63210cd5f3112fdc4b264c747547fdf
    "application" => array(
        "market_id" => 'tool_huluxia',      //floor_web ; tool_huluxia ; floor_huluxia(20141492,4.3.0.2) ; tool_tencent(335,4.1.1.6.2)
        "app_version" => '4.3.1.2',
        "versioncode" => '389'
    ),
    "device_code" => "[设备代码]",      //例如：[d]03ab4502-97d4-4e5b-a12s-6b4nf24bha28
);