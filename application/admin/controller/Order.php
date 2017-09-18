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
        $search = $this->_search(1);
        $where = array_merge($where,$search);
<<<<<<< HEAD
		
        $list = db('order')
            ->alias('a')
            ->join('think_product b','b.id=a.proid')
            ->join('think_member c','c.id=a.mid')
            ->field('a.id,a.ordid,a.is_send,a.ordtitle,a.ordprice,a.payment_buyer_email,a.ordfee,a.ordstatus,a.ordbuynum,a.ordtime,a.finishtime,b.divides,c.phone')
            ->order('a.ordtime desc')
            ->where($where)
=======

        $list = db('order')
            ->alias('a')
            ->join('think_product b','b.id=a.proid')
            ->field('a.id,a.ordid,a.ordtitle,a.ordprice,a.payment_buyer_email,a.ordfee,a.ordstatus,a.ordbuynum,a.ordtime,a.finishtime,b.divides')
            ->order('ordtime desc')
>>>>>>> b7b7ce70e46143263b0990a44b58872ca586abc9
            ->paginate(5,false,[
            'query'=>[
                's_keywords'=>input('s_keywords'),
                "s_date"=>input('s_date'),
				's_username'=>input('s_username'),
                "s_status"=>input('s_status')
            ]
        ]);
		
		
		$member =input('s_username');
        $search = $this->_search();
		$search1 = $this->_search();
		$search1['ordstatus']=1;

        $totals = db('order')->where($search)->sum('ordfee');
        $free_totals = db('order')->where($search1)->sum('ordfee');
        $counts = db('order')->where($search)->count();
        $free_counts = db('order')->where($search1)->count('ordfee');

        if($counts>0){
            $percent = round($free_counts/$counts*100);
        }else{
            $percent = 0;
        }

        $day = $this->get_current_day_durun();
        $search['ordtime']=['between',[$day['start'],$day['end']]];
		$search1['ordtime']=['between',[$day['start'],$day['end']]];

        $_totals = db('order')->where($search)->sum('ordfee');
        $_free_totals = db('order')->where($search1)->sum('ordfee');
        $_counts = db('order')->where($search)->count();
        $_free_counts = db('order')->where($search1)->count('ordfee');
        if($_counts>0){
            $_percent = round($_free_counts/$_counts*100,2);
        }else{
            $_percent = 0;
        }
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
		/**/


		// 查询状态为1的用户数据 并且每页显示10条数据
        $count = db('order')->count('*');
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
            'name'=>'订单详情'
        ];
        if($id){
            $vo = db('order')->field('dates',true)->find($id);
            $m = db('member')->field('dates',true)->find($id);
            $vo['product'] = json_decode($vo['product'],true);
            $this->assign('info',$vo);
            $this->assign('m',$m);
        }
        $this->assign('model',$model);
        return view();
    }

    public function send($id=0){
        $model = [
            'name'=>'设置发货'
        ];
        if($id){
            $vo = db('order')->field('dates',true)->find($id);
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

    public function add_send($id=0){
        $param = request()->param();
        $param['dates']=time();
        $param['is_send']=1;
        if(!db('order')->update($param)){
            return ['status'=>0,'msg'=>'发货失败'];
        }
        return ['status'=>1,'msg'=>'发货成功','redirect'=>Url('index')];
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
        $divides=0;
        $list = db('order')
            ->alias('a')
            ->join('think_product b','b.id=a.proid')
            ->where($where)
            ->field('a.ordid,a.ordtitle,a.ordprice,a.ordfee,a.ordstatus,a.ordbuynum,a.ordtime,a.finishtime,b.divides')
            ->order('ordtime desc')
            ->select();

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
            $divides += $list[$k]['divides']=$v['ordstatus']?($v['divides']*$v['ordfee']):0;
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
            array('divides','分成金额'),
        );

        $this->exportExcel($title,$xlsCell,$list,"{$title}账单信息   生成日期:".date('Y-m-d',time()));
        return '';
    }

    /**
     * 搜索
     * @param array $param
     * @return array
     */
    protected function _search($t=0){
        $where=[];
        $param = request()->param();

        if(!empty($param['s_username'])){
            $where1['phone']=['like',"%".$param['s_username']."%"];
            $member = db('member')->field('id')->where($where1)->select();
            $_id = [];
            foreach ($member as $k=>$v){
                $_id[]=$v['id'];
            }
            $where[$t?'a.mid':'mid']=['in',$_id];
        }

        if(!empty($param['s_keywords'])){
            $where[$t?'a.ordid':'ordid']=['like',"%".$param['s_keywords']."%"];
        }

        if(isset($param['s_status'])){

            if($param['s_status']==0){
                $where[$t?'a.ordstatus':'ordstatus']=0;
            }else if($param['s_status']==1){
                $where[$t?'a.ordstatus':'ordstatus']=1;
            }else if($param['s_status']==2){
                $where[$t?'a.is_send':'is_send']=0;
            }else if($param['s_status']==3){
                $where[$t?'a.is_send':'is_send']=1;
            }
        }

        if(!empty($param['s_date'])){
            $date = explode('-',$param['s_date']);
            $date[1] = "$date[1] 24:00";
            $where[$t?'a.ordtime':'ordtime']=['between',[strtotime($date[0]),strtotime($date[1])]];
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