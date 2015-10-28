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

    module.exports = {

    }
});