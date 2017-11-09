<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2017/11/9
 * Time: 13:17
 */

namespace Service;

/**
 * 先说思路：先根据接口返回的数据（经纬度）、根据百度地图的当前定位，
 * 然后计算出每条数据到到当前点的距离（精确度最好要高，）。
 * 这里我使用数组arrayKeys保存所有的距离。
 * 然后创建一个字典dataDic把当前距离distance作为key,values为key对应下的数据model。
 * 然后我们将数据arrayKeys进行排序。这样只要arrayKeys排序了，对应的model其实也相当于排序了。
 * Class Baibu 实现的是 百度LBS服务
 * @package Service
 */
class Baibu{
    private $url = [
      'geotable'=>  'http://api.map.baidu.com/geodata/v4/geotable',
      'geocolumn'=>'http://api.map.baidu.com/geodata/v4/column',
      'poi'=>'http://api.map.baidu.com/geodata/v4/poi'
    ];

    /**
     * 创建位置表
     * @param $table
     * @return bool|mixed
     */
    public function geotable_create($table){
        $result = $this->http("{$this->url["geotable"]}/create",$table);
        return $result;
    }

    /**
     * 查询位置表
     * @return bool|mixed
     */
    public function geotable_select(){
        $table = new Geotable();
        $result = $this->http('list',$table);
        return $result;
    }

    /**
     * 获取位置详细
     * @param string $id
     * @return array|bool|mixed
     */
    public function geotable_detail($id=''){
        if(!$id){
            return ['status'=>0,'message'=>'缺少必要参数'];
        }
        $table = new Geotable();
        $result = $this->http("{$this->url["geotable"]}/detail",['id'=>$id,'ak'=>$table->ak]);
        return $result;
    }

    /**
     * 删除
     * @param string $id
     * @return array|bool|mixed
     */
    public function geotable_delete($id=''){
        if(!$id){
            return ['status'=>0,'message'=>'缺少必要参数'];
        }
        $table = new Geotable();
        $result = $this->http("{$this->url["geotable"]}/delete",['id'=>$id,'ak'=>$table->ak]);
        return $result;
    }

    /**
     * 更新表
     * @param $table
     * @return array|bool|mixed
     */
    public function geotable_update($table){
        if(!$table){
            return ['status'=>0,'message'=>'请传入Geotable对象'];
        }
        $result = $this->http("{$this->url["geotable"]}/update",$table);
        return $result;
    }

    public function geocolumn_create($column){
        if(!$column){
            return ['status'=>0,'message'=>'请传入Geocolumn对象'];
        }
        $result = $this->http("{$this->url["geocolumn"]}/create",$column);
        return $result;
    }

    public function geocolumn_select($column){
        if(!$column){
            return ['status'=>0,'message'=>'请传入Geocolumn对象'];
        }
        $result = $this->http("{$this->url["geocolumn"]}/list",$column);
        return $result;
    }

    /**
     * 查看列
     * @param $column
     * @return array|bool|mixed
     */
    public function geocolumn_detail($column){
        if(!$column){
            return ['status'=>0,'message'=>'请传入Geocolumn对象'];
        }

        $result = $this->http("{$this->url["geocolumn"]}/detail",$column);
        return $result;
    }

    public function geocolumn_update($column){
        if(!$column){
            return ['status'=>0,'message'=>'请传入Geocolumn对象'];
        }
        $column = json_decode(json_encode($column),true);
        if($column['type']==4){
            unset($column['is_search_field']);
            unset($column['is_index_field']);
        }
        $result = $this->http("{$this->url["geocolumn"]}/update",$column);
        return $result;
    }

    public function geocolumn_delete($column){
        if(!$column){
            return ['status'=>0,'message'=>'请传入Geocolumn对象'];
        }

        $result = $this->http("{$this->url["geocolumn"]}/delete",$column);
        return $result;
    }


    /**
     * 请求数据
     * @param string $api
     * @param $data
     * @return bool|mixed
     */
    private function http($api='',$data){
        if(is_object($data)){
            $data = json_decode(json_encode($data),true);
        }
        if(!$api){
            return false;
        }
        if(!$data){
            return false;
        }
        $curl = new \Curl\Curl();
        $response = $curl->post($api,$data);
        return json_decode($response->response,true);
    }

}

/**
 * Class Geotable  LBS位置表
 * @package Service
 */
class Geotable{
    public $id;
    /**
     * @var string
     */
    public $name;
    /**
     * @var int
     */
    public $geotype=1;
    public $is_published=1;
    public $timestamp;
    /**
     * @var string ak
     */
    public $ak='xooZZG25yNjbmCFGytrRyor0';
    public $sn;
}

class Geocolumn{
    public $name;
    public $key;
    public $type=3;   //1：Int64, 2：double, 3：string, 4：在线图片url
    public $is_search_field=0;
    public $is_index_field=0;
    public $geotable_id;
    public $default_value='';
    public $id;
    public $ak='xooZZG25yNjbmCFGytrRyor0';
}