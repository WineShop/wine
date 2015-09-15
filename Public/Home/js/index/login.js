/**
 * Created by kevin on 15-9-13.
 */
define(function(require, exports, module){
    var main = require('main');
    var T    = require('T');

    main.modalEvent();

    var register = function(){

        $(document).delegate('#register','click',function(){
           if(!checkPut()) return false;
           var param = $("#form_register").serialize();
           var url  = $("#form_register").attr('action');
           $("button:submit").addClass("log-in").attr("disabled", true);
           T.restPost(url,param,function(success){
               main.modalAlert(success.msg);
               main.redirect('/');
           },function(error){
                main.modalAlert(error.msg,'danger');
                $(".checkCode").click();
           })
            $("button:submit").removeClass("log-in").attr("disabled", false);
        });


        //变换验证码
        $(document).delegate(".checkCode",'click',function(){
            var verifyimg = $(".checkCode").attr("src");
            if( verifyimg.indexOf('?')>0){
                $(".checkCode").attr("src", verifyimg+'&random='+Math.random());
            }else{
                $(".checkCode").attr("src", verifyimg.replace(/\?.*$/,'')+'?'+Math.random());
            }
        });

        //表单验证
        var checkPut = function(){
            if($.trim($("#inputEmail").val()) != '' && $.trim($("#inputPassword").val()) !='' && $.trim($("#inputRePassword").val()) !='' && $.trim($("#verify").val()) !='')
            {
                if($.trim($("#inputPassword").val()) != $.trim($("#inputRePassword").val()))
                {
                    main.modalAlert('对不起，两次密码不一致！','danger');
                    return false;
                }
                var reg = /^\w+@(\w+\.)+[a-zA-Z0-9]{1,5}$/;
                if(!reg.test($.trim($("#inputEmail").val())))
                {
                    main.modalAlert('对不起，邮箱格式不对！','danger');
                    return false;
                }
            }else{
                main.modalAlert('对不起，不能为空！','danger');
                return false;
            }
            return true;
        }


        //点击登录的时候弹窗
        $(document).delegate('#modal_rigister','click',function(){
            var url = $(this).data('url');
            T.ajaxLoad(url,'alterModal',{},function(){
                $('#alterModal').modal('show');
            });
        });


    }




    var login = function(b){
        $(document).delegate('#login_btn_cart','click',function(){
            var param ={
                username    : $.trim($("#inputusername").val()),
                password    : $.trim($("#inputpassword").val()),
                verify      : $.trim($("#verify").val())
            }

            var type   = checkFormPut();
            if(!type) return false;
            param.type = type;
            console.log(param);
            $("button:submit").addClass("log-in").attr("disabled", true);

            T.restPost($("#loginform").attr('action'),param,function(success){
                main.modalAlert(success.msg);
                main.redirect('/');
            },function(error){
                main.modalAlert(error.msg,'danger');
                $(".checkCode").click();
            });
            $("button:submit").removeClass("log-in").attr("disabled", false);
        });




        //变换验证码
        $(document).delegate(".checkCode",'click',function(){
            var verifyimg = $(".checkCode").attr("src");
            if( verifyimg.indexOf('?')>0){
                $(".checkCode").attr("src", verifyimg+'&random='+Math.random());
            }else{
                $(".checkCode").attr("src", verifyimg.replace(/\?.*$/,'')+'?'+Math.random());
            }
        });

        //验证表单
        var checkFormPut = function(){
            if($.trim($("input[name=username]").val()) != '' && $.trim($("input[name=password]").val()) !='' && $.trim($("input[name=verify]").val()) !='')
            {
                var reg1 = /^\w+@(\w+\.)+[a-zA-Z0-9]{1,5}$/;
                var reg2 = /^\d{11}&/;
                if(reg2.test($.trim($("#inputusername").val())))
                {
                    return 3;           //3代表手机
                }else if(reg1.test($.trim($("#inputusername").val())))
                {
                    return 2;           //2表示邮箱
                }else{
                    return 1;
                }

            }else{
                main.modalAlert('对不起，不能为空！','danger');
                return false;
            }
        }

        //点击注册的时候弹窗
        $(document).delegate('#modal_login','click',function(){
            var url = $(this).data('url');
            T.ajaxLoad(url,'alterModal',{},function(){
                $('#alterModal').modal('show');
            });
        });

    }


    module.exports = {
        login     :  login,
        register  :  register
    }

});