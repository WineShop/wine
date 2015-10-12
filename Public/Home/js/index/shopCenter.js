
function ztlistMoveleft(obj){
    obj.find(".ztlist_style_item_ww").animate({"margin-left":"0px"},600,function(){
        $(this).find(".ztlist_style_item_w1").last().prependTo($(this));
        $(this).css("margin-left","-988px");
        obj.find(".index_cd_leftarrow_mask").hide();
    });
}
function ztlistMoveright(obj){
    obj.find(".ztlist_style_item_ww").animate({"margin-left":"-1976px"},600,function(){
        $(this).find(".ztlist_style_item_w1").first().appendTo($(this));
        $(this).css("margin-left","-988px");
        obj.find(".index_cd_rightarrow_mask").hide();
    });

}
function setztlistCur(obj){
    obj.next().find("span").removeClass("current");
    obj.next().find("span").eq(parseInt(index_cd_cur)).addClass("current");
}


$(function(){

    function init(obj,index_cd_move){
        index_cd_move = setInterval(function(){ztlistMoveright(obj);},4000);
        obj.find(".index_cd_leftarrow").click(function(){
            $(this).find(".index_cd_leftarrow_mask").show();
            ztlistMoveleft(obj);
        });
        obj.find(".index_cd_rightarrow").click(function(){
            $(this).find(".index_cd_rightarrow_mask").show();
            ztlistMoveright(obj);
        });
        obj.find(".index_cd_leftarrow").mouseenter(function(){
            clearInterval(index_cd_move);
        });
        obj.find(".index_cd_leftarrow").mouseleave(function(){
            index_cd_move = setInterval(function(){ztlistMoveright(obj);},4000);
        });
        obj.find(".index_cd_rightarrow").mouseenter(function(){
            clearInterval(index_cd_move);
        });
        obj.find(".index_cd_rightarrow").mouseleave(function(){
            index_cd_move = setInterval(function(){ztlistMoveright(obj);},4000);
        });
        obj.find(".ztlist_style_item_w").mouseenter(function(){
            clearInterval(index_cd_move);
        });
        obj.find(".ztlist_style_item_w").mouseleave(function(){
            index_cd_move = setInterval(function(){ztlistMoveright(obj);},4000);
        });
    }


    var num = 1;
    $('.ztlist_style').each(function(){
        var leng = $(this).find('.ztlist_style_item_w1').length;
        if(leng > 1)
        {
            $(this).find('.ztlist_style_item_ww').css({'margin-left':'-988px'});
            var time = 'time'+num;
            init($(this),time);
        }
        num = num +1;
    })



    //首页图片动画
    $(".ztlist_style_item_list").mouseenter(function(e){
        $(this).find(".cc1").slideUp('normal');
    });
    $(".ztlist_style_item_list").mouseleave(function(e){
        $(this).find(".cc1").slideDown('normal');
    });


    $(".ztlist_style_item_w1").each(function(){
        $(this).find('.ztlist_style_item').eq(2).addClass('mr0');   //第3个
        $(this).find('.ztlist_style_item').eq(5).addClass('mr0');   //第6个
        $(this).find('.ztlist_style_item').eq(3).addClass('mb0');   //第4个
        $(this).find('.ztlist_style_item').eq(4).addClass('mb0');   //第5个
        $(this).find('.ztlist_style_item').eq(5).addClass('mb0');   //第6个
    });

});	





