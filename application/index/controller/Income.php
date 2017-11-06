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
                ->order('ordtime desc')
                ->paginate(10);
        $this->assign('list',$list);
        $this->get_details($id);
        return view();
    }
    public function has(){
        return view();
    }

    /**
     * 导出数据
     * @param string $time
     * @param string $user
     * @return array|string
     */
    public function export($time='',$user=''){
        $id = session('_mid');
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
//        $list = db('order')
//            ->field('ordid,ordtitle,ordbuynum,ordprice,ordfee,ordtime,finishtime,ordstatus')
//            ->where($where)->select();
        $list = db('order')
            ->alias('a')
            ->join('think_product b','b.id=a.proid')
            ->where('a.mid','eq',$id)
            ->field('a.ordid,a.ordtitle,a.ordprice,a.ordfee,a.ordstatus,a.ordbuynum,a.ordtime,a.finishtime,b.divides')
            ->order('ordtime desc')
            ->select();
		if(empty($list)){
			 return array('status'=>0,'msg'=>'没有需要导出的数据');
		}
        $divides=0;
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

        if(request()->isPost()){
            if(!empty($list)){
                return array('status'=>1,'msg'=>'有导出数据','redirect'=>Url('export')."?time={$time}&user={$user}");
            }else{
                return array('status'=>0,'msg'=>'没有需要导出的数据');
            }
        }else{

            $this->exportExcel($title,$xlsCell,$list,"{$title}账单信息   生成日期:".date('Y-m-d',time()));
        }
    }
    /**
     * 导出Excel
     * @param $expTitle
     * @param $expCellName
     * @param $expTableData
     * @param $header
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    protected function exportExcel($expTitle,$expCellName,$expTableData,$header=''){
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName = date('_YmdHis_').'对账单';//or $xlsTitle 文件名称可根据自己情况设定

        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        vendor("PHPExcel.PHPExcel");

        $objPHPExcel = new \PHPExcel();

        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $header);
        for($i=0;$i<$cellNum;$i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]);
        }

        // Miscellaneous glyphs, UTF-8
        for($i=0;$i<$dataNum;$i++){
            for($j=0;$j<$cellNum;$j++){
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
            }
        }
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        return '';
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

	
	public function delete($id=''){
		if(empty($id)){
			return json(['status'=>0,'msg'=>'参数错误']);
		}
		if(!db('order')->delete($id)){
			return json(['status'=>0,'msg'=>'删除失败']);
		}
		return json(['status'=>1,'msg'=>'删除成功']);
	}

	public function delete_all($id=''){
		if(empty($id)){
			return json(['status'=>0,'msg'=>'参数错误']);
		}
		if(!db('order')->where('id','in',$id)->delete()){
			return json(['status'=>0,'msg'=>'删除失败']);
		}
		return json(['status'=>1,'msg'=>'删除成功']);
	}
}