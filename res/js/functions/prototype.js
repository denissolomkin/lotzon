(function () {

    Array.prototype.last = function () {
        return this[this.length - 1];
    };

    Array.prototype.shuffle = function () {
        var i = this.length, j, temp;
        var rnd = Math.random;
        if (i == 0) return this;
        while (--i) {
            j = ( rnd() * ( i + 1 ) ) | 0;
            temp = this[i];
            this[i] = this[j];
            this[j] = temp;
        }
        return this;
    }

    if (typeof Array.isArray === 'undefined') {
        Array.isArray = function(obj) {
            return Object.prototype.toString.call(obj) === '[object Array]';
        }
    };

    String.prototype.replaceArray = function (find, replace) {
        var replaceString = this;
        var replaceMatch = replace;
        var replaceFind = find;
        var regex;
        for (var i = 0; i < find.length; i++) {
            if ($.isArray(find))
                replaceFind = find[i];
            regex = new RegExp(replaceFind, "g");
            if ($.isArray(replace))
                replaceMatch = replace[i];
            replaceString = replaceString.replace(regex, replaceMatch);
            if (!$.isArray(find))
                break;
        }
        return replaceString;
    };

    Storage.prototype.setObj = function (key, obj) {
        return this.setItem(key, JSON.stringify(obj))
    };

    Storage.prototype.getObj = function (key) {
        return JSON.parse(this.getItem(key))
    };


    // not very sure if this work cross-frame
    Object.isObjectLiteral = function (object) {
        return object && object.constructor && object.constructor.name === 'Object';
    }

    Object.deepExtend = function (destination, source) {
        for (var property in source) {
            if (Object.isObjectLiteral(destination[property]) && Object.isObjectLiteral(source[property])) {

                destination[property] = destination[property] || {};
                arguments.callee(destination[property], source[property]);
            } else {
                destination[property] = source[property];
            }
        }
        return destination;
    };

    Object.size = function (obj) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) size++;
        }
        return size;
    };

    $.fn.getPath = function () {
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

    $.fn.serializeObject = function () {
        var obj = {};
        var assignByPath = function (obj, path, value) {
            if (path.length == 1) {
                if (path[0])
                    obj[path[0].replace(':', '.')] = value;
                else obj[value] = value;
                return obj;
            } else if (obj[path[0]] === undefined) {
                obj[path[0].replace(':', '.')] = {};
            }
            return assignByPath(obj[path.shift()], path, value);
        }

        $.each(this.serializeArray(), function (i, o) {
            var n = o.name,
                v = o.value;
            path = n.replace('.', ':').replace(/\]\[/g, '.').replace(/\[/g, '.').replace(']', '').split('.');

            assignByPath(obj, path, v);
        });

        return obj;
    };




})();