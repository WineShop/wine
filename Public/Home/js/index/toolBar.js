/**
 * Created by kevin on 15-9-13.
 */
define(function (require, exports, module) {
    var T   = require('T');
    var main = require('main');

    main.modalEvent();

    //头部topbar会员中心显示与隐藏
    var Account= document.getElementById('account');
    var Downmenu= document.getElementById('dbox');
    var timer = null;//定义定时器变量
    //鼠标移入div1或div2都把定时器关闭了，不让他消失
    Account.onmouseover = Downmenu.onmouseover = function ()
    {
        //改变箭头方向
        $("i#icount").attr("class","fa fa-angle-up");

        //改变背景颜色
        Account.style.backgroundColor = '#fff';
        //显示下拉框
        $("#dbox").show();
        //关闭定时执行
        clearTimeout(timer);
    }

    //鼠标移出div1或div2都重新开定时器，让他延时消失
    Account.onmouseout = Downmenu.onmouseout = function ()
    {
        $("i#icount").attr("class","fa fa-angle-down");
        Account.style.backgroundColor = '#F5F5F5';
        //开定时器，每隔200微妙下拉框消失
        timer = setTimeout(function () {
            $("#dbox").hide(); }, 200);
    }
    //头部topbar会员收藏显示与隐藏
    userfavor;favormenu;time;
    var userfavor= document.getElementById('userfavor');
    var favormenu= document.getElementById('favormenu');
    var time = null;//定义定时器变量
    //鼠标移入div1或div2都把定时器关闭了，不让他消失
    userfavor.onmouseover = favormenu.onmouseover = function ()
    {
        //改变箭头方向

        //改变背景颜色
        userfavor.style.backgroundColor = '#fff';
        //显示下拉框
        $("#favormenu").show();
        //关闭定时执行
        clearTimeout(time);
    }

    //鼠标移出div1或div2都重新开定时器，让他延时消失
    userfavor.onmouseout = favormenu.onmouseout = function ()
    {
        userfavor.style.backgroundColor = '#F5F5F5';
        //开定时器，每隔200微妙下拉框消失
        time = setTimeout(function () {
            $("#favormenu").hide(); }, 10);
    }



    //卖家中心显隐
   // usersell;sellmenu;clock;
    var usersell= document.getElementById('sell');
    var sellmenu= document.getElementById('sellmenu');
    var clock = null;//定义定时器变量

   /* $('#sell').mouseover(function(){
        $(this).css({'background-color':"#fff"});
        $("#sellmenu").show();
    })
*/


    //鼠标移入div1或div2都把定时器关闭了，不让他消失
    usersell.onmouseover = sellmenu.onmouseover = function ()
    {
        //改变箭头方向

        //改变背景颜色
        usersell.style.backgroundColor = '#fff';
        //显示下拉框
        $("#sellmenu").show();
        //关闭定时执行
        clearTimeout(clock);
    }

    //鼠标移出div1或div2都重新开定时器，让他延时消失
    usersell.onmouseout = sellmenu.onmouseout = function ()
    {
        usersell.style.backgroundColor = '#F5F5F5';
        //开定时器，每隔200微妙下拉框消失
        clock = setTimeout(function () {
            $("#sellmenu").hide(); }, 20);
    }


    //退出
    $("#logout").click(function(){
        T.restPost('/wine/out',{},function(success){
              main.modalAlert(success.msg);
              main.redirect();
        })
    });

})
