<?php
# @Author: 魏巍
# @Date:   2017-11-16T17:42:05+08:00
# @Email:  524314430@qq.com
# @Last modified by:   魏巍
# @Last modified time: 2017-11-18T17:37:22+08:00



/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017/8/4
 * Time: 15:24
 */
namespace app\index\controller;

class Product extends Base
{
    protected function _initialize()
    {
        parent::_initialize();
        if (!session('?_mid')) {
            $this->redirect('/');
        }
    }
    public function pension()
    {
        $where['status']=0;
        $where['type']=1;
        $where['uid']=session('_mid');
        $list = db('article')->where($where)->paginate(8, false);
        $this->assign('list', $list);
        return view();
    }
    /**
     * 添加房型
     * @return \think\response\View
     */
    public function add_pension()
    {
        if (request()->isGet()) {
            $list = db('version')->field('dates', true)->where(['status'=>0])->select();
            foreach ($list as $k  => $v) {
                $list[$k]['image'] = $this->site['url'].$v['image'];
            }
            $this->assign('list', $list);
            return view();
        }
        $uid = session('_mid');
        if (!$uid) {
            return json([
                'status'=>0,
                'msg'=>'您没有登录'
            ]);
        }
        $param = request()->param();
        if (isset($param['facilities'])) {
            $param['facilities']=implode(',', $param['facilities']);
        } else {
            $param['facilities']='';
        }
        if (isset($param['city'])) {
            $province = db('provinces')->where('province', 'like', '%'.$param['hcity'].'%')->find();
            $city = db('cities')->where('city', 'like', '%'.$param['hproper'].'%')
                ->where('provinceid', 'eq', $province['provinceid'])
                ->find();
            if ($province) {
                $param['provid']=$province['id'];
            }
            if ($city) {
                $param['city_id']=$city['id'];
            }
            unset($param['city']);
            unset($param['hcity']);
            unset($param['hproper']);
            unset($param['harea']);
        }
        $param['column_id']=4;
        $param['type']=1;
        $param['uid']=$uid;
        if (!db('article')->insert($param)) {
            return json([
                'status'=>0,
                'msg'=>'添加失败'
            ]);
        }
        return json([
            'status'=>1,
            'msg'=>'添加成功',
            'redirect'=>Url('product/pension')
        ]);
    }


    public function index()
    {
        $where['status']=0;
        $where['type']=1;
        $list = db('article')->where($where)->paginate(8);
        $this->assign('list', $list);
        return view();
    }

    public function has()
    {
        $id = session('_mid');
        $where['mid']=$id;
        $list = db('my_product')
            ->alias('a')
            ->field('a.id,a.mid,a.pid,a.click,a.url,a.short_url,a.date,b.title,b.image,b.price,b.divides,b.status')
            ->join('think_product b', 'b.id=a.pid')
            ->where($where)
            ->paginate(4);
        $this->assign('list', $list);
        return view();
    }

    public function add_room()
    {
        return view();
    }

    /**
     * 删除
     * @param int $id
     * @return \think\response\Json
     */
    public function delete($id=0)
    {
        if (!db('my_product')->delete($id)) {
            return json(['status'=>0,'msg'=>'删除失败',db('my_product')->getlastsql()]);
        }
        return json(['status'=>1,'msg'=>'删除成功']);
    }

    /**
     * 删除所有
     * @param string $id
     * @return \think\response\Json
     */
    public function delete_all($id='')
    {
        if (empty($id)) {
            return json(['status'=>0,'msg'=>'参数错误']);
        }
        if (!db('my_product')->where('id', 'in', $id)->delete()) {
            return json(['status'=>0,'msg'=>'删除失败']);
        }
        return json(['status'=>1,'msg'=>'删除成功']);
    }
}
