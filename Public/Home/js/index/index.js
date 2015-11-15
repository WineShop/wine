/**
 * Created by kevin on 15-9-11.
 */
define(function (require, exports, module) {
    var T   = require('T');
    var main = require('main');

    main.modalEvent();

    //显示菜单分类
    $("#all-goods").show();

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
        /*$("div.pic").mouseenter(function(e){
            $(this).find("a").animate({
                top:'-15px',height:'165px'
            },'normal');
            $(this).find("div").animate({
                 top:'-15px'
            },'normal');
        });
        $("div.pic").mouseleave(function(e){
            $(this).find("a").animate({
                top:'0px',height:'165px'
             },'normal');
            $(this).find("div").animate({
                top:'0px'
            },'normal');
        });*/
    })


    //获取活动和公告
    var getArticle = function(aurl,nurl){
        //活动
        T.restPost(aurl,{},function(success){
            if(success.code == 1000){
                var str = '';
                var data = success.data;
                for(var i in data){
                    str += '<li><a href="/Article/detail/id/'+data[i].id+'.html">'+data[i].title+'</a></li>';
                }
                $(".home_active").html(str);
            }
        },function(error){

        });

        //公告
        T.restPost(nurl,{},function(success){
            if(success.code == 1000){
                var str = '';
                var data = success.data;
                for(var i in data){
                    str += '<li><a href="/Article/detail/id/'+data[i].id+'.html">'+data[i].title+'</a></li>';
                }
                $(".home_notice").html(str);
            }
        },function(error){

        });

    }

    module.exports = {
        getArticle : getArticle
    };
})
