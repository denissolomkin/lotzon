$(function () {

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

    jQuery.fn.getPath = function () {
        if (this.length != 1) throw 'Requires one element.';

        var path, node = this;
        while (node.length) {
            var realNode = node[0], name = realNode.localName;
            if (!name) break;
            name = name.toLowerCase();

            var parent = node.parent();

            var siblings = parent.children(name);
            if (siblings.length > 1) {
                name += ':eq(' + siblings.index(realNode) + ')';
            }

            path = name + (path ? '>' + path : '');
            node = parent;
        }

        return path;
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