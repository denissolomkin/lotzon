$(function () {

    var prepareHelper = function (options, arguments) {

        options = typeof options === 'string' ? options : null;

        if (arguments.length > 2) {
            var args = [];
            for (var i = 1; i <= arguments.length; i++)
                typeof arguments[i] === 'string' && args.push('"' + arguments[i] + '"');
            options = args.join(', ');
        }

        return options;
    };

    Handlebars.registerHelper({

        'avatar': Player.getAvatar,

        'player': function (fn, options) {

            options = prepareHelper(options, arguments);
            var response = eval("Player." + fn.toString());
            D.log('Player.' + fn + (options ? '(' + options + ')' : ''), 'handlebars');
            return typeof response === 'function' && eval("Player." + fn + "(" + options + ")") || response;
        },

        'device': function (fn, options) {
            options = prepareHelper(options, arguments);
            var response = eval("Device." + fn.toString());
            D.log('Device.' + fn + (options ? '(' + options + ')' : ''), 'handlebars');
            return typeof response === 'function' && eval("Device." + fn + "(" + options + ")") || response;
        },

        'lottery': function (fn, options) {

            options = prepareHelper(options, arguments);
            var response = eval("Tickets." + fn.toString());
            D.log('Tickets.' + fn + (options ? '(' + options + ')' : ''), 'handlebars');
            return typeof response === 'function' && eval("Tickets." + fn + "(" + options + ")") || response;
        },

        'number': Player.fineNumbers,
        'count': Player.getCount,

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

        'partial': function f(name, args) {

            var template = Cache.template(name);
            args = args || {};

            if (!template || typeof template !== 'function') {
                setTimeout(function () {
                    (name, args);
                }, 100);
            } else {
                return new Handlebars.SafeString(template(args));
            }


        },

        'variable': function (name, args, opt) {

            console.log(this, name, args, opt);
        },


        'cache': function () {

        },

        'social': function (network, id) {

            var href = '';

            switch (network) {
                case "Facebook":
                    href = ""
                    break;
                case "Google":
                    href = ""
                    break;
                case "Twitter":
                    href = ""
                    break;
                case "Vkontakte":
                    href = ""
                    break;
                case "Odnoklassniki":
                    href = ""
                    break;

            }

            return href + '/' + id;
        },


        'i18n': function () {
            return Cache.i18n(arguments);
        },

        'from': Livedate.fn.from,
        'day': Livedate.fn.day,

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