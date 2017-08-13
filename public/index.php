<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
define('WEB_HOST', "http://t.jswei.top/");
define('PXOXY_WEB_HOST', "http://pinkan.cn/");
define('WX_APPID', "wxf02790fbcadf974a");
define('WX_MCHID', "1382132602");
define('WX_KEY', "214ADK1238123K312FDAS94313232113");
define('WX_APPSECRET', "d5f062346b24ca499e6997fc2f38d4db");

define('WX_JS_API_CALL_URL', '');
define('WX_SSLCERT_PATH', '');
define('WX_SSLKEY_PATH', '');
define('WX_CURL_TIMEOUT', 60);

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
