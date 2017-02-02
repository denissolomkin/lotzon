(function () {


    /* ========================================================= */
    //                        Lottery
    /* ========================================================= */

    Ticket = {

        tabs   : '.ticket-items .ticket-tabs li',
        balls  : '.ticket-item .ticket-balls li',
        actions: '.ticket-item .ticket-actions li',

        activate: function () {

            D.log('Ticket.activate');

            Ticket.setBallsMargins();
            $(Ticket.balls).off().on('click', Ticket.action.clickBall);
            $(Ticket.actions).filter('.ticket-random').off().on('click', Ticket.action.clickRandom);
            $(Ticket.actions).filter('.ticket-favorite').off().on('click', Ticket.action.clickFavorite);
            $(Ticket.actions).find('.after i').off().on('click', function () {
                $('.profile .ul_li[data-link="profile-info"]').click();
            });
            
        },

        render: function () {
            D.log('Ticket.render');

            if (Tickets.isComplete()) {
                
                this.complete();

            } else {

                if(document.getElementById('lottery-ticket-tabs'))
                    R.push({
                        href: 'lottery-ticket-tabs',
                        json: Tickets
                    });

            }

        },

        isBannerActive: function(){

            var ticketBanner = document.getElementById('banner-desktop-context-ticket');
            return ticketBanner
                && ticketBanner.children
                && ticketBanner.children.length;

        },

        switch: function () {
            
            D.log('Ticket.switch');

            if (Tickets.isComplete()) {

                this.complete();

            } else {

                var node = document.getElementById('lottery-ticket-item'),
                    isLi = ('nodeType' in this) && this.tagName == 'LI',
                    isReselect = !isLi
                        && Tickets.selectedTab
                        && (document.querySelectorAll(Ticket.tabs + ':not(.done)').length && document.querySelectorAll(Ticket.tabs)[Tickets.selectedTab - 1].classList.contains('done')),
                    tabs = [];

                if(isLi && Ticket.isBannerActive())
                    return;

                //up b
                Banner.update();
                
                switch (true) {

                    /* фактическое нажатие */
                    case isLi:
                        Tickets.selectedTab = 1 + Array.prototype.indexOf.call(this.parentNode.children, this);
                        break;

                    /* еще не назначено, либо выбранный уже заполнен, но есть еще незаполненные*/
                    case !Tickets.selectedTab:
                    case isReselect:

                        switch (true) {
                            case (tabs = document.querySelectorAll(Ticket.tabs + ':not(.done):not(.unavailable)')) && tabs.length !== 0:
                                // console.debug('>>tabs case 1',tabs);
                                break;
                            case (tabs = [ document.querySelectorAll(Ticket.tabs)[6] ]) && Tickets.captchaTime() && tabs.length !== 0:
                                // console.debug('>>tabs case 1.5',tabs);
                                break;
                            case (tabs = document.querySelectorAll(Ticket.tabs + ':not(.done)')) && tabs.length !== 0:
                                // console.debug('>>tabs case 2',tabs);
                                break;
                            case (tabs = document.querySelectorAll(Ticket.tabs)) && tabs.length !== 0:
                                // console.debug('>>tabs case 3',tabs);
                                break;
                        }
                        // console.debug('>>tabs[]',tabs);
                        Tickets.selectedTab = 1 + Array.prototype.indexOf.call(tabs[0].parentNode.children, tabs[0]);
                        
                        break;
                }

                if (!node || (isLi && Ticket.isLoading()))
                    return;

                DOM.removeClass('active', document.querySelectorAll(Ticket.tabs));
                DOM.addClass('active', document.querySelectorAll(Ticket.tabs)[Tickets.selectedTab - 1]);

                R.push({
                    href    : 'lottery-ticket-item',
                    template: Tickets.isAvailable() ? 'lottery-ticket-item' : 'lottery-ticket-unavailable' + Tickets.selectedTab,
                    json    : Tickets,
                    after   : function () {

                        if (Tickets.selectedTab == Tickets.randomTicket)
                            Banner.load('banner-desktop-context-ticket');

                        Tickets.isGold()
                            ? document.querySelector('.ticket-box').classList.add('gold')
                            : document.querySelector('.ticket-box').classList.remove('gold');

                        // init timer
                        Tickets.getSevenTimer();
                    }
                });

            }
        },

        complete: function () {

            D.log('Ticket.complete');
            var box = $('.ticket-items').parent();

            if (!box.length)
                return;

            R.push({
                template: 'lottery-ticket-complete',
                json    : Tickets
            });

        },

        validate: function () {
            return Ticket.countBalls() === Tickets.requiredBalls && !Ticket.isBannerActive();
        },

        update: function () {

            Ticket.render();
            return true;

        },

        action: {

            clickFavorite: function (e) {

                if(Ticket.isBannerActive())
                    return false;

                if ($(e.target).hasClass('after') || Ticket.isLoading())
                    return false;

                Ticket.clearBalls();

                if (Player.favorite.length) {

                    for (num in Player.favorite)

                        /* increase in performance */
                        if (parseInt(num) + 1 === Player.favorite.length) {
                            $(Ticket.balls).filter(':eq(' + (Player.favorite[num] - 1) + ')').click();
                        } else {
                            $(Ticket.balls).filter(':eq(' + (Player.favorite[num] - 1) + ')').addClass('select').find('input').prop('checked', true);
                        }

                    $(this).addClass('select');

                } else if (Device.get() >= 0.6) {

                    $(this).find('.after').fadeIn(200);

                }

            },

            clickBall: function (event) {

                if(Ticket.isBannerActive())
                    return false;

                var li = $(this),
                    requiredBalls;

                switch (true) {

                    case $(Ticket.tabs).filter(':eq(' + (Tickets.selectedTab - 1) + ')').hasClass('done'): // if ticket already done
                    case Ticket.isLoading(): // if ticket sending now
                    case Ticket.countBalls() === Tickets.requiredBalls && !li.hasClass('select'): // if balls already all
                    case !$(event.target).is('li'): // if target is not li
                        return true;
                        break;
                    default :

                        Ticket.clearActions();

                        li.toggleClass('select')
                            .children('input').click();

                        requiredBalls = Tickets.requiredBalls - Ticket.countBalls();

                        requiredBalls === 0 || Device.get() < 0.6
                            ? $('.balls-count').hide()
                            : $('.balls-count').show().find('b').html(requiredBalls);

                        break;
                }

            },

            clickRandom: function (e) {

                if(Ticket.isBannerActive())
                    return false;

                if ($(e.target).hasClass('after') || Ticket.isLoading())
                    return false;

                Ticket.clearActionsAfter();

                var lotInterval,
                    li = $(this),
                    after = $(this).find('.after');

                if (Device.get() >= 0.6) {
                    after.fadeIn(300);
                    setTimeout(function () {
                        after.fadeOut(300);
                    }, 1800);
                }

                lotInterval = window.setInterval(Ticket.randomBalls, 200);

                window.setTimeout(function () {
                    window.clearInterval(lotInterval);
                    Ticket.randomBalls(true);
                    li.addClass('select');
                }, 800);


            }

        },

        isLoading: function () {
            return $('.add-ticket').hasClass('loading');
        },

        randomBalls: function (isLastIterration) {

            var ticketCache = [];

            Ticket.clearBalls();

            do {

                do {
                    rand = Math.floor((Math.random() * Tickets.totalBalls));
                } while ($.inArray(rand, ticketCache) > -1);

                ticketCache.push(rand);

                if (isLastIterration) {

                    /* increase in performance */
                    ( ticketCache.length === Tickets.requiredBalls
                        ? $(Ticket.balls).filter(':eq(' + rand + ')').click()
                        : $(Ticket.balls).filter(':eq(' + rand + ')').addClass('select').find('input').prop('checked', true));

                } else {
                    $(Ticket.balls).filter(':eq(' + rand + ')').addClass('select');
                }


            } while (ticketCache.length != Tickets.requiredBalls);


        },

        countBalls: function () {
            return $('input[type="checkbox"][name="combination[]"]').filter(':checked').length;
        },

        clearBalls: function () {

            var $selectedBalls = $(Ticket.balls).filter('.select'),
                count = $selectedBalls.length;

            if (count)
                $.each($selectedBalls, function (index, el) {
                    el = $(el);

                    /* increase in performance */
                    (count === index + 1) && el.find(':checked').length
                        ? el.click()
                        : el.removeClass('select').find(':checked').prop('checked', false);
                });
        },

        clearActionsAfter: function (fade) {

            if ($(Ticket.actions).find('.after:visible').length)
                $(Ticket.actions).find('.after:visible').fadeOut(fade ? 150 : 0);

        },

        clearActions: function () {

            $(Ticket.actions).filter('.select').removeClass('select');

        },

        setBallsMargins: function () {
            return;

            if (Device.get() < 0.6) {
                var ticketBox = $('.ticket-item');
                var ticketBalls = $('.ticket-numbers li');
                var ticketActions = $('.ticket-actions li');
                var ticketNumbersBox = $('.ticket-numbers');
                var ticketScrollBox = $('.scroll-box');
                var blocksForAligning = $('.ticket-numbers, .ticket-actions');
                var ticketTabs = $('.ticket-tabs, .timer-box');

                var ticketBtn = $('.ticket-btn.add-ticket');
                var result = this.getBallsMargins(ticketBox, ticketBalls);

                ticketScroll = $('.ticket-numbers-wrapper').width();
                ticketScrollBox.width(ticketScroll + 16);
                ticketNumbersBox.width(ticketScroll);

                ticketNumbersBox
                ticketTabs.css({
                    'margin': '0 ' + (result + 10) + 'px'
                });

                blocksForAligning.css({
                    'margin': '0 ' + (result) + 'px'
                });
                ticketBtn.css({
                    'margin-right': 10 + result + 'px'
                });
            }
        },

        getBallsMargins: function (box, balls) {

            var boxWidth = box.outerWidth();
            var ballWidth = balls.outerWidth();
            var margin, padding, count;


            if ((boxWidth ) % 60 > 0) {
                console.log((boxWidth + 30) % 40 > 0, '(boxWidth + 30) % 60', (boxWidth + 30) % 60);
                return boxWidth % 60 / 2
            }
        },

        error: {
            gold: function () {
            }
        }

    }

})();
