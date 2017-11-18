<?php
# @Author: 魏巍
# @Date:   2017-11-16T17:42:05+08:00
# @Email:  524314430@qq.com
# @Last modified by:   魏巍
# @Last modified time: 2017-11-18T17:40:26+08:00



namespace service;

class Category
{
    /**
    * 返回一维栏目
    * @param $cate Array 栏目
    * @param $level  int 等级
    * @param $html string 分隔符
    * @param $margin int 空隙
    * @return Array 栏目
    */
    public static function unlimitForLevel($cate, $fid=0, $level=0, $html='|--', $margin=15)
    {
        $arr=array();
        foreach ($cate as  $v) {
            if ($v['fid']==$fid) {
                $v['level']=$level+1;
                $v['html']=$html;
                $v['margin']=$level*$margin;
                $arr[]=$v;
                $arr=array_merge($arr, self::unlimitForLevel($cate, $v['id'], $level+1, $html, $margin));
            }
        }
        return $arr;
    }

    public static function limitForLevel($cate, $fid=0, $level=0, $html='--', $margin=15)
    {
        $arr=array();
        foreach ($cate as  $v) {
            if ($v['fid']==$fid) {
                $v['level']=$level+1;
                $v['html']=str_repeat($html, $level);
                $arr[]=$v;
                $arr=array_merge($arr, self::unlimitForLevel($cate, $v['id'], $level+1, $html, $margin));
            }
        }
        return $arr;
    }
    /**
    * 返回多维栏目
    * @param $cate Array 栏目
    * @param $fid int 父亲id
    * @param $name string 分隔符
    * @return Array 栏目
    */
    public static function unlimitedForLevel($cate, $fid=0, $name='child')
    {
        $arr=array();
        foreach ($cate as  $v) {
            if ($v['fid']==$fid) {
                $v[$name]=self::unlimitedForLevel($cate, $v['id'], $name);
                $arr[]=$v;
            }
        }
        return $arr;
    }


    /**
    * 返回多维栏目
    * @param $cate Array 栏目
    * @param $fid int 父亲id
    * @param $name string 分隔符
    * @return Array 栏目
    */
    public static function unlimitedForLevel1($cate, $fid=0, $name='child')
    {
        $arr=array();
        foreach ($cate as  $v) {
            if ($v['id']==$fid) {
                $v[$name]=self::unlimitedForLevel1($cate, $v['uid'], $name);
                $arr[$v['id']]=$v;
            }
        }
        return $arr;
    }

    public static function getParents($cate, $curent)
    {
        $arr=array();
        if (is_array($curent)) {
            foreach ($cate as $v) {
                if ($v['id']==$curent['fid']) {
                    $arr[]=$v;
                    $arr=array_merge($arr, self::getParents($cate, $v));
                }
            }
        } else {
            foreach ($cate as $v) {
                if ($v['id']==$curent) {
                    $arr[]=$v;
                    $arr=array_merge($arr, self::getParents($cate, $v));
                }
            }
        }
        $arr=array_reverse($arr);
        return $arr;
    }

    public static function get_location($cate, $curent)
    {
        if (empty($curent)) {
            return '';
        }
        $arr[0]=[
            'title'=>'首页',
            'name'=>'',
        ];
        if (is_array($curent)) {
            foreach ($cate as $k => $v) {
                if ($v['id']==$curent['fid']) {
                    $arr[++$k]=$v;
                    $arr=array_merge($arr, self::getparents($cate, $v));
                }
            }
        } else {
            foreach ($cate as $k => $v) {
                if ($v['id']==$curent) {
                    $arr[++$k]=$v;
                    $arr=array_merge($arr, self::getparents($cate, $v));
                }
            }
        }
        $html = '<ol class="breadcrumb location">';
        foreach ($arr as $v) {
            $html.="<li><a href=\"".U('/'.$v['name'])."\">".$v['title']."</a></li>";
        }
        $html.='<li class="active">'.$curent['title'].'</li></ol>';
        return $html;
    }
    /**
     * [getchildrenid 获取子栏目ID]
     * @param  [type] $cate [栏目]
     * @param  [type] $fid  [父栏目id]
     * @return [type]       [结果]
     */
    public static function getChildrenById($cate, $id)
    {
        $arr=array();
        foreach ($cate as $v) {
            if ($v['fid']==$id) {
                $arr[]=$v['id'];
                $arr=array_merge($arr, self::getChildrenById($cate, $v['id']));
            }
        }
        return $arr;
    }

    /**
     * 获取父亲栏目
     * @param $cate
     * @param $fid
     * @return array
     */
    public static function getParentsById($cate, $fid)
    {
        $arr=array();
        foreach ($cate as $v) {
            if ($v['id']==$fid) {
                $arr[]=$v['id'];
                $arr=array_merge($arr, self::getParentsById($cate, $v['fid']));
            }
        }
        return $arr;
    }

    /**
     * 获取子栏目
     * @param $cate
     * @param $id
     * @return array
     */
    public static function get_children($cate, $id)
    {
        $arr=array();
        foreach ($cate as $v) {
            if ($v['fid']==$id) {
                $arr[]=$v;
                $arr=array_merge($arr, self::getposition($v['id']));
            }
        }
        return $arr;
    }
}
