/**
 * Created by kevin on 15-10-19.
 */
define(function(require, exports, module){
    T    = require('T');
    main = require('main');
    main.modalEvent();

    var pay_init = function(){
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
    }

    module.exports = {
        pay_init : pay_init
    }
});