define(function (require, exports, module) {
    var T    = require('T');
    var main = require('main');

    var detail_init = function(gid,uexist){

        this.dt_init = function(){
            //首页默认被选中
            $(".menu li").eq(0).addClass('active');

            //菜单移动时显示和收缩
            $("#all-goods").hide();
            $(".goods").mouseover(function(){
                $("#all-goods").show();
            });

            $(".goods").mouseout(function(){
                $("#all-goods").hide();
            });

            //添加和减少
            $('.jia').click(function(){
                var num = $(".goodnum").val();
                num++;
                $(".goodnum").val(num);
                $("#oneA").removeClass("important");
            });

            $('.jian').click(function(){
                var num = $(".goodnum").val();
                if(num>1)
                {
                    num--;
                    $(".goodnum").val(num);
                }else{
                    $("#oneA").addClass("important");
                    main.modalAlert('对不起，数量最少为1','danger');
                }
            });
        }

        //立即购买
        this.detail_order = function(){

            if(uexist){
                var gprice     = $("em.price").eq(0).text();//价格
                var parameters = $("a.cur").text();//参数
                if($(".weight").length>0){
                    var s      = $(".weight .cur").index()+1;
                    var string = String(gid)+String(s);
                }else{
                    var string = String(gid);
                }
                $("#inputprice").val(gprice);
                $("#inputsort").val(string);
                $("#inputparameters").val(parameters);
                document.orderform.submit();
            }else{
                showBg();
            }
        }

        //收藏
        this.detail_favor = function(){
            if(uexist){
                T.restPost('/User/favor',{id:gid},function(success){
                    main.modalAlert(success.msg);
                },function(error){
                    main.modalAlert(error.msg,'danger');
                })
            }else{
                showBg();
            }
        }

    }

    var showBg = function(){
        $("#dialog").fadeIn();
        var bh = $("body").height();
        var bw = $("body").width();
        $("#fullbg").css({
            height:bh,
            width:bw,
            display:"block"
        });
    }


    var instance = function(gid,uexist){
        var obj = new detail_init(gid,uexist);
        return obj;
    }
    module.exports = instance;
});


