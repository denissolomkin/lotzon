(function () {

    Player = {

        count: {},
        balance: {},
        currency: {},
        language: {},
        favorite: [],

        init: function (init) {

            D.log('Player.init', 'func');

            if (typeof init === 'object') {

                /* +/- */
                if (init.hasOwnProperty('count')) {
                    if (Object.size(this.count)) {
                        init.count = this.extendCount(init.count);
                    }
                }

                if (init.hasOwnProperty('games') && init.games.online && init.games.online.Uid) {
                    App.id = init.games.online.Id;
                    WebSocketAjaxClient('app/' + init.games.online.Key + '/' + init.games.online.Uid, {action: 'start'});
                    delete init.games.online;
                }

                Object.deepExtend(this, init);

                if (init.hasOwnProperty('count')) {
                    Player.renderCount(init.count);
                }

                if (init.hasOwnProperty('balance')) {
                    Player.updateBalance();
                }

            }

            this.initPing();

            return this;
        },

        "extendCount": function (object, extendedCount, currentCount, keys) {

            extendedCount = extendedCount || {};
            currentCount = currentCount || this.count;

            /* перебираем полученный счетчик*/
            for (prop in object) {
                if (object.hasOwnProperty(prop)) {

                    // console.warn(currentCount);

                    /* перебираем текущий счетчик*/
                    for (count in currentCount) {
                        if (currentCount.hasOwnProperty(count)) {

                            /* если ключи совпадают */
                            if (count === prop) {

                                /* и текущий счетчик с ключом объект */
                                if (typeof currentCount[count] === 'object') {

                                    var key = keys && keys.slice() || [];
                                    key.push(count);

                                    //console.error(object[prop], key);

                                    this.extendCount(
                                        object[prop],
                                        extendedCount,
                                        currentCount[count],
                                        key
                                    );

                                    /* если же нашли сам счетчик, то получаем конечное значение */
                                } else {

                                    //console.error(object, count, extendedCount, currentCount, keys);
                                    var source = {},
                                        value = object[count];

                                    switch (value[0]) {
                                        case '+':
                                        case '-':
                                            value = parseInt(currentCount[count]) + parseInt(value);
                                            break;

                                        default :
                                            value = parseInt(value);
                                    }

                                    source[count] = value;

                                    if (keys) {
                                        var keysClone = keys.slice();
                                        while (keysClone.length) {
                                            var temp = {},
                                                key = keysClone.pop();
                                            temp[key] = source;
                                            source = temp;
                                            //console.log(source);
                                        }
                                    }

                                    Object.deepExtend(extendedCount, source);
                                    //console.info(extendedCount);
                                }


                                /* или если в текущем счетчике есть объект с таким ключом */
                            } else if (typeof currentCount[count] === 'object') {

                                var key = keys && keys.slice() || [],
                                    temp = {};

                                key.push(count);
                                temp[prop] = object[prop];

                                //console.warn(object, extendedCount, currentCount, keys, temp, key, object);

                                this.extendCount(
                                    temp,
                                    extendedCount,
                                    currentCount[count],
                                    key
                                );
                            }
                        }
                    }
                }
            }

            return extendedCount;

        },

        "renderCount": function (counters) {

            if (!counters)
                return;

            for (key in counters) {
                if (counters.hasOwnProperty(key)) {

                    var holders = document.getElementsByClassName('count-' + key);

                    for (var i = 0; i < holders.length; i++) {
                        var count = this.getCount(key);
                        if (count != holders[i].innerHTML) {
                            holders[i].innerHTML = count;
                            if (count) {
                                DOM.fadeIn(holders[i]);
                            } else {
                                DOM.fadeOut(holders[i]);
                            }
                        }
                    }

                    if (key === 'notifications') {
                        Comments.renderNotifications();
                    }

                    if (typeof counters[key] === 'object') {
                        this.renderCount(counters[key]);
                    }
                }
            }
        },

        "prepareCount": function (key, value, count) {

            if (typeof value === 'object') {
                for (var prop in value) {
                    if (value.hasOwnProperty(prop)) {

                        value[prop] = this.prepareCount(
                            prop,
                            value[prop],
                            this.count[key] && this.count[key][prop] || 0
                        );

                    }
                }
            } else {
                switch (value[0]) {
                    case '+':
                    case '-':
                        value = count + parseInt(value);
                        break;

                    default :
                        value = parseInt(value);
                }
            }

            return value;
        },

        decrement: function (count, value) {
            value = value || 1;
            return this.getCount(count) && this.setCount(count, this.getCount(count) - value);
        },

        setCount: function (count, value) {

            var obj = {count: {}};

            if (typeof count === 'object' && !isNumeric(value)) {
                obj.count = count;
                this.init(obj);
            } else if (typeof count === 'string' && isNumeric(value)) {
                obj.count[count] = value;
                this.init(obj);
            }


            return this;
        },

        updateCount: function (key, value) {
            return this.getCount(key, null, value);
        },

        getCount: function (key, object, newValue) {

            var count = 0;
            object = object || this.count;

            if (!key) {
                count = this.getCount(object, null, newValue);

            } else if (typeof key === 'string') {
                for (var prop in object) {
                    if (object.hasOwnProperty(prop)) {
                        if (prop === key) {
                            /* for {prop:{}} where prop === key */
                            if (typeof object[prop] === 'object') {
                                count = this.getCount(object[prop], null, newValue);
                                //console.log(key, count);
                                break;
                            } else {
                                if (newValue)
                                    object[prop] = newValue;
                                count = parseInt(object[prop]);
                                //console.log(key, count, newValue);
                                break;
                            }
                        } else if (typeof object[prop] === 'object') {
                            count += parseInt(this.getCount(key, object[prop], newValue));
                            //console.log(key, prop, count);
                        }
                    }
                }

            } else if (typeof key === 'object') {
                for (var prop in key) {
                    if (key.hasOwnProperty(prop)) {
                        if (typeof key[prop] === 'object') {
                            count += this.getCount(key[prop], null, newValue);
                        } else {
                            if (newValue)
                                key[prop] = newValue;
                            count += parseInt(key[prop]);
                        }
                        //console.log(key, count);
                    }
                }
            }

            //console.log(key, object, count);
            return count;
        },

        getCurrency: function (currency, number) {

            if(typeof number === 'object')
                number = undefined;

            switch (currency) {
                case 'money':
                case 'MONEY':
                default:
                    currency = isNumeric(currency)
                        ? parseFloat((currency * Player.currency.coefficient).toFixed(2))
                        : (typeof number !== 'undefined' ? parseFloat((number * Player.currency.coefficient).toFixed(2)) : Player.currency.iso);
                    break;
                case 'points':
                case 'POINT':
                    currency = typeof number !== 'undefined'
                        ? number
                        : Cache.i18n('title-of-points');
                    break;
                case 'lotzon':
                    currency = Cache.i18n('title-lotzon');
                    break;
            }

            return currency;
        },

        "formatCurrency": function (value, part) {

            function round(a, b) {
                b = b || 0;
                return parseFloat(a.toFixed(b));
            }

            var format = null;

            if (["iso", "one", "few", "many"].indexOf(part) > -1) {
                var format = part;
                part = null;
            }


            if (!value || value == '' || value == 'undefined')
                value = null;


            switch (value) {
                case null:
                    return Player.currency['iso'];
                    break;
                case 'coefficient':
                case 'rate':
                    return (Player.currency[value] ? Player.currency[value] : 1);
                    break;
                case 'iso':
                case 'one':
                case 'few':
                case 'many':
                    return (Player.currency[value] ? Player.currency[value] : Player.currency['iso']);
                    break;
                default:
                    value = round((parseFloat(value) * Player.currency['coefficient']), 2);
                    if ((format == 'many' || (!format && value >= 5)) && Player.currency['many']) {
                        return (!part || part == 1 ? value : '') + (part == 1 ? null : (!part ? ' ' : '') + Player.currency['many']);
                    } else if ((format == 'few' || (!format && (value > 1 || value < 1))) && Player.currency['few']) {
                        return (!part || part == 1 ? value : '') + (part == 1 ? null : (!part ? ' ' : '') + Player.currency['few']);
                    } else if ((format == 'one' || (!format && value == 1)) && Player.currency['one']) {
                        return (!part || part == 1 ? value : '') + (part == 1 ? null : (!part ? ' ' : '') + Player.currency['one']);
                    } else {
                        return (!part || part == 1 ? value : '') + (part == 1 ? null : (!part ? ' ' : '') + Player.currency['iso']);
                    }
                    break;
            }
        },

        getPrivacy: function(key){
            return this.hasOwnProperty('privacy') && this.privacy.hasOwnProperty(key) && this.privacy[key];
        },

        updatePoints: function (newSum) {
            var balance = {
                balance: {
                    points: typeof newSum !== 'undefined' && parseFloat(newSum).toFixed(2) || this.balance.points
                }
            };
            return this.init(balance);

        },

        updateMoney: function (newSum) {
            var balance = {
                balance: {
                    money: typeof newSum !== 'undefined' && parseFloat(newSum).toFixed(2) || this.balance.money
                }
            };
            return this.init(balance);
        },

        fineNumbers: function (sum) {
            return isNumeric(sum) ? parseFloat(sum).toFixed(2).replace('.00', '').replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ').replace('.', ',') : 0;
        },

        updateBalance: function () {
            if (this.hasOwnProperty('balance')) {
                for (key in this.balance) {
                    if (this.balance.hasOwnProperty(key)) {
                        var holders = document.getElementsByClassName('holder-' + key);
                        for (var i = 0; i < holders.length; i++) {
                            holders[i].innerHTML = this.fineNumbers(this.balance[key]);
                        }
                    }
                }
            }
        },

        initPing: function () {

            if (this.pingInterval) {
                window.clearInterval(this.pingInterval);
                delete this.pingInterval;
            }

            this.pingInterval = window.setInterval(Player.ping, (Config.timeout.ping ? Config.timeout.ping : 60) * 1000);

        },

        ping: function () {

            var forms = Content.forms4ping(),
                users = Content.users4ping();

            Form.post({
                action: '/ping',
                data: {
                    forms: forms,
                    users: users
                }
            })
        },

        getMoney: function(){
            return parseFloat(this.balance.money);
        },

        checkMoney: function (input_money) {

            input_money = input_money
                .replace(/[^\d.-]/g, ".")
                .replace('..', '.');
            input_money = input_money.match(/\d*[,.]\d{2}/) || input_money;

            if (!isNumeric(input_money))
                input_money = parseFloat(input_money);

            if (input_money > this.getMoney())
                input_money = this.getMoney();

            if (!input_money)
                input_money = null;

            return input_money;
        },

        calcPoints: function (input_money) {
            var calc_points;
            calc_points = parseInt(input_money * this.currency.rate);
            return calc_points;
        },

        renderFavorite: function () {
            balls = {};
            for (var i = 1; i <= Tickets.totalBalls; i++)
                balls[i] = this.favorite.indexOf(i) !== -1;
            return balls;
        },

        getOnline: function (data) {
            switch (Player.isOnline(data)) {
                case true:
                    return 'online';
                    break;
                case false:
                    return 'offline';
                    break;
                case null:
                default:
                    return null;
                    break;
            }
        },

        isOnline: function (data) {

            if(!data) return null;

            var ping = data && typeof data === 'object' ? data.hasOwnProperty('ping') && data.ping : data,
                playerId = data && typeof data === 'object' && data.hasOwnProperty('id') && data.id;

            return (playerId && playerId == this.id) || !ping
                ? null
                : ping && parseInt(ping) + Livedate.diff + Config.timeout.online > moment().unix();
        },

        getAvatar: function (img, id, width) {

            if ('nodeType' in this) {
                this.classList.remove('loading');
                this.style.background = 'none';
                this.src = '/res/img/default.jpg';
                return;
            }

            img = typeof img === 'string' ? img : this.img;
            id = typeof id === 'string' ? id : this.id;
            width = typeof width === 'number' ? width : 200;
            return (img
                ? Config.filestorage + '/users/' + width + '/' + img
                : '/res/img/default.jpg');
        },

        getAdmin: function (id) {
            return id ? id == Config.adminId : Config.adminId;
        }

    };

})();