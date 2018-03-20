<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/3/20
 * Time: 9:00
 */
namespace app\index\model;
use think\Model;

class Push extends Model{
    private $refer;
    protected function initialize(){
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        //TODO:自定义的初始化
        $this->refer = db('config')->find(1);
    }

    public function push(){

    }
}