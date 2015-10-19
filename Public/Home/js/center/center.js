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

        //订单菜单
        $(".order_namelist span").eq(0).addClass("active");
        $(".order_namelist span").mouseenter(function(){
            $(this).addClass("active").siblings().removeClass("active")
        });



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


    }


    module.exports = {
        center_init : center_init
    }
});