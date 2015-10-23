(function () {


    /* ========================================================= */
    /*                      INIT CLASSES
     /* ========================================================= */

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

    /* ========================================================= */
    /*                      CALLBACKS
     /* ========================================================= */

    C = {

        "init": function () {

            // handlers
            $(window).on('resize', W.resize);
            $(window).on('scroll', W.scroll);
            $(document).on('click', W.hide);
            $(I.goTop).on('click', W.goTop);

            /* navigation */
            $(document).on('click', I.Tabs, Navigation.switchTab);
            $(document).on('click', I.Cats, Navigation.switchCat);
            $(document).on('click', 'a', Navigation.loadBlock);
            $(document).on('click', 'div.back', Navigation.backBlock);

            /* ticket */
            $(document).on('click', I.TicketTabs, Ticket.switch);

            /* new message*/
            $(document).on('input', ".enter-friend-name", Message.searchAddressee);
            $(document).on('click', ".nm-change", Message.clearAddressee);
            $(document).on('click', ".nm-friend", Message.setAddressee);
            $(document).on('click', ".message-form-btn", Message.send);

            $(document).on('click', 'form button[type="submit"]', Form.submit);
            $(document).on('input', 'form input', Form.validate);

            /* profile*/
            $(document).on('click', '.pi-ph.true i', Profile.removeAvatar);
            $(document).on('click', '.pi-ph.true i', Profile.updateAvatar);

            $(document).on('click', '.ae-current-combination li', Profile.openFavorite);
            $(document).on('click', '.ae-combination-box li', Profile.selectFavorite);

            $(document).on('input', 'form input.cc-sum', Profile.validateConvert);
            $(document).on('input', 'form input.cco-sum', Profile.validateCashout);

            /* callback menu*/
            this.menu();

        },

        "menu": Menu.init,

        "blog": Blog.init,

        "lottery": Lottery.init,

        "post": {

            "profile-edit": Profile.updateDetails,
            "profile-settings": Profile.updateSettings,
            "profile-billing": Profile.updateBilling,
            "profile-convert": Profile.convertMoney,
            "profile-cashout": Profile.cashoutMoney,

        },

        "games-game": function () {

            WebSocketAjaxClient();
            $(document).on('click', '.mx .players .m .btn-ready', GameAction.ready);
            $(document).on('click', '.mx .players .m .btn-pass', GameAction.pass);
            $(document).on("mouseenter", '.players .m .card', mouseEnter);
            $(document).on("mouseleave", '.players .m .card', mouseLeave);
            $(document).on("touchstart", '.players .m .card', touchstart);
            $(document).on("touchmove", '.players .m .card', touchMove);
            $(document).on("touchend", '.players .m .card', touchend);


        },


        "profile-edit": function () {

            $('input[type="text"][name="bd"]').inputmask("d.m.y", {autoUnmask: false});

        },

        "profile-billing": function () {

            $('input[type="tel"][name="billing[phone]"]').inputmasks(phoneMask);
            $('input[type="tel"][name="billing[qiwi]"]').inputmasks(phoneMask);
            $('input[type="text"][name="billing[yandexMoney]"]').inputmask({mask: '410019{7,10}', placeholder: ''});
            $('input[type="text"][name="billing[webMoney]"]').inputmask('a999999999999');

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
        }
    };

})();