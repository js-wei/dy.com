<?php
return [
	'view_replace_str'  =>  [
		'__ROOT__'=>'/',
		'__PUBLIC__'=>'/static/home',
		'__PLUG__'=>'/static/plug',
	    '__JS__'=>'/static/home1/js',
	    '__CSS__'=>'/static/home1/css',
	    '__IMAGES__'=>'/static/home1/img',
	    '__SELF__'=>$_SERVER['REQUEST_URI'] 
	],
	'default_return_type'=>'jsonp',
    'default_jsonp_handler'	 => 'callback',
	'paginate'               => [
	    'type'     => 'bootstrap',
	    'var_page' => 'p',
	    'list_rows'=>10
	]
];