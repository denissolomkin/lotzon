$(function () {

    var prepareHelper = function (fn, options, arguments, model) {

        options = typeof options === 'string' ? options : null;

        if (arguments.length > 2) {
            var args = [];
            for (var i = 1; i <= arguments.length; i++)
                typeof arguments[i] === 'string' && args.push('"' + arguments[i] + '"');
            options = args.join(', ');
        }

        var response = eval(model + "." + fn.toString());
        D.log(model + '.' + fn + (options ? '(' + options + ')' : ''), 'handlebars');
        return typeof response === 'function' ? eval(model + "." + fn + "(" + (options ? options : '') + ")") : response;
    };

    Handlebars.registerHelper({
        /**
         * @description loop for(max > min; max--) return current counter
         * @param {int} max
         * @param {int} min
         * @param {fn} options
         * @returns {String}
         */
        'forMaxToMin': function (max, min, options) {
            var ret = "";
            for (; max >= min; max--) {
                ret = ret + options.fn(max);
            }
            return ret;
        },
        /**
         * @description return one splitted element getSplittedEl("1-2-3","-", 0)
         * @param {str} str
         * @param {str} delimiter
         * @param {int} el
         * @returns {str}
         */
        'getSplittedEl': function (str, delimiter, el) {
            return str.split(delimiter)[el];
        },
        /**
         * @description оборачивает аргументы 
         * 
         * @param {string} funcNmae
         * @returns {String}
         */
        'createFunc': function(funcNmae){
            if(arguments.length > 1){
                var params = "";
                for (var i = 1; i < arguments.length -1; i++){
                    params+=arguments[i];
                    if(i < arguments.length - 2){
                        params+=",";
                    }
                        
                }

                return funcNmae+"("+params+")";
            }
        },
        'avatar': Player.getAvatar,
        'number': Player.fineNumbers,
        'count': Player.getCount,
        'from': Livedate.fn.from,
        'day': Livedate.fn.day,
        'i18n': function () {
            return Cache.i18n(arguments);
        },

        'player': function (fn, options) {
            return prepareHelper(fn, options, arguments, 'Player');
        },

        'device': function (fn, options) {
            return prepareHelper(fn, options, arguments, 'Device');
        },

        'lottery': function (fn, options) {
            return prepareHelper(fn, options, arguments, 'Tickets');
        },

        'partial': function f(name, args) {

            try {
                var template = Cache.template(name);
                args = args || {};
                return new Handlebars.SafeString(template(args));
            } catch (e) {
                D.error(name + ': ' + e.message);
            }
        },

        'social': function (network, id) {

            var href = "";
            switch (network) {
                case "Facebook":
                    href = "https://facebook.com/"
                    break;
                case "Google":
                    href = "https://plus.google.com/"
                    break;
                case "Twitter":
                    href = "https://twitter.com/intent/user?user_id="
                    break;
                case "Vkontakte":
                    href = "http://vk.com/id"
                    break;
                case "Odnoklassniki":
                    href = "http://www.odnoklassniki.ru/profile/"
                    break;

            }

            return href + id;
        },

        'reverse': function (context, options) {
            var fn = options.fn, inverse = options.inverse;
            var length = 0, ret = "", data;

            if (Handlebars.Utils.isFunction(context)) {
                context = context.call(this);
            }

            if (options.data) {
                data = Handlebars.createFrame(options.data);
            }

            if (context && typeof context === 'object') {
                if (Handlebars.Utils.isArray(context)) {
                    length = context.length;
                    for (var j = context.length - 1; j >= 0; j--) {//no i18n
                        if (data) {
                            data.index = j;
                            data.first = (j === 0);
                            data.last = (j === (context.length - 1));
                        }
                        ret = ret + fn(context[j], {data: data});
                    }
                } else {
                    var keys = Object.keys(context);
                    length = keys.length;
                    for (j = length; j >= 0; j--) {
                        var key = keys[j - 1]
                        if (context.hasOwnProperty(key)) {
                            if (data) {
                                data.key = key;
                                data.value = context[key];
                                data.index = j;
                                data.first = (j === 0);
                            }
                            ret += fn(context[key], {data: data});
                        }
                    }
                }
            }

            if (length === 0) {
                ret = inverse(this);
            }

            return ret;
        },


        'variable': function (name, args, opt) {

            console.log(this, name, args, opt);
        },


        'cache': function () {

        },

        'false': function (v1) {
            return v1 === false;
        },
        'null': function (v1) {
            return v1 === null;
        },
        'true': function (v1) {
            return v1 === true;
        },
        'eq': function (v1, v2) {
            return v1 === v2;
        },
        'like': function (v1, v2) {
            return v1 == v2;
        },
        'ne': function (v1, v2) {
            return v1 !== v2;
        },
        'lt': function (v1, v2) {
            return v1 < v2;
        },
        'gt': function (v1, v2) {
            return v1 > v2;
        },
        'lte': function (v1, v2) {
            return v1 <= v2;
        },
        'gte': function (v1, v2) {
            return v1 >= v2;
        },
        'and': function (v1, v2) {
            return v1 && v2;
        },
        'or': function (v1, v2) {
            return v1 || v2;
        }

    });

    Handlebars.JavaScriptCompiler.prototype.nameLookup = function (parent, name, type) {

        if (parent === "helpers") {
            if (Handlebars.JavaScriptCompiler.isValidJavaScriptVariableName(name))
                return parent + "." + name;
            else
                return parent + "['" + name + "']";
        }

        if (/^[0-9]+$/.test(name)) {
            return parent + "[" + name + "]";
        } else if (Handlebars.JavaScriptCompiler.isValidJavaScriptVariableName(name)) {
            // ( typeof parent.name === "function" ? parent.name() : parent.name)
            return "(typeof " + parent + "." + name + " === 'function' ? " + parent + "." + name + "() : " + parent + "." + name + ")";
        } else {
            return "(typeof " + parent + "['" + name + "'] === 'function' ? " + parent + "['" + name + "']() : " + parent + "." + name + ")";
        }
    };

});