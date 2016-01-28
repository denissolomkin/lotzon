$(function () {

    Durak = {

        run: function () {

            Cards.createCardsWrapper();
            Cards.setVariation();
            Cards.emptyFields();
            Cards.drawTrump();

        },

        action: function () {

            if (!document.getElementById('games-online-field')) {

                R.push({
                    'template': 'games-online-field',
                    'json'    : {},
                    'url'     : false,
                    'after'   : Durak.action
                });

            } else {

                Game.run() && Durak.run();
                Cards.setupForDevices();
                Cards.drawFields();
                Cards.premove();
                Cards.initStatuses();
                Game.end() && Durak.end();
            }

        },

        end: function(){

            if (!$('.mx .players .wt').is(":visible")) {

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
        },

        error: function () {

            Drag.rollback();
            Cards.premove();

        }

    }

});