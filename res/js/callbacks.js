(function () {

    // callbacks

    C = {

        "init": function(){

            // classes
            I = {
                /* navigation and tabs */
                Tabs: '.content-box-tabs a',
                Cats: '.content-box-cat a',
                TicketTabs: '.ticket-tabs li',
                /* menu */
                menu: '.menu',
                menuMain: '.menu-main',
                menuMore:'.menu-more',
                menuProfile:'.menu-profile',
                menuBalance:'.menu-balance',
                menuItem:'.menu-item',
                menuBtn:'.menu-btn',
                menuProfileBtn:'.menu-profile-btn',
                menuBalanceBtn:'.menu-balance-btn',
                menuBtnItem:'.menu-btn-item',
                balanceBtn:'.balance-btn',
                /* communication */
                comment: '.comment',
                notifications: '.c-notifications',
                showNotifications: '.c-show-notifications',
                hideNotifications: '.c-hide-notifications',
                notificationsList: '.c-notifications-list',
                closeList: '.c-notifications-list .close-list',
                closeNotification: '.c-notification .close-notification',
                textArea: '.message-form-area',
            }

            // extend tickets
            Tickets = $.extend(Tickets, TicketsFunctions);

            // handlers
            $(window).on('resize', windowResize);
            $(window).on('scroll', windowScroll);
            $(document).on('click', hideBlocks);

            /* navigation */
            $(document).on('click', I.Tabs, switchTab);
            $(document).on('click', I.Cats, switchCat);
            $(document).on('click', 'a', loadBlock);
            $(document).on('click', 'div.back', backBlock);

            /* ticket */
            $(document).on('click', I.TicketTabs, switchTicket);

            /* new message*/
            $(document).on('input', ".enter-friend-name", searchMessageAddressee);
            $(document).on('click', ".nm-change", clearMessageAddressee);
            $(document).on('click', ".nm-friend", setMessageAddressee);
            $(document).on('click', ".message-form-btn", sendMessage);
            
            /* go top */
            $('.go-to-top').click(function(){$('html, body').animate({scrollTop:0}, 'slow');return false;});

        },

        "lottery": function(){

            initOwlCarousel();
            renderTicket();

        },

        "blog": function(){

            R.render({
                'box': $('.content-box-content:visible'),
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
                'url': false,
                'callback': function(){

                    $("header a").on('click', loadPage);
                    $('[href="/' + R.Path[1] + '"]').click();

                }
            });

            R.render({
                'box': 'inf-slider',
                'template': 'menu-slider',
                'json': Slider,
                'url': false,
                'callback': function(){

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
                }
            });


            if (!isMobile()) {
                $(I.menuMore).addClass('menu-item');
                $(I.menuProfile).addClass('menu-item');
            }

            // MENU
            $(document).on('click', I.menuBtn, function () {
                var mobile = isMobile();

                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');

                    if (mobile) {
                        $(I.menu).fadeOut(200);
                    } else {
                        $(I.menuMore).fadeOut(200);
                    }
                }
                else {
                    $(I.menuBtnItem).removeClass('active');
                    $(this).addClass('active');

                    $(I.menuBalance).hide();
                    $(I.menuProfile).hide();

                    if (mobile) {
                        $(I.menu).fadeIn(200);
                    } else {
                        $(I.menuMore).fadeIn(200);
                    }
                }
            });

            // PROFILE MENU
            $(document).on('click', I.menuProfileBtn, function () {
                var mobile = isMobile();

                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                    $(I.menuProfile).fadeOut(200);
                }
                else {
                    $(I.menuBtnItem).removeClass('active');
                    $(this).addClass('active');

                    $(I.menuBalance).hide();

                    if (mobile) {
                        $(I.menu).hide();
                    }
                    else {
                        $(I.menuMore).hide();
                    }

                    $(I.menuProfile).fadeIn(200);
                }
            });

            // BALANCE MENU
            $(document).on('click', I.menuBalanceBtn, function () {
                var mobile = isMobile();

                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                    $(I.menuBalance).fadeOut(200);
                }
                else {
                    $(I.menuBtnItem).removeClass('active');
                    $(this).addClass('active');

                    $(I.menuProfile).hide();

                    if (mobile) {
                        $(I.menu).hide();
                    }
                    else {
                        $(I.menuMore).hide();
                    }

                    $(I.menuBalance).fadeIn(200);
                }
            });

            $(document).on('click', I.balanceBtn, function () {
                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                    $(I.menuBalance).fadeOut(200);
                }
                else {
                    $(I.menuBtnItem).removeClass('active');
                    $(this).addClass('active');

                    $(I.menuProfile).hide();
                    $(I.menuBalance).fadeIn(200);
                    $(I.menuMore).hide();

                    $(I.menuBalance).fadeIn(200);
                }
            });

            // Stop Propogation
            $(I.menuItem).on('click', function (event) {
                event.stopPropagation();
            });

            $(I.menuBtnItem).on('click', function (event) {
                event.stopPropagation();
            });

        },

        "communications-messages": function(){

            /* ========================================================= */
            //                     COMMUNICATION
            /* ========================================================= */

            // COMMENTS ============================== //

            $(I.comment).on('click', function (event) {
                event.stopPropagation();
                $(I.comment).removeClass('active');
                if (detectDevice() === 'mobile') {
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
                }
                else {
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