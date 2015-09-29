/**
 * Created by kevin on 15-9-14.
 */
define(function(require, exports, module){
    var main = require('main');
    var T    = require('T');

    var login = function(){
        /* 登陆表单获取焦点变色 */
        $(".login-form").on("focus", "input", function(){
            $(this).closest('.item').addClass('focus');
        }).on("blur","input",function(){
                $(this).closest('.item').removeClass('focus');
            });


        $(".login-btn").click(function(){
            $("button:submit").addClass("log-in").attr("disabled", true);
            var url     = $("#login").attr('action');
            var param   = {
                username    : $("input[name=username]").val(),
                password    : $("input[name=password]").val()
            };
            T.restPost(url,param,function(success){
                main.modalAlert(success.msg,'success');
                main.redirect('/Admin/Index/index',2000);
            },function(error){
                main.modalAlert(error.msg,'danger');
            })
            $("button:submit").removeClass("log-in").attr("disabled", false);
        });




        //初始化选中用户名输入框
        $("#itemBox").find("input[name=username]").focus();
        //刷新验证码
        var verifyimg = $(".verifyimg").attr("src");
        $(".reloadverify").click(function(){
            if( verifyimg.indexOf('?')>0){
                $(".verifyimg").attr("src", verifyimg+'&random='+Math.random());
            }else{
                $(".verifyimg").attr("src", verifyimg.replace(/\?.*$/,'')+'?'+Math.random());
            }
        });

        //placeholder兼容性
        //如果支持
        function isPlaceholer(){
            var input = document.createElement('input');
            return "placeholder" in input;
        }
        //如果不支持
        if(!isPlaceholer()){
            $(".placeholder_copy").css({
                display:'block'
            })
            $("#itemBox input").keydown(function(){
                $(this).parents(".item").next(".placeholder_copy").css({
                    display:'none'
                })
            })
            $("#itemBox input").blur(function(){
                if($(this).val()==""){
                    $(this).parents(".item").next(".placeholder_copy").css({
                        display:'block'
                    })
                }
            })


        }

    }


    module.exports = login;

});