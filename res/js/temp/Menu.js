(function () {

    Menu = {

        init: function(){

            D.log('Menu.init','func');

            // Navigation menu
            R.push({
                'box': '.menu',
                'template': 'menu-navigation',
                'json': Menu
            });

            // Balance menu
            R.push({
                'box': '.balance',
                'template': 'menu-balance',
                'json': Player
            });

        },

        after: function () {


            D.log('Menu.after','func');
            $("header a").on('click', Navigation.loadPage);
            $(document).on('click', I.menuBtnItem, Menu.click);
            $('[href="/' + Navigation.path[1] + '"]').first().click();
            Menu.switch();

        },

        click: function (event) {

            D.log('Menu.click','func');
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

            D.log('Menu.switch','func');
            if (Device.isMobile()) {
                // alert("menu.js mobile");
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

            D.log('Menu.hide','func');
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
            D.log('Menu.fix','func');
            (!Device.isMobile() && yScroll > 135) || (Device.isMobile() && yScroll > 0)
                ? $('body').addClass('fixed')
                : $('body').removeClass('fixed');
        }

    }


})();