$(function () {


    // ======================================= //

    $.extend(Player, {

        getCurrency: function (value, part) {

            function round(a, b) {
                b = b || 0;
                return parseFloat(a.toFixed(b));
            }

            var format = null;

            if ($.inArray(part, ["iso", "one", "few", "many"]) >= 0) {
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

        updatePoints: function (points) {
            this.balance.points = parseInt(points) || this.balance.points;
            points = this.balance.points.toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
            $('.holder-points').text(points);
            return this;
        },

        updateMoney: function (money) {
            this.balance.money = parseFloat(money).toFixed(2) || this.balance.money;
            money = parseFloat(this.balance.money).toFixed(2).toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
            $('.holder-money').text(money.replace('.00', ''));
            return this;
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

            if(!isNumeric(input_money))
                input_money = parseFloat(input_money);

            if (input_money > this.balance.money)
                input_money = this.balance.money;

            if(!input_money)
                input_money = null;

            return input_money;
        },

        calcPoints: function (input_money) {

            var calc_points;
            calc_points = parseInt(input_money * this.currency.rate);
            return calc_points;
        },

        extend: function(source) {
            for (var property in source) {
                if (source[property] && source[property].constructor &&
                    source[property].constructor === Object) {
                    this[property] = this[property] || {};
                    arguments.callee(this[property], source[property]);
                } else {
                    this[property] = source[property];
                }
            }
            return this;
        }
    });

});