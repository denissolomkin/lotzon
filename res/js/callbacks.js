(function () {

    // callbacks

    C = {

        "init": function(){

            // variables
            $Tabs = '.content-box-tabs a';
            $Cats = '.content-box-cat a';
            $TicketTabs = '.ticket-tabs li';

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

            // handlers
            $(window).on('resize', windowResize);
            $(document).on('click', $Tabs, switchTab);
            $(document).on('click', $Cats, switchCat);
            $(document).on('click', 'a', loadBlock);
            $(document).on('click', 'div.back', backBlock);
            $("header a").on('click', loadPage);
            $(document).on('click', $TicketTabs, switchTicket);

        },

        "lottery": function(){

            runOwlCarousel();

        },

        "blog": function(){

            runOwlCarousel();

        },

        "menu": function () {

            /* ========================================================= */
            //                        MENUS
            /* ========================================================= */


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

        }
    };

})();