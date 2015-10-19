$(function () {

    Durak = {

        reply: function () {
            $('.re').hide();
            $('.ot-exit').html('Ожидаем соперника').show();
            return;
        },

        stack: function () {
            return;
        },

        start: function () {

            $(document).on('click', '.tmp_but', function (e) {
                if (Device.get() <= 0.5) {
                    W.toggleFullScreen();
                }
            });
            return;
        },

        ready: function () {

            console.log(action, onlineGame);
            Cards.setupForDevices();
            $('.tmp_but').html(Device.detect() + document.documentElement.clientHeight);

            /*
             1) если действие "ждем", "старт" или "готовы", но все обязаны подтердить готовность
             или 2) еще нет блоков игроков
             */
            if (($.inArray(onlineGame.action, ['move', 'timeout', 'pass']) == -1 &&
                (onlineGame.action != 'ready' || Object.size(onlineGame.players) == onlineGame.current.length)
                ) || !$('.mx .players').children('div').length) {

                Game.run();
                Cards.createCardsWrapper();
                Game.variations();
                Cards.emptyFields();
                Cards.drawTrump();
            }

            Cards.drawFields();
            Cards.premove();
            Cards.initStatuses();
            Game.updateTimeOut(onlineGame.timeout);
            Game.end();
        },

        quit: function () {
            if (onlineGame.quit != Player.id) {
                $('.re').hide();
                $('.ot-exit').html('Соперник вышел').show();
            } else
                appId = 0;
        },

        error: function () {

            alert('error');
            Cards.premove();
            Game.error();
            Drag.rollback();

        }


    }

    Durak.move = Durak.timeout = Durak.pass = Durak.wait = Durak.ready;

});