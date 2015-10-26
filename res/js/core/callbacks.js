(function () {

    /* ========================================================= */
    /*                      CALLBACKS
     /* ========================================================= */

    Callbacks = {

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

            /* form */
            $(document).on('click', 'form button[type="submit"]',   Form.submit);
            $(document).on('input', 'form input.required',          Form.validate);

            /* profile*/
            $(document).on('click', '.pi-ph.true i', Profile.removeAvatar);
            $(document).on('click', '.pi-ph.true i', Profile.updateAvatar);

            $(document).on('click', '.ae-current-combination li', Profile.openFavorite);
            $(document).on('click', '.ae-combination-box li', Profile.selectFavorite);

            $(document).on('input', 'form input.cc-sum', Profile.validateConvert);
            $(document).on('input', 'form input.cco-sum', Profile.validateCashout);

            /* game */
            $(document).on('click', '.mx .players .m .btn-ready', GameAction.ready);
            $(document).on('click', '.mx .players .m .btn-pass', GameAction.pass);

        },

        "render": {

            "blog": Blog.init,
            "lottery": Lottery.init,
            "games-game": WebSocketAjaxClient,
            "profile-edit": Profile.init,
            "profile-billing": Profile.init,
            "communications-messages": Message.init

        },

        "post": {

            "profile-edit": Profile.updateDetails,
            "profile-settings": Profile.updateSettings,
            "profile-billing": Profile.updateBilling,
            "profile-convert": Profile.convertMoney,
            "profile-cashout": Profile.cashoutMoney

        },

        "validate":{

            "lottery-ticket": Ticket.validateTicket,
            "profile-convert": Profile.validateConvert,
            "profile-cashout": Profile.validateCashout
        }

    };

})();