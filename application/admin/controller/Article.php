<?php
# @Author: 魏巍
# @Date:   2017-12-11T08:35:19+08:00
# @Email:  524314430@qq.com
# @Last modified by:   魏巍
# @Last modified time: 2017-12-11T09:09:28+08:00



namespace app\admin\controller;

class article extends Base
{
    protected function _initialize()
    {
        parent::_initialize();
        $this->assign('aid', input('aid/d'));
        $this->assign('fid', input('fid/d'));
    }

    public function test()
    {
        return view();
    }
    public function index($aid=0)
    {
        $model = [
            'name'=>'文档管理'
        ];
        $where=[];
        if ($aid) {
            $where['column_id']=$aid;
        }

        $search = $this->_search();
        $where = array_merge($where, $search);

        $list = db('article')->where($where)->order('date desc')->paginate(15, false, [
            'query'=>[
                's_keywords'=>input('s_keywords'),
                "s_date"=>input('s_date'),
                "s_status"=>input('s_status')
            ]
        ]);
        // 查询状态为1的用户数据 并且每页显示10条数据
        $count = db('article')->count('*');
        $this->assign('count', $count);
        $this->assign('aid', $aid);
        $this->assign('model', $model);
        $this->assign('list', $list);
        return view();
    }

    public function set_account($id=0, $aid=0)
    {
        $model = [
            'name'=>'支付账号'
        ];
        if ($id) {
            $vo = db('wechat_config')->field('dates', true)->where(['fid'=>$id])->find();
            $this->assign('info', $vo);
            $this->assign('fid', $id);
        }
        $this->assign('model', $model);
        $this->assign('aid', $aid);
        return view();
    }

    /**
     * [add_handler 修改/添加控制器]
     * @param integer $id [description]
     */
    public function add_account_handler($id=0)
    {
        $param = request()->param();
        if ($param['attr']) {
            $attr=$this->makeAttr($param['attr']);	//重置属性
            $param=array_merge($param, $attr);
            unset($param['attr']);
            unset($param['none']);
        }
        if ($id) {
            $param['dates']=time();
            if (!db('volunteer')->update($param)) {
                return ['status'=>0,'msg'=>'修改失败请重试'];
            }
            return ['status'=>1,'msg'=>'修改成功','redirect'=>Url('index')];
        } else {
            $param['date']=time();
            if (!db('volunteer')->insert($param)) {
                return ['status'=>0,'msg'=>'添加失败请重试'];
            }
            return ['status'=>1,'msg'=>'添加成功','redirect'=>Url('index')];
        }
    }

    /**
     * [get_city 获取城市]
     * @param  integer $id [description]
     * @return [type]      [description]
     */
    public function get_city($id=0)
    {
        if (!$id) {
            return ['status'=>0,'msg'=>'参数错误'];
        }
        $pro = db('provinces')->where('id', 'eq', $id)->find();
        $list = db('cities')
                ->where('provinceid', 'eq', $pro['provinceid'])
                ->where('status', 'eq', 0)
                ->select();
        return json([
            'status'=>1,
            'data'=>$list
        ]);
    }
    /**
     * [get_custom 获取自定义地区]
     * @param  integer $id [description]
     * @return [type]      [description]
     */
    public function get_custom($id=0)
    {
        if (!$id) {
            return ['status'=>0,'msg'=>'参数错误'];
        }
        $list = db('custom')
                ->where('city_id', 'eq', $id)
                ->where('status', 'eq', 0)
                ->select();
        return json([
            'status'=>1,
            'data'=>$list
        ]);
    }

