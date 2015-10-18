/**
 * Created by kevin on 15-10-18.
 */
define(function(require, exports, module){
    var T    = require('T');
    var main = require('main');

    //添加  减少  和删除
    var shopCarOpt = function(obj){

        var $obj = $(obj);
        var rel  = $obj.attr('rel');
        var url  = $obj.data('url');

        //增加数据
        if(rel=="jia"){
            var num   = $obj.next().val();
            var gid   = $obj.next().attr("id");
            var a     =  parseInt(num)+1;
            $obj.next().val(a);
        }

        //减少数据
        if(rel=="jian"){
            var num=$obj.prev().val();
            var gid=$obj.prev().attr("id")

            //如果文本框的值大于0才执行减去方法
            if(num >0){
                //并且当文本框的值为1的时候，减去后文本框直接清空值，不显示0
                if(num==1)
                {
                    main.modalAlert("对不起，数量最少为1",'danger');
                    return true;
                }else{    //否则就执行减减方法
                    var a = parseInt(num)-1;
                    $obj.prev().val(a);
                }
            }
        }

        //删除数据
        var html="<div class='shopcart_main_none'><div class='shopcart_main_none_img'></div><div class='shopcart_main_none_main'><p>你的购物车还是空的哦赶紧行动吧!</p><a  href='/'>马上购物</a></div>";
        if(rel=="del") {
            var gid = $obj.data('id');
        }


        T.restPost(url,{sort:gid},function(success){
            var data = success.data;
            $("span#count").text(data.count);
            $("span#total").text(data.price);
            $("span#sum").text(data.sum);
            $("em.price").text(data.price);
            var a = data.sum;
            if(a=="0"){ $(".text").remove();$("#form").html(html);}
            if(rel == 'del'){  $obj.parents("tr").empty(); }
        },function(error){

        });
    }

    var collect = function(uexist,obj){
        if(uexist){
            var favorid = $(obj).data('id');
            T.restPost('/User/favor',{id:favorid},function(success){
                main.modalAlert(success.msg);
            },function(error){

            });
        }else{
            $("#head_login").click();
        }
    }


    var showOrder = function(uexist){
        if(uexist){
           $("#form").submit();
        }else{
            $("#head_login").click();
        }
    }



    $(".shopCarOpt").click(function(){
        shopCarOpt(this);
    })



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



    module.exports = {
        collect   : collect,
        showOrder : showOrder
    }
});