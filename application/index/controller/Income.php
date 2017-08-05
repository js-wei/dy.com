<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017/8/4
 * Time: 15:24
 */
namespace app\index\controller;

class Income extends Base{
    protected function _initialize(){
        parent::_initialize();
        if(!session('?_mid')){
            $this->redirect('/');
        }
    }
    public function index(){
        return view();
    }

    public function has(){
        return view();
    }
}