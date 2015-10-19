$(function () {


    // ======================================= //

    Player = $.extend(Player, {

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
        }

    });
});