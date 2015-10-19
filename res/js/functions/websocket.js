$(function () {

    step = 0;

    WebSocketAjaxClient = function (id) {

        if (isNumeric(id))
            step = id;
        else
            step++;

        data = Cache['games-game'][step];


        if (data.error)
            $("#report-popup").show().find(".txt").text(getText(data.error)).fadeIn(200);
        else {

            sample = null;

            path = data.path;
            if (data.res) {


                if (data.res.appId && data.res.appId != onlineGame.appId) {
                    onlineGame = {};
                } else if (onlineGame.winner) {
                    onlineGame['winner'] = null;
                    onlineGame['fields'] = null;
                }

                $.each(data.res, function (index, value) {
                    onlineGame[index] = value;
                });

                if (data.res.appName)
                    appName = data.res.appName;

                if (data.res.appMode)
                    appMode = data.res.appMode;

                if (data.res.appId) {
                    appId = data.res.appId;
                    data = null;
                }

                playAudio([appName, onlineGame.action]);
            }

            action = data && data.res && data.res.action ? data.res.action : onlineGame.action;
            eval(appName+'.'+action)(data);

        }

    }

});