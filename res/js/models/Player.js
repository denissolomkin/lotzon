// jQuery less

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

                if (init.hasOwnProperty('count')) {
                    for (key in init.count) {
                        if (this.count.hasOwnProperty(key)) {
                            var holders = document.getElementsByClassName('count-' + key);
                            for (var i = 0; i < holders.length; i++) {
                                holders[i].innerHTML = init.count[key];
                            }
                        }
                    }
                }

                if (init.hasOwnProperty('balance')) {
                    for (key in init.balance) {
                        if (this.balance.hasOwnProperty(key)) {
                            var holders = document.getElementsByClassName('holder-' + key);
                            for (var i = 0; i < holders.length; i++) {
                                holders[i].innerHTML = this.fineNumbers(init.balance[key]);
                            }
                        }
                    }
                }

                Object.deepExtend(this, init);

            }

            return this;
        },

        getCurrency: function (currency) {

            switch (currency) {
                case 'money':
                default:
                    currency = Player.currency.iso;
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

        formatCurrency: function (value, part) {

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

        decrement: function (count) {
            return this.getCount(count) && this.setCount(count, this.getCount(count) - 1);
        },

        setCount: function (count, value) {

            var obj = {count: {}};

            if (typeof count === 'object' && !value) {
                obj.count = count;
                this.init(obj);
            } else if (typeof count === 'string' && value) {
                obj.count[count] = value;
                this.init(obj);
            }

            return this;
        },

        getCount: function (key) {
            return typeof key === 'string' && this.count.hasOwnProperty(key) && this.count[key];
        },

        updatePoints: function (newSum) {
            var balance = {
                balance: {
                    points: typeof newSum !== 'undefined' && parseInt(newSum) || this.balance.points
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

        getAvatar: function (img, id) {
            img = typeof img === 'string' ? img : this.img;
            id = typeof id === 'string' ? id : this.id;
            return (img
                ? '/filestorage/avatars/' + ((Math.ceil(id / 100)) + '/' + img)
                : '/res/img/default.jpg');
        }

    };

})();