/**
 * Created by kevin on 15-9-11.
 */
define(function (require, exports, module) {
    var T   = require('T');
    var main = require('main');


    //banner切换
    var banner = function(){
        var PicTotal = 5;
        var CurrentIndex;
        var ToDisplayPicNumber = 0;
        $("div.LunBo div.LunBoNum span").click(DisplayPic);
        function DisplayPic() {
            // 测试是父亲的第几个儿子
            CurrentIndex = $(this).index();
            // 删除所有同级兄弟的类属性
            $(this).parent().children().removeClass("CurrentNum")
            // 为当前元素添加类
            $(this).addClass("CurrentNum");
            // 隐藏全部图片
            var Pic = $(this).parent().parent().children("ul");
            $(Pic).children().hide();
            // 显示指定图片
            $(Pic).children("li").eq(CurrentIndex).show();
        }

        function PicNumClick() {
            $("div.LunBo div.LunBoNum span").eq(ToDisplayPicNumber).trigger("click");
            ToDisplayPicNumber = (ToDisplayPicNumber + 1) % PicTotal;
            setTimeout(function(){
                PicNumClick();
            },3000);
        }
        setTimeout(function(){
            PicNumClick();
        },3000);
    }

    banner();




    $(document).ready(function () {
        //首页默认被选中
        $(".menu li").eq(0).addClass('active');

        //幻灯片首个li设置class为CurrentPic
        $(".LunBoslide>li").eq(0).attr("class","CurrentPic");

        //首页图片动画
        $("div.pic").mouseenter(function(e){
            $(this).find("a").animate({
                top:'-15px',height:'140px'
            },'normal');
            $(this).find("div").animate({
                 top:'-15px'
            },'normal');
        });
        $("div.pic").mouseleave(function(e){
            $(this).find("a").animate({
                top:'0px',height:'140px'
             },'normal');
            $(this).find("div").animate({
                top:'0px'
            },'normal');
        });
    })

/*
   //首页添加到购物车
   var AddProductToCart = function (i)
    { var a='{$url}';
        var goodid=i;
        var url='{:U("Shopcart/addItem")}';//地址
        $.ajax({
            type:'post', //传送的方式,get/post
            url:'{:U("Shopcart/addgood")}', //发送数据的地址
            data:{
                id:goodid,tag:1
            },
            dataType: "json",
            success:function(data)
            {

                var html='<li><img src="'+data.src+'">'+data.title+'<span class="red" style="text-align:left"> '
                    +data.price+'元</span>'+'<a name="'+goodid+'" rel="del" class="red" onclick="delcommon(event)">删除</a>'+'</li>';//创建节点
                if(data.exsit=="1"){
                    alert("购物车已存在该商品，数量+1");
                    //后台返回数据，判断购物车中是否已存在，存在，不追加节点

                }
                else{
                    alert(data.msg);//后台返回数据，判断购物车中是否已存在，不存在，追加节点
                    $("p.sc_goods_none").remove();//移除节点
                    $("ul.sc_goods_ul").append(html);//追加节点

                }
                if(data.num=="0"){ //判断数量是否为0，为0则隐藏底部查看工具
                    $("div.sc_goods_foot").hide();
                }
                else{$("div.sc_goods_foot").show();}
            },
            error:function (XMLHttpRequest, ajaxOptions, thrownError) {
                alert(XMLHttpRequest+thrownError);
            }
        })
    }*/




})
