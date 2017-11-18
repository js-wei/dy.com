<?php
# @Author: 魏巍
# @Date:   2017-11-16T17:42:05+08:00
# @Email:  524314430@qq.com
# @Last modified by:   魏巍
# @Last modified time: 2017-11-18T17:40:19+08:00


namespace Service;

/**
 * 先说思路：先根据接口返回的数据（经纬度）、根据百度地图的当前定位，
 * 然后计算出每条数据到到当前点的距离（精确度最好要高，）。
 * 这里我使用数组arrayKeys保存所有的距离。
 * 然后创建一个字典dataDic把当前距离distance作为key,values为key对应下的数据model。
 * 然后我们将数据arrayKeys进行排序。这样只要arrayKeys排序了，对应的model其实也相当于排序了。
 *  $lbs = new \service\Baibu();
$table = new \service\Geotable();
$table->name="商家信息";
$table->timestamp=time();
//        $_table = $lbs->geotable_create($table);
//        p($_table);
$column = new \Service\Geocolumn();
$column->name="商铺介绍";
$column->key="info";
//$column->type=4;
$column->geotable_id='1000002303';
//$_column = $lbs->geocolumn_create($column);     //创建列
//$_column = $lbs->geocolumn_select($column);     //查询列
//$column->id='1000002887';
//$_column = $lbs->geocolumn_detail($column);      //查看列
//$_column = $lbs->geocolumn_update($column);        //更新列
//$_column = $lbs->geocolumn_delete($column);        //删除列
//p($_column);

//pois

$poi = new \service\Geopoi();
$poi->title="西二旗商场";
$poi->address="上地信息路";
$poi->tags="market";
$poi->longitude=116.316343;
$poi->latitude=40.058071;
$poi->coord_type=3;
$poi->geotable_id=1000002303;
$poi->column=[
'image'=>'https://bootstrap-vue.js.org/_nuxt/img/icon.f0a8c9e.png',
'info'=>'西二旗商场是上地地区最大的最全的商场'
];

$result = $lbs->geopoi_create($poi);
p($result);
 * Class Baibu 实现的是 百度LBS服务
 * @package Service
 */
class Baibu
{
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
    public function geotable_create($table)
    {
        $result = $this->http("{$this->url["geotable"]}/create", $table);
        return $result;
    }

    /**
     * 查询位置表
     * @return bool|mixed
     */
    public function geotable_select()
    {
        $table = new Geotable();
        $result = $this->http('list', $table);
        return $result;
    }

    /**
     * 获取位置详细
     * @param string $id
     * @return array|bool|mixed
     */
    public function geotable_detail($id='')
    {
        if (!$id) {
            return ['status'=>0,'message'=>'缺少必要参数'];
        }
        $table = new Geotable();
        $result = $this->http("{$this->url["geotable"]}/detail", ['id'=>$id,'ak'=>$table->ak]);
        return $result;
    }

    /**
     * 删除
     * @param string $id
     * @return array|bool|mixed
     */
    public function geotable_delete($id='')
    {
        if (!$id) {
            return ['status'=>0,'message'=>'缺少必要参数'];
        }
        $table = new Geotable();
        $result = $this->http("{$this->url["geotable"]}/delete", ['id'=>$id,'ak'=>$table->ak]);
        return $result;
    }

    /**
     * 更新表
     * @param $table
     * @return array|bool|mixed
     */
    public function geotable_update($table)
    {
        if (!$table) {
            return ['status'=>0,'message'=>'请传入Geotable对象'];
        }
        $result = $this->http("{$this->url["geotable"]}/update", $table);
        return $result;
    }

    /**
     * 创建列
     * @param $column
     * @return array|bool|mixed
     */
    public function geocolumn_create($column)
    {
        if (!$column) {
            return ['status'=>0,'message'=>'请传入Geocolumn对象'];
        }
        $result = $this->http("{$this->url["geocolumn"]}/create", $column);
        return $result;
    }

