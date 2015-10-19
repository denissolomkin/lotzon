$(function () {

    GameAction = {

        ready: function (e) {

            price = appMode.split('-');
            if ((price[0] == 'POINT' && Player.balance.points < parseInt(price[1])) || (price[0] == 'MONEY' && Player.balance.money < Player.getCurrency(price[1], 1))) {

                $("#report-popup").show().find(".txt").text(M.i18n('INSUFFICIENT_FUNDS')).fadeIn(200);

            } else {

                var path = 'app/' + appName + '/' + appId;
                var data = {'action': 'ready'}
                WebSocketAjaxClient(path, data);
            }
        },

        pass: function (e) {

            var path = 'app/' + appName + '/' + appId;
            var data = {
                'action': 'pass'
            }

            WebSocketAjaxClient(path, data);
        }

    }

});