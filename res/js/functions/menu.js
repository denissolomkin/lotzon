(function () {

    Menu = {

        init: function(){

            // Slider carousel
            R.push({
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

            // Balance menu
            R.push({
                'box': 'balance',
                'template': 'menu-balance',
                'json': Player,
                'url': false,
                'callback': function () {

                    $("header a").on('click', Navigation.loadPage);
                    $(document).on('click', I.menuBtnItem, Menu.click);
                    $('[href="/' + Navigation.path[1] + '"]').first().click();
                    Menu.switch();

                }
            });

        },

        click: function (event) {

            event.stopPropagation();

            var isActive = $(this).hasClass('active'),
                isMobile = Device.isMobile(),
                menuClass = '.' + $(this).attr('class').replace(/ |menu-btn-item|active/g, '');

            Menu.hide();

            if (isActive)
                return false;
            else
                $(this).addClass('active');

            switch (menuClass) {
                case I.menuBtn:
                    if (isMobile) {
                        $(I.menuMain).show();
                        $(I.menuMore).show();
                        $(I.menu).fadeIn(200);
                    } else {
                        $(I.menuMore).fadeIn(200);
                    }
                    break;

                case I.menuProfileBtn:
                    if (isMobile) {
                        $(I.menuProfile).show();
                        $(I.menuMain).hide();
                        $(I.menu).fadeIn(200);
                    } else {
                        $(I.menuProfile).fadeIn(200);
                    }
                    break;

                case I.balanceBtn:
                case I.menuBalanceBtn:
                    $(I.menuBalance).fadeIn(200);
                    break;

                default:
                    break;
            }

        },

        switch: function () {

            if (Device.isMobile()) {
                $(I.menuMore).removeClass('menu-item');
                $(I.menuProfile).removeClass('menu-item');
                $(I.balanceBtn).hide();
            } else {
                $(I.menuMore).addClass('menu-item');
                $(I.menuProfile).addClass('menu-item');
                $(I.balanceBtn).show();
            }

            this.hide();

        },

        hide: function () {

            $(I.menuProfile + ":visible").fadeOut(200);
            $(I.menuBalance + ":visible").fadeOut(200);
            $(I.menuMore + ":visible").fadeOut(200);

            if (Device.isMobile()) {
                $(I.menu + ":visible").hide();
                $(I.menuMain + ":visible").fadeOut(200);
            }

            $(I.menuBtnItem + ".active").removeClass('active');
        },

        fix: function () {
            (!Device.isMobile() && yScroll > 135) || (Device.isMobile() && yScroll > 0)
                ? $('body').addClass('fixed')
                : $('body').removeClass('fixed');
        }

    }


})();