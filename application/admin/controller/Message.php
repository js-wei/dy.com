<?php
# @Author: 魏巍 <jswei>
# @Date:   2017-11-16T17:42:05+08:00
# @Email:  524314430@qq.com
# @Last modified by:   jswei
# @Last modified time: 2017-11-17T20:51:19+08:00

namespace app\admin\controller;
use getuisdk\IGeTui;
use getuisdk\IGtNotificationTemplate;
use getuisdk\IGtAppMessage;

class Message extends Base{
    protected function  _initialize(){
        parent::_initialize();
    }

    /**
     * 首页
     * @param int $aid
     * @return \think\response\View
     */
    public function index(){
        $where=[];
        $search = $this->_search();
        $where = array_merge($where,$search);

        $list = db('Message')->where($where)->order('date desc')->paginate(15,false,[
            'query'=>[
                's_keywords'=>input('s_keywords'),
                "s_date"=>input('s_date'),
                "s_status"=>input('s_status')
            ]
        ]);
        // 查询状态为1的用户数据 并且每页显示10条数据
        $count = db('Message')->count('*');
        $this->assign('count',$count);
        $this->assign('list',$list);
        return view();
    }

    /**
     * 添加
     * @param int $id
     * @return \think\response\View
     */
    public function add($id=0){
        $model = [
            'name'=>'添加消息'
        ];
        if($id){
            $vo = db('product')->field('dates',true)->find($id);
            //p($vo);die;
            $this->assign('info',$vo);
        }
        $this->assign('model',$model);
        return view();
    }

    public function add_handler($id=0){
        $param = request()->param();
        $param['date']=time();
        $param['type']=0;

//        $member = db('member')->where('status','eq',0)->select();
//        foreach ($member as $k => $v){
//            $param['mid']=$v['id'];
//            if(!db('message')->insert($param)){
//                return ['status'=>0,'msg'=>'添加失败请重试'];
//            }
//        }
        if(!db('message')->insert($param)){
            return ['status'=>0,'msg'=>'添加失败请重试'];
        }
        $push  =$this->pushMessageToApp($param['title'],$param['title']);
        return $push;
        //return ['status'=>1,'msg'=>'添加成功','redirect'=>Url('index')];
    }

    /**
     * 搜索
     * @param array $param
     * @return array
     */
    protected function _search($param=[]){
        $where=[];
        if(empty($param)){
            $param = request()->param();
        }
        if(!empty($param['s_keywords'])){
            $where['title']=['like',"%".$param['s_keywords']."%"];
        }
        if(!empty($param['s_status'])){
            $where['status']=$param['s_status']>-1?$param['s_status']:'';
        }
        if(!empty($param['s_date'])){
            $date = explode('-',$param['s_date']);
            $date[1] = "$date[1] 24:00";
            $where['date']=['between',[strtotime($date[0]),strtotime($date[1])]];
        }

        $this->assign('search',[
            's_keywords'=>!empty($param['s_keywords'])?$param['s_keywords']:'',
            's_date'=>!empty($param['s_date'])?$param['s_date']:'',
            's_status'=>!empty($param['s_status'])?$param['s_status']:-1
        ]);
        return $where;
    }
    /**
     * [status 状态操作]
     * @param  [type] $id [修改id]
     * @param  [type] $type  [操作类型]
     * @return [type]     [description]
     */
    public function status($id,$type){
        $type = ($type=="delete-all")?"delete":$type;
        $_result = $this->_status($id,'message',$type);
        return $_result;
    }

    protected  function pushMessageToApp($title='',$content=""){
        $appId = config('GeTui.AppID');
        $appSecret = config('GeTui.AppSecret');
        $appKey = config('GeTui.AppKey');
        $igt = new IGeTui('',$appId,$appSecret);

        //定义透传模板，设置透传内容，和收到消息是否立即启动启用
        $template =  new IGtNotificationTemplate();
        $template->setAppId($appId);                   //应用appid
        $template->setAppKey($appKey);                 //应用appkey
        $template->setTransmissionType(1);            //透传消息类型
        $template->setTransmissionContent($title);//透传内容
        $template->setTitle($content);      //通知栏标题
        $template->setText($content);     //通知栏内容
        $template->setLogo("");                       //通知栏logo
        $template->setLogoURL("");                    //通知栏logo链接
        $template->setIsRing(true);                   //是否响铃
        $template->setIsVibrate(true);                //是否震动
        $template->setIsClearable(true);              //通知栏是否可清除

        $message = new IGtAppMessage();
        $message->setIsOffline(true);
        $message->setOfflineExpireTime(10 * 60 * 1000);//离线时间单位为毫秒，例，两个小时离线为3600*1000*2
        $message->setData($template);

        $appIdList=[$appId];
        $message->setAppIdList($appIdList);
        $rep = $igt->pushMessageToApp($message,"任务组名");
        return $rep;
    }
}
