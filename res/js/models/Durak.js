$(function () {

    Durak = {

        action: function () {

            /*
             1) если действие "ждем", "старт" или "готовы", но все обязаны подтердить готовность
             или 2) еще нет блоков игроков
             */
            if (($.inArray(App.action, ['move', 'timeout', 'pass']) == -1 &&
                (App.action != 'ready' || Object.size(App.players) == App.current.length)
                ) || !$('.mx .players').children('div').length) {

                Game.run();
                Cards.createCardsWrapper();
                Cards.setVariation();
                Cards.emptyFields();
                Cards.drawTrump();
            }

            Cards.setupForDevices();
            Cards.drawFields();
            Cards.premove();
            Cards.initStatuses();
            Game.updateTimeOut(App.timeout);
            Game.end();
        },

        error: function () {

            Cards.premove();
            Game.callback.error();
            Drag.rollback();

        }

    }

});