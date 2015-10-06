$(function () {


    /* ========================================================= */
    //                        TICKETS
    /* ========================================================= */

    function activateTicket() {

        setBallsMargins();
        $('.ticket-item .ticket-random').off().on('click', clickTicketRandom);
        $('.ticket-item .ticket-favorite').off().on('click', clickTicketFavorite);
        $('.ticket-item .ball-number').off().on('click', clickTicketBall);
        $('.ticket-item .ticket-favorite .after i').off().on('click', function () {
            $('.profile .ul_li[data-link="profile-info"]').click();
        });
        $('.ticket-item .add-ticket').on('click', addTicket);
    }

    renderTicket = function () {

        D.log('renderTicket');

        if (Tickets.isComplete()) {

            $Box = $('.ticket-items').parent();

            R.render({
                "template": 'lottery-ticket-complete',
                "json": Tickets,
                "url": false
            });

        } else {

            $Box = $('.ticket-items');

            R.render({
                "template": 'lottery-ticket-tabs',
                "json": Tickets,
                "url": false,
                "callback": function () {
                    $(I.TicketTabs).not('.done').first().click();
                }
            });

        }

    }

    switchTicket = function () {

        D.log('switchTicket');

        if (Tickets.isComplete()) {

            $Box = $('.ticket-items').parent();

            R.render({
                "template": 'lottery-ticket-complete',
                "json": Tickets,
                "url": false
            });

        } else {

            $Box = $('.ticket-items');
            $Tab = $(this).is('li')
                ? $(this)
                : ($(I.TicketTabs).not('.done').first() || $(I.TicketTabs).first());


            $(I.TicketTabs).removeClass('active');
            $($Tab).addClass('active');

            R.render({
                "template": 'lottery-ticket-item',
                "json": Tickets,
                "url": false,
                "callback": activateTicket
            });

        }
    }

    function clickTicketRandom(e) {

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
            lotInterval = window.setInterval(randomTicketBalls, 200);

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

    }

    function clickTicketFavorite() {
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

    }

    function clickTicketBall() {

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
    }

    function addTicket() {

        if ($(this).hasClass('on') && !$(this).data('disabled')) {

            $Box = $('.ticket-items');
            $Button = $(this);
            $Button.data('disabled', true);

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
                        switchTicket();
                    } else {
                        throw(data.message);
                    }
                }, "json"
            );

        }
    }

    function randomTicketBalls() {

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

    }


    /* ========================================================= */
    /* ========================================================= */


    // TICKET ================================= //

    TicketsFunctions = {
        "ballsHTML": function () {
            var html = '';
            for (i = 1; i <= this.totalBalls; i++) {

                html += "<li class='ball-number number-" + i + ($.inArray(i, this.balls[$(I.TicketTabs).filter('.active').data('ticket')]) == -1 ? '' : ' select') + "'>" + i + "</li>";
            }
            return html;

        },

        "tabsHTML": function () {
            var html = '';
            for (i = 1; i <= this.totalTickets; i++) {
                html += "<li data-ticket='" + i + "' class='" + (this.balls && this.balls[i] ? 'done' : '') + "'><span>" + M.i18n('title-ticket') + " </span>#" + i + "</li>";
            }
            return html;
        },

        "isDone": function () {
            return (this.balls && this.balls[$(I.TicketTabs).filter('.active').data('ticket')] && this.balls[$(I.TicketTabs).filter('.active').data('ticket')].length && this.balls[$(I.TicketTabs).filter('.active').data('ticket')].length == this.selectedBalls);
        },

        "isComplete": function () {
            return (this.balls && Object.keys(this.balls).length == this.totalTickets);
        },

        "completeHTML": function () {

            var html = '';

            $.each(this.balls, function (index, ticket) {
                html += "<ul class='ticket-result'><li class='ticket-number-result'><span>БИЛЕТ</span> #" + index + "";
                $.each(ticket, function (number, ball) {
                    html += "<li class='ball-number-result'>" + ball + "</li>";
                });
                html += "</ul>";
            });

            return html;
        }
    };
});