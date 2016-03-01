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
        },
        isEmpty = function(element) {
            return (!element || (typeof element === 'string' && element == '' ));
        };

    Handlebars.registerHelper({
        /**
         * @description loop for(max > min; max--) return current counter
         *
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
         * @description loop for(; min >= max; min++) return current counter
         *
         * @param {int} min
         * @param {int} max
         * @param {fn} options
         * @returns {String}
         */
        'forMinToMax': function ( min, max, options) {
            var ret = "";
            for (; min <= max; min++) {
                ret = ret + options.fn(min);
            }
            return ret;
        },
//        ^ delete that sh... 'forMaxToMin'

        /**
         * @description loop for(num "operator >:>=:<:<=" num2) return current counter Example {{#each (for 3 "<" 0) as |value key|}} {{this}} {{/each}} // return 3 2 1
         *
         * @param {int} num
         * @param {string} operator
         * @param {int} num2
         * @param {fn} options
         * @returns {String} counter
         */
        'for': function (num, operator, num2) {
            var ret = [];

            switch (operator) {
                default: //" < "
                    for (; num < num2; num++) {
                        ret.push(num);
                    }
                    break;
                case "<=":
                    for (; num <= num2; num++) {
                        ret.push(num);
                    }
                    break;
                case ">=":
                    for (; num >= num2; num--) {
                        ret.push(num);
                    }
                    break;
                case ">":
                    for (; num > num2; num--) {
                        ret.push(num);
                    }
                    break;
            }
            ;
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
            if (!str || !delimiter || !el)
                return;
            return str.split(delimiter)[el];
        },
        /**
         * @description оборачивает аргументы из шаблонизатора в функцию funcNmae(args,args2,...)
         *
         * @param {string} funcName
         * @returns {String} "funcName(args,args2,...)"
         */
        'createFunc': function (funcName) {
            if (arguments.length > 1) {
                var params = "";
                for (var i = 1; i < arguments.length - 1; i++) {
                    params += arguments[i];
                    if (i < arguments.length - 2) {
                        params += ",";
                    }

                }

                return funcName + "(" + params + ")";
            }
        },
        'reverseNum': function (num1, num2) {
            return num1 - num2;
        },
        'badgesCountdown': function () {
//            return;
            if (!this.timeout || !this.timer){
                return;
            }
            
            var data = this;

            setTimeout(function () {
                var item = document.querySelector('#badges-' + (data.key?"notifications-"+data.key:"messages-"+data.id));
                if (!item) {
                    return "no item found";
                }
                
                switch (data.timeout) {
                    case "close":
                        item.parentNode.removeChild(item);
                        break;
                    case "button":
                        return "There no code for 'Button' action!... ";
                        break;
                }
            }, data.timer * 1000);

            return;
        },
        'countHTML': function(key){
            var count = Player.getCount(key);
            return '<span class="count count-' + key + '"'
            + (count ? '' : ' style="display:none;"')
            + '>' + count + '</span>';
        },
        'avatar': Player.getAvatar,
        'convertСurrency': Player.convertСurrency,
        'number': Player.fineNumbers,
        'count': Player.getCount,
        'from': Livedate.fn.from,
        'mobile': Device.isMobile,
        'day': Livedate.fn.day,
        'emotions': Comments.getEmotionsHTML,
        'limit': function (key) {
            return Config.hasOwnProperty('limits') && Config.limits.hasOwnProperty(key) ? Config.limits[key] : 5;
        },
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
            http://www.w3schools.com/jsref/event_onload.asp

                    console.log(this, name, args, opt);
        },
        'cache': function () {

        },
        'checkAll': function () {
            console.log(arguments);
            for (var i = 0; i < arguments.length; i++)
                if(isEmpty(arguments[i]))
                    return true;

            return false;
        },
        'isFill' : function () {
            for (var i = 0; i < arguments.length; i++)
                if(isEmpty(arguments[i]))
                    return false;

            return true;
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
        'neNotStrict': function (v1, v2) {
            return v1 != v2;
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
        },
        'diff': function (v1, v2) {
            return parseFloat(v1) - parseFloat(v2);
        },

        'getSmile': function (text) {
            for (var val in Comments.emotionsToServer) {
                text = text.split(Comments.emotionsToServer[val]).join(val);
            }
            text = text.replace(/\n/ig, '<br>');
            return text;
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

}
);