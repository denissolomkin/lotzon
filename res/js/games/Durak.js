$(function () {

    Apps.Durak = {

        run: function () {

            Cards.createCardsWrapper();
            Cards.setVariation();
            Cards.emptyFields();
            Cards.drawTrump();

        },

        action: {

            default: function () {

                if (Game.hasField()) {
                    Game.run() && Apps.Durak.run();
                    Cards.setupForDevices();
                    Cards.drawFields();
                    Cards.premove();
                    Cards.initStatuses();
                    Game.drawExit();
                    Game.end() && Apps.Durak.end();
                }
            },

            error: function () {

                Drag.rollback();
                Cards.premove();

            }
        },

        end: function(){

            if (!$('.mx .players .wt').is(":visible")) {

                setTimeout(function () {

                    if ($('.mx .players .exit').is(":visible")) {
                        $('.mx .card, .mx .deck').fadeOut();
                        $('.mx .players .wt').fadeOut();
                    }

                }, 5000);
            }
        }

    }

});