$(function () {

    Durak = {

        run: function () {

            Cards.createCardsWrapper();
            Cards.setVariation();
            Cards.emptyFields();
            Cards.drawTrump();

        },

        action: function () {

            if (!document.getElementById('games-online-field')) {

                R.push({
                    'template': 'games-online-field',
                    'json'    : {},
                    'url'     : false,
                    'after'   : Durak.action
                });

            } else {

                Game.run() && Durak.run();
                Cards.setupForDevices();
                Cards.drawFields();
                Cards.premove();
                Cards.initStatuses();
                Game.updateTimeOut(App.timeout);
                Game.end();
            }

        },

        error: function () {

            Cards.premove();
            Drag.rollback();

        }

    }

});