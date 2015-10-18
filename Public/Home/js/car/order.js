/**
 * Created by kevin on 15-10-19.
 */
define(function(require, exports, module){
    T    = require('T');
    main = require('main');

    var saveAddress = function(){
        //判断是否是默认地址
        if ($("#isdefault").checked==true) {
            var info="yes";
        }else {
            var info="no";
        }
        var p   = $("#address").val();
        var oid = $("#orderid").val();
        var real= $("#realname").val();
        var ph  = $("#phone").val();
        var b   = $("#youbian").val();
        var param = {posi:p,pho:ph,rel:real,id:oid,msg:info};
        T.restPost('/Shopcart/savemsg',param,function(success){
            var data = success.data;
            if (data.msg=="yes") {
                $("#f_default").remove();var str='<p id="f_default"><input type="radio" name="sender" value="'+data.addressid+'" id="default" checked="checked"/>&nbsp;&nbsp;收件人：'+data.realname+'&nbsp;&nbsp;&nbsp;联系电话：'+data.cellphone+'&nbsp;'+'&nbsp;&nbsp;&nbsp;收货地址：'+data.address+'</p>';
                $("#senderdetail").append(str);
            }else{
                var str='<p><input type="radio" id="new" name="sender" value="'+data.addressid+'" checked="checked"/>&nbsp;&nbsp;收件人：'+data.realname+'&nbsp;&nbsp;&nbsp;联系电话：'+data.cellphone+'&nbsp;'+'&nbsp;&nbsp;&nbsp;收货地址：'+data.address+'</p>';
                $("#senderdetail").append(str);
            }

            $("#formsender").toggle();
        },function(error){

        });
    }


    $(".saveAddress").click(function(){
        saveAddress();
    })
    module.exports = {

    }
})