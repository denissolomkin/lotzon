
(function () {

    Callbacks = {

        "init": function () {

            EventHandler.on({

                'click': [

                    /* global */
                    ['a', R.push],

                    /* badges */
                    ['.badge-close', Content.badge.close, 1],

                    /* messages */
                    {".mark-read": Messages.do.markRead},

                    /* notifications */
                    {".close-notification": Comments.do.closeNotification},
                    {".c-show-notifications": Comments.do.showNotifications},
                    [".c-hide-notifications", Comments.do.deleteNotifications],
                    [".close-list", Comments.do.closeNotifications],
                    [".view-comment", Comments.do.viewComment, 1],

                    /* new reply */
                    {".comment-reply-btn": Comments.do.replyForm},
                    {".comment-content": Comments.do.mobileForm},

                    /* complain */
                    {".comment-complain-btn": Comments.do.complainForm},

                    //
                    {".back": Navigation.do.backBlock},
                    {".close-pop-box": Content.destroyPopBox}

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
            

                 
            $(document).on('click', ".nm-search .nm-friend", Messages.do.setUser);
            $(document).on('click', Device.do.hide);
             /*smiles*/
            $(document).on('click ', ".message-form-smileys", Comments.showSmiles);
            $(document).on('click', ".smiles img", Comments.chooseSmiles);
            
            

            $(document).on("paste", '[contenteditable]', Comments.pasteText);
            $(document).on("keydown", '[contenteditable]', Comments.checkInput);


            // $(document).on('click', '.no-image, .thumb', Comments.showPreviewImage);
            $(document).on('click', '.thumb .i-x-slim', Comments.deleteImage);
            $(document).on('click', '.input-file-wrapper', Comments.uploadImage);
      

            $(document).on('touchstart', '.site-overlay', function () {
                return false
            });
            
            // stop "Device.do.hide" on .toggle-btn click | add toggle-slide
            $(document).on('touchend click', '.toggle-btn', function (event) {
                event.stopPropagation();
                event.preventDefault();
                
                $('.toggle-btn').not($(this)).removeClass('active').next('ul').slideUp("fast");
                $(this).toggleClass('active').next('ul').slideToggle("fast");
            });
            
            $(document).on('touchmove', '.site-overlay', function () {
                return false
            });
            $(document).on('touchend', '.site-overlay', Device.do.hide);
            $('.go-to-top').on('click', Device.do.goTop);


            /* ticket */
            $(document).on('click', Ticket.tabs, Ticket.switch);


            /* new message*/
            $(document).on('input', ".enter-friend-name", Messages.do.searchUser);
            $(document).on('click', ".nm-change", Messages.do.clearUser);


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
            // $(document).on('click', '.pi-ph.true', Profile.do.removeAvatar);
            $(document).on('click', '.pi-ph.true', Profile.do.updateAvatar);
            $(document).on('change', 'form input.repeat-pass', Profile.validate.passwordRepeat);
            $(document).on('change', '.cc-out .cc-sum', Profile.validate.convert);



            $(document).on('click', '.ae-combination-box li', Profile.do.selectFavorite);
            $(document).on('click', '.s-lang .radio-text', Profile.do.changeLanguage);
            $(document).on('click', '.change', Profile.do.openOption);
            $(document).on('click', '.change-favorite', Profile.do.openFavorite);
            $(document).on('click', '.choice .change', Profile.do.cancelFavorite);
            $(document).on('click', '.bonus-banner-view-item', Bonuses.showBanner);
            $(document).on('click', '.bonus-share-banner-view .close', Bonuses.hideBanner);

            $(document).on('click', '#games-moment .close-pop-box, #games-random .close-pop-box', function(){location.reload();});

            // $(document).on('click', '.banner-copy-btn', Bonuses.copyBanner);
            // $(document).on('click', '.banner-copy-btn a', Bonuses.downloadFile);


            /* game */
            $(document).on('click', '.mx .players .m .btn-ready', Game.do.ready);
            $(document).on('click', '.mx .players .m .btn-pass', Game.do.pass);
            $(document).on('click', '.mx .players .m .btn-sb-ready', Apps.SeaBattle.do.ready);
            $(document).on('click', '.mx .exit', Game.do.exit);
            $(document).on('click', '.mx .table .cell, .mx .table li', Game.do.move);
            $(document).on('click', '.mx .players .m button, .mx .msg-buttons button', Game.do.button); /* !!! keep order of handlers*/
            $(document).on('click', '.mx .players .m button.btn-start, .mx .msg-buttons button.btn-start', Game.do.start);
            $(document).on('click', '.mx .players .m .btn-sb-random', Apps.SeaBattle.genFieldSeaBattle);

            /* messages */
            $(document).on('click', '.get-full-picture', Comments.getFullPicture);

            /* support */
            $(document).on('click', '.support h1', Support.do.collapse);

        },

        "get": {

            "menu-slider": Slider.after,
            "menu-balance": Navigation.ready,
            "menu-navigation": Navigation.ready,
            "menu-navigation-mobile": Navigation.ready,

            "prizes": Prize.init,

            "blog-post-view": Blog.init,
            "blog-post-view-comments-replyform": Comments.after.replyForm,

            "profile-bonuses": Bonuses.init,

            "communication-messages": Messages.init,
            "communication-comments-replyform": Comments.after.replyForm,
            "communication-comments-view": Comments.after.showComment,

            "games-online": Carousel.initOwl,
            "games-online-view": Games.online.init,
            "games-online-view-now-list": Games.online.timeout,
            "games-chance": Carousel.initOwl,
            "games-chance-view": Games.chance.init,
            "games-slots-view": Games.slots.init,
            "games-random": Games.random.init,
            "games-moment": Games.random.init,

            "profile-edit": Profile.init.edit,
            "profile-billing": Profile.init.billing,

            "users-view-messages": Messages.after.markRead,

            "lottery": Lottery.init,
            "lottery-view": Lottery.view,
            "lottery-tickets": Ticket.render,
            "lottery-ticket-item": Ticket.activate,
            "lottery-ticket-tabs": Ticket.switch,

            "reports-transactions": Content.initDaterange,
            "reports-referrals": Content.initDaterange,

            "support-faq": Support.init,


        },

        "post": { // new

            "profile-convert": Player.updateBalance,
            "profile-cashout": Profile.after.cashout,
            "lottery-ticket": Ticket.update,
            "lottery-gold": Ticket.update,
            "prizes-exchange-goods": Prize.update.exchange,
            "blog-post-view-comments": Comments.after.reply,
            "communication-comments": Comments.after.reply,
            "communication-notifications": Comments.renderNotifications,
            "users-requests-view": Profile.after.request,
            "games-slots-view": Games.slots.play

        },

        "error": {

            "lottery-gold": Ticket.error.gold,
            "prizes-exchange-goods": Prize.error.exchange,
            "lottery-ticket": Ticket.update

        },

        "put": { // update

            "profile-edit": Profile.update.details,
            "profile-settings": Profile.update.settings,
            "profile-billing": Profile.update.billing,

        },

        "delete": {

            "communication-notifications": Comments.renderNotifications,
            "users-view-messages": Messages.after.markRead,
            "profile-social": Profile.update.details,

        },

        "validate": {

            "lottery-ticket": Ticket.validate,
            "profile-convert": Profile.validate.convert,
            "profile-cashout": Profile.validate.cashout,
            "prizes-exchange-goods": Prize.validate.exchange,
            "blog-post-view-comments": Comments.validate.reply,
            "app-view": Games.online.validate.create,

        },

        "submit": {

            "communication-comments":  Comments.submit,
            "communication-messages":  Comments.submit,
            "blog-post-view-comments": Comments.submit
        }

    };

})();
