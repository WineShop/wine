/**
 * Created by kevin on 15-10-19.
 */
define(function(require, exports, module){
    var T    = require('T');
    var main = require('main');
    main.modalEvent();


    //我的主页所显示订单
    var order_index = function(){
        $(".delorder").click(function(){
            document.delform.submit();
        });

        $(".pf_qiandao").click(function(){
            T.restPost('/Center/enter',{},function(success){
                main.modalAlert(success.msg);
                var score = $("span#score").html();
                var new_socre = parseInt(score) + 10;
                $("span#score").html(new_socre);
            },function(error){
                main.modalAlert(error.msg,'danger');
            });
        });

    }

    //订单管理
    var order_menage = function(){
        $(".delorder").click(function(){
            document.delform.submit();
        })

    }


    module.exports = {
        order_index   : order_index,
        order_menage  : order_menage
    }
});