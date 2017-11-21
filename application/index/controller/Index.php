<?php
# @Author: 魏巍
# @Date:   2017-11-16T17:42:05+08:00
# @Email:  524314430@qq.com
# @Last modified by:   jswei
# @Last modified time: 2017-11-21T16:42:39+08:00



namespace app\index\controller;

use service\Amap;
use service\Around;
use service\CoordType;
use service\Data;
use service\DetailPoi;
use service\Local;
use service\Poi;
use service\Table;

class Index extends Base
{
    public function index($tpl='')
    {
        $tpl=$tpl?$tpl:'';
        return view($tpl);
    }
    public function upload_head()
    {
        return view();
    }
    public function lbs()
    {
        $amap = new Amap();
        /*
        $table = new Table();
        $table->name='商家信息';
        //$result = $amap->create_table($table);

        //添加指定位置信息
        $data = new Data();
        $data->tableid='5a051fc2305a2a284b735221';
        $poi =new Poi();
        $poi->_name="交通规划设计院";
        $poi->_address="干将西路1359号苏州市航道管理处";
        $poi->_location="120.579151,31.301185";
        $poi->coordtype=CoordType::autonavi;
        $poi->_append=[
            //'_id'=>5,               //_id 更新数据
            'telephone'=>''
        ];
        //删除指定ids
        $poi1 =new Poi();
        $poi1->_append=['ids'=>'5'];

        $data->set_data($poi);  //设置信息点

        //$result = $amap->insert($data);
        //$result = $amap->update($data->get_query_data());
        //$result = $amap->delete($data);
         */



        /*
        //本地搜索
        $local = new Local();
        $local->tableid='5a051fc2305a2a284b735221';
        $local->limit=5;
        $local->keywords='大学|科研';
        $local->set_filters([
            'type'=>1,
            'price'=>'[100,500]'
        ]);
        $result = $amap->get_location_pois($local);
        //周边检索
        $around = new Around();
        $around->center='120.579274,31.30826';
        $around->tableid='5a051fc2305a2a284b735221';
        $result = $amap->around($around);
        */

        //
        $poi =new DetailPoi();
        $poi->_id=10;
        $poi->tableid='5a051fc2305a2a284b735221';
        $result = $amap->detail_poi($poi);
        return $result;
    }

    public function getui()
    {
        $g = new \service\GeTui();
        $g->setClientID('0b056f0b02b629a0e3a809f1bf504fb2');
        $g->push_single('双十二大酬宾一律八折', '为了迎接双十二现在进行大酬宾活动，在本店消费的用户一律享受八折优惠');
    }

    public function login()
    {
        return view();
    }

    public function word2html()
    {
        $path = ROOT_PATH .'public'.DS .'data'. DS  ;
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($path .'我的测试文档.docx');
        $f = $path.'mydoc.html';
        $phpWord->save($f, 'HTML', false);
    }

    /**
     * 检测用户
     * @param $phone
     * @return bool
     */
    public function check_phone($phone)
    {
        $i = db('member')->where('phone', 'eq', $phone)->count();
        return $i?false:true;
    }

    public function signup($phone='', $verify='', $password='', $confirm_password='')
    {
        if (request()->IsGet()) {
            return view();
        }

        if (empty($phone)) {
            return json([
                'status'=>0,
                'msg'=>'请输入手机号'
            ]);
        }
        if (empty($verify)) {
            return json([
                'status'=>0,
                'msg'=>'请输入验证码'
            ]);
        }
        if (empty($password)) {
            return json([
                'status'=>0,
                'msg'=>'请输入密码'
            ]);
        }
        if (empty($confirm_password)) {
            return json([
                'status'=>0,
                'msg'=>'请输入确认密码'
            ]);
        }
        if ($verify!=cookie($verify.'_session_code')) {
            return json(['status'=>0,'msg'=>'验证码错误']);
        }
        if ($confirm_password!=$password) {
            return json([
                'status'=>0,
                'msg'=>'输入的密码不一致'
            ]);
        }
        $param = request()->param();
        $param['date']=time();
        $param['type']=1;
        unset($param['confirm_password']);
        unset($param['verify']);
        $param['password'] = substr(md5($password), 10, 15);

        if (!db('member')->insert($param)) {
            return json([
                'status'=>0,
                'msg'=>'抱歉注册失败,请稍后再试'
            ]);
        }
        return json([
            'status'=>1,
            'msg'=>'恭喜你注册成功,是否登录管理后台!',
            'redirect'=>Url('/')
        ]);
    }
}
