$(function () {

    var prepareHelper = function (fn, options, arguments, model) {

            options = typeof options !== 'string'
                ? (arguments.length == 3 ? JSON.stringify(options) : '' )
                : options;

            if (arguments.length > 3) {
                var args = [];
                for (var i = 2; i <= arguments.length; i++)
                    typeof arguments[i] === 'string' && args.push('"' + arguments[i] + '"');
                options += args.join(', ');
            }

            var response = eval(model + "." + fn.toString());

            D.log(model + '.' + fn + (options ? '(' + options + ')' : ''), 'handlebars');
            return typeof response === 'function' ? response(options) : response;
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
            if(typeof operator === 'object') {
                num = 0;
                operator = '<';
                num2 = num;
            } else
                num2 = typeof num2 !== 'object' ? num2 : operator;

            switch (operator) {
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
                default: //" < "
                    for (; num < num2; num++) {
                        ret.push(num);
                    }
                    break;
            }

            return ret;
        },
        'splitMode': function(str) {
            str = str.split('-');
            if (str.length >= 3) {
                var ob = {
                    'currency': str[0],
                    'value': str[1],
                    'players': str[2]
                }
                if (str[3]) {
                    var tmp = str[3].split('&');
                    // console.debug(tmp);
                    for (var i = 0; i < tmp.length; i++) {
                        var tmp2 = tmp[i].split('=');
                        ob[tmp2[0]] = tmp2[1];
                    }
                }
                // console.debug(ob)
                return ob;
            }
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
                var item = document.querySelector('#badges-' + (data.uid?"system-"+data.uid:(data.key?"notifications-"+data.key:"messages-"+data.id)));
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
        'solvency': function(val, str){
            if (str === "money"){
                return parseFloat(Player.balance.money) >= parseFloat(val) ? true : false;
            }else{
                return parseFloat(Player.balance.points) >= parseFloat(val) ? true : false;
            }
            return false;
        },
        'currentPrivacy': function(key){
            var rules = ['nobody', 'friends', 'all'];
            var privacy = Player.getPrivacy(key) || 2;
            var html  = i18n("title-profile-privacy-" + rules[privacy]);
            return html;
        },
        'privacyHTML': function(key, def){

            var rules = ['nobody', 'friends', 'all'],
                icons = ['i-lock', 'i-person3', 'i-earth'],
                lock = typeof def === 'number' ? def : false,
                html = '<div class="buttons-group '+(lock !== false ? ' disabled' : '')+'">';
                
            for (var i = 0; i < rules.length; i++)
                html += '<div class="button"><input type="radio" ' + (lock !== false ? ' disabled' : '') +  ' id="profile-privacy-'+key+'-'+i+'" class="profile-privacy" name="privacy[' + key + ']" value="' + i + '"' + (i == lock || i == Player.getPrivacy(key) ? 'checked="checked"' : '') + '> <label for="profile-privacy-'+key+'-'+i+'"><i class="'+icons[i]+'"></i><span>' + i18n("title-profile-privacy-" + rules[i]) + '</span></label></div>';

            html += '</div>';
            return html;
        },
        'avatar': Player.getAvatar,
        'currency': Player.getCurrency,
        'number': Player.fineNumbers,
        'count': Player.getCount,
        'from': Livedate.fn.from,
        'day': Livedate.fn.day,
        'days': function (date) {
            return moment(date).daysInMonth();
        },
        'mobile': Device.isMobile,
        'emotions': Comments.getEmotionsHTML,
        'limit': function (key) {
            return Config.hasOwnProperty('limits') && Config.limits.hasOwnProperty(key) ? Config.limits[key] : 5;
        },
        'i18n': function () {
            return Cache.i18n(arguments);
        },
        'moment': function (fn, options) {
            return prepareHelper(fn, options, arguments, 'moment');
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
        'config': function (fn, options) {
            return prepareHelper(fn, options, arguments, 'Config');
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
        /**
         * проверяет наличие значений в строке
         * @param  {str} inStr "POINT-25-2-field=10x10"
         * @param  {pattern} pattern "POINT|field"
         * @return {bool}     
         */
        'matchStr':function(inStr, pattern){
            console.debug('matchStr', inStr , pattern);
            pattern = new RegExp(pattern, "i");
            if(inStr.match( pattern )) return true;
            return false;
        },
        'false': function (v1) {
            return v1 === false;
        },
        'null': function (v1) {
            return v1 === null;
        },
        'isset': function (v1) {
            return v1 !== undefined && v1 !== null;
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
            console.log(v1, v2);
            return v1 !== v2;
        },
        'notlike': function (v1, v2) {
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