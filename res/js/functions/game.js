$(function () {

    Game = {

        stack: function () {
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

        cancel: function () {
            /*
             *  отказ от ожидания в стеке
             */
            $('.rls-r-ts').hide();
            $('.rls-r-t').show();
            $('.prc-but-cover').hide();
        },

        run: function () {
            $('.ngm-rls').fadeOut(200);
            if ((   $.inArray(onlineGame.action, ['move', 'timeout', 'pass']) == -1 &&
                (onlineGame.action != 'ready' || Object.size(onlineGame.players) == onlineGame.current.length)
                )
                || !$('.mx .players').children('div').length) {
                $('.mx').html($('.mx-tmpl').html());
                Game.seatPlayers();
                Game.setPlayersDetailes();
                Game.setFullScreenHeigth();

            }

        },

        quit: function () {

            appId = 0;
            WebSocketAjaxClient('update/' + appName);
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

        back: function () {

            if (!$('.rls-r-t').is(':visible')) {
                Game.cancel();
            }

            $('.ngm-bk').fadeOut(200);
            window.setTimeout(function () {
                $('.ch-bk').fadeIn(200);
            }, 200);
        },

        update: function (receiveData) {

            if (receiveData.res.points)
                Balance.updatePoints(receiveData.res.points);

            if (receiveData.res.money)
                Balance.updateMoney(receiveData.res.money);

            if (receiveData.res.modes)
                OnlineGames.Modes[receiveData.res.key] = receiveData.res.modes;

            if (receiveData.res.variations)
                OnlineGames.Variations[receiveData.res.key] = receiveData.res.variations;

            if (receiveData.res.audio)
                OnlineGames.Audio[receiveData.res.key ? receiveData.res.key : receiveData.res.appName] = receiveData.res.audio;

            if (receiveData.res.key) {

                $(".ngm-rls-bk .rls-r .rls-r-t .rls-r-t-rating .rls-r-t-rating-points").text(
                    (receiveData.res.rating && receiveData.res.rating.POINT ? receiveData.res.rating.POINT : "0"));

                $(".ngm-rls-bk .rls-r .rls-r-t .rls-r-t-rating .rls-r-t-rating-money").text(
                    (receiveData.res.rating && receiveData.res.rating.MONEY ? receiveData.res.rating.MONEY : "0"));

                $('.ngm-rls-bk .rls-r .rls-mn-bk .bt').removeClass('button-disabled').removeAttr('disabled');
                $('.cell .bt').first().click();
            }

            if (receiveData.res.fund) {
                $('.prz-fnd-mon').text(receiveData.res.fund && receiveData.res.fund.MONEY ? Player.getCurrency(receiveData.res.fund.MONEY, 1) : 0);
                $('.prz-fnd-pnt').text(receiveData.res.fund && receiveData.res.fund.POINT ? parseInt(receiveData.res.fund.POINT) : 0);
            }
        },

        error: function () {

            if (onlineGame.appId == 0) {
                $('.mx .tm').countdown('pause');
                appId = onlineGame.appId;
                $('.mx .prc-but-cover').hide();
                $('.ngm-rls').fadeIn(200);
            }
        },

        seatPlayers: function () {

            if (players = onlineGame.players) {
                D.log('рассадили игроков');
                if (onlineGame.action == 'wait') {

                    var player = {
                        "avatar": "",
                        "name": "ждем..."
                    };

                    for (i = Object.size(players); i < onlineGame.playerNumbers; i++) {
                        index = 0 - i;
                        players[index] = player;
                    }
                }

                var orders = players;

                if (players[Player.id].order && onlineGame.action == 'start') {

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
                    bet = price = onlineGame.appMode.split('-');
                    $('.mx .players .player' + index + ' .gm-pr').prepend(
                        '<div class="pr-cl">' +
                        '<div class="btn-pass">пас</div>' +
                        '<div class="msg-move">ваш ход</div>' +
                        '</div>'
                    ).append(
                        '<div class="pr-md"><span class="cards-number"></span><i class="icon-reload"></i></div>' +
                        '<div class="pr-pr"><b>' + (bet[0] == 'MONEY' ? Player.getCurrency(bet[1], 1) : bet[1]) + '</b><span>' + (bet[0] == 'MONEY' ? Player.getCurrency(bet[1], 2) : 'баллов') + '</span></div>' +
                        '<div class="pr-pt"><div class="icon-wallet wallet"></div><div><span class="plMoneyHolder">' + Player.balance.money + '</span> ' + Player.getCurrency() + '</div><div><span class="plPointHolder">' + Player.balance.points + '</span> баллов</div></div>'
                    );
                }
            });
        },

        variations: function () {

            if (onlineGame.variation && onlineGame.variation.type && onlineGame.variation.type == 'revert')
                $('.cards > .mx').addClass('Revert');
            if (onlineGame.variation && onlineGame.variation.cards)
                $('.pr-md .cards-number').html(onlineGame.variation.cards);


        },

        end: function () {
            if (!onlineGame.winner) {

                sample && playAudio([appName, sample]);

            } else {

                if (!$('.mx .players .wt').is(":visible")) {

                    playAudio([appName, ($.inArray(Player.id, onlineGame.winner) != -1 ? 'Win' : 'Lose')]);

                    $.each(onlineGame.players, function (index, value) {
                        $('.mx .players .player' + index + ' .wt').removeClass('loser').html(
                            (value.result > 0 ? 'Выигрыш' : 'Проигрыш') + '<br>' +
                            (onlineGame.currency == 'MONEY' ? Player.getCurrency(value.win, 1) : parseInt(value.win)) + ' ' +
                            (onlineGame.currency == 'MONEY' ? Player.getCurrency() : 'баллов')
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

            var singleGame = $('.single-game');
            if (singleGame.length > 0) {

                $("meta[name='mobile-web-app-capable']").attr('content', 'yes');
                $('main').addClass('active_small_devices');
                $('footer').addClass('unvisible');
            }
        },

        updateTimeOut: function (time, format) {
            return;
        }

    }

});