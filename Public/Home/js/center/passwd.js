/**
 * Created by kevin on 15-10-19.
 */
define(function(require, exports, module){
    var T    = require('T');
    var main = require('main');
    main.modalEvent();


    //修改密码
    var modify_pwd = function(){
       $(".sub_pwd").addClass("log-in").attr("disabled", true);
        var param = $("#mypwd").serialize();
        var url   =  $("#mypwd").attr('action');
        T.restPost(url,param,function(success){
            main.modalAlert(success.msg);
            main.redirect('',1500);
        },function(error){
            $("#mypwd").find(".Validform_checktip").text(error.msg);
        });
        $(".sub_pwd").removeClass("log-in").attr("disabled", false);

    }

    $(".sub_pwd").click(function(){
        modify_pwd();
    })


    //验证邮箱：
    var checkmail = function(){
        $("#sendmail").attr('disabled','disabled');
        $("#send_info").parent().hide();
        T.restPost('/Account/send_email',{},function(success){
            var data = success.data;
            $("#send_info").parent().show();
            $("#send_info").html(data.msg);
            setTimeout(function(){
                $("#alterModal").modal('hide');
            },1500)
        },function(error){
            $("#send_info").parent().show();
            $("#send_info").html(error.msg);
        });
        $("#sendmail").attr('disabled','');
    }

    $("#sendmail").click(function(){
        checkmail();
    })


    //设置或者修改支付密码
    var setpaykey = function(){
        $("#show_paykey_info").hide();
        var paykey   = $.trim($("input[name=paykey]").val());
        var repaykey = $.trim($("input[name=repaykey]").val());

        if(paykey != repaykey){
           $("#show_paykey_info").show();
           $("#show_paykey_info td:last-child").html('对不起，两次密码不一致');
            return false;
        }
        if(paykey == '' || repaykey=='' || paykey.length < 6 || repaykey.length < 6){
            $("#show_paykey_info").show();
            $("#show_paykey_info td:last-child").html('对不起，密码必须大于等于6位数！');
            return false;
        }
        var is_add = $("input[name=is_add]").val();
        var code   = $("input[name=code]").val();
        var oldpaykey = '';

        if(is_add != 1){
            oldpaykey = $.trim($("input[name=oldpaykey]").val());
            if(oldpaykey == ''){
                $("#show_paykey_info").show();
                $("#show_paykey_info td:last-child").html('对不起，原密码不能为空！');
                return false;
            }
        }

        var param = {
            is_add    : is_add,
            code      : code,
            oldpaykey : oldpaykey,
            paykey    : paykey,
            repaykey  : repaykey
        };
        $("#paykey_but").attr('disabled',true);
        T.restPost('/Account/savepaykey',param,function(success){
            $("#show_paykey_info").show();
            $("#show_paykey_info td:last-child").html(success.msg);
            setTimeout(function(){
                $("#alterModal").modal('hide');
            },1500)
        },function(error){
            $("#show_paykey_info").show();
            $("#show_paykey_info td:last-child").html(error.msg);
        });
        $("#paykey_but").attr('disabled',false);

    }
    $("#paykey_but").click(function(){
        setpaykey();
    });



    //修改个人信息
    var perserInfo = function(){
        param = $("#information").serialize();
        T.restPost('/Center/update',param,function(success){
            main.modalAlert(success.msg);
            main.redirect('');
        },function(error){
            main.modalAlert(error.msg,'danger');
        });
    };
    $("#edit_person_info").click(function(){
        perserInfo();
    });

    module.exports = {

    }
});