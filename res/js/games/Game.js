(function () {

    Game = {

        index: null,
        players: null,
        buttons: {
            start: {
                class: 'btn-primary btn-start',
                action: 'start',
                title: 'button-game-new'
            },
            replay: {
                class: 'btn-secondary',
                action: 'replay',
                title: 'button-game-replay'
            }
        },
        field: null,
        gameClass: "",

        init: function (init) {
        },

        isRun: function () {
            return App.uid;
        },

        run: function () {

            // отрисовка игроков

            if ((['move', 'timeout', 'pass'].indexOf(App.action) == -1 &&
                (App.action != 'ready' || Object.size(App.players) == App.current.length))
                || !document.querySelectorAll('.mx .players div').length) {

                D.log('Game.run', 'game');

                Game.field = document.getElementById('games-online-field');
                
                // запоминает классы при ините
                if(!Game.gameClass){
                    Game.gameClass = Game.field.className;
                }
                Game.field.className = (Game.gameClass+" "+App.key);

                DOM.hide(Game.field.parentNode.children);
                DOM.show(Game.field);

                var field = Game.field.getElementsByClassName('mx')[0];

                field.innerHTML = '<div class="players"></div>';

                Game.drawExit();
                Game.setPlayersDetailes(Game.seatPlayers());
                Game.setFullScreenHeigth();

                return true;

            } else
                return false;

        },

        hasField: function () {

            if (!document.getElementById('games-online-field')) {
                R.push({
                    'template': 'games-online-field',
                    'json': {},
                    'url': false,
                    'after': Apps[App.key].action
                });
                return false;

            } else
                return true;
        },

        drawExit: function () {
            if (App.action == 'ready' || App.action == 'wait' || App.action == 'stack') {
                
                $('.mx .players').append('<div class="back-button exit"><i class="i-arrow-slim-left"></i> <span>'+Cache.i18n('button-games-exit')+'</span> </div>');
                
            }
        },

        playerTimer: {

            add: function(index){
                this.index = (index = index || this.index);
                if (App.timestamp && timestamp != App.timestamp // Math.abs($($('#tm').countdown('getTimes')).get(-1)-App.timeout) > 2
                    || !$('.mx .players .player' + index + ' .gm-pr .pr-ph-bk .circle-timer').length) {

                    $('.mx .players .player' + index + ' .gm-pr .pr-ph-bk .circle, .mx .players .player' + index + ' .gm-pr .pr-ph-bk .circle-timer')
                        .remove();
                    $('.mx .players .player' + index + ' .gm-pr .pr-ph-bk')
                        .prepend('<div class="circle-timer"><div class="timer-r"></div><div class="timer-slot"><div class="timer-l"></div></div></div>')
                        .find('.timer-r,.timer-l')
                        .css('animation-duration', App.timeout + 's');
                }
                return this;
            },

            remove: function(index){
                this.index = (index = index || this.index);
                $('.mx .players .player' + index + ' .gm-pr .pr-ph-bk .circle, .mx .players .player' + index + ' .gm-pr .pr-ph-bk .circle-timer')
                    .remove();
                return this;
            },

            circle: function(index){
                this.index = (index = index || this.index);
                $('.mx .players .player' + index + ' .gm-pr .pr-ph-bk')
                    .prepend('<div class="circle"></div>');
                return this;
            },

            removeAll: function(){
                $('.mx .players .gm-pr .pr-ph-bk .circle-timer')
                    .remove();
            }
        },

        seatPlayers: function () {

            var players = App.players;

            D.log('Game.seatPlayers', 'game');
            if (players) {

                D.log('рассадили игроков');

                if (['wait', 'stack'].indexOf(App.action) !== -1) {

                    var player = {
                        "avatar": "",
                        "name"  : i18n('waiting...')
                    };

                    for (i = Object.size(players); i < App.playerNumbers; i++) {
                        index = 0 - i;
                        players[index] = player;
                    }
                }

                var orders = players;

                if (players[Player.id].order && App.action == 'start') {

                    orders = Object.keys(players);
                    var order = players[Player.id].order;

                    orders.sort(function (a, b) {
                        a = players[a].order;
                        b = players[b].order;
                        check = a == order ? 1 : (
                            b == order ? -1 : (
                                (a < order && b < order) || (a > order && b > order) ? (a - b) : (b - a)))
                        return check;
                    });

                }

                $.each(orders, function (index, value) {
                    value = typeof value == 'object' ? index : value;
                    div = '<div class="player' + value + (value == Player.id ? ' m' : ' o col' + (Object.size(players) - 1)) + '"></div>';
                    $('.mx .players').append(div);
                })

            }
            
            return players;

        },

        setPlayersDetailes: function (players) {

            D.log('Game.setPlayersDetailes', 'game');
            $.each(players, function (index, value) {

                value.avatar = index < 0
                    ? "url(/res/css/img/preloader.gif)"
                    : (value.avatar
                        ? "url('/filestorage/avatars/" + Math.ceil(parseInt(value.pid) / 100) + "/" + value.avatar + "')"
                        : "url('/res/img/default.jpg')");

                $('.mx .players .player' + index).append(
                    '<div class="gm-pr">' +
                    '<div class="pr-ph-bk">' +
                    '<div class="pr-ph" style="background-image: ' + value.avatar + '">' +
                    '<div class="mt"></div>' +
                    '<div class="wt"></div>' +
                    '<div class="pr-nm">' + value.name + '</div></div></div></div>');

                if (index == Player.id) {

                    bet = price = App.mode.split('-');
                    $('.mx .players .player' + index + ' .gm-pr')
                        .prepend(
                            '<div class="pr-cl">' +
                            '<div class="btn-pass">пас</div>' +
                            '<div class="msg-move">ваш ход</div>' +
                            '</div>')
                        .append(
                            '<div class="pr-md"><span class="cards-number"></span><i class="icon-reload"></i></div>' +
                            '<div class="pr-pr"><b>' + (bet[0] == 'MONEY' ? Player.formatCurrency(bet[1], 1) : bet[1]) + '</b><span>' + (bet[0] == 'MONEY' ? Player.formatCurrency(bet[1], 2) : 'баллов') + '</span></div>' +
                            '<div class="pr-pt"><div class="icon-wallet wallet"></div><div><span class="plMoneyHolder">' + Player.balance.money + '</span> ' + Player.formatCurrency() + '</div><div><span class="plPointHolder">' + Player.balance.points + '</span> баллов</div></div>');
                }
            });
        },

        end: function () {

            D.log('Game.end', 'game');
            if (!App.winner) {
                sample && Apps.playAudio([App.key, sample]);
                Game.updateTimeOut(App.timeout);
                return false;
            } else {
                sample = (isArray(App.winner) ? App.winner.indexOf(Player.id) != -1 : App.winner == Player.id) ? 'Win' : 'Lose';
                Apps.playAudio([App.key, sample]);
                Game.playerTimer.removeAll();
                Game.destroyTimeOut();
                
                var messages = {};
                for(var index in App.players) {
                    messages[index] =
                        (App.players[index].result > 0 ? 'Выигрыш' : 'Проигрыш') + '<br>' +
                        (App.currency == 'MONEY' ? Player.formatCurrency(App.players[index].win, 1) : parseInt(App.players[index].win)) + ' ' +
                        (App.currency == 'MONEY' ? Player.getCurrency() : 'баллов');
                }
                Game.drawMessages(messages);
                return true;
            }
        },

        drawButtons: function(buttons) {

            var playerButtons = document.querySelector('.mx .players .pr-cl'),
                html = '';

            for(var index in buttons){
                if(typeof  buttons[index] == 'object') {
                    html += '<button class="' + buttons[index].class + '" data-action="' + buttons[index].action + '">' + i18n(buttons[index].title) + '</button>';
                } else if(typeof buttons[index] == 'string') {
                    html += '<div>' + i18n(buttons[index]) + '</div>';
                }
            }
            playerButtons.innerHTML = html;
        },

        drawStatuses: function(statuses) {

            var playerStatuses = document.querySelectorAll('.mx .players .mt'),
                playerStatus = null,
                index = 0;

            for (; index < playerStatuses.length; index++) {
                playerStatuses[index].style.display = 'none';
            }

            for ( index in statuses ) {
                playerStatus = document.querySelector('.mx .players .player' + index + ' .mt');
                playerStatus.style.display = '';
                playerStatus.innerHTML = i18n(statuses[index]);
            }
        },

        drawMessages: function(messages) {

            var playerMessages = document.querySelectorAll('.mx .players .wt'),
                playerMessage = null,
                index = 0;

            for (; index < playerMessages.length; index++) {
                playerMessages[index].style.display = 'none';
            }

            for ( index in messages ) {
                playerMessage = document.querySelector('.mx .players .player' + index + ' .wt');
                playerMessage.style.display = '';
                playerMessage.innerHTML = messages[index];
                console.error(playerMessage, messages[index]);
            }
        },

        setFullScreenHeigth: function () {

            D.log('Game.setFullScreenHeigth', 'game');
            var singleGame = $('#games-online-field');
            if (singleGame.length > 0) {
                if (Device.isMobile()) {
                    $("meta[name='mobile-web-app-capable']").attr('content', 'yes');
                    $('main, .content-top').addClass('active_small_devices');
                    $('footer').addClass('unvisible');
                }
            }
        },

        destroyTimeOut: function () {
            $("#gameTimer").countdown('destroy');
        },

        updateTimeOut: function (time, format) {

            return false;

            if (time) {
                console.log('обновление таймаута');
                format = format || '{mnn}<span>:</span>{snn}';
                if (time < 1) {
                    Game.onTimeOut();
                } else {
                    var timer = $("#gameTimer");

                    if (timer.countdown('getTimes') && timer.countdown('getTimes').reduce(function (a, b) {
                            return a + b;
                        }) == 0) {
                        timer.countdown('destroy');
                    }

                    if (!timer.countdown('getTimes')) {
                        timer.countdown({until: time, layout: format, onExpiry: Game.onTimeOut});

                    } else if (!timer.countdown('option', 'layout') || timer.countdown('option', 'layout') != format)
                        timer.countdown('option', {layout: format});

                    timer.countdown({until: time}).countdown('resume').countdown('option', {until: time});


                }
            }
        },

        onTimeOut: function () {

            console.info('тайм-аут');
            Games.timer.removeAll();

            var path = 'app/' + App.key + '/' + App.uid,
                data = {'action': 'timeout'};
            WebSocketAjaxClient(path, data);
        },

        do: {

            ready: function (e) {

                D.log('Game.action.ready', 'game');

                price = App.mode.split('-');
                if ((price[0] == 'POINT' && Player.balance.points < parseInt(price[1])) || (price[0] == 'MONEY' && Player.balance.money < Player.formatCurrency(price[1], 1))) {

                    $("#report-popup").show().find(".txt").text(M.i18n('INSUFFICIENT_FUNDS')).fadeIn(200);

                } else {

                    var path = 'app/' + App.key + '/' + App.uid;
                    var data = {'action': 'ready'}
                    WebSocketAjaxClient(path, data);
                }
            },

            exit: function (e) {

                if (this.classList.contains('button-disabled'))
                    return false;
                else
                    this.classList.add('button-disabled');

                var that = this,
                    path = 'app/' + App.key + '/' + App.uid,
                    data = {
                        'action': 'quit'
                    };

                window.setTimeout(function () {
                    that.classList.remove('button-disabled');
                }, 1000);

                WebSocketAjaxClient(path, data);
            },

            pass: function (e) {

                D.log('Game.action.pass', 'game');
                var path = 'app/' + App.key + '/' + App.uid;
                var data = {
                    'action': 'pass'
                };
                WebSocketAjaxClient(path, data);
            },

            move: function (e) {

                var cell = this;

                if (App.players[Player.id].moves <= 0) {
                    //    $("#report-popup").find(".txt").text(getText('ENOUGH_MOVES'));
                    //    $("#report-popup").show().fadeIn(200);
                } else if (Player.id != App.current) {
                    //    $("#report-popup").find(".txt").text(getText('NOT_YOUR_MOVE'));
                    //    $("#report-popup").show().fadeIn(200);
                } else if (compare(cell.classList, ['m', 'o', 'b'])) {
                    //    $("#report-popup").find(".txt").text(getText('CELL_IS_PLAYED'));
                    //    $("#report-popup").show().fadeIn(200);
                } else {

                    cell.classList.add('b');
                    window.setTimeout(function () {
                        cell.classList.remove('b');
                    }, 1000);

                    var path = 'app/' + App.key + '/' + App.uid,
                        data = {
                            'action': 'move',
                            'cell'  : cell.getAttribute('data-cell')
                        };

                    WebSocketAjaxClient(path, data);
                }
            },

            button: function() {

                var path = 'app/' + App.key + '/' + App.uid,
                    data = {
                        'action': this.getAttribute('data-action')
                    };

                WebSocketAjaxClient(path, data);

            },

            start: function() {

                App.uid = 0;

                var path = 'app/' + App.key + '/' + App.uid,
                    data = {
                    'action': 'start',
                    'mode': App.mode
                };

                WebSocketAjaxClient(path, data);
            },

        },

        /* common callbacks without Apps */
        callback: {

            quit: function () {

                D.log('Game.action.quit', 'game');
                R.push('/games/online/' + App.id);
                App.uid = 0;
                Game.destroyTimeOut();
                Games.online.now();

            },

            update: function (data) {

                D.log('Game.callback.update', 'game');
                if (data.res.points)
                    Player.updatePoints(data.res.points);

                if (data.res.money)
                    Player.updateMoney(data.res.money);

                if (data.res.modes)
                    Apps.modes[data.res.key] = data.res.modes;

                if (data.res.variations)
                    Apps.variations[data.res.key] = data.res.variations;

                if (data.res.audio)
                    Apps.audio[data.res.key ? data.res.key : data.res.appName] = data.res.audio;

                if (data.res.key) {

                    $(".ngm-rls-bk .rls-r .rls-r-t .rls-r-t-rating .rls-r-t-rating-points").text(
                        (data.res.rating && data.res.rating.POINT ? data.res.rating.POINT : "0"));

                    $(".ngm-rls-bk .rls-r .rls-r-t .rls-r-t-rating .rls-r-t-rating-money").text(
                        (data.res.rating && data.res.rating.MONEY ? data.res.rating.MONEY : "0"));

                    $('.ngm-rls-bk .rls-r .rls-mn-bk .bt').removeClass('button-disabled').removeAttr('disabled');
                    $('.cell .bt').first().click();
                }

                if (data.res.fund) {
                    $('.prz-fnd-mon').text(data.res.fund && data.res.fund.MONEY ? Player.formatCurrency(data.res.fund.MONEY, 1) : 0);
                    $('.prz-fnd-pnt').text(data.res.fund && data.res.fund.POINT ? parseInt(data.res.fund.POINT) : 0);
                }
            },

            stack: function () {

                /* записались в стек - запустили игру */
                return Apps[App.key].action();

            },

            cancel: function () {

                /* отказ от ожидания в стеке */
                return Game.callback.quit();

            },


            back: function () {
                /* назад к играм */
                D.log('Game.callback.back', 'game');
                if (!$('.rls-r-t').is(':visible')) {
                    Game.cancel();
                }
                $('.ngm-bk').fadeOut(200);
                window.setTimeout(function () {
                    $('.ch-bk').fadeIn(200);
                }, 200);
            }
        },

        action: {

            reply: function () {

                /* повтор игры */
                var statuses = {};
                statuses[Player.id] = 'title-game-ready';

                Game.drawStatuses(statuses);
                Game.drawButtons([
                    Game.buttons.start,
                    'title-game-waiting-player'
                ]);
                Game.drawMessages();

                return;
            },


            error: function () {

                /* ошибка */

                D.log('Game.callback.error', 'game');

                if (App.uid == 0) {
                    Game.callback.quit();
                }
            },

            quit: function () {

                /* выход из игры */

                D.log('Game.callback.quit', 'game');
                if (App.quit != Player.id) {

                    Game.drawButtons([
                        Game.buttons.start,
                        'title-player-quit'
                    ]);

                } else
                    App.uid = 0;
            }
        }

    }

})();