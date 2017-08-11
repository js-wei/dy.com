<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017/8/4
 * Time: 15:24
 */
namespace app\index\controller;

class Income extends Base{
    protected function _initialize(){
        parent::_initialize();
        if(!session('?_mid')){
            $this->redirect('/');
        }
    }
    public function index(){
        $id = session('_mid');
        $list = db('order')
                ->alias('a')
                ->join('think_product b','b.id=a.proid')
                ->where('a.mid','eq',$id)
                ->field('a.id,a.ordtitle,a.ordprice,a.ordfee,a.ordstatus,a.ordbuynum,a.ordtime,a.finishtime,b.divides')
                ->paginate(5);
        $this->assign('list',$list);
        $this->get_details($id);
        return view();
    }

    public function has(){
        return view();
    }

    public function get_details($id){
        $c = db('order')->where('mid','eq',$id)->count();
        $c1 = db('order')->where('mid','eq',$id)->where('ordstatus','eq',1)->count();
        $list = db('order')
            ->alias('a')
            ->join('think_product b','b.id=a.proid')
            ->where('a.mid','eq',$id)
            ->field('a.id,a.ordfee,a.ordstatus,b.divides')
            ->select();
        $sum = 0;
        foreach ($list as $k => $v){
            if($v['ordstatus']==1){
                $sum += $v['ordfee']*$v['divides'];
            }
        }
        $this->assign('c',$c?$c:0);
        $this->assign('c1',$c1?$c1:0);
        $this->assign('sum',$sum);
    }
}