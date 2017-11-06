$(function () {
    //退出
    $(document).on('click','.logout',function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        $.get(url,function (result) {
            if(result.status==1){
                layer.alert(result.msg,{icon:6,end:function () {
                    window.location.href=result.redirect;
                }})
            }
        })
    });
    $(document).on('click','.link',function (e) {
        e.preventDefault();
        var t = $(this).attr('data-role');
        layer.open({
            type: 2,
            title: '更新信息操作',
            skin: 'layui-layer-demo',
            closeBtn: 1,
            area: ['680px', '480px'],
            anim: 2,
            shadeClose: true,
            content: '/account/alter?t=' + t
        });
    })
});