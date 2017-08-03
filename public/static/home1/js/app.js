$(function () {
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
    })
});