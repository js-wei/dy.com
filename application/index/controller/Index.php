<?php
namespace app\index\controller;
use service\Amap;
use service\Around;
use service\CoordType;
use service\Data;
use service\Poi;
use service\Table;

class Index extends Base{
    public function index($tpl=''){
        $tpl=$tpl?$tpl:'';
	    return view($tpl);
    }
    public function upload_head(){
        return view();
    }
    public function lbs(){
        $amap = new Amap();
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


        $a = new Around();
        $a->center='120.579725,31.299554';
        $a->timerange=5;
        $result = $amap->around($a);
        return $result;
    }



    public function login(){
        return view();
    }

    public function word2html(){
        $path = ROOT_PATH .'public'.DS .'data'. DS  ;
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($path .'我的测试文档.docx');
        $f = $path.'mydoc.html';
        $phpWord->save($f,'HTML',false);
    }

    /**
     * 检测用户
     * @param $phone
     * @return bool
     */
    public function check_phone($phone){
        $i = db('member')->where('phone','eq',$phone)->count();
        return $i?false:true;
    }

    public function signup($phone='',$verify='',$password='',$confirm_password=''){
        if(request()->IsGet()){
            return view();
        }

        if(empty($phone)){
            return json([
                'status'=>0,
                'msg'=>'请输入手机号'
            ]);
        }
        if(empty($verify)){
            return json([
                'status'=>0,
                'msg'=>'请输入验证码'
            ]);
        }
        if(empty($password)){
            return json([
                'status'=>0,
                'msg'=>'请输入密码'
            ]);
        }
        if(empty($confirm_password)){
            return json([
                'status'=>0,
                'msg'=>'请输入确认密码'
            ]);
        }
        if($verify!=cookie($verify.'_session_code')){
            return json(['status'=>0,'msg'=>'验证码错误']);
        }
        if($confirm_password!=$password){
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
        $param['password'] = substr(md5($password),10,15);

        if(!db('member')->insert($param)){
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