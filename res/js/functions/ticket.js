$(function () {


    /* ========================================================= */
    //                        TICKETS
    /* ========================================================= */

    Ticket = {
        activate: function () {

            D.log('activateTicket');

            Ticket.setBallsMargins();
            $('.ticket-item .ticket-random').off().on('click', Ticket.clickRandom);
            $('.ticket-item .ticket-favorite').off().on('click', Ticket.clickFavorite);
            $('.ticket-item .ball-number').off().on('click', Ticket.clickBall);
            $('.ticket-item .ticket-favorite .after i').off().on('click', function () {
                $('.profile .ul_li[data-link="profile-info"]').click();
            });
            $('.ticket-item .add-ticket').on('click', Ticket.add);
        },

        render: function () {

            D.log('renderTicket');

            if (Tickets.isComplete()) {

                this.complete();

            } else {

                var box = $('.ticket-items');

                R.push({
                    box: box,
                    template: 'lottery-ticket-tabs',
                    json: Tickets,
                    url: false,
                    callback: function () {
                        $(I.TicketTabs).not('.done').first().click();
                    }
                });

            }

        },

        switch: function () {

            D.log('switchTicket');

            if (Tickets.isComplete()) {

                this.complete();

            } else {

                var box = $('.ticket-items'),
                    tab = $(this).is('li')
                        ? $(this)
                        : ($(I.TicketTabs).not('.done').first() || $(I.TicketTabs).first());

                $(I.TicketTabs).removeClass('active');
                $(tab).addClass('active');

                R.push({
                    tab: tab,
                    box: box,
                    template: 'lottery-ticket-item',
                    json: Tickets,
                    url: false,
                    callback: Ticket.activate
                });

            }
        },

        complete: function () {

            D.log('ticketComplete');
            var box = $('.ticket-items').parent();

            R.push({
                box: box,
                template: 'lottery-ticket-complete',
                json: Tickets,
                url: false
            });

        },

        clickRandom: function (e) {

            if ($(e.target).hasClass('after'))
                return false;

            if (!$(this).hasClass('select')) {

                var after = $(this).find('.after');
                after.fadeIn(300);

                setTimeout(function () {
                    after.fadeOut(300);
                }, 2000);

                if ($('.ticket-favorite .after:visible').length)
                    $('.ticket-favorite .after').fadeOut(150);

                if ($('.ticket-balls li.select').length > 0) {
                    $('.ticket-balls li.select').removeClass('select');
                }

                var lotInterval;
                lotInterval = window.setInterval(Ticket.randomBalls, 200);

                window.setTimeout(function () {
                    window.clearInterval(lotInterval);
                    if ($('.ticket-balls li.select').length == Tickets.selectedBalls) {
                        $('.add-ticket').addClass('on');
                    }
                }, 1000);

                $('.balls-count').hide();
                $(this).addClass('select');

            } else {
                $('.ticket-actions, .ticket-item').find('li.select').removeClass('select');

                if ((6 - $('.ticket-balls li.select').length) > 0) {
                    $('.balls-count').show();
                    $('.balls-count b').html(6 - $('.ticket-balls li.select').length);
                    $('.add-ticket').removeClass('on');
                } else {

                    $('.balls-count').hide();
                    $('.add-ticket').addClass('on');
                }
            }

        },

        clickFavorite: function () {
            if (!$(this).hasClass('select')) {

                if ($('.ticket-random .after:visible').length)
                    $('.ticket-random .after').fadeOut(150);

                if ($('.ticket-item li.select').length > 0) {
                    $('.ticket-item li.select').removeClass('select');
                }

                if (Player.favorite.length) {
                    for (var i = 0; i <= 5; ++i) {
                        $('.ticket-balls .number-' + Player.favorite[i]).addClass('select');
                    }
                    $(this).addClass('select');
                    $('.balls-count b').html(0);
                    $('.add-ticket').addClass('on');
                } else {
                    if ($(this).find('.after:hidden').length) {
                        $(this).find('.after').fadeIn(200);
                    } else {
                        $(this).find('.after').fadeOut(200);
                    }
                }
            } else {
                $('.ticket-item li.select').removeClass('select');
            }

            if ((6 - $('.ticket-balls li.select').length) > 0) {
                $('.balls-count').show();
                $('.balls-count b').html(6 - $('.ticket-balls li.select').length);
                $('.add-ticket').removeClass('on');
            } else {

                $('.balls-count').hide();
                $('.add-ticket').addClass('on');
            }

        },

        clickBall: function () {

            $('.ticket-favorite .after:visible').fadeOut(300);

            if ($('.tb-tabs_li[data-ticket="' + $('.ticket-balls').data('ticket') + '"]').hasClass('done')) {
                return;
            }

            if ($('.ticket-balls li.select').length == 6) {
                if (!$(this).hasClass('select')) {
                    return;
                }
            }

            if (!$(this).hasClass('ticket-random') && !$(this).hasClass('ticket-favorite')) {
                if (!$(this).hasClass('select')) {
                    var lim = $('.ticket-balls li.select').length;
                    var sel = 5 - lim;
                    if (lim < 6) {
                        $(this).addClass('select');
                        $('.balls-count b').html(sel);
                        if (lim == 5) {
                            $('.balls-count').hide();
                            $('.add-ticket').addClass('on');
                        }
                    }
                } else {
                    var lim = $('.ticket-balls li.select').length;
                    var sel = 6 - lim + 1;
                    $(this).removeClass('select');
                    $('.balls-count b').html(sel);
                    $('.balls-count').show();
                    $('.add-ticket').removeClass('on');
                }
            } else {
                var lim = $('.ticket-balls li.select').length;
                var sel = 6 - lim + 1;
                $(this).removeClass('select');
                $('.balls-count b').html(sel);
                $('.balls-count').show();
                $('.add-ticket').removeClass('on');
            }
        },

        add: function () {

            if ($(this).hasClass('on') && !$(this).hasClass('waiting')) {

                var ticket = {
                    "combination": [],
                    "tickNum": null
                };

                $('.ticket-balls li.select').each(function (id, li) {
                    ticket.combination.push(parseInt($(li).text()));
                });

                ticket.tickNum = $(I.TicketTabs + '.active').data('ticket');

                $.post(
                    U.Generate.Post('/lottery/addTicket'),
                    ticket,
                    function (data) {
                        if (data.status == 1) {
                            Tickets.balls[ticket.tickNum] = ticket.combination;
                            $(I.TicketTabs + '.active').addClass('done');
                            Ticket.switch();
                        } else {
                            throw(data.message);
                        }
                    }, "json"
                );

            }
        },

        randomBalls: function () {

            if ($('.ticket-balls').find('li.select').length > 0) {
                $('.ticket-balls').find('li.select').removeClass('select');
            }

            var ticketCache = [];

            do {
                do {
                    rand = Math.floor((Math.random() * 49) + 1);
                } while ($.inArray(rand, ticketCache) > -1);
                ticketCache.push(rand);

            } while (ticketCache.length != 6);

            $(ticketCache).each(function (id, num) {
                $('.ticket-balls').find('.number-' + num).addClass('select');
            });

        },

        setBallsMargins: function () {

            if (Device.get() < 0.6) {
                var ticketBox = $('.ticket-item');
                var ticketBalls = $('.ticket-numbers li');
                var ticketActions = $('.ticket-actions li');
                var ticketNumbersBox = $('.ticket-numbers');

                var result = this.getBallsMargins(ticketBox, ticketBalls);
                var margin = result.margin;
                var padding = result.padding;

                ticketBox.css({
                    'padding-left': padding + 'px',
                    'padding-right': padding + 'px'
                });

                ticketNumbersBox.css('margin-right', -margin + 'px');

                ticketBalls.css({
                    'margin-right': margin + 'px',
                    'margin-bottom': 0.7 * margin + 'px'
                });

                ticketActions.css({
                    'margin-right': margin + 'px'
                });
            }
        },

        getBallsMargins: function (box, balls) {

            var boxWidth = box.outerWidth();
            var ballWidth = balls.outerWidth();
            var margin, padding, count;

            if (boxWidth < 7 * 1.8 * ballWidth || boxWidth / 1.8 * ballWidth < 8) {
                margin = Math.floor((boxWidth - 7 * ballWidth) / 7);
                padding = margin / 2 + (boxWidth - 7 * ballWidth - 7 * margin) / 2;
            }
            else {
                count = Math.floor(boxWidth / (1.8 * ballWidth));
                margin = Math.floor((boxWidth - count * ballWidth) / (count));
                padding = margin / 2 + (boxWidth - count * ballWidth - (count) * margin) / 2;
            }

            return {
                margin: margin,
                padding: padding
            };
        }
    }


});