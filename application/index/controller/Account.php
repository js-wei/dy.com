<?php
# @Author: 魏巍 <jswei>
# @Date:   2017-11-16T17:42:05+08:00
# @Email:  524314430@qq.com
# @Last modified by:   魏巍
# @Last modified time: 2017-11-18T17:36:32+08:00

namespace app\index\controller;

class Account extends Base
{
    protected function _initialize()
    {
        parent::_initialize();
        if (!session('?_mid')) {
            $this->redirect('/');
        }
    }
    public function information()
    {
        $this->assign('demo_time', $this->request->time());
        return view();
    }
}
