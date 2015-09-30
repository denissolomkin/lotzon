$(function () {

    // DETECT DEVICE ========================== //

    detectDevice = function () {

        switch ($('.js-detect').css('opacity')) {
            case '0.1':
                return 'mobile';
            case '0.2':
                return 'tablet';
            case '0.3':
                return 'desktop';
            case '0.4':
                return 'desktop-hd';
        }
    }
    // ======================================= //

    menuMobile = function () {
        var device = detectDevice();
        var menuMobile = (device === 'mobile' || device === 'tablet') ? true : false;
        return menuMobile;
    }


    // OWL CAROUSEL =========================== //
    var $matchesCarousel = $('.matches-inf-wrapper.carousel');
    var owl = null;

    if ($matchesCarousel.css('box-shadow') !== 'none') {
        $matchesCarousel.owlCarousel({
            singleItem: true,
            autoPlay: false
        });

        var owl = $matchesCarousel.data('owlCarousel');
    }
    // ======================================== //

    // handler functions
    loadPage = function (event) {

        if (!event.isPropagationStopped()) {

            event.stopPropagation();
            $Box = $('.content-top');
            $Tab = $(this);

            R.render({
                "json": {}
            });

        }
        return false;

    }

    loadBlock = function (event) {

        if (!event.isPropagationStopped()) {

            event.stopPropagation();
            $Box = $(this).parents('.content-main');
            $Tab = $(this);
            D.log(['loadBlock:', $Tab.attr('href')]);

            R.render({
                "callback": function (rendered, findClass) {
                    $(findClass).addClass('slideInRight');
                }
            });

        }
        return false;

    }

    backBlock = function (event) {

        if (!event.isPropagationStopped()) {

            event.stopPropagation();
            $Box = $(this).parents('.content-box');
            $Tab = $(this);

            D.log(['backBlock:', $Tab.attr('href')]);

            $($Tabs + '.active', $Box.prev()).click();
            $Box.prev().addClass('slideInLeft').show().next().remove();

        }

        return false;

    }

    switchTab = function (event) {

        if (!event.isPropagationStopped()) {

            event.stopPropagation();
            $Box = $(this).parents('.content-box').find('.content-box-content');
            $Tab = $(this);
            $Href = $Tab.attr('href');

            D.log(['switchTab:', $Href]);

            if (U.isAnchor($Href)) {

                $($Tabs, $Tab.parents('.content-box-header')).removeClass('active');
                $(' > div', $Box).hide();
                $('.content-box-item.' + $Href, $Box).show();
                $Tab.addClass('active');

            } else {
                R.render();
            }
        }

        return false;
    }

    switchCat = function (event) {

        if (!event.isPropagationStopped()) {

            event.stopPropagation();
            $Cat = $(this);
            D.log(['switchCat:', $Cat.attr('href')]);

            // with animation
            if ($($Cats, $Box).filter('.active').length) {

                $($Cats, $Box).removeClass('active');
                $('.content-box-item-content > div', $Box).fadeOut(200);
                setTimeout(function () {
                    $('.content-box-item-content > div.category-' + $Cat.data('category'), $Box).fadeIn(200);
                }, 200);

                // without animation
            } else {
                $('.content-box-item-content > div', $Box).hide();
                $('.content-box-item-content > div.category-' + $Cat.data('category'), $Box).show();
            }

            $Cat.addClass('active');

        }

        return false;

    }

    /* ========================================================= */
    /* ========================================================= */



    /* ========================================================= */
    //                        TICKETS
    /* ========================================================= */

    renderTicket = function () {

        D.log('renderTicket');

        if ($Tickets.isComplete()) {

            $Box = $('.ticket-items').parent();

            R.render({
                "template": 'ticket-complete',
                "json": $Tickets,
                "url": false
            });

        } else {

            $Box = $('.ticket-items');

            R.render({
                "template": 'ticket-tabs',
                "json": $Tickets,
                "url": false,
                "callback": function () {
                    $($TicketTabs).not('.done').first().click();
                }
            });

        }

    }

    switchTicket = function () {

        D.log('switchTicket');

        if ($Tickets.isComplete()) {

            $Box = $('.ticket-items').parent();

            R.render({
                "template": 'ticket-complete',
                "json": $Tickets,
                "url": false
            });

        } else {

            $Box = $('.ticket-items');
            $Tab = $(this).is('li')
                ? $(this)
                : ($($TicketTabs).not('.done').first() || $($TicketTabs).first());


            $($TicketTabs).removeClass('active');
            $($Tab).addClass('active');

            R.render({
                "template": 'ticket-item',
                "json": $Tickets,
                "url": false,
                "callback": function () {
                    activateTicket();
                    if (detectDevice() === 'mobile') {
                        setBallsMargins();
                    }
                }
            });

        }
    }

    function activateTicket() {
        $('.ticket-item .ticket-random').off().on('click', clickTicketRandom);
        $('.ticket-item .ticket-favorite').off().on('click', clickTicketFavorite);
        $('.ticket-item .ball-number').off().on('click', clickTicketBall);
        $('.ticket-item .ticket-favorite .after i').off().on('click', function () {$('.profile .ul_li[data-link="profile-info"]').click();});
        $('.ticket-item .add-ticket').on('click', addTicket);
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
                if ($('.ticket-balls li.select').length == $Tickets.selectedBalls) {
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

            if ($Tickets.favorite.length) {
                for (var i = 0; i <= 5; ++i) {
                    $('.ticket-balls .number-' + $Tickets.favorite[i]).addClass('select');
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

            ticket.tickNum = $($TicketTabs + '.active').data('ticket');

            $.ajax({
                url: U.Generate.Ajax('ticket'),
                type: 'post',
                data: ticket,
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        $Tickets.balls[ticket.tickNum] = ticket.combination;
                        $($TicketTabs + '.active').addClass('done');
                        switchTicket();
                    } else {
                        throw(data.message);
                    }
                }
            });

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



    // TICKET ================================= //
    var $ticketBox = '.ticket-item';
    var $ticketBalls = '.ticket-numbers li';
    var $ticketActions = '.ticket-actions li';
    var $ticketNumbersBox = '.ticket-numbers';

    function setBallsMargins() {

        console.log('setBallsMargins');
        var ticketBox = $($ticketBox);
        var ticketBalls = $($ticketBalls);
        var ticketActions = $($ticketActions);
        var ticketNumbersBox = $($ticketNumbersBox);

        var result = getBallsMargins(ticketBox, ticketBalls);
        var margin = result.margin;
        var padding = result.padding;

        ticketBox.css({
            'padding-left': padding + 'px',
            'padding-right': padding + 'px',
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

    function getBallsMargins($box, $balls) {
        var boxWidth = $box.outerWidth();
        var ballWidth = $balls.outerWidth();
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
    /* ========================================================= */
    /* ========================================================= */


    windowResize = function () {

        var device = detectDevice();

        // MENU =================================== //
        var mobile  = menuMobile();
        if ( mobile ) {
            $menuMore.removeClass('menu-item');

            if ( $menuBtn.hasClass('active') ) {
                $menu.show();
            }
            else {
                $menu.hide();
            }
        }
        else {
            $menuMore.addClass('menu-item');

            if ( $menuBtn.hasClass('active') ) {
                $menuMore.show();
            }
            else {
                $menuMore.hide();
            }
        }
        // ======================================== //



        // TICKET ================================= //
        if (device === 'mobile') {
            setBallsMargins();
        }
        // ======================================== //


        // OWL CAROUSEL =========================== //
        if ($matchesCarousel.css('box-shadow') === 'none') {
            if (owl !== null) {
                owl.destroy();
                owl = null;
            }
        }
        else {
            if (owl === null) {
                $matchesCarousel.owlCarousel({
                    singleItem: true,
                    autoPlay: false
                });
                owl = $matchesCarousel.data('owlCarousel');
            }
        }
        // ======================================== //
    }
});