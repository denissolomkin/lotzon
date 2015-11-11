(function () {

    Callbacks = {

        "init": function () {
            // handlers
            $(window).on('resize', Device.do.resize);
            $(window).on('scroll', Device.do.scroll);
            $(document).on('click', Device.do.hide);
            $(I.goTop).on('click', Device.do.goTop);

            /* navigation */
            $(document).on('click', I.Tabs, Navigation.do.switchTab);
            $(document).on('click', I.Cats, Navigation.do.switchCat);
            $(document).on('click', 'a', Navigation.do.loadBlock);
            $(document).on('click', 'div.back', Navigation.do.backBlock);

            /* ticket */
            $(document).on('click', I.TicketTabs, Ticket.switch);

            /* new message*/
            $(document).on('input', ".enter-friend-name", Message.do.searchAddressee);
            $(document).on('click', ".nm-change", Message.do.clearAddressee);
            $(document).on('click', ".nm-friend", Message.do.setAddressee);
            $(document).on('click', ".message-form-btn", Message.do.send);

            /* form */
            $(document).on('click', 'form button[type="submit"]', Form.do.submit);

            $(document).on('change', 'form.filter-render-list input', Content.autoload);
            $(document).on('submit', 'form.filter-render-list', Content.enableAutoload);

            $(document).on('input', 'form input[type="text"].required', Form.do.validate);
            $(document).on('change', 'form input[type="radio"].required', Form.do.validate);
            $(document).on('change', 'form input[type="checkbox"].required', Form.do.validate);

            /* profile*/
            $(document).on('click', '.pi-ph.true i', Profile.do.removeAvatar);
            $(document).on('click', '.pi-ph.true i', Profile.do.updateAvatar);

            $(document).on('click', '.ae-current-combination li', Profile.do.openFavorite);
            $(document).on('click', '.ae-combination-box li', Profile.do.selectFavorite);
            $(document).on('click', '.s-lang .radio-text', Profile.do.changeLanguage);

            /* game */
            $(document).on('click', '.mx .players .m .btn-ready', Game.do.ready);
            $(document).on('click', '.mx .players .m .btn-pass', Game.do.pass);

            /* support */
            $(document).on('click', '.support h1', Support.do.collapse);

        },

        "get": {

            "menu-slider": Slider.after,
            "menu-balance": Navigation.ready,
            "menu-navigation": Navigation.ready,

            "blog": Blog.init,
            "blog-post-view": Blog.loadPostData,

            "games-game": WebSocketAjaxClient,

            "profile-edit": Profile.init,
            "profile-billing": Profile.init,

            "communications-messages": Message.init,

            "lottery": Lottery.init,
            "lottery-history-view": Lottery.view,
            "lottery-ticket-item": Ticket.activate,
            "lottery-ticket-tabs": Ticket.switch,

            "reports-transactions": Reports.init,

            "support-rules": Support.init,
            "support-faq": Support.init

        },

        "post": { // new

            "profile-convert": Profile.update.convert,
            "profile-cashout": Profile.update.cashout,
            "lottery-ticket": Ticket.update,
            "prizes-exchange-goods": Prize.update.exchange,

        },

        "put": { // update

            "profile-edit": Profile.update.details,
            "profile-settings": Profile.update.settings,
            "profile-billing": Profile.update.billing,

        },

        "delete": {},

        "validate": {

            "lottery-ticket": Ticket.validate,
            "profile-convert": Profile.validate.convert,
            "profile-cashout": Profile.validate.cashout,
            "prizes-exchange-goods": Prize.validate.exchange,

        }

    };

})();