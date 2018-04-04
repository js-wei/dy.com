<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/3/20
 * Time: 9:00
 */
namespace app\index\model;
use think\Model;

class Punch extends Model{
    private $refer;
    protected function initialize(){
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        header('Content-type:text/html;charset=utf-8;');
        //TODO:自定义的初始化
        $this->refer = db('config')->find(1);
    }
    /**
     * @param int $uid    用户id
     * @param int $type   签到类型:0上班,1下班
     * @return array
     */
    public function is_punch(){
        $uid = input('uid/d');
        $type = input('type/d');
        $_data = $this
            ->where('uid','eq',$uid)
            ->where('ec','eq',$type)
            ->whereTime('date', 'today')
            ->count();
        return $_data?1:0;
    }

    /**
     * @param string $push  打卡时间
     * @param int $type     0上班,1下班
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function punch(){
        //header('Content-type:html/text;charset=utf-8;');
        $push=input('punch/s');
        $type=input('type/d');
        $uid = input('uid/d');
        $data = request()->post();
        $data['position'] = isset($data['position'])?json_encode($data['position']):'';
        $push = $push?$push:date('Y-m-d H:i:s',time());
        $msg = $type?'下班签到':'上班签到';
        if(!$type){
            $start_push_time = date('Y-m-d',time())." ".$this->refer['start_push_time'];
            $start_later = $this->refer['start_time']?$this->refer['start_time']:0;
            $diff = $this->_compare($start_push_time,$push);

            if($diff<=$start_later){
                $_result = [
                    'type'=>1,
                    'title'=>'正点签到',
                    'later'=>$diff
                ];
            }else if($diff>$start_later && $diff<150){
                $_result = [
                    'type'=>2,
                    'title'=>'迟到',
                    'later'=>$diff
                ];
            }else{
                $_result = [
                    'type'=>4,
                    'title'=>'旷工',
                    'later'=>$diff
                ];
            }
        }else{
            $end_push_time = date('Y-m-d',time())." ".$this->refer['end_push_time'];
            $end_before = $this->refer['end_time']?$this->refer['end_time']:0;
            $diff = $this->_compare($end_push_time,$push);

            if($diff<$end_before && $diff<-60){
                $_result = [
                    'type'=>4,
                    'title'=>'旷工',
                    'later'=>-$diff
                ];
            }else if($diff>-60 && $diff<=$end_before){
                $_result = [
                    'type'=>3,
                    'title'=>'早退',
                    'later'=>$diff
                ];
            }else if($diff>=$end_before && $diff<60){
                $_result = [
                    'type'=>1,
                    'title'=>'正点签到',
                    'later'=>$diff
                ];
            }else{
                $_result = [
                    'type'=>5,
                    'title'=>'加班',
                    'later'=>$diff
                ];
            }
        }

        $data['title']  = $_result['title'];
        $data['later']  = $_result['later'];
        $data['type']  = $_result['type'];
        $data['ec']  = $type?1:0;
        $data['date']  = strtotime($push);
        $data['dates']  = time();

        $_data = $this
            ->where('uid','eq',$uid)
            ->where('ec','eq',$type)
            ->whereTime('date', 'today')
            ->select();

        if(empty($_data)){
            if(!$this->insert($data)){
                return [
                    'status'=>0,
                    'msg'=>"您今日{$msg}失败"
                ];
            }
            return [
                'status'=>1,
                'msg'=>"您今日{$msg}成功",
				'punch_time'=>$push
            ];
        }else{
            return [
                'status'=>2,
                'msg'=>"您今日已经{$msg}了"
            ];
        }
    }

    /**
     * 比较时间
     * @param $start    开始时间戳
     * @param $end      结束时间戳
     * @return float
     */
    protected function _compare($start,$end=0){
        $minute=floor((strtotime($end)-strtotime($start))%86400/60);
        return $minute;
    }
}