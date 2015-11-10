/**
 * Created by Kevin on 15-1-22.
 */
var version = '20150508';

seajs.config({
    //基础路径
    base: '/Public/',
    // 别名配置
    alias: {

		//TSB common公共的js
        jquery      : 'static/jquery.min.js',
        main        : 'libs/main.js',
        T           : 'libs/TSB.js',
        Public      : 'Home/js/public.js',

        //首页
        Menudown    : 'static/js/menudown.js',
        Index       : 'Home/js/index/index.js',
        ToolBar     : 'Home/js/index/toolBar.js',
        Login       : 'Home/js/index/login.js',
        Menu        : 'Home/js/index/menu.js',

        //详细内容页面
        Detail      : 'Home/js/detail/detail.js',
        AtcIndex    : 'Home/js/article/index.js',

        //购物车
        Carlist     : 'Home/js/car/carlist.js',
        Order       : 'Home/js/car/order.js',
        Pay         : 'Home/js/car/pay.js',


        //用户中心
        Ucenter     : 'Home/js/center/center.js',
        Uorder      : 'Home/js/center/order.js',
        Coupon      : 'Home/js/center/coupon.js',
        Passwd      : 'Home/js/center/passwd.js',
        Address     : 'Home/js/center/address.js',

        //后台登陆
        AdminLogin  : 'Admin/js/login/login.js'



    },
    // 预加载项
    preload: [
        "jquery"
    ],
    // 调试模式
    //debug: true,
    // 映射配置
    map: [
        [/^(.*\.(?:css|js))(?:.*)$/i, '$1?version=' + version]
    ],
    // 文件编码
    charset: 'utf-8'
});
