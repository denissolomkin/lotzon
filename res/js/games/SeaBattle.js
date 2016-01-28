$(function () {

    SeaBattle = {

        run: function () {


        },

        action: function () {

            if (!document.getElementById('games-online-field')) {

                R.push({
                    'template': 'games-online-field',
                    'json'    : {},
                    'url'     : false,
                    'after'   : SeaBattle.action
                });

            } else {

                Game.run() && SeaBattle.run();
                Game.updateTimeOut(App.timeout);
                Game.end();
            }

        },

        error: function () {

        }

    }

});