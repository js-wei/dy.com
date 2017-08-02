<?php
namespace app\admin\controller;

class Member extends Base {
	protected function  _initialize(){
		parent::_initialize();
	}

	public function index($aid=0){
        $where=[];
        if($aid){
            $where['column_id']=$aid;
        }

        $search = $this->_search();
        $where = array_merge($where,$search);

        $list = db('member')->where($where)->order('date desc')->paginate(15,false,[
            'query'=>[
                's_keywords'=>input('s_keywords'),
                "s_date"=>input('s_date'),
                "s_status"=>input('s_status')
            ]
        ]);
        // 查询状态为1的用户数据 并且每页显示10条数据
        $count = db('member')->count('*');
        $this->assign('count',$count);
        $this->assign('aid',$aid);
        $this->assign('list',$list);
        return view();
	}

    public function add($id=0){
        $model = [
            'name'=>'添加用户'
        ];
        if($id){
            $vo = db('member')->field('dates',true)->find($id);
            //p($vo);die;
            $this->assign('info',$vo);
        }
        $this->assign('model',$model);
        return view();
    }

    protected function _search($param=[]){
        $where=[];
        if(empty($param)){
            $param = request()->param();
        }
        if(!empty($param['s_keywords'])){
            $where['username|nickname']=['like',"%".$param['s_keywords']."%"];
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

    public function set_password($id=0){
        if(!$id){
            return json([
               'status'=>0,
               'msg'=>'参数错误'
            ]);
        }
        if(!db('member')->update([
            'id'=>$id,
            'password'=>substr(md5('123456'),10,15),
            'dates'=>time(),
            'status'=>0
        ])){
            return json([
                'status'=>0,
                'msg'=>'操作失败'
            ]);
        }
        return json([
            'status'=>1,
            'msg'=>'恭喜您初始化成功,初始密码是:123456',
            'reditect'=>Url('index')
        ]);
    }
    /**
     * [status 状态操作]
     * @param  [type] $id [修改id]
     * @param  [type] $type  [操作类型]
     * @return [type]     [description]
     */
    public function status($id,$type){
        $type = ($type=="delete-all")?"delete":$type;
        $_result = $this->_status($id,'member',$type,'head');
        return $_result;
    }
}