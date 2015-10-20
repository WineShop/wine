/**
 * Created by kevin on 15-10-21.
 */

define(function(require, exports, module){
    var T    = require('T');
    var main = require('main');
    main.modalEvent();


    //购物车显示与隐藏
    var showcart = function(){
       var Shopcart  = document.getElementById('shopping_cart');
       var Goodsmenu = document.getElementById('goods');
       var timerr = null;//定义定时器变量
       //鼠标移入div1或div2都把定时器关闭了，不让他消失
       Shopcart.onmouseover =Goodsmenu.onmouseover = function ()
       {
           Goodsmenu.style.display = 'block';
           clearTimeout(timerr);
       }
       //鼠标移出div1或div2都重新开定时器，让他延时消失
       Shopcart.onmouseout =Goodsmenu.onmouseout = function ()
       {
           //开定时器
           timerr= setTimeout(function () {
               Goodsmenu.style.display = 'none'; }, 10);
       }
   }


    showcart();



    var shopcart = function(obj){
        var $obj = $(obj);
        var str  = $obj.parent().prev().html();
        var rel  = $obj.attr('rel');
        if(rel == "del")
        {
            var id = $obj.attr('name');
            T.restPost('/Shopcart/delItem',{sort:id},function(success){
                var data = success.data;
                $obj.closest("li").remove();
                if(data.sum == "0"){  //判断购物车数量是否为0，为0则隐藏底部查看工具
                    $("div.sc_goods_foot").hide();
                    var html='<p class="sc_goods_none" >您的购物车还是空的，赶紧行动吧！</p>';
                    $("ul.sc_goods_ul").html(html)
                }else{
                    $("div.sc_goods_foot").show();
                }
                var str = "(" + data.sum + "件|"+data.price+"元)";
                $("#shopping_cart").html(str);
//                main.modalAlert(data.msg);
            },function(error){

            });
        }
    }

    $(".nav_cat_del").click(function(){
        shopcart(this);
    })



    var choose_nav = function(){
        $(function(){
            //频道页，内容页商品分类子类的显示与隐藏
            $('.all-goods .item').hover(function(){
                $(this).addClass('active').find('s').hide();
                $(this).find('.product-wrap').show();
            },function(){
                $(this).removeClass('active').find('s').show();
                $(this).find('.product-wrap').hide();
            });

            //tab菜单切换
            $(".note ul li").mouseenter(function(){
                $(this).addClass("selected").siblings().removeClass("selected")
            });

            $(".note ul li").mouseenter(function(){
                var index=$(".note ul li").index(this);
                $("#msg>div").eq(index).css("display", "block").siblings().css("display", "none");
            });



            //分类菜单的显示隐藏
            var oDiv4 = document.getElementById('all-goods');
            //鼠标移入div1或div2都把定时器关闭了，不让他消失
            oDiv4.onmouseover = function ()
            {
                $("i#iarrow").attr("class","glyphicon glyphicon-chevron-up");
                oDiv4.style.display = 'block';
            }



            $("h3.tab a").mouseenter(function(){
                $(this).addClass("selected").siblings().removeClass("selected")
            });
            $("h3.tab a").mouseenter(function(){
                var index=$("h3.tab a").index(this);
                $(".tab_view>div").eq(index).css("display", "block").siblings().css("display", "none");
            })



            $("a.search_btn").click(function(){
                var words=$("input.search_input").val();
                if(words){
                    document.Searchform.submit();
                }else{
                    main.modalAlert("搜索内容不能为空",'danger');
                }
            });


        });
    }

    choose_nav();


    module.exports = {

    }
});