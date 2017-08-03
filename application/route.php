<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
// 给User控制器设置快捷路由
Route::rule('user/register','index/index/register');
Route::rule('user/get_location','index/index/get_location');
Route::rule('user/signup','index/index/signup');
Route::rule('user/message','index/publish/send_message');
Route::rule('user/login','index/publish/login');
Route::rule('user/logout','index/publish/logout');
Route::rule('user/forget','index/publish/forget');

// 给Account控制器设置快捷路由
Route::rule('account/information','index/account/information');
Route::rule('account/personal_info','index/user/personal_info');

