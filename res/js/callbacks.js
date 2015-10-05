(function () {

    // callbacks

    C = {

        "init": function(){

            // variables

            $Tabs = '.content-box-tabs a';
            $Cats = '.content-box-cat a';
            $TicketTabs = '.ticket-tabs li';

            Tickets = $.extend(
                Tickets,
                {
                    "ballsHTML": function () {
                        var html = '';
                        for (i = 1; i <= this.totalBalls; i++) {

                            html += "<li class='ball-number number-" + i + ($.inArray(i, this.balls[$($TicketTabs).filter('.active').data('ticket')]) == -1 ? '' : ' select') + "'>" + i + "</li>";
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
                        return (this.balls && this.balls[$($TicketTabs).filter('.active').data('ticket')] && this.balls[$($TicketTabs).filter('.active').data('ticket')].length && this.balls[$($TicketTabs).filter('.active').data('ticket')].length == this.selectedBalls);
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
                }
            );

            // handlers
            $(window).on('resize', windowResize);

            /* navigation */
            $(document).on('click', $Tabs, switchTab);
            $(document).on('click', $Cats, switchCat);
            $(document).on('click', 'a', loadBlock);
            $(document).on('click', 'div.back', backBlock);

            /* ticket */
            $(document).on('click', $TicketTabs, switchTicket);

            /* new message*/
            $(document).on('input', ".enter-friend-name", searchMessageAddressee);
            $(document).on('click', ".nm-change", clearMessageAddressee);
            $(document).on('click', ".nm-friend", setMessageAddressee);
            $(document).on('click', ".message-form-btn", sendMessage);


        },

        "lottery": function(){

            runOwlCarousel();
            renderTicket();

        },

        "blog": function(){

            $Box = $('.content-box-content:visible');
            R.render({
                'template': 'posts',
                'url': false
            })

        },

        "menu": function () {

            /* ========================================================= */
            //                        MENUS
            /* ========================================================= */

            R.render({
                'box': 'balance',
                'template': 'menu-balance',
                'json': Player,
                'url': false
            });

            R.render({
                'box': 'inf-slider',
                'template': 'menu-slider',
                'json': Slider,
                'url': false
            });

            $menu = $('.menu');
            $menuMain = $('.menu-main');
            $menuMore = $('.menu-more');
            $menuProfile = $('.menu-profile');
            $menuBalance = $('.menu-balance');
            $menuItem = $('.menu-item');
            $menuBtn = $('.menu-btn');
            $menuProfileBtn = $('.menu-profile-btn');
            $menuBalanceBtn = $('.menu-balance-btn');
            $menuBtnItem = $('.menu-btn-item');
            $balanceBtn = $('.balance-btn');

            if (!menuMobile()) {
                $menuMore.addClass('menu-item');
                $menuProfile.addClass('menu-item');
            }

            // MENU
            $menuBtn.on('click', function () {
                var mobile = menuMobile();

                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');

                    if (mobile) {
                        $menu.fadeOut(200);
                    }
                    else {
                        $menuMore.fadeOut(200);
                    }
                }
                else {
                    $menuBtnItem.removeClass('active');
                    $(this).addClass('active');

                    $menuBalance.hide();
                    $menuProfile.hide();

                    if (mobile) {
                        $menu.fadeIn(200);
                    }
                    else {
                        $menuMore.fadeIn(200);
                    }
                }
            });

            // PROFILE MENU
            $menuProfileBtn.on('click', function () {
                var mobile = menuMobile();

                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                    $menuProfile.fadeOut(200);
                }
                else {
                    $menuBtnItem.removeClass('active');
                    $(this).addClass('active');

                    $menuBalance.hide();

                    if (mobile) {
                        $menu.hide();
                    }
                    else {
                        $menuMore.hide();
                    }

                    $menuProfile.fadeIn(200);
                }
            });

            // BALANCE MENU
            $menuBalanceBtn.on('click', function () {
                var mobile = menuMobile();

                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                    $menuBalance.fadeOut(200);
                }
                else {
                    $menuBtnItem.removeClass('active');
                    $(this).addClass('active');

                    $menuProfile.hide();

                    if (mobile) {
                        $menu.hide();
                    }
                    else {
                        $menuMore.hide();
                    }

                    $menuBalance.fadeIn(200);
                }
            });

            $balanceBtn.on('click', function () {
                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                    $menuBalance.fadeOut(200);
                }
                else {
                    $menuBtnItem.removeClass('active');
                    $(this).addClass('active');

                    $menuProfile.hide();
                    $menuBalance.fadeIn(200);
                    $menuMore.hide();

                    $menuBalance.fadeIn(200);
                }
            });

            // Stop Propogation
            $menuItem.on('click', function (event) {
                event.stopPropagation();
            });

            $menuBtnItem.on('click', function (event) {
                event.stopPropagation();
            });

            $("#countdownHolder").countdown({
                until: (Slider.timer),
                layout: '{hnn}<span>:</span>{mnn}<span>:</span>{snn}'
            });

            $(".slider-top").owlCarousel({
                navigation : false,
                slideSpeed : 300,
                paginationSpeed : 400,
                singleItem: true,
                autoPlay: true
            });
                    
            $("header a").on('click', loadPage);
          
            $('[href="/' + R.Path[1] + '"]').click();
        }
    };

})();