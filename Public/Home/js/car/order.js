/**
 * Created by kevin on 15-10-19.
 */
define(function(require, exports, module){
    T    = require('T');
    main = require('main');
    main.modalEvent();

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


    //验证优惠券是否可用
    var checkcode = function(){
        var str = $("input#code").val();
        if(str!==""){
            T.restPost('/User/checkcode',{couponid:str},function(success){
                    $("span.tips").html(success.msg);

            },function(error){
                $("span.tips").empty();
                $("#code").val('');
                $("span.tips").html(error.msg);
            });
        }//if结束
    }

    //提交订单
    var makeorder = function(){
        //判断默认地址是否选中,获取地址id
        if ($("#default").checked==true) {
            var def = $("#default").val();
            $("#senderid").val(def);
            document.myform.submit();
        }
        var val = $('input:radio[name="sender"]:checked').val();
        //判断新地址是否选中,获取地址id
        if(val==null){
            main.modalAlert("请选择一个地址!",'danger');
            return false;
        }else{
            $("#senderid").val(val);
            document.myform.submit();
        }
    }


    $(".saveAddress").click(function(){
        saveAddress();
    })

    $(".makeorder").click(function(){
        makeorder();
    })

    $(".checkcode").blur(function(){
        checkcode();
    })


    $(document).ready(function(){
        $("#show").click(function(){
            $("#formsender").toggle();
        });
    });

    module.exports = {

    }
})