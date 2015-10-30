(function () {

    step = 0;

    WebSocketAjaxClient = function (id) {

        if (isNumeric(id))
            step = id;
        else
            step++;

        data = Cache.get('games-game')[step];


        if (data.error)
            $("#report-popup").show().find(".txt").text(getText(data.error)).fadeIn(200);
        else {

            sample = null;

            path = data.path;
            if (data.res) {


                if (data.res.appId && data.res.appId != App.id) {
                    App = {};
                } else if (App.winner) {
                    App['winner'] = null;
                    App['fields'] = null;
                }

                $.each(data.res, function (index, value) {
                    App[index] = value;
                });

                if (data.res.appName)
                    App.name = data.res.appName;

                if (data.res.appMode)
                    App.mode = data.res.appMode;

                if (data.res.appId) {
                    App.id = data.res.appId;
                    data = null;
                }

                Apps.playAudio([App.name, App.action]);
            }

            action = data && data.res && data.res.action ? data.res.action : App.action;
            eval(App.name+'.'+action)(data);

        }

    }

})();
