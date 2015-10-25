/**
 * Created by kevin on 15-10-26.
 */
define(function(require, exports, module){
    var T    = require('T');
    var main = require('main');
    main.modalEvent();

    var coupon = function(obj){
        var id  = $(obj).data('id');
        var url ='/User/getcoupon';
        T.restPost(url,{couponid:id},function(success){
              main.modalAlert(success.msg);
              main.redirect('');
        },function(error){
            main.modalAlert(error.msg,'danger');
        });
    }

    $(".get_coupon").click(function(){
        coupon(this);
    });

    module.exports = {
        coupon   : coupon
    }

})