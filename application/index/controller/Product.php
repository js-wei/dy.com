<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017/8/4
 * Time: 15:24
 */
namespace app\index\controller;

class Product extends Base{
    protected function _initialize(){
        parent::_initialize();
        if(!session('?_mid')){
            $this->redirect('/');
        }
    }
    public function index(){
        $where['status']=0;
        $list = db('product')->where($where)->paginate(8);
        $this->assign('list',$list);
        return view();
    }

    public function has(){
        $id = session('_mid');
        $where['mid']=$id;
        $list = db('my_product')
            ->alias('a')
			->field('a.id,a.mid,a.pid,a.click,a.url,a.short_url,a.date,b.title,b.image,b.price,b.divides,b.status')
            ->join('think_product b','b.id=a.pid')
			->where($where)
            ->paginate(4);
        $this->assign('list',$list);
        return view();
    }



    /**
     * 删除
     * @param int $id
     * @return \think\response\Json
     */
    public function delete($id=0){
        if(!db('my_product')->delete($id)){
            return json(['status'=>0,'msg'=>'删除失败',db('my_product')->getlastsql()]);
        }
        return json(['status'=>1,'msg'=>'删除成功']);
    }

    /**
     * 删除所有
     * @param string $id
     * @return \think\response\Json
     */
    public function delete_all($id=''){
        if(empty($id)){
            return json(['status'=>0,'msg'=>'参数错误']);
        }
        if(!db('my_product')->where('id','in',$id)->delete()){
            return json(['status'=>0,'msg'=>'删除失败']);
        }
        return json(['status'=>1,'msg'=>'删除成功']);
    }

}