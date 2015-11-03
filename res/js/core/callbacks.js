(function () {

    /* ========================================================= */
    /*                      CALLBACKS
     /* ========================================================= */

    Callbacks = {

        "init": function () {

            // handlers
            $(window).on('resize', Device.resize);
            $(window).on('scroll', Device.scroll);
            $(document).on('click', Device.hide);
            $(I.goTop).on('click', Device.goTop);

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
            $(document).on('click', 'form button[type="submit"]', Form.submit);
            $(document).on('input', 'form input[type="text"].required', Form.validate);
            $(document).on('change', 'form input[type="radio"].required', Form.validate);
            $(document).on('change', 'form input[type="checkbox"].required', Form.validate);

            /* profile*/
            $(document).on('click', '.pi-ph.true i', Profile.removeAvatar);
            $(document).on('click', '.pi-ph.true i', Profile.updateAvatar);

            $(document).on('click', '.ae-current-combination li', Profile.openFavorite);
            $(document).on('click', '.ae-combination-box li', Profile.selectFavorite);

            /* game */
            $(document).on('click', '.mx .players .m .btn-ready', GameAction.ready);
            $(document).on('click', '.mx .players .m .btn-pass', GameAction.pass);

        },

        "get": {

            "blog": Blog.init,
            "blog-post-view": Blog.loadPostData,
            "lottery": Lottery.init,
            "games-game": WebSocketAjaxClient,
            "profile-edit": Profile.init,
            "profile-billing": Profile.init,
            "communications-messages": Message.init,
            "lottery-history-view": Lottery.view

        },

        "post": { // new

            "profile-convert": Profile.convertMoney,
            "profile-cashout": Profile.cashoutMoney,
            "lottery-ticket": Ticket.update,

        },

        "put": { // update

            "profile-edit": Profile.updateDetails,
            "profile-settings": Profile.updateSettings,
            "profile-billing": Profile.updateBilling,

        },

        "delete": {},

        "validate": {

            "lottery-ticket": Ticket.validate,
            "profile-convert": Profile.validateConvert,
            "profile-cashout": Profile.validateCashout

        }

    };

})();