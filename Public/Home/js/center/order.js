/**
 * Created by kevin on 15-10-19.
 */
define(function(require, exports, module){
    var T    = require('T');
    var main = require('main');
    main.modalEvent();


    //我的主页所显示订单
    var order_index = function(){
        //签到
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




    module.exports = {
        order_index   : order_index
    }
});