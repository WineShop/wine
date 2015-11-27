/**
 * Created by kevin on 15-11-27.
 */
define(function(require, exports, module){
    var T    = require('T');
    var main = require('main');
    main.modalEvent();

    var getsort = function(picpath){
        $.fn.raty.defaults.path = picpath;
        $("#goodid").raty({
            number: 5,//多少个星星设置
            targetType: 'hint',//类型选择，number是数字值，hint，是设置的数组值
            path      : picpath,
            hints     : ['差','一般','较好','好','非常好'],
            cancelOff : 'cancel-off-big.png',
            cancelOn  : 'cancel-on-big.png',
            size      : 24,
            starHalf  : 'star-half-big.png',
            starOff   : 'star-off-big.png',
            starOn    : 'star-on-big.png',
            target    : '#goodid-hint',
            cancel    : false,
            targetKeep: true,
            targetText: '请选择评分',

            click: function(score, evt) {
                $("input[name='goodscore']").val(score);
            }
        });

        $("#delivery").raty({
            number: 5,//多少个星星设置
            targetType: 'hint',//类型选择，number是数字值，hint，是设置的数组值
            path      : picpath,
            hints     : ['差','一般','较好','好','非常好'],
            cancelOff : 'cancel-off-big.png',
            cancelOn  : 'cancel-on-big.png',
            size      : 24,
            starHalf  : 'star-half-big.png',
            starOff   : 'star-off-big.png',
            starOn    : 'star-on-big.png',
            target    : '#delivery-hint',
            cancel    : false,
            targetKeep: true,
            targetText: '请选择评分',

            click: function(score, evt) {
                $("input[name='deliveryscore']").val(score);
            }
        });

        $("#service").raty({
            number: 5,//多少个星星设置
            targetType: 'hint',//类型选择，number是数字值，hint，是设置的数组值
            path      : picpath,
            hints     : ['差','一般','较好','好','非常好'],
            cancelOff : 'cancel-off-big.png',
            cancelOn  : 'cancel-on-big.png',
            size      : 24,
            starHalf  : 'star-half-big.png',
            starOff   : 'star-off-big.png',
            starOn    : 'star-on-big.png',
            target    : '#service-hint',
            cancel    : false,
            targetKeep: true,
            targetText: '请选择评分',

            click: function(score, evt) {
                $("input[name='servicescore']").val(score);
            }
        });
    }


    $("#sub_comment").click(function(){
        var param = $("#comment_form").serialize();
        var url   = $("#comment_form").attr('action');
        T.restPost(url,param,function(success){
           main.modalAlert(success.msg);
           main.redirect('/Center/index');
        },function(error){
            main.modalAlert(error.msg,'danger');
        });
    })
    module.exports = {
        getsort  : getsort
    }
})