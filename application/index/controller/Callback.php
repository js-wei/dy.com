<?php
# @Author: 魏巍
# @Date:   2017-11-16T17:42:05+08:00
# @Email:  524314430@qq.com
# @Last modified by:   魏巍
# @Last modified time: 2017-11-18T17:36:47+08:00



namespace app\index\controller;

use org\Upload;
use think\File;

class Callback extends Base
{
    protected function _initialize()
    {
        parent::_initialize();
    }

    public function callback_alipay()
    {
        require_once(VENDOR_PATH.'alipay/alipay.config.php');
        Vendor('alipay.lib.alipay_notify', '.class.php');
        file_put_contents('./data/param.txt', json_encode(request()->param()));
        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyReturn();
        file_put_contents('./data/verify_notify.txt', json_encode($verify_result));
        //验证成功
        if ($verify_result) {
            $order = db('order')->where('ordid', 'eq', input('out_trade_no'))->find();
            if ($order && $order['ordstatus']==0) {
                //点击
                db('my_product')
                    ->where('pid', 'eq', $order['proid'])
                    ->where('mid', 'eq', $order['mid'])
                    ->setInc('buy');

                if (!db('order')->update([
                    'id'=>$order['id'],
                    'finishtime'=>time(),
                    'ordstatus'=>1,
                    'payment_type'=>'支付宝',
                    'payment_trade_no'=>input('trade_no'),
                    'payment_buyer_email'=>input('buyer_email'),
                    'payment_notify_time'=>input('notify_time')
                ])) {
                }
                echo 'success';
            }
        } else {
            //验证失败
            echo "fail";
        }
    }
}
