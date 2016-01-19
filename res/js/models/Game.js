(function () {

    Game = {

        init: function (init) {
        },

        /**
         * @description проверяет класс ".single-game"
         * @returns {Boolean}
         */
        isRun: function () {
            return document.getElementsByClassName('games-online-game').length;
        },

        run: function () {

            // отрисовка игроков

            if ((['move', 'timeout', 'pass'].indexOf(App.action) == -1 &&
                (App.action != 'ready' || Object.size(App.players) == App.current.length))
                || !document.querySelectorAll('.mx .players div').length) {

                D.log('Game.run', 'game');

                var game = document.getElementById('games-online-field');

                DOM.hide(game.parentNode.children);
                DOM.show(game);

                var field = game.getElementsByClassName('mx')[0];

                field.classList.add(App.key);
                field.innerHTML =
                    '<div class="players"></div>' +
                    '<div class="deck"></div>' +
                    '<div class="table"></div>' +
                    '<div class="off"></div>';

                Game.seatPlayers();
                Game.setPlayersDetailes();
                Game.setFullScreenHeigth();
                return true;

            } else
                return false;

        },

        seatPlayers: function () {

            D.log('Game.seatPlayers', 'game');
            if (players = App.players) {
                D.log('рассадили игроков');
                if (App.action == 'wait') {

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

        },

        setPlayersDetailes: function () {

            D.log('Game.setPlayersDetailes', 'game');
            $.each(players, function (index, value) {
                value.avatar = index < 0 ? "url(../tpl/img/preloader.gif)" : (value.avatar ? "url('../filestorage/avatars/" + Math.ceil(parseInt(value.pid) / 100) + "/" + value.avatar + "')" : "url('/res/img/default.jpg')");
                $('.mx .players .player' + index).append(
                    '<div class="gm-pr">' +
                    '<div class="pr-ph-bk">' +
                    '<div class="pr-ph" style="background-image: ' + value.avatar + '">' +
                    '<div class="mt"></div>' +
                    '<div class="wt"></div>' +
                    '<div class="pr-nm">' + value.name + '</div></div></div></div>');

                if (index == Player.id) {
                    bet = price = App.mode.split('-');
                    $('.mx .players .player' + index + ' .gm-pr').prepend(
                        '<div class="pr-cl">' +
                        '<div class="btn-pass">пас</div>' +
                        '<div class="msg-move">ваш ход</div>' +
                        '</div>'
                    ).append(
                        '<div class="pr-md"><span class="cards-number"></span><i class="icon-reload"></i></div>' +
                        '<div class="pr-pr"><b>' + (bet[0] == 'MONEY' ? Player.formatCurrency(bet[1], 1) : bet[1]) + '</b><span>' + (bet[0] == 'MONEY' ? Player.formatCurrency(bet[1], 2) : 'баллов') + '</span></div>' +
                        '<div class="pr-pt"><div class="icon-wallet wallet"></div><div><span class="plMoneyHolder">' + Player.balance.money + '</span> ' + Player.formatCurrency() + '</div><div><span class="plPointHolder">' + Player.balance.points + '</span> баллов</div></div>'
                    );
                }
            });
        },

        end: function () {

            D.log('Game.end', 'game');
            if (!App.winner) {

                sample && Apps.playAudio([App.key, sample]);

            } else {

                if (!$('.mx .players .wt').is(":visible")) {

                    Apps.playAudio([App.key, ($.inArray(Player.id, App.winner) != -1 ? 'Win' : 'Lose')]);

                    $.each(App.players, function (index, value) {
                        $('.mx .players .player' + index + ' .wt').removeClass('loser').html(
                            (value.result > 0 ? 'Выигрыш' : 'Проигрыш') + '<br>' +
                            (App.currency == 'MONEY' ? Player.formatCurrency(value.win, 1) : parseInt(value.win)) + ' ' +
                            (App.currency == 'MONEY' ? Player.getCurrency() : 'баллов')
                        ).addClass(value.result < 0 ? 'loser' : '').fadeIn();
                    });

                    setTimeout(function () {

                        if ($('.mx .players .exit').is(":visible")) {
                            $('.mx .card, .mx .deck').fadeOut();
                            $('.mx .players .wt').fadeOut();
                        }

                    }, 5000);
                }

            }
        },

        setFullScreenHeigth: function () {

            D.log('Game.setFullScreenHeigth', 'game');
            var singleGame = $('.single-game');
            if (singleGame.length > 0) {

                $("meta[name='mobile-web-app-capable']").attr('content', 'yes');
                $('main, .content-top').addClass('active_small_devices');
                $('footer').addClass('unvisible');
            }
        },

        destroyTimeOut: function () {
            $("#gameTimer").countdown('destroy');
        },

        updateTimeOut: function (time, format) {
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
            $('.mx .players .gm-pr .pr-ph-bk .circle-timer').remove();
            var path = 'app/' + App.key + '/' + App.uid;
            var data = {'action': 'timeout'};
            WebSocketAjaxClient(path, data);
        },

        do: {

            ready: function (e) {

                D.log('Game.action.ready', 'game');

                price = App.mode.split('-');
                if ((price[0] == 'POINT' && Player.balance.points < parseInt(price[1])) || (price[0] == 'MONEY' && Player.balance.money < Player.formatCurrency(price[1], 1))) {

                    $("#report-popup").show().find(".txt").text(M.i18n('INSUFFICIENT_FUNDS')).fadeIn(200);

                } else {

                    var path = 'app/' + App.key + '/' + App.uid ;
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
                    path = 'app/' + App.key + '/' + App.uid ,
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
                var path = 'app/' + App.key + '/' + App.uid ;
                var data = {
                    'action': 'pass'
                };
                WebSocketAjaxClient(path, data);
            }
        },

        callback: {

            quit: function () {

                D.log('Game.action.quit', 'game');
                R.push('/games/online/'+App.id );
                App.uid  = 0;
                WebSocketAjaxClient('update/' + App.key);
                Game.destroyTimeOut();

            },

            // error: function (data) {

            //     alert(data.res.error);

            // },

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

                D.log('Game.callback.stack', 'game');
                if ($('.rls-r-t').is(':visible')) {
                    /*
                     * постановка в стек
                     * */
                    $('.rls-r-ts').show();
                    $('.rls-r-t').hide();
                    $('.prc-but-cover').show();
                } else {
                    /*
                     * выход из игры при другом сопернике
                     * */
                    $('.ngm-gm .gm-mx .msg.winner .re').hide();
                    $('.ngm-gm .gm-mx .msg.winner .ch-ot').hide();
                    $('.ot-exit').html('Ожидаем соперника').show();
                }
            },

            cancel: function () {
                /* отказ от ожидания в стеке */
                D.log('Game.callback.cancel', 'game');
                $('.rls-r-ts').hide();
                $('.rls-r-t').show();
                $('.prc-but-cover').hide();
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
                /* повтор игры
                 * */
                D.log('Game.callback.reply', 'game');
                $('.re').hide();
                $('.ot-exit').html('Ожидаем соперника').show();
                return;
            },


            error: function () {
                /* ошибка */
                D.log('Game.callback.error', 'game');
                if (App.uid  == 0) {
                    $('.mx .tm').countdown('pause');
                    $('.mx .prc-but-cover').hide();
                    $('.ngm-rls').fadeIn(200);
                }
            },

            quit: function () {
                /* выход из игры */
                D.log('Game.callback.quit', 'game');
                if (App.quit != Player.id) {
                    $('.re').hide();
                    $('.ot-exit').html('Соперник вышел').show();
                } else
                    App.uid  = 0;
            }
        }

    }

})();