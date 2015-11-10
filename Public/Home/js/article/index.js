/**
 * Created by kevin on 15-11-6.
 */
define(function (require, exports, module) {
    var T    = require('T');
    var main = require('main');
    main.modalEvent();

    //销售排行版
    var hot_sale = function(){
        T.restPost('/Article/ajaxHotSale',{},function(success){
            var data = success.data;
            var str = '';
            for(var i in data)
            {
                str += "<li><div class='li_left'><a title='"+data[i]['title']+"' href='/Article/detail/id/"+data[i]['id']+"'>\
                        <img src='"+data[i]['picUrl']+"'/> \
                    </a></div> \
                    <div class='li_right'><p class='tit'><a  href='/Article/detail/id/"+data[i]['id']+"'>"+data[i]['title']+"</a></p>\
                        <p class='red'>￥"+data[i]['price']+"</p></div> \
                    </li>";
            }

            $("#hot_sale").html(str);
        },function(error){

        })
    }

    hot_sale();


    //热门排行
    var hot_view = function(){
        T.restPost('/Article/ajaxHotView',{},function(success){
            var data = success.data;
            var str = '';
            for(var i in data)
            {
                str += "<li class='clearfix drink-me'><h3><a href='/Article/detail/id/"+data[i]['id']+"'>"+data[i]['title']+"</a></h3>\
                    <a href='/Article/detail/id/"+data[i]['id']+"'><img src='"+data[i]['picUrl']+"' width='90' height='90'/></a>\
                    <p><a href='/Article/detail/id/"+data[i]['id']+"'>"+data[i]['description']+"</a></p>\
                    <span class='tweet-detail-func cBlue'>￥"+data[i]['price']+"</span>\
                    </li>";
            }
            $("#hot_view").html(str);
            sidePic();
        },function(error){

        })
    }

    hot_view();


    //图片动画
    $("li.face").mouseenter(function(e){
        $(this).find("a.list-img").animate({
            top:'-15px',height:'176px'
        },'normal');
        $(this).find("div").animate({
            top:'-15px'
        },'normal');
    });
    $("li.face").mouseleave(function(e){
        $(this).find("a.list-img").animate({
            top:'0px',height:'176px'
        },'normal');
        $(this).find("div").animate({
            top:'0px'
        },'normal');
    });



    //左侧图片动画
    var sidePic = function(){
        $(document).ready(function(){
            $(".list-tweet >li:first").removeClass("drink-me");
            $(".list-tweet >li:first").addClass("eat-me");

            $("#layout-t span:first").addClass("current");
            $("#layout-t .tab-bd-con:gt(0)").hide();
            $("#layout-t span").mouseover(function(){//mouseover 改为 click 将变成点击后才显示，mouseover是滑过就显示
                $(this).addClass("current").siblings("span").removeClass("current");
                $("#layout-t .tab-bd-con:eq("+$(this).index()+")").show().siblings(".tab-bd-con").hide().addClass("current");
            });

            $(".list-tweet >li").mouseover(function(){
                $(this).removeClass("drink-me");
                $(this).addClass("eat-me");
                $(".list-tweet >li:first").removeClass("eat-me");
                $(".list-tweet >li:first").addClass("drink-me");
            });
            $(".list-tweet >li").mouseout(function(){
                $(this).removeClass("eat-me");
                $(this).addClass("drink-me");
                $(".list-tweet >li:first").addClass("eat-me");
                $(".list-tweet >li:first").removeClass("drink-me");
            });
            $(".list-tweet >li").mouseover(function(){
                $(this).removeClass("drink-me");
                $(this).addClass("eat-me");
            });
        });
    }


})