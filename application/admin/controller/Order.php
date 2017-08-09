<?php
/**
 * Created by PhpStorm.
 * User: 魏巍
 * Date: 2017/8/2
 * Time: 17:50
 */
namespace app\admin\controller;

class Order extends Base{
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

        $list = db('order')->where($where)->order('ordtime desc')->paginate(5,false,[
            'query'=>[
                's_keywords'=>input('s_keywords'),
                "s_date"=>input('s_date'),
                "s_status"=>input('s_status')
            ]
        ]);
        $member =input('s_username');

        $totals = db('order')->where($where)->sum('ordfee');
        $free_totals = db('order')->where($where)->where('ordstatus','eq',1)->sum('ordfee');
        $counts = db('order')->where($where)->count();
        $free_counts = db('order')->where($where)->where('ordstatus','eq',1)->count('ordfee');
        if($counts>0){
            $percent = $free_counts/$counts*100;
        }else{
            $percent = 0;
        }

        $day = $this->get_current_day_durun();
        $where['ordtime']=['between',[$day['start'],$day['end']]];

        $_totals = db('order')->where($where)->where($where)->sum('ordfee');
        $_free_totals = db('order')->where($where)->where($where)->where('ordstatus','eq',1)->sum('ordfee');
        $_counts = db('order')->where($where)->where($where)->count();
        $_free_counts = db('order')->where($where)->where($where)->where('ordstatus','eq',1)->count('ordfee');
        if($_counts>0){
            $_percent = $_free_counts/$_counts*100;
        }else{
            $_percent = 0;
        }
        // 查询状态为1的用户数据 并且每页显示10条数据
        $count = db('order')->count('*');



        $this->assign('member',$member);
        $this->assign('totals',$totals);
        $this->assign('free_totals',$free_totals);
        $this->assign('counts',$counts);
        $this->assign('free_counts',$free_counts);
        $this->assign('percent',$percent);
        $this->assign('_totals',$_totals);
        $this->assign('_free_totals',$_free_totals);
        $this->assign('_counts',$_counts);
        $this->assign('_free_counts',$_free_counts);
        $this->assign('_percent',$_percent);

        $this->assign('count',$count);
        $this->assign('list',$list);
        return view();
    }

    /**
     * 今日0-24点时间戳
     * @return array
     */
    protected function get_current_day_durun(){
        $d = date('Y-m-d',time());
        $start = strtotime("{$d} 00:00");
        $end = strtotime("{$d} 24:00");
        return ['start'=>$start,'end'=>$end];
    }

    /**
     * 添加
     * @param int $id
     * @return \think\response\View
     */
    public function add($id=0){
        $model = [
            'name'=>'添加产品'
        ];
        if($id){
            $vo = db('order')->field('dates',true)->find($id);
            //p($vo);die;
            $this->assign('info',$vo);
        }
        $this->assign('model',$model);
        return view();
    }

    public function add_handler($id=0){
        $param = request()->param();
        if($id){
            $param['dates']=time();
            if(!db('order')->update($param)){
                return ['status'=>0,'msg'=>'修改失败请重试'];
            }
            return ['status'=>1,'msg'=>'修改成功','redirect'=>Url('index')];
        }else{
            $param['date']=time();
            if(!db('order')->insert($param)){
                return ['status'=>0,'msg'=>'添加失败请重试'];
            }
            return ['status'=>1,'msg'=>'添加成功','redirect'=>Url('index')];
        }
    }

    public function export($time='',$user=''){
        $where=[];
        if($time){
            $time1 = explode('-',$time);
            $where['ordtime']=['between',[strtotime($time1[0]),strtotime($time1[1])]];
        }
        if($user){
            $member = db('member')->field('id')->where('phone','eq',$user)->find();
            $where['mid']=$member['id'];
        }

        $title = $user?$time."|".$user:$time;
        $list = db('order')->field('ordid,ordtitle,ordbuynum,ordprice,ordfee,ordtime,finishtime,ordstatus')->where($where)->select();

        if(request()->isPost()){
            if(!empty($list)){
                return array('status'=>1,'msg'=>'有导出数据','redirect'=>Url('export')."?time={$time}&user={$user}");
            }else{
                return array('status'=>0,'msg'=>'导出数据');
            }
        }

        foreach ($list as $k=>$v){
            $list[$k]['ordtime']=date('Y-m-d H:i:s',$v['ordtime']);
            $list[$k]['finishtime']=$v['finishtime']?date('Y-m-d H:i:s',$v['finishtime']):'/';
            $list[$k]['ordstatus']=$v['ordstatus']?'已支付':'未支付';
        }
        $xlsCell  = array(
            array('ordid','订单号'),
            array('ordtitle','产品名称'),
            array('ordbuynum','购买数量'),
            array('ordprice','产品单价/元'),
            array('ordfee','支付金额/元'),
            array('ordtime','下单时间'),
            array('finishtime','支付时间'),
            array('ordstatus','是否付款'),
        );

        $this->exportExcel($title,$xlsCell,$list,"{$title}账单信息   生成日期:".date('Y-m-d',time()));
        return '';
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
            $where['ordid|ordtitle']=['like',"%".$param['s_keywords']."%"];
        }
        if(!empty($param['s_username'])){
            $where1['phone']=['like',"%".$param['s_username']."%"];
            $member = db('member')->field('id')->where($where1)->select();
            $_id = [];
            foreach ($member as $k=>$v){
                $_id[]=$v['id'];
            }
            $where['mid']=['in',$_id];
        }

        if(isset($param['s_status'])){
            if($param['s_status']==1){
                $where['ordstatus']=['eq',1];
                }
            if($param['s_status']==0){
                $where['ordstatus']=['eq',0];
            }
        }

        if(!empty($param['s_date'])){
            $date = explode('-',$param['s_date']);
            $date[1] = "$date[1] 24:00";
            $where['ordtime']=['between',[strtotime($date[0]),strtotime($date[1])]];
        }

        $this->assign('search',[
            's_keywords'=>!empty($param['s_keywords'])?$param['s_keywords']:'',
            's_username'=>!empty($param['s_username'])?$param['s_username']:'',
            's_date'=>!empty($param['s_date'])?$param['s_date']:'',
            's_status'=>!empty($param['s_status'])?$param['s_status']:''
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
        $_result = $this->_status($id,'order',$type,'');
        return $_result;
    }
}