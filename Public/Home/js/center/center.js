/**
 * Created by kevin on 15-10-19.
 */
define(function(require, exports, module){
    T    = require('T');
    main = require('main');
    main.modalEvent();

    var center_init = function(){
        //首页默认被选中
        $(".menu li").eq(0).addClass('active');

        //菜单移动时显示和收缩
        $("#all-goods").hide();
        $(".goods").mouseover(function(){
            $("#all-goods").show();
        });

        $(".goods").mouseout(function(){
            $("#all-goods").hide();
        });



        /** 左边菜单高亮 **/
        url = window.location.pathname + window.location.search;
        url = url.replace(/(\/(p)\/\d+)|(&p=\d+)|(\/(id)\/\d+)|(&id=\d+)|(\/(group)\/\d+)|(&group=\d+)/, "");
        if(url == '/Center/needpay.html' || url=='/Center/tobeshipped.html' || url=='/Center/tobeconfirmed.html'){
            $("a[href='/Center/allorder.html']").addClass("current") ;
        }else{
            $("a[href='" + url + "']").addClass("current") ;
        }

    }


    var center_order_action = function(){
        //全选的实现
        $(".check-all").click(function(){
            $(".ids").prop("checked", this.checked);
        });
        $(".ids").click(function(){
            var option = $(".ids");
            option.each(function(i){
                if(!this.checked){
                    $(".check-all").prop("checked", false);
                    return false;
                }else{
                    $(".check-all").prop("checked", true);
                }
            });
        });


        //取消订单
        $(".cancle_order").click(function(){
            var url = $(this).data('url');
            var id  = $(this).data('id');
            T.restPost(url,{id:id},function(success){
                main.modalAlert(success.msg);
                main.redirect('');
            },function(error){
                main.modalAlert(error.msg,'danger');
            });
        });

        //订单管理  删除订单 提交按钮方式
        $(".delorder").click(function(){
            var url = $("form[name=delform]").attr('action');
            var param = $("form[name=delform]").serialize();
            T.restPost(url,param,function(success){
                main.modalAlert(success.msg);
                main.redirect('');
            },function(error){
                main.modalAlert(error.msg,'danger');
            });
        })


        //删除一个订单
        $(".del_one_order").click(function(){
            var url = $(this).data('url');
            var param = {tag:$(this).data('tag')}
            T.restPost(url,param,function(success){
                main.modalAlert(success.msg);
                main.redirect('');
            },function(error){
                main.modalAlert(error.msg,'danger');
            });
        })

    }

    //获取密码安全等级
    var mima_leve = function(){
        T.restPost('/Center/ajaxMimaLeve',{},function(success){
            var data = success.data;
            if(data.leve == 0){
                $("#leve_tiao").addClass('col2');
                $("#leve_str").html('低');
            }else if(data.leve == 1){
                $("#leve_tiao").addClass('col23');
                $("#leve_str").html('中');
            }else if(data.leve == 2){
                $("#leve_tiao").addClass('col24');
                $("#leve_str").html('高');
            }else if(data.leve == 3){
                $("#leve_tiao").addClass('col25');
                $("#leve_str").html('较高');
            }
        })
    }

    //图片动画
    var pic_flash = function(){
        $(".pic_flash li").mouseenter(function(e){
            $(this).find("p").first().animate({
                top:'-15px',height:'190px'
            },'normal');
        });
        $(".pic_flash li").mouseleave(function(e){
            $(this).find("p").first().animate({
                top:'0px',height:'190px'
            },'normal');
        });
    }


    //确认收获
    var completegood = function(id,url){
        T.restPost(url,{id:id},function(success){
            main.modalAlert(success.data);
            main.redirect();
        },function(error){
            main.modalAlert(error.data,'danger');
        })
    }

    $(".completegood").click(function(){
        var url = $(this).data('url');
        var id  = $(this).data('id');
        completegood(id,url);
    });

    /**
     * 公共的  给出参数和url 去请求处理
     * @param param
     * @param url
     */
    var getCommonAjax = function(param,url){
        T.restPost(url,param,function(success){
            main.modalAlert(success.msg);
            main.redirect();
        },function(error){
            main.modalAlert(error.msg,'danger');
        });
    }


    //换货
    $(document).delegate("#sub_change",'click',function(){
        var url    = $("#changegood").attr('action');
        var param  = $("#changegood").serialize();
        getCommonAjax(param,url);
    });
    //退货
    $(document).delegate('#sub_backgood','click',function(){
        var url    = $("#form_backgood").attr('action');
        var param  = $("#form_backgood").serialize();
        getCommonAjax(param,url);
    });
    //确认换货  提交快递单号
    $("#sub_changekuaidi").click(function(){
        var url   =  $("#form_changekuaidi").attr('action');
        var param =  $("#form_changekuaidi").serialize();
        getCommonAjax(param,url);
    });

    module.exports = {
        center_init : center_init,
        center_order_action : center_order_action,
        mima_leve : mima_leve,
        pic_flash : pic_flash
    }
});