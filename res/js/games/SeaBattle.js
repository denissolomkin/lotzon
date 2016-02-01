$(function () {

    Apps.SeaBattle = {

        run: function () {


        },

        action: function () {

            if (Game.field()) {
                Game.run() && Apps.SeaBattle.run();
                Game.updateTimeOut(App.timeout);
                Game.end();
            }

        },

        error: function () {

        }

    }

});