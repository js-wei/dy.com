<?php
namespace app\index\controller;
use app\index\controller;

class Publish extends Base{
	
	protected function  _initialize(){
		parent::_initialize();
	}

	public function get_site(){
		$data=[
			'title'=>$this->site['title'],
			'logo'=>$this->site['url'].$this->site['logo'],
			'keywords'=>$this->site['keywords'],
			'description'=>$this->site['description'],
			'url'=>$this->site['url'],
		];
		return ['status'=>1,'data'=>$data];
	}

	public function get_colunm($id=0){
		$data = db('column')->field('id,title,name')->where(['status'=>0,'fid'=>0])->order('sort asc')->select();
		if($id==0){
			$data[0]['active']=1;
		}else{
			foreach ($data as $k => $v) {
				if($v['id']==$id){
					$data[$k]['active']=1;
				}else{
					$data[$k]['active']=0;
				}
			}
		}
		return ['status'=>1,'data'=>$data];
	}
}