    /**
     * @param int $aid
     * @param int $id
     * @param int $fid
     * @return \think\response\View
     */
    public function add($aid=0, $id=0, $fid=0)
    {
        $model = [
            'name'=>'文档管理'
        ];
        $vo = [];
        $city=[];
        $_temp=[];
        if ($id) {
            $vo = db('article')->field('dates', true)->find($id);
            $vo['pics'] = $vo['pics']?explode(',', $vo['pics']):'';
            if (isset($vo['attrs']) && !empty($vo['attrs'])) {
                $vo['attrs'] = explode(',', $vo['attrs']);
            }
            if ($vo['events']) {
                $vo['events'] = json_decode($vo['events'], true);
                foreach ($vo['events'] as $k=>$v) {
                    $_temp[$v['date']]=$v['events'];
                }
            }
            $prov = db('provinces')->find($vo['provid']);
            $city = db('cities')->where('provinceid', 'eq', $prov['provinceid'])->select();
        }

        $column = db('column')->field('date', true)->find($aid);
        $list = \Service\Category::LimitForLevel(db('column')->field('id,fid,title')->where('status=0')->select());


        if ($column['fid']!=$fid) {
            $fid = $column['fid'];
        }

        $_list = db('provinces')->where('status=0')->select();
        $_custom = db('custom')->where('status=0')->select();

        $this->assign('json_events', json_encode($_temp));
        $this->assign('info', $vo);
        $this->assign('_fid', $fid);
        $this->assign('custom', $_custom);
        $this->assign('column_list', $list);
        $this->assign('province_list', $_list);
        $this->assign('city_list', $city);
        $this->assign('model', $model);
        $this->assign('aid', $aid);
        $this->assign('column', $column);
        return view();
    }
    /**
     * [articled_handler 修改/添加控制器]
     * @param integer $id [description]
     */
    public function add_handler($id=0, $aid=0, $_id=0)
    {
        $param = request()->post();
        $_events=[];

        //日期事件
        if (isset($param['events']) && isset($param['dates']) && isset($param['prices'])) {
            foreach ($param['events'] as $k => $v) {
                $_events[]=[
                    'date'=>$param['dates'][$k],
                    'date1'=>preg_replace('/-/', '', $param['dates'][$k]),
                    'events'=>$param['events'][$k],
                    'prices'=>$param['prices'][$k],
                ];
            }
            unset($param['events']);
            unset($param['dates']);
            unset($param['prices']);
        }
        $_events = $_events?json_encode($_events):'';
        $param['events'] =$_events;

        $param['attrs']=isset($param['attrs'])?implode(',', $param['attrs']):'';
        $param['content']=htmlspecialchars(input('content/s'));
        $param['pics']=isset($param['pics'])?implode(',', $param['pics']):'';
        $param['author'] = $param['author']?$param['author']:'公司人事部';

        if ($param['attr']) {
            $attr=$this->makeAttr($param['attr']);	//重置属性
            $param=array_merge($param, $attr);
            unset($param['attr']);
            unset($param['none']);
        }

        unset($param['aid']);
        unset($param['p']);
        unset($param['fid']);
        unset($param['_id']);
        

        if ($id) {
            $param['dates']=time();
            if (!db('article')->update($param)) {
                return ['status'=>0,'msg'=>'修改失败请重试'];
            }
            return ['status'=>1,'msg'=>'修改成功',
                'redirect'=>
                    Url('index?aid='.input('aid').'&p='.input('p').'&fid='.input('fid').'&_id='.input('_id'))];
        } else {
            $param['date']=time();

            if (!db('article')->insert($param)) {
                return ['status'=>0,'msg'=>'添加失败请重试'];
            }
            return ['status'=>1,'msg'=>'添加成功','redirect'=>
                Url('index?aid='.input('aid').'&p='.input('p').'&fid='.input('fid').'&_id='.input('_id'))];
        }
    }

    public function events($date='')
    {
        $this->assign('date', $date);
        return view();
    }

    public function zhoubian($id=0, $aid=0)
    {
        $model = [
            'name'=>"周边景点"
        ];
        $where=[];
        if ($aid) {
            $where['fid']=$id;
        }
        if (!$id) {
            $this->error('参数错误');
        }
        $search = $this->_search();
        $where = array_merge($where, $search);
        $list = db('zhoubian')->where($where)->order('date desc')->paginate(15, false, [
            'query'=>[
                's_keywords'=>input('s_keywords'),
                "s_date"=>input('s_date'),
                "s_status"=>input('s_status')
            ]
        ]);
        $article = db('article')->field('id,title')->find($id);
        $count = db('taste')->count('*');
        $this->assign('count', $count);
        $this->assign('aid', $aid);
        $this->assign('model', $model);
        $this->assign('list', $list);
        $this->assign('f', $article);
        return view();
    }

