<?php
namespace app\index\controller;

class Index extends Base{
    public function index($tpl=''){
        $tpl=$tpl?$tpl:'';
	    return view($tpl);
    }

    public function lbs(){
        $lbs = new \service\Baibu();
        $table = new \service\Geotable();
        $table->name="商家信息";
        $table->timestamp=time();
//        $_table = $lbs->geotable_create($table);
//        p($_table);
        $column = new \Service\Geocolumn();
        $column->name="商铺图片";
        $column->key="image";
        $column->type=4;
        $column->geotable_id='1000002303';
        $_column = $lbs->geocolumn_create($column);     //创建列
        //$_column = $lbs->geocolumn_select($column);     //查询列
        $column->id='1000002885';
        //$_column = $lbs->geocolumn_detail($column);      //查看列
        //$_column = $lbs->geocolumn_update($column);        //更新列
        //$_column = $lbs->geocolumn_delete($column);        //更新列
        p($_column);
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