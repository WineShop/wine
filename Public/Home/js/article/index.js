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
                str += "<li><a class='picture' href='/Article/detail/id/"+data[i]['id']+"'>\
                        <img src='"+data[i]['picUrl']+"'/> \
                    </a>\
                    <a class='title' href='/Article/detail/id/"+data[i]['id']+"'>"+data[i]['title']+"</a>\
                    <span>￥"+data[i]['price']+"</span>\
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
                str += "<li><a class='picture' href='/Article/detail/id/"+data[i]['id']+"'>\
                        <img src='"+data[i]['picUrl']+"'/> \
                    </a>\
                    <a class='title' href='/Article/detail/id/"+data[i]['id']+"'>"+data[i]['title']+"</a>\
                    <span>￥"+data[i]['price']+"</span>\
                    </li>";
            }
            $("#hot_view").html(str);
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
})