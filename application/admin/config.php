<?php
# @Author: 魏巍 <jswei>
# @Date:   2017-11-16T17:42:05+08:00
# @Email:  524314430@qq.com
# @Last modified by:   jswei
# @Last modified time: 2017-11-17T20:52:36+08:00



return [
	'view_replace_str'  =>  [
		'__ROOT__'=>'/',
		'__PUBLIC__'=>'/static',
		'__PLUG__'=>'/static/plug',
	    '__JS__'=>'/static/admin/js',
	    '__CSS__'=>'/static/admin/css',
	    '__IMAGES__'=>'/static/admin/img',
	    '__SELF__'=>$_SERVER['REQUEST_URI']
	],
	'PAGE_SIZE'=>15
];