    /**
     * 查询列
     * @param $column
     * @return array|bool|mixed
     */
    public function geocolumn_select($column)
    {
        if (!$column) {
            return ['status'=>0,'message'=>'请传入Geocolumn对象'];
        }
        $result = $this->http("{$this->url["geocolumn"]}/list", $column);
        return $result;
    }

    /**
     * 查看列
     * @param $column
     * @return array|bool|mixed
     */
    public function geocolumn_detail($column)
    {
        if (!$column) {
            return ['status'=>0,'message'=>'请传入Geocolumn对象'];
        }

        $result = $this->http("{$this->url["geocolumn"]}/detail", $column);
        return $result;
    }

    /**
     * 更新列
     * @param $column
     * @return array|bool|mixed
     */
    public function geocolumn_update($column)
    {
        if (!$column) {
            return ['status'=>0,'message'=>'请传入Geocolumn对象'];
        }
        $column = json_decode(json_encode($column), true);
        if ($column['type']==4) {
            unset($column['is_search_field']);
            unset($column['is_index_field']);
        }
        $result = $this->http("{$this->url["geocolumn"]}/update", $column);
        return $result;
    }

    /**
     * 删除列
     * @param $column
     * @return array|bool|mixed
     */
    public function geocolumn_delete($column)
    {
        if (!$column) {
            return ['status'=>0,'message'=>'请传入Geocolumn对象'];
        }
        $result = $this->http("{$this->url["geocolumn"]}/delete", $column);
        return $result;
    }

    /**
     * 创建位置信息
     * @param $poi
     * @return array|bool|mixed
     */
    public function geopoi_create($poi)
    {
        if (!$poi) {
            return ['status'=>0,'message'=>'请传入Geopoi对象'];
        }
        $data = $poi->get_data();
        $result = $this->http("{$this->url["poi"]}/create", $data);
        return $result;
    }

    /**
     * 请求数据
     * @param string $api
     * @param $data
     * @return bool|mixed
     */
    private function http($api='', $data)
    {
        if (is_object($data)) {
            $data = json_decode(json_encode($data), true);
        }
        if (!$api) {
            return false;
        }
        if (!$data) {
            return false;
        }

        $curl = new \Curl\Curl();
        $response = $curl->post($api, $data);
        return json_decode($response->response, true);
    }
}

/**
 * Class Geotable  LBS位置表
 * @package Service
 */
class Geotable
{
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

/**
 * Class Geocolumn
 * @package Service
 */
class Geocolumn
{
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

/**
 * Class Geopoi 位置数据
 * @package Service
 */
class Geopoi
{
    public $title;
    public $address;
    public $tags;
    public $latitude;
    public $longitude;
    public $polygons;
    public $coord_type=1;
    public $geotable_id;
    public $ak='xooZZG25yNjbmCFGytrRyor0';
    private $flag=true;
    private $msg=[];
    /**
     * @var array
     */
    public $column=[];

    public function get_data()
    {
        $this->check();
        if (!$this->flag) {
            return $this->msg;
        }
        $data = $this->get_query_column();
        return $data;
    }

    /**
     * 验证码数据完整性
     * @return bool|\think\response\Json
     */
    private function check()
    {
        if (!$this->ak) {
            $this->flag=false;
            $this->msg=[
                'status'=>401,
                'message'=>'缺少必要参数用户的ak'
            ];
        }
        if (!$this->geotable_id) {
            $this->flag=false;
            $this->msg=[
                'status'=>402,
                'message'=>'缺少必要参数数据表id'
            ];
        }
        if (!$this->coord_type) {
            $this->flag=false;
            $this->msg=[
                'status'=>403,
                'message'=>'缺少必要参数坐标的类型'
            ];
        }
        if (!$this->latitude || !$this->longitude) {
            $this->flag=false;
            $this->msg=[
                'status'=>404,
                'message'=>'缺少必要参数经纬坐标'
            ];
        }
    }

    /**
     * 获取自定义栏目内容
     * @return array
     */
    private function get_query_column()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->column) {
            foreach ($data['column'] as $k=>$v) {
                if ($v) {
                    $data[$k]=$v;
                }
            }
        }
        unset($data['column']);
        return $data;
    }
}
