$(function () {


    // DETECT DEVICE ========================== //

    detectDevice = function () {

        switch ($('.js-detect').css('opacity')) {
            case '0.1':
                return 'mobile';
            case '0.2':
                return 'tablet';
            case '0.3':
                return 'desktop';
            case '0.4':
                return 'desktop-hd';
        }
    }
    // ======================================= //


    menuMobile = function () {
        var device = detectDevice();
        var menuMobile = (device === 'mobile' || device === 'tablet') ? true : false;
        return menuMobile;
    }

    // ======================================= //
    isMobile = function () {
        return $('.js-detect').css('opacity') < 0.3;
    }
    // ======================================= //

    $.fn.serializeObject = function(){
        var obj = {};
        var assignByPath = function (obj,path,value){
            if (path.length == 1) {
                if(path[0])
                    obj[path[0].replace(':','.')] = value;
                else obj[value]=value;
                return obj;
            } else if (obj[path[0]] === undefined) {
                obj[path[0].replace(':','.')] = {};
            }
            return assignByPath(obj[path.shift()],path,value);
        }

        $.each( this.serializeArray(), function(i,o){
            var n = o.name,
                v = o.value;
            path = n.replace('.',':').replace(/\]\[/g,'.').replace(/\[/g,'.').replace(']','').split('.');

            assignByPath(obj,path,v);
        });

        return obj;
    };

});