    public function add_zhoubian($id=0, $aid=0)
    {
        $model = [
            'name'=>'添加体验'
        ];
        if ($id) {
            $vo = db('zhoubian')->field('dates', true)->find($id);
            $this->assign('info', $vo);
        }
        $this->assign('model', $model);
        $this->assign('aid', $aid);
        return view();
    }


    public function tiyan($id=0, $aid=0, $t=0)
    {
        $where=[];
        if ($aid) {
            $where['fid']=$id;
        }
        if (!$t) {
            $this->error('参数错误');
        }
        if (!$id) {
            $this->error('参数错误');
        }
        if ($t==1) {
            $model = [
                'name'=>'攻略管理'
            ];
        }
        if ($t==4) {
            $model = [
                'name'=>'游记管理'
            ];
        }
        $where['type']=$t;
        $search = $this->_search();
        $where = array_merge($where, $search);
        $list = db('taste')->where($where)->order('date desc')->paginate(15, false, [
            'query'=>[
                's_keywords'=>input('s_keywords'),
                "s_date"=>input('s_date'),
                "s_status"=>input('s_status')
            ]
        ]);
        $article = db('article')->field('id,title')->find($id);
        $count = db('taste')->count('*');
        $this->assign('count', $count);
        $this->assign('aid', $aid);
        $this->assign('model', $model);
        $this->assign('list', $list);
        $this->assign('f', $article);
        return view();
    }

    public function comment($id=0, $aid)
    {
        $model = [
            'name'=>'评论管理'
        ];
        if ($id) {
            $vo = db('taste')->field('dates', true)->find($id);
            $count = db('comments')->where(['aid'=>$vo['fid']])->count('*');
            $this->assign('info', $vo);
            $this->assign('count', $count);
        }
        $this->assign('model', $model);
        $this->assign('aid', $aid);
        return view();
    }

    public function all($id=0, $uid=0)
    {
        if (!$uid||!$id) {
            return ['status'=>0,'msg'=>'修改失败请重试'];
        }
        $vo = db('comments')->field('dates', true)->find($id);
        $list = db('comments')->field('dates', true)->where(['aid'=>$vo['aid']])->select();

        if (empty($list)) {
            return ['status'=>0,'msg'=>'没有更多的数据'];
        }
        foreach ($list as $k=>$v) {
            $m = model('comments')->find($v['id']);
            $list[$k]['nickname'] = get_member_nickname($v['uid']);
            $list[$k]['head'] = $m->infor['head']?$m->infor['head']:'/static/admin/img/avatars/head.jpg';
            $list[$k]['title'] = $m->article['title'];
            $list[$k]['sortID'] = $v['cid'];
            $list[$k]['date'] = date('Y-m-d', $v['date']);
            unset($list['aid']);
            unset($list['uid']);
        }
        $this->assign('list', json_encode($list));
        return view();
    }
    public function see($id=0, $aid=0)
    {
        if (!$id) {
            $this->error('参数错误');
        }
        $model = [
            'name'=>'体验审核'
        ];
        $taste = db('taste')->find($id);
        $this->assign('info', $taste);
        $this->assign('model', $model);
        $this->assign('aid', $aid);
        return view();
    }

    public function set_see($id=0, $pid=0, $aid=0, $t=0, $p=1)
    {
        $db = db('taste');
        if ($t==0) {
            if (!$db->update([
                'id'=>$id,
                'status'=>0,
                'dates'=>time()
            ])) {
                return ['status'=>0,'msg'=>'操作失败'];
            }
            return [
                'status'=>1,'msg'=>'操作成功',
                'redirect'=>Url('tiyan?aid='.$aid.'&p='.$p.'&id='.$pid)
            ];
        } else {
            if (!$db->update([
                'id'=>$id,
                'status'=>3,
                'dates'=>time()
            ])) {
                return ['status'=>0,'msg'=>'操作失败'];
            }
            return [
                'status'=>1,'msg'=>'操作成功',
                'redirect'=>Url('tiyan?aid='.$aid.'&p='.$p.'&id='.$pid)
            ];
        }
    }

