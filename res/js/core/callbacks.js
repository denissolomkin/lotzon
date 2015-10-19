(function () {


    // classes
    I = {

        /* navigation and tabs */
        Tabs: '.content-box-tabs a',
        Cats: '.content-box-cat a',
        TicketTabs: '.ticket-tabs li',

        /* menu */
        menu: '.menu',
        menuMain: '.menu-main',
        menuMore: '.menu-more',
        menuProfile: '.menu-profile',
        menuBalance: '.menu-balance',
        menuItem: '.menu-item',
        menuBtn: '.menu-btn',
        menuProfileBtn: '.menu-profile-btn',
        menuBalanceBtn: '.menu-balance-btn',
        menuBtnItem: '.menu-btn-item',
        balanceBtn: '.balance-btn',

        /* communication */
        comment: '.comment',
        notifications: '.c-notifications',
        showNotifications: '.c-show-notifications',
        hideNotifications: '.c-hide-notifications',
        notificationsList: '.c-notifications-list',
        closeList: '.c-notifications-list .close-list',
        closeNotification: '.c-notification .close-notification',
        textArea: '.message-form-area',

        /* other */
        goTop: '.go-to-top',
    }

    // callbacks

    C = {

        "init": function () {

            // handlers
            $(window).on('resize',  W.resize);
            $(window).on('scroll',  W.scroll);
            $(document).on('click', W.hide);
            $(I.goTop).on('click',  W.goTop);

            /* navigation */
            $(document).on('click', I.Tabs,     Navigation.switchTab);
            $(document).on('click', I.Cats,     Navigation.switchCat);
            $(document).on('click', 'a',        Navigation.loadBlock);
            $(document).on('click', 'div.back', Navigation.backBlock);

            /* ticket */
            $(document).on('click', I.TicketTabs, Ticket.switch);

            /* new message*/
            $(document).on('input', ".enter-friend-name",   Message.searchAddressee);
            $(document).on('click', ".nm-change",           Message.clearAddressee);
            $(document).on('click', ".nm-friend",           Message.setAddressee);
            $(document).on('click', ".message-form-btn",    Message.send);


        },

        "lottery": function () {

            Carousel.initOwl();
            Ticket.render();

        },

        "blog": function () {

            R.render({
                'box': $('.content-box-content:visible'),
                'template': 'blog-posts',
                'url': false
            })

        },

        "games-game": function () {

            WebSocketAjaxClient();
            $(document).on('click', '.mx .players .m .btn-ready', GameAction.ready);
            $(document).on('click', '.mx .players .m .btn-pass', GameAction.pass);
            $(document).on("mouseenter",'.players .m .card', mouseEnter);
            $(document).on("mouseleave",'.players .m .card', mouseLeave);
            $(document).on("touchstart",'.players .m .card', touchstart);
            $(document).on("touchmove",'.players .m .card', touchMove);
            $(document).on("touchend",'.players .m .card', touchend);


        },

        "menu": function () {

            /* ========================================================= */
            //                        MENUS
            /* ========================================================= */

            // Balance menu
            R.render({
                'box': 'balance',
                'template': 'menu-balance',
                'json': Player,
                'url': false,
                'callback': function () {

                    $("header a").on('click', Navigation.loadPage);
                    $(document).on('click', I.menuBtnItem, Menu.click);
                    $('[href="/' + R.Path[1] + '"]').first().click();
                    Menu.switch();

                }
            });

            // Slider carousel
            R.render({
                'box': 'inf-slider',
                'template': 'menu-slider',
                'json': Slider,
                'url': false,
                'callback': function () {

                    $("#countdownHolder").countdown({
                        until: (Slider.timer),
                        layout: '{hnn}<span>:</span>{mnn}<span>:</span>{snn}'
                    });

                    $(".slider-top").owlCarousel({
                        navigation: false,
                        slideSpeed: 300,
                        paginationSpeed: 400,
                        singleItem: true,
                        autoPlay: true
                    });

                }
            });


        },

        "communications-messages": function () {

            /* ========================================================= */
            //                     COMMUNICATION
            /* ========================================================= */

            // COMMENTS ============================== //

            $(I.comment).on('click', function (event) {
                event.stopPropagation();
                $(I.comment).removeClass('active');
                if (Device.detect() === 'mobile') {
                    $(this).addClass('active');
                }
            });

            $(I.hideNotifications).on('click', function () {
                $(I.notifications).fadeOut('fast', function () {
                    $(I.notifications).remove();
                });
            });

            $(I.closeList).on('click', function () {
                $(I.notifications).slideUp('fast', function () {
                    $(I.notifications).remove();
                });
            });

            $(I.closeNotification).on('click', function () {
                if ($(I.notificationsList).find('.c-notification').length < 2) {
                    $(I.notifications).slideUp('fast', function () {
                        $(I.notifications).remove();
                    });
                } else {
                    $(this).parent().slideUp('fast', function () {
                        $(this).remove();
                    });
                }
            });

            $(I.showNotifications).on('click', function (event) {
                $(I.notificationsList).slideDown('fast');
            });

            $(I.notificationsList).on('click', function (event) {
                event.stopPropagation();
            });

            // $notifications.on('click', function(event) {
            // 	event.stopPropagation();
            // });

            // TEXTAREA ------------------------- //
            function h(e) {
                $(e).css({'height': 'auto', 'overflow-y': 'hidden'}).height(e.scrollHeight);
            }

            $(I.textArea).each(function () {
                h(this);
            }).on('input', function () {
                h(this);
            });
            // --------------------------------- //
            // ======================================= //

            /* ========================================================= */
            /* ========================================================= */
        }
    };

})();