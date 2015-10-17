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


        //加入购物车
        this.detail_cart = function(href){
            var url     = "/Shopcart/addItem";        //地址
            var gnum    = $(".goodnum").val();       //数量
            var gprice  = $(".price").eq(0).text(); //价格
            var src     = $(".img img").attr("src"); //图片
            var image   = '<img src="'+src+'" width="40" height="40">';
            var title   = $("#tit").text();         //名称
            var parameters=$(".weight .cur").text();//参数

            if($(".weight").length>0){
                var s       = $(".weight .cur").index()+1;
                var string  = String(gid)+String(s);
            }else{
                var string=String(gid);
            }
            var data    = {id:gid,num:gnum,price:gprice,i:parameters,sort:string };
            T.restPost(url,data,function(success){
                var data = success.data
                var html='<li><dl><dt class="mini-img">'+href+image+'</a><dd><span class="mini_title">'+href+title+'</a></span><span class="mini-cart-count red"> ￥'
                    +gprice+'<em class="'+string+'">*'+gnum+'</em></span>'+'</dd><dd><span class="mini-cart-info">'+parameters+'</span><span class="mini-cart-del"><a name="'+string+'" rel="del"  onclick="delcommon(event);return false;">删除</a></span></dd>'+'</dl></li>';//创建节点〉

                if(data.exsit=="1"){
                    $("em."+string).text("*"+data.num);
                    //后台返回数据，判断购物车中是否已存在，存在，不追加节点
                }else{
                    //后台返回数据，判断购物车中是否已存在，不存在，追加节点
                    $("p.sc_goods_none").remove();//移除节点
                    $("ul.sc_goods_ul").append(html);//追加节点
                }
                if(data.num=="0"){ //判断数量是否为0，为0则隐藏底部查看工具
                    $("div.sc_goods_foot").hide();
                }else{
                    $("div.sc_goods_foot").show();
                }
                var car_str = '('+data.sum+'件 | <font color="#FDF657">'+data.fee+'</font>元)';
                $('#shopping_cart').html(car_str);
                $('#totalnum').text(data.sum);
                $('#fee').text(data.fee);
                $('#showIncludeCart').fadeIn();
            },function(error){

            });
        }



        //计算套餐价格
        this.setprice = function(num,groupprice){
            price   = null;
            var str = groupprice;
            var value=str.split("、");
            var i   = num-1;
            $("#resetprice").text(value[i]);
            $("#inputprice").val(value[i])
            $(".weight a").eq(i).addClass("cur").siblings().removeClass("cur");;
            var para=$(".weight a").eq(i).text();
            $("#inputparameters").val(para);

        }

        //提交问答
        this.sub_question = function(){
            var str=$("#question").val();
            T.restPost('/service/addmessage',{content:str,goodid:gid},function(success){
                    var data = success.data;
                    var html='<div class="qa_cont clearfix"><div class="question"><span class="time">'+data.time+'</span><span><img src="/Public/Home/images/question.png">问：'+str+'？</span></div>            </div>';
                    $("#header4").prepend(html);
                    $(".btn_submit").text("提交成功");
            },function(error){

            });
        }


        //购买组合
        this.buyFive =  function(){
            if(uexist){
                document.fiveform.submit();
            } else{
                showBg();
            }
        }

    }



    //显示灰色 jQuery 遮罩层
    var showBg = function () {
        $("#dialog").fadeIn();
        var bh = $("body").height();
        var bw = $("body").width();
        $("#fullbg").css({
            height:bh,
            width:bw,
            display:"block"
        });

    }


    //获取最左侧七个
    T.restPost('/Article/ajaxLeftShop',{},function(success){
        var data = success.data;
        var str = '';
        for(var i in data)
        {
              str +='<dd>\
                        <a class="details_right_img" href="/Article/detail?id='+data[i]['id']+'" title="'+data[i]['title']+'"><img src="'+data[i]['pic_path']+'"  alt="'+data[i]['title']+'" style="display: inline-block;"></a>\
                        <a href="Article/detail?id='+data[i]['id']+'" class="details_right_title" title="'+data[i]['title']+'">'+data[i]['title']+'</a>\
                        <span class="fwb mcm_title_price">￥<span class="red">'+data[i]['price']+'</span></span>\
                    </dd>'
        }
        $("#left_shop").after(str);
    },function(error){
        var str ='<dd>'+error.msg+'</dd>';
        $("#left_shop").after(str);
    })


    var instance = function(gid,uexist){
        var obj = new detail_init(gid,uexist);
        return obj;
    }
    module.exports = instance;
});