    public function add_tiyan($pid=0, $aid=0, $id=0, $t=0)
    {
        if ($t==1) {
            $model = [
                'name'=>'添加攻略'
            ];
        }
        if ($t==4) {
            $model = [
                'name'=>'添加游记'
            ];
        }
        if ($id) {
            $vo = db('taste')->field('dates', true)->find($id);
            $this->assign('info', $vo);
        }
        $vo1 = db('article')->field('dates', true)->find($pid);
        $_r=[];
        if ($vo1['rooms']) {
            $r = json_decode($vo1['rooms'], true);
            if ($r) {
                foreach ($r as $jj=>$ii) {
                    $_r[]=db('rooms')->find($jj);
                }
            }
        }

        $this->assign('list', $_r);
        $this->assign('model', $model);
        $this->assign('aid', $aid);
        return view();
    }

    public function add_commont($cid=0, $id=0, $aid=0)
    {
        $model = [
            'name'=>'添加评论'
        ];
        if ($cid) {
            $vo = db('comments')->field('dates', true)->find($cid);
            $this->assign('info', $vo);
        }
        $vo1 = db('article')->field('id,title')->find($id);
        $_r=[];
//        if($vo1['rooms']){
//            $r = json_decode($vo1['rooms'],true);
//            if($r){
//                foreach ($r as $jj=>$ii){
//                    $_r[]=db('rooms')->find($jj);
//                }
//            }
//        }
        $this->assign('list', $_r);
        $this->assign('model', $model);
        $this->assign('aid', $aid);
        $this->assign('minsu', $vo1);
        return view();
    }

    public function comm($id=0, $aid=0)
    {
        $model = [
            'name'=>'评论管理'
        ];
        $where['a.type']=2;
        if ($id) {
            $where['a.pid']=$id;
        }

        $search = $this->_search1();
        $where = array_merge($where, $search);

        $list = db('comments')
            ->alias('a')
            ->join('think_article b', 'b.id=a.pid')
            ->field('a.id,a.pid,a.uid,a.rid,a.content,a.type,a.hits,a.love,a.seem,a.date,a.status,b.title')
            ->where($where)
            ->order('date desc')
            ->paginate(15, false, [
            'query'=>[
                's_keywords'=>input('s_keywords'),
                "s_date"=>input('s_date'),
                "s_status"=>input('s_status')
            ]
        ]);
        $article = db('article')->field('id,title')->find($id);
        $count = db('comments')->count('*');
        $this->assign('count', $count);
        $this->assign('aid', $aid);
        $this->assign('model', $model);
        $this->assign('list', $list);
        $this->assign('f', $article);
        return view();
    }

    public function add_comment_handler($id=0, $aid=0, $pid=0, $_id=0, $p='')
    {
        $param = request()->param();
        unset($param['aid']);
        //unset($param['pid']);
        unset($param['p']);
        unset($param['_id']);
        //$param['aid'] = $aid;

        if ($id) {
            $param['dates']=time();
            if (!db('comments')->update($param)) {
                return ['status'=>0,'msg'=>'修改失败请重试'];
            }
            return [
                'status'=>1,
                'msg'=>'修改成功',
                'redirect'=>Url('comm?aid='.$aid.'&pid='.$pid.'&p='.$p.'&_id='.$_id)
            ];
        } else {
            $param['date']=time();
            $param['type']=2;
            if (!db('comments')->insert($param)) {
                return ['status'=>0,'msg'=>'添加失败请重试'];
            }
            return [
                'status'=>1,
                'msg'=>'添加成功',
                'redirect'=>Url('comm?aid='.$aid.'&pid='.$pid.'&p='.$p.'&_id='.$_id)
            ];
        }
    }

