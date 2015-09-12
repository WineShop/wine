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
        jquery      : 'Home/js/jquery.min.js',
        TSB         : 'libs/main.js',
        T           : 'libs/TSB.js',
        Public      : 'Home/js/public.js',

        //首页
        Menudown    : 'static/js/menudown.js',
        Index       : 'Home/js/index/index.js'



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
