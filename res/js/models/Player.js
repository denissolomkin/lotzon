// jQuery less

(function () {

    Player = {

        init: function (init) {

            D.log('Player.init', 'func');
            Object.deepExtend(this, init);

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

        updatePoints: function (newSum) {

            var holders = document.querySelectorAll('.holder-points');
            this.balance.points = parseInt(newSum) || this.balance.points;
            newSum = this.fineNumbers(this.balance.points);

            for (var i = 0; i < holders.length; i++) {
                holders[i].innerHTML = newSum;
            }

            return this;
        },

        updateMoney: function (newSum) {

            var holders = document.querySelectorAll('.holder-money');
            this.balance.money = newSum && parseFloat(newSum).toFixed(2) || this.balance.money;
            newSum = this.fineNumbers(this.balance.money);

            for (var i = 0; i < holders.length; i++) {
                holders[i].innerHTML = newSum;
            }

            return this;
        },

        fineNumbers: function (sum) {
            return parseFloat(sum).toFixed(2).replace('.00', '').replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ').replace('.', ',');
        },

        updateBalance: function () {
            this.updatePoints();
            this.updateMoney();
            return this;
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

        getAvatar: function (img, id) {
            console.log('avatar: ',arguments);
            img = img || this.img;
            id = id || this.id;
            return '/filestorage/avatars/' + (Math.ceil(id / 100)) + '/' + img;
        }

    };

})();