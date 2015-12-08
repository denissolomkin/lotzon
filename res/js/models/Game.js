$(function () {

    Game = {
        init: function (init) {
        },
        /**
         * @description проверяет класс ".single-game"
         * @returns {Boolean}
         */
        isRun: function () {
            return document.getElementsByClassName('.single-game').length;
        },
        run: function () { // отрисовка игроков

            Player.id = 37;

            D.log('Game.run', 'game');
            $('.tmp_but').html(Device.detect() + Device.get() + document.documentElement.clientWidth);
            $(document).on('click', '.tmp_but', function (e) {
                if (Device.get() <= 0.6)
                    Device.toggleFullScreen();
            });

            $('.ngm-rls').fadeOut(200);

            if (($.inArray(App.action, ['move', 'timeout', 'pass']) == -1 &&
                    (App.action != 'ready' || Object.size(App.players) == App.current.length)
                    ) || !$('.mx .players').children('div').length) {

                $('.mx').html('<div class="players"></div>' +
                        '<div class="deck"></div>' +
                        '<div class="table"></div>' +
                        '<div class="off"></div>');

                Game.seatPlayers();
                Game.setPlayersDetailes();
                Game.setFullScreenHeigth();
            }

        },
        seatPlayers: function () {

            D.log('Game.seatPlayers', 'game');
            if (players = App.players) {
                D.log('рассадили игроков');
                if (App.action == 'wait') {

                    var player = {
                        "avatar": "",
                        "name": "ждем..."
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

                sample && Apps.playAudio([App.name, sample]);

            } else {

                if (!$('.mx .players .wt').is(":visible")) {

                    Apps.playAudio([App.name, ($.inArray(Player.id, App.winner) != -1 ? 'Win' : 'Lose')]);

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
        updateTimeOut: function (time, format) {
            D.log('Game.updateTimeOut', 'game');
            return;
        },
        do: {
            quit: function () {
                D.log('Game.action.quit', 'game');

                App.id = 0;
                WebSocketAjaxClient('update/' + App.name);
                $('.tm').countdown('pause');


                if ($('.ngm-gm .gm-mx .msg.winner .button.exit').hasClass('button-disabled')) {

                    /*
                     *  выход из игры и возврат к правилам
                     */

                    $('.ngm-gm .gm-mx .msg.winner .button').removeClass('button-disabled').removeAttr('disabled');

                } else if ($('.ngm-gm .gm-mx .players .exit').is(":visible")) {

                    /*
                     *  выход из игры и возврат к правилам
                     */

                    $('.ngm-gm .gm-mx .players .exit').removeClass('button-disabled');
                }

                $('.rls-r-ts').hide();
                $('.rls-r-t').show();
                $('.prc-but-cover').hide();
                $('.ngm-rls').fadeIn(200);
            },
            ready: function (e) {

                D.log('Game.action.ready', 'game');

                price = App.mode.split('-');
                if ((price[0] == 'POINT' && Player.balance.points < parseInt(price[1])) || (price[0] == 'MONEY' && Player.balance.money < Player.formatCurrency(price[1], 1))) {

                    $("#report-popup").show().find(".txt").text(M.i18n('INSUFFICIENT_FUNDS')).fadeIn(200);

                } else {

                    var path = 'app/' + App.name + '/' + App.id;
                    var data = {'action': 'ready'}
                    WebSocketAjaxClient(path, data);
                }
            },
            pass: function (e) {

                D.log('Game.action.pass', 'game');
                var path = 'app/' + App.name + '/' + App.id;
                var data = {
                    'action': 'pass'
                }

                WebSocketAjaxClient(path, data);
            }
        },
        callback: {
            update: function (receiveData) {

                D.log('Game.callback.update', 'game');
                if (receiveData.res.points)
                    Player.updatePoints(receiveData.res.points);

                if (receiveData.res.money)
                    Player.updateMoney(receiveData.res.money);

                if (receiveData.res.modes)
                    Apps.Modes[receiveData.res.key] = receiveData.res.modes;

                if (receiveData.res.variations)
                    Apps.Variations[receiveData.res.key] = receiveData.res.variations;

                if (receiveData.res.audio)
                    Apps.Audio[receiveData.res.key ? receiveData.res.key : receiveData.res.appName] = receiveData.res.audio;

                if (receiveData.res.key) {

                    $(".ngm-rls-bk .rls-r .rls-r-t .rls-r-t-rating .rls-r-t-rating-points").text(
                            (receiveData.res.rating && receiveData.res.rating.POINT ? receiveData.res.rating.POINT : "0"));

                    $(".ngm-rls-bk .rls-r .rls-r-t .rls-r-t-rating .rls-r-t-rating-money").text(
                            (receiveData.res.rating && receiveData.res.rating.MONEY ? receiveData.res.rating.MONEY : "0"));

                    $('.ngm-rls-bk .rls-r .rls-mn-bk .bt').removeClass('button-disabled').removeAttr('disabled');
                    $('.cell .bt').first().click();
                }

                if (receiveData.res.fund) {
                    $('.prz-fnd-mon').text(receiveData.res.fund && receiveData.res.fund.MONEY ? Player.formatCurrency(receiveData.res.fund.MONEY, 1) : 0);
                    $('.prz-fnd-pnt').text(receiveData.res.fund && receiveData.res.fund.POINT ? parseInt(receiveData.res.fund.POINT) : 0);
                }
            },
            stack: function () {

                D.log('Game.callback.stack', 'game');
                if ($('.rls-r-t').is(':visible')) {
                    /*
                     *  постановка в стек
                     */
                    $('.rls-r-ts').show();
                    $('.rls-r-t').hide();
                    $('.prc-but-cover').show();
                } else {
                    /*
                     *  выход из игры при другом сопернике
                     */
                    $('.ngm-gm .gm-mx .msg.winner .re').hide();
                    $('.ngm-gm .gm-mx .msg.winner .ch-ot').hide();
                    $('.ot-exit').html('Ожидаем соперника').show();
                }
            },
            reply: function () {
                D.log('Game.callback.reply', 'game');
                $('.re').hide();
                $('.ot-exit').html('Ожидаем соперника').show();
                return;
            },
            stack: function () {
                D.log('Game.callback.stack', 'game');
                return;
            },
                    quit: function () {
                        D.log('Game.callback.quit', 'game');
                        if (App.quit != Player.id) {
                            $('.re').hide();
                            $('.ot-exit').html('Соперник вышел').show();
                        } else
                            App.id = 0;
                    },
            cancel: function () {
                D.log('Game.callback.cancel', 'game');
                /*
                 *  отказ от ожидания в стеке
                 */
                $('.rls-r-ts').hide();
                $('.rls-r-t').show();
                $('.prc-but-cover').hide();
            },
            back: function () {

                D.log('Game.callback.back', 'game');
                if (!$('.rls-r-t').is(':visible')) {
                    Game.cancel();
                }

                $('.ngm-bk').fadeOut(200);
                window.setTimeout(function () {
                    $('.ch-bk').fadeIn(200);
                }, 200);
            },
            error: function () {

                D.log('Game.callback.error', 'game');
                if (App.id == 0) {
                    $('.mx .tm').countdown('pause');
                    $('.mx .prc-but-cover').hide();
                    $('.ngm-rls').fadeIn(200);
                }
            }
        }

    }

});