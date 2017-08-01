jQuery(document).ready(function() {
	var flag = true;
    $("#city").click(function (e) {
        SelCity(this,e);
    });
    /*
        Fullscreen background
    */
    $.backstretch($('body').attr('data-id'));
    
    $('#top-navbar-1').on('shown.bs.collapse', function(){
    	$.backstretch("resize");
    });
    $('#top-navbar-1').on('hidden.bs.collapse', function(){
    	$.backstretch("resize");
    });

    /*
        Form
    */
    $('input[name="phone"]').blur(function () {
        var v = $(this).val(),
            tel = /^1[3|4|5|8][0-9]\d{4,8}$/;
        if(v!='' && tel.test(v)){
            $(this).removeClass('input-error');
            flag =  true;
        }else{
            $(this).focus();
            $(this).addClass('input-error');
            flag =  false;
        }
    });
    $('input[name="email"]').blur(function () {
        var v = $(this).val(),
            email = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
        if(v!='' && email.test(v)){
            $(this).removeClass('input-error');
            flag =  true;
        }else{
            $(this).focus();
            $(this).addClass('input-error');
            flag =  false;
        }
    });
    $('input[name="repeat_password"]').blur(function () {
        var v = $(this).val(),
            v1 = $('input[type="password"]').val();
        if(v!='' && v1 == v){
            $(this).removeClass('input-error');
            flag =  true;
        }else{
            $(this).addClass('input-error');
            flag =  false;
        }
    });
    $('input[name="alipay"]').blur(function () {
        var v = $(this).val(),
            email = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/,
            tel = /^1[3|4|5|8][0-9]\d{4,8}$/;
        if(v==''){
            $(this).focus();
            $(this).addClass('input-error');
            flag =  false;
		}else{
            if(email.test(v) || tel.test(v)){
                $(this).removeClass('input-error');
                flag =  true;
            }else{
                $(this).focus();
                $(this).addClass('input-error');
                flag =  false;
            }
        }
    });


    $('.registration-form fieldset:first-child').fadeIn('slow');
    
    $('.registration-form input[type="text"], .registration-form input[type="password"], .registration-form textarea').on('focus', function() {
    	$(this).removeClass('input-error');
    });
    
    // next step
    $('.registration-form .btn-next').on('click', function() {
    	var parent_fieldset = $(this).parents('fieldset');
    	var next_step = true;
    	
    	parent_fieldset.find('input[type="text"], input[type="password"], textarea').each(function() {
    		if( $(this).val() == "" ) {
    			$(this).addClass('input-error');
    			next_step = false;
    		}
    		else {
    			$(this).removeClass('input-error');
    		}
    	});
    	if(next_step && flag) {
    		parent_fieldset.fadeOut(400, function() {
	    		$(this).next().fadeIn();
	    	});
    	}
    });
    
    // previous step
    $('.registration-form .btn-previous').on('click', function() {
    	$(this).parents('fieldset').fadeOut(400, function() {
    		$(this).prev().fadeIn();
    	});
    });

    // submit
    $('.registration-form').on('submit', function(e) {
        e.preventDefault();
        $('input[name="alipay"]').trigger('blur');
        $(this).find('input[type="text"], input[type="password"],textarea').each(function() {
            if( $(this).val() == "" ) {
                e.preventDefault();
                $(this).addClass('input-error');
            }else {
                $(this).removeClass('input-error');
            }
        });
        if(flag){
            var form = $('form');
            var index = layer.load(2, {
                shard: [0.6, "#000"]
            });
            $.post(form.attr('action'),form.serialize(),function (result) {
                layer.close(index);
                if(result.status==0){
                    layer.msg(result.msg, {
                        offset: 'b',
                        anim: 6
                    });
                }else{

                    layer.confirm(result.msg,{
                        icon:6,
                        title:'提示信息',
                        btn: ['现在登录','以后登录']
                    },function(){
                        window.location.href=result.redirect;
                    },function () {
                        form[0].resetForm();
                    });
                }
            });
        }
    });
});