    public function add_tiyan_handler($id=0, $aid=0, $pid=0, $type=0, $_id=0)
    {
        $param = request()->param();
        unset($param['aid']);
        unset($param['pid']);
        unset($param['p']);
        unset($param['_id']);
        $param['fid'] = $pid;

        if ($id) {
            $param['dates']=time();
            if (!db('taste')->update($param)) {
                return ['status'=>0,'msg'=>'修改失败请重试'];
            }
            return [
                'status'=>1,
                'msg'=>'修改成功',
                'redirect'=>Url('tiyan?aid='.$aid.'&id='.$pid.'&fid='.input('fid/d').'&t='.$type.'&_id='.$_id)
            ];
        } else {
            $param['date']=time();
            if (!db('taste')->insert($param)) {
                return ['status'=>0,'msg'=>'添加失败请重试'];
            }
            return [
                'status'=>1,'msg'=>'添加成功',
                'redirect'=>Url('tiyan?aid='.$aid.'&id='.$pid.'&fid='.input('fid/d').'&t='.$type.'&_id='.$_id)
            ];
        }
    }

    public function add_zhoubian_handler($id=0, $aid=0, $pid=0)
    {
        $param = request()->param();
        unset($param['aid']);
        unset($param['pid']);
        unset($param['p']);
        $param['fid'] = $pid;

        if ($id) {
            $param['dates']=time();
            if (!db('zhoubian')->update($param)) {
                return ['status'=>0,'msg'=>'修改失败请重试'];
            }
            return ['status'=>1,'msg'=>'修改成功','redirect'=>Url('zhoubian')."?aid=$aid&id=$pid&fid=".input('fid')];
        } else {
            $param['date']=time();
            if (!db('zhoubian')->insert($param)) {
                return ['status'=>0,'msg'=>'添加失败请重试'];
            }
            return ['status'=>1,'msg'=>'添加成功','redirect'=>Url('zhoubian')."?aid=$aid&id=$pid&fid=".input('fid')];
        }
    }


    public function fangjian($id=0, $aid=0)
    {
        $model = [
            'name'=>'房间管理'
        ];
        $where['nid']=$id;

        if (!$id) {
            $this->error('出现错误');
        }
        $f = db('article')->field('id,title')->find($id);
        $search = $this->_search();
        $where = array_merge($where, $search);

        $list = db('rooms')->where($where)->order('date desc')->paginate(15, false, [
            'query'=>[
                's_keywords'=>input('s_keywords'),
                "s_date"=>input('s_date'),
                "s_status"=>input('s_status')
            ]
        ]);
        // 查询状态为1的用户数据 并且每页显示10条数据
        $count = db('rooms')->count('*');
        $this->assign('count', $count);
        $this->assign('aid', $aid);
        $this->assign('model', $model);
        $this->assign('list', $list);
        $this->assign('f', $f);
        return view();
    }


    public function add_rooms($id=0, $nid=0)
    {
        $model = [
            'name'=>'房间管理'
        ];
        $arc = db('article')->field('id,title')->find($nid);
        if ($nid && !$arc) {
            //$this->error('出现错误');
        }
        $_temp=[];
        $room=[];
        if ($id) {
            if ($room['events']) {
                $room['events'] = json_decode($room['events'], true);
                foreach ($room['events'] as $k=>$v) {
                    $_temp[$v['date']]=$v['events'];
                }
            }
        }
        $this->assign('json_events', json_encode($_temp));
        $this->assign('id', $id);
        $this->assign('model', $model);
        return view();
    }


    public function add_rooms_handler($id=0, $nid=0, $aid=0, $fid=0, $_id=0)
    {
        $params = request()->post();
        $params['pics']=isset($params['pics'])?implode(',', $params['pics']):'';
        unset($params['aid']);
        unset($params['fid']);
        unset($params['_id']);
        $_events=[];
        //日期事件
        if (isset($params['events']) && isset($params['dates']) && isset($params['prices'])) {
            foreach ($params['events'] as $k => $v) {
                $_events[]=[
                    'date'=>$params['dates'][$k],
                    'date1'=>preg_replace('/-/', '', $params['dates'][$k]),
                    'events'=>$params['events'][$k],
                    'prices'=>$params['prices'][$k],
                ];
            }
            unset($params['events']);
            unset($params['dates']);
            unset($params['prices']);
        }
        $_events = $_events?json_encode($_events):'';
        $params['events'] =$_events;
        //p($params);die;
        if ($id) {
            $params['dates']=time();
            if (!db('rooms')->where('id', 'eq', $id)->update($params)) {
                return json([
                   'status'=>0,
                   'msg'=>'操作失败'
                ]);
            }
            return json([
                'status'=>1,
                'msg'=>'操作成功',
                'redirect'=>Url("article/fangjian?id={$nid}&aid={$aid}&fid={$fid}&_id={$_id}")
            ]);
        } else {
            $params['date']=time();
            if (!db('rooms')->insert($params)) {
                return json([
                    'status'=>0,
                    'msg'=>'操作失败'
                ]);
            }
            return json([
                'status'=>1,
                'msg'=>'操作成功',
                'redirect'=>Url("article/fangjian?id={$nid}&aid={$aid}&fid={$fid}&_id={$_id}")
            ]);
        }
    }

