$(function () {


    // ======================================= //

    Balance = $.extend(Balance, {

        updatePoints: function (points) {
            Balance.points = parseInt(points) || Balance.points;
            points = Balance.points.toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
            $('.plPointHolder').text(points);
        },

        updateMoney: function (money) {
            Balance.money = parseFloat(money).toFixed(2) || Balance.money;
            money = parseFloat(Balance.money).toFixed(2).toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
            $('.plMoneyHolder').text(money.replace('.00', ''));
        }

    });
});