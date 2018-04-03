<?php
# @Author: 魏巍
# @Date:   2017-11-16T17:42:05+08:00
# @Email:  524314430@qq.com
# @Last modified by:   魏巍
# @Last modified time: 2017-11-22T16:05:05+08:00

use think\Route;

// 给publish控制器设置快捷路由
Route::rule('user/register', 'index/index/register');
Route::rule('user/get_location', 'index/index/get_location');
Route::rule('user/signup', 'index/index/signup');
Route::rule('user/message', 'index/publish/send_message');
Route::rule('user/login', 'index/publish/login');
Route::rule('user/logout', 'index/publish/logout');
Route::rule('user/forget', 'index/publish/forget');

//给Api控制器设置快捷路由
Route::rule('api/config', 'index/api/get_site');
Route::rule('api/login', 'index/api/login');
Route::rule('api/register', 'index/api/register');
Route::rule('api/set_password', 'index/api/set_password');
Route::rule('api/send_email_code', 'index/api/send_email_code');
Route::rule('api/send_message', 'index/api/send_message');
Route::rule('api/check_code', 'index/api/check_code');
Route::rule('api/personal', 'index/api/personal_info');
Route::rule('api/check_nickname', 'index/api/check_nickname');
Route::rule('api/get_ip_location', 'index/api/get_ip_location');
Route::rule('api/get_province', 'index/api/get_province');
Route::rule('api/get_city', 'index/api/get_city');
Route::rule('api/get_areas', 'index/api/get_areas');
Route::rule('api/upgrade_nickname', 'index/api/upgrade_nickname');
Route::rule('api/upgrade_sex', 'index/api/upgrade_sex');
Route::rule('api/upgrade_head', 'index/api/upgrade_head');
Route::rule('api/upgrade_phone', 'index/api/upgrade_phone');
Route::rule('api/upgrade_email', 'index/api/upgrade_email');
Route::rule('api/set_hobbise', 'index/api/set_hobbise');
Route::rule('api/push_app', 'index/api/push_app');
Route::rule('api/push_recive', 'index/api/push_recive');
Route::rule('api/set_information', 'index/api/set_information');
Route::rule('api/get_ip', 'index/api/get_ip');
Route::rule('api/carousel', 'index/api/carousel');
Route::rule('api/query', 'index/api/query');
Route::rule('api/push', 'index/api/push');
Route::rule('api/set_sysconf', 'index/api/set_sysconf');
Route::rule('api/upgrade_address', 'index/api/upgrade_address');
Route::rule('api/upgrade_birthday', 'index/api/upgrade_birthday');
Route::rule('api/punch', 'index/api/punch');
Route::rule('api/is_punch', 'index/api/is_punch');
