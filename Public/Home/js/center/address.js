/**
 * Created by kevin on 15-10-19.
 */
define(function(require, exports, module){
    var T    = require('T');
    var main = require('main');
    main.modalEvent();

    //设置地址为默认
   var set_address = function(){
       var val=$('input:radio[name="id"]:checked').val();
       //判断新地址是否选中,获取地址id
       if(val==null){
           main.modalAlert("请选择一个地址!",'danger');return false;
       }

       T.restPost('/Center/shezhi',{id:val},function(success){
           main.modalAlert(success.msg);
           main.redirect('');
       },function(error){
           main.modalAlert(error.msg,'danger');
       });

   }
    $(".setAddress").click(function(){
        set_address();
    });



    //删除地址
    var del_address = function(){
        var val=$('input:radio[name="id"]:checked').val();
        //判断新地址是否选中,获取地址id
        if(val==null){
            main.modalAlert("请选择一个地址!",'danger');return false;
        }

        T.restPost('/Center/deleteAddress',{id:val},function(success){
            main.modalAlert(success.msg);
            main.redirect('');
        },function(error){
            main.modalAlert(error.msg,'danger');
        });

    }
    $(".delAddress").click(function(){
        del_address();
    });


    module.exports = {

    }
});