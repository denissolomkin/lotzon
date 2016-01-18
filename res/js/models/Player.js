(function () {

    Player = {

        count   : {},
        balance : {},
        currency: {},
        language: {},
        favorite: [],

        init: function (init) {

            D.log('Player.init', 'func');

            if (typeof init === 'object') {

                /* +/- */
                if (init.hasOwnProperty('count')) {
                    for (key in init.count) {
                        if (init.count.hasOwnProperty(key)) {
                            init.count[key] = this.prepareCount(key, init.count[key]);
                        }
                    }
                }

                if (init.hasOwnProperty('balance')) {
                    for (key in init.balance) {
                        if (this.balance.hasOwnProperty(key)) {
                            var holders = document.getElementsByClassName('holder-' + key);
                            for (var i = 0; i < holders.length; i++) {
                                init.balance[key] = parseFloat(init.balance[key]);
                                holders[i].innerHTML = this.fineNumbers(init.balance[key]);
                            }
                        }
                    }
                }

                if (init.hasOwnProperty('game') && init.game.uid) {
                    WebSocketAjaxClient('app/' + init.game.key + '/' + init.game.uid, {action: 'start'});
                    delete init.game;
                }

                Object.deepExtend(this, init);

                if (init.hasOwnProperty('count')) {
                    for (key in init.count) {
                        if (this.count.hasOwnProperty(key)) {

                            var holders = document.getElementsByClassName('count-' + key);

                            for (var i = 0; i < holders.length; i++) {
                                var count = this.getCount(key);
                                holders[i].innerHTML = count;
                                if (count)
                                    DOM.fadeIn(holders[i]);
                                else
                                    DOM.fadeOut(holders[i]);
                            }

                            if (key === 'notifications') {
                                Comments.renderNotifications();
                            }
                        }
                    }
                }

            }

            this.initPing();

            return this;
        },

        "prepareCount": function (key, value, count) {

            if (typeof value === 'object') {
                for (var prop in value) {
                    if (value.hasOwnProperty(prop)) {
                        value[prop] = this.prepareCount(prop, value[prop], this.count[key] && this.count[key][prop] || 0);
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

        getCount: function (key, object) {

            var count = 0;
            object = object || Player.count;

            if (!key) {
                count = this.getCount(object);

            } else if (typeof key === 'string') {
                for (var prop in object) {
                    if (object.hasOwnProperty(prop)) {
                        if (prop === key) {
                            if (typeof object[prop] === 'object') {
                                count = this.getCount(object[prop]);
                                break;
                            } else {
                                count = parseInt(object[prop]);
                                break;
                            }
                        } else if (typeof object[prop] === 'object') {
                            count += parseInt(Player.getCount(key, object[prop]));
                        }
                    }
                }

            } else if (typeof key === 'object') {
                for (var prop in key) {
                    if (key.hasOwnProperty(prop)) {
                        count += (typeof key[prop] === 'object') ? Player.getCount(key[prop]) : parseInt(key[prop]);
                    }
                }
            }

            //console.log(key, object, count);
            return count;
        },

        getCurrency: function (currency) {

            switch (currency) {
                case 'money':
                default:
                    currency = isNumeric(currency) ? currency * Player.currency.coefficient : Player.currency.iso;
                    break;
                case 'points':
                    currency = Cache.i18n('title-points');
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
            return parseFloat(sum).toFixed(2).replace('.00', '').replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ').replace('.', ',');
        },

        updateBalance: function () {
            return Player.updatePoints().updateMoney();
        },

        initPing: function () {

            if (this.pingInterval) {
                window.clearInterval(this.pingInterval);
                delete this.pingInterval;
            }

            this.pingInterval = window.setInterval(Player.ping, (Config.timeout.ping ? Config.timeout.ping : 1) * 1000);

        },

        ping: function () {

            Form.post({
                action: '/ping',
                data  : {forms: Content.forms4ping()}
            })
        },

        checkMoney: function (input_money) {

//            input_money = input_money.replaceArray([',','б','Б','ю','Ю'], '.');
            input_money = input_money.replace(/[^\d.-]/g, ".");
            input_money = input_money.replace('..', '.');
//            input_money = input_money.replace(/[^\d.-]/g, "");
            input_money = input_money.match(/\d*[,.]\d{2}/) || input_money;

            if (!isNumeric(input_money))
                input_money = parseFloat(input_money);

            if (input_money > this.balance.money)
                input_money = this.balance.money;

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

        isOnline: function (ping) {
            return ping + Config.timeout.online > new Date();
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