define(function(require, exports, module){
    var T   = require('T');
    var main = require('main');

    var detail_init = function(){
        $("#all-goods").hide();
        $(".goods").mouseover(function(){
            $("#all-goods").show();
        })

        $(".goods").mouseout(function(){
            $("#all-goods").hide();
        })
    }


    module.exports = {
        detail_init : detail_init
    };
});