    public function status_rooms($id, $type)
    {
        $type = ($type=="delete-all")?"delete":$type;
        $_result = $this->_status(
            $id,
            'rooms',
            $type,
            'image',
            Url('fangjian?id='.input("_fid").'&aid='.input('aid').'&p='.input('p').'&fid='.input('fid').'&_id='.input('_id'))
        );
        return $_result;
    }


    /**
     * [status 状态操作]
     * @param  [type] $id [修改id]
     * @param  [type] $type  [操作类型]
     * @return [type]     [description]
     */
    public function status($id, $type)
    {
        $type = ($type=="delete-all")?"delete":$type;
        $_result = $this->_status(
            $id,
            'article',
            $type,
            'image',
            Url('index?aid='.input('aid').'&p='.input('p').'&fid='.input('fid').'&_id='.input('_id'))
        );
        return $_result;
    }

    public function set_status($id, $type)
    {
        $type = ($type=="delete-all")?"delete":$type;
        $_result = $this->_status(
            $id,
            'taste',
            $type,
            '',
            Url('index?aid='.input('aid').'&p='.input('p').'&fid='.input('fid').'&_id='.input('_id'))
        );
        return $_result;
    }

    public function set_status1($id, $type)
    {
        $type = ($type=="delete-all")?"delete":$type;
        $_result = $this->_status($id, 'zhoubian', $type, '', '', 0);
        return $_result;
    }

    public function set_status_comm($id, $type)
    {
        $type = ($type=="delete-all")?"delete":$type;
        $_result = $this->_status($id, 'comments', $type, '', '', 0);
        return $_result;
    }

    protected function _search($param=[])
    {
        $where=[];
        if (empty($param)) {
            $param = request()->param();
        }
        if (!empty($param['s_keywords'])) {
            $where['title|keywords']=['like',"%".$param['s_keywords']."%"];
        }
        if (!empty($param['s_status'])) {
            $where['status']=$param['s_status']>-1?$param['s_status']:'';
        }
        if (!empty($param['s_date'])) {
            $date = explode('-', $param['s_date']);
            $date[1] = "$date[1] 24:00";
            $where['date']=['between',[strtotime($date[0]),strtotime($date[1])]];
        }

        $this->assign('search', [
            's_keywords'=>!empty($param['s_keywords'])?$param['s_keywords']:'',
            's_date'=>!empty($param['s_date'])?$param['s_date']:'',
            's_status'=>!empty($param['s_status'])?$param['s_status']:-1
        ]);
        return $where;
    }
    protected function _search1($param=[])
    {
        $where=[];
        if (empty($param)) {
            $param = request()->param();
        }
        if (!empty($param['s_keywords'])) {
            $where['a.content']=['like',"%".$param['s_keywords']."%"];
        }
        if (!empty($param['s_status'])) {
            $where['a.status']=$param['s_status']>-1?$param['s_status']:'';
        }
        if (!empty($param['s_date'])) {
            $date = explode('-', $param['s_date']);
            $date[1] = "$date[1] 24:00";
            $where['a.date']=['between',[strtotime($date[0]),strtotime($date[1])]];
        }

        $this->assign('search', [
            's_keywords'=>!empty($param['s_keywords'])?$param['s_keywords']:'',
            's_date'=>!empty($param['s_date'])?$param['s_date']:'',
            's_status'=>!empty($param['s_status'])?$param['s_status']:''
        ]);
        return $where;
    }
}
