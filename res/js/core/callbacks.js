(function () {

    Callbacks = {

        "init": function () {

            EventListener.on({

                'click': [

                    /* global */
                    ['a', R.push],

                    /* notifications */
                    {".close-notification": Comments.do.closeNotification},
                    {".c-show-notifications": Comments.do.showNotifications},
                    [".c-hide-notifications", Comments.do.deleteNotifications],
                    [".close-list", Comments.do.closeNotifications],
                    [".view-comment", Comments.do.viewComment, 1],

                    /* new reply */
                    {".comment-reply-btn": Comments.do.replyForm},
                    {".comment-content": Comments.do.mobileForm},

                    {".mark-message": Message.do.markMessage},
                    //
                     {"div.back": Navigation.do.backBlock}

                ]
            });

            /* todo
             Event.window([
             {'resize': Device.do.resize},
             {'scroll': Device.do.scroll}
             ])
             */


            // handlers
            $(window).on('resize', Device.do.resize);
            $(window).on('scroll', Device.do.scroll);

            $(document).on('click', ".nm-search .nm-friend", Message.do.setUser);

            $(document).on('click', Device.do.hide);

            /* navigation */
            $(document).on('touchstart', '.site-overlay', function () {
                return false
            });
            $(document).on('touchmove', '.site-overlay', function () {
                return false
            });
            $(document).on('touchend', '.site-overlay', Device.do.hide);
            $('.go-to-top').on('click', Device.do.goTop);

            /* ticket */
            $(document).on('click', Ticket.tabs, Ticket.switch);

            /* new message*/
            $(document).on('input', ".enter-friend-name", Message.do.searchUser);
            $(document).on('click', ".nm-change", Message.do.clearUser);

            /* form */
            $(document).on('submit', 'form:not(.render-list-form)', Form.do.submit);
            $(document).on('input', 'form input[type="text"].required', Form.do.validate);
            $(document).on('change', 'form input[type="radio"].required', Form.do.validate);
            $(document).on('change', 'form input[type="checkbox"].required', Form.do.validate);
            $(document).on('change', 'form textarea.required', Form.do.validate);
            $(document).on('change', 'form input[type="file"].required', Form.do.validate);

            /* autoload */
            $(document).on('submit', 'form.render-list-form', Content.enableAutoload);
            $(document).on('input', 'form.render-list-form input', Content.autoload);
            $(document).on('change', 'form.render-list-form', Content.autoload);

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
            "menu-navigation-mobile": Navigation.ready,

            "blog-post-view": Blog.init,
            "blog-post-view-comments-replyform": Comments.after.replyForm,
            "communication-comments-replyform": Comments.after.replyForm,
            "communication-comments-view": Comments.after.showComment,

            "games-online": Carousel.initOwl,
            "games-chance": Carousel.initOwl,
            "games-game": WebSocketAjaxClient,
            "games-spin": slotMachine.init,

            "profile-edit": Profile.init,
            "profile-billing": Profile.init,

            "communication-messages": Message.init,

            "lottery": Lottery.init,
            "lottery-history-view": Lottery.view,
            "lottery-ticket-item": Ticket.activate,
            "lottery-ticket-tabs": Ticket.switch,

            "reports-transactions": Content.initDaterange,
            "reports-referrals": Content.initDaterange,

            "support-rules": Support.init,
            "support-faq": Support.init

        },

        "post": { // new

            "profile-convert": Player.updateBalance,
            "profile-cashout": Player.updateBalance,
            "lottery-ticket": Ticket.update,
            "lottery-gold": Ticket.update,
            "prizes-exchange-goods": Prize.update.exchange,
            "blog-post-view-comments": Comments.after.reply,
            "communication-comments": Comments.after.reply,

            "communication-notifications": Comments.renderNotifications, // delete

        },

        "error": {

            "lottery-gold": Ticket.error.gold,
            "prizes-exchange-goods": Prize.error.exchange,

        },

        "put": { // update

            "profile-edit": Profile.update.details,
            "profile-settings": Profile.update.settings,
            "profile-billing": Profile.update.billing,

        },

        "delete": {

            "communication-notifications": Comments.renderNotifications,

        },

        "validate": {

            "lottery-ticket": Ticket.validate,
            "profile-convert": Profile.validate.convert,
            "profile-cashout": Profile.validate.cashout,
            "prizes-exchange-goods": Prize.validate.exchange,
            "blog-post-view-comments": Comments.validate.reply,

        }

    };

})();
