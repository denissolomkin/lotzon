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
                    'after'   : this.action
                });

            } else {

                Game.run() && this.run();
                Game.updateTimeOut(App.timeout);
                Game.end();
            }

        },

        error: function () {

        }

    }

});