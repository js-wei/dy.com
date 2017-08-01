<?php
function init(){
    $old = '001.png';
    $new = '002.png';
    //创建一个png透明图
    $img = imagecreatefrompng($old);
    setpng($img,$old,$new);
}
function setpng($imgid,$filename,$savename){
    $bg = 'bg.png';//背景图
    $new = imagecreatefrompng($bg);//创建一个png透明图
    list($width,$height)=getimagesize($filename);//获取长和宽
    $white = imagecolorallocate($imgid,0,255,0);//选择一个替换颜色。这里是绿色
    cleancolor($imgid,$white);
    imagecolortransparent($imgid,$white);//把选择的颜色替换成透明
    imagecopymerge($new,$imgid,0,0,0,0,$width,$height,100);//合并图片
    imagepng($new,$savename);//保存图片
    imagedestroy($imgid);//销毁
    imagedestroy($new);
    echo '<img src="'.$filename.'"><br /><img src="'.$savename.'">';
}

function cleancolor($imgid,$color){
    $width = imagesx($imgid);//获取宽
    $height = imagesy($imgid);//获取高
    for($i=0;$i<$width;$i++){
        for($k=0;$k<$height;$k++){
            //对比每一个像素
            $rgb = imagecolorat($imgid,$i,$k);
            $r = ($rgb >> 16)&0xff;//取R
            $g = ($rgb >> 8)&0xff;//取G
            $b = $rgb&0xff;//取B
            $randr = 1.5;
            $randg = 1;
            $randb=1;
            //蓝色RGB大致的位置。替换成绿色
            if($r<=60*$randr && $g<=200*$randg && $b<=255*$randb && $b*$randb>=100){
                    //如果能够精确的计算出要保留位置的，这里可以写绝对的数字
                if($i>=$width/2 && $i<=$width/2 && $k>=$height/2 && $k<=$height/2){
                    
                }else{
                    //改变颜色
                    imagesetpixel($imgid,$i,$k,$color);
                }
            }
        }
    }
}