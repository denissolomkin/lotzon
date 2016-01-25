$(function () {

    WhoMore = {

        run: function () {


        },

        action: function () {

            if (!document.getElementById('games-online-field')) {

                R.push({
                    'template': 'games-online-field',
                    'json'    : {},
                    'url'     : false,
                    'after'   : WhoMore.action
                });

            } else {

                Game.run() && WhoMore.run();
                Game.updateTimeOut(App.timeout);
                Game.end();
            }

        },

        error: function () {

        }

    }

});