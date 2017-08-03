<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017/8/3
 * Time: 16:39
 */
namespace app\index\controller;

class Account extends Base{
    protected function _initialize(){
        parent::_initialize();
    }
    public function information(){
        return view();
    }
}