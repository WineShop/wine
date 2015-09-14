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
               window.location.href='/User/registerSuccess.html';
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
                    main.modalAlert('对不起，两次密码不一致!','danger');
                    return false;
                }
            }else{
                main.modalAlert('对不起，不能为空!','danger');
                return false;
            }
            return true;
        }

    }




    var login = function(){


    }


    module.exports = {
        login     :  login,
        register  :  register
    }

});