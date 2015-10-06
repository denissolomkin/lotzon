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

    getCurrency = function (value, part) {
        function round(a,b) {
            b=b||0;
            return parseFloat(a.toFixed(b));
        }

        var format=null;

        if ($.inArray(part, ["iso","one","few","many"])>=0){
            var format=part;
            part=null;
        }

        if(!value || value=='' || value=='undefined')
            value=null;


        switch (value){
            case null:
                return Player.currency['iso'];
                break;
            case 'coefficient':
            case 'rate':
                return (Player.currency[value]?Player.currency[value]:1);
                break;
            case 'iso':
            case 'one':
            case 'few':
            case 'many':
                return (Player.currency[value]?Player.currency[value]:Player.currency['iso']);
                break;
            default:
                value = round((parseFloat(value)*Player.currency['coefficient']),2);
                if((format=='many' || (!format && value>=5)) && Player.currency['many']){
                    return (!part || part==1 ? value : '') + (part==1 ? null : (!part ? ' ' : '') + Player.currency['many']);
                } else if((format=='few' || (!format && (value>1 || value<1))) && Player.currency['few']){
                    return (!part || part==1 ? value : '') + (part==1 ? null : (!part ? ' ' : '') + Player.currency['few']);
                } else if((format=='one' || (!format && value == 1)) && Player.currency['one']){
                    return (!part || part==1 ? value : '') + (part==1 ? null : (!part ? ' ' : '') + Player.currency['one']);
                } else {
                    return (!part || part==1 ? value : '') + (part==1 ? null : (!part ? ' ' : '') + Player.currency['iso']);
                }
                break;
        }
    }

    isMobile = function () {
        return $('.js-detect').css('opacity') < 0.3;
    }
    // ======================================= //

    updatePoints = function (points) {
        playerPoints = parseInt(points) || playerPoints;
        points=playerPoints.toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
        $('.plPointHolder').text(points);
    }

    updateMoney = function (money) {
        // money = money || playerMoney;
        playerMoney = parseFloat(money).toFixed(2) || playerMoney;
        money=parseFloat(playerMoney).toFixed(2).toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
        $('.plMoneyHolder').text(money.replace('.00',''));
    }


    isNumeric = function (mixed_var) {
        //   example 1: isNumeric(186.31); returns 1: true
        //   example 2: isNumeric('Kevin van Zonneveld'); returns 2: false
        //   example 3: isNumeric(' +186.31e2'); returns 3: true
        //   example 4: isNumeric(''); returns 4: false
        //   example 5: isNumeric([]); returns 5: false
        //   example 6: isNumeric('1 '); returns 6: false
        var whitespace =
            " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
        return (typeof mixed_var === 'number' || (typeof mixed_var === 'string' && whitespace.indexOf(mixed_var.slice(-1)) === -
            1)) && mixed_var !== '' && !isNaN(mixed_var);
    }


    playAudio = function (key) {
        if (!$.cookie("audio-off")) {
            if ($.isArray(key)){
                if(appAudio && appAudio[key[0]] && (file = appAudio[key[0]][key[1]]))
                    $('<audio src=""></audio>').attr('src', 'tpl/audio/' + file).trigger("play");
            } else if (key) {
                $('<audio src=""></audio>').attr('src', 'tpl/audio/' + key).trigger("play");
            }
        }
    }

    Object.size = function(obj) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) size++;
        }
        return size;
    };

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