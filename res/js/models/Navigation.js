(function () {

    Navigation = {

        path: [],
        loadedBlocks: 0,

        init: function (init) {

            D.log('Navigation.init', 'func');
            Object.deepExtend(this, init);

            window.onpopstate = function (event) {
                D.log(["location: " + document.location, "state: " + JSON.stringify(event.state)], 'info');
                if (event.state)
                    R.push(event.state);
            };


            return this;

        },

        load: function () {

            this.loadedBlocks = 0;

            // Navigation menu
            R.push({
                'box': '.menu',
                'template': 'menu-navigation',
                'json': this
            });

            // Balance menu
            R.push({
                'box': '.balance',
                'template': 'menu-balance',
                'json': Player
            });
        },

        check: function () {

            if (++Navigation.loadedBlocks === 2)
                Navigation.ready()

        },

        ready: function () {

            this.path = window.location.pathname.split('/');
            this.path[1] = this.path[1] || 'blog';

            D.log('Navigation.ready', 'func');
            $("header a").off().on('click', this.loadPage);
            $(I.menuBtnItem).off().on('click', this.click);

            var element = [];

            while(this.path.length > 1 && !element.length){
                element = $('[href="' + this.path.join("/") + '"]').first().click();
                this.path.pop();
            }

            this.switch();

        },

        click: function (event) {

            D.log('Navigation.click', 'func');
            event.stopPropagation();

            var isActive = $(this).hasClass('active'),
                isMobile = Device.isMobile(),
                menuClass = '.' + $(this).attr('class').replace(/ |menu-btn-item|active/g, '');

            Navigation.hide();

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

            D.log('Navigation.switch', 'func');
            if (Device.isMobile()) {
                $(I.menuMore).removeClass('menu-item');
                $(I.menuProfile).removeClass('menu-item');
                $(I.balanceBtn).hide();
            } else {
                $(I.menuMore).addClass('menu-item');
                $(I.menuProfile).addClass('menu-item');
                $(I.balanceBtn).show();
            }

            Navigation.hide();

        },

        hide: function () {

            D.log('Navigation.hide', 'func');
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
            D.log('Navigation.fix', 'func');
            (!Device.isMobile() && yScroll > 135) || (Device.isMobile() && yScroll > 0)
                ? $('body').addClass('fixed')
                : $('body').removeClass('fixed');
        },

        // handler functions
        loadPage: function (event) {

            if (!event.isPropagationStopped()) {

                event.stopPropagation();
                var tab = $(this),
                    box = $('.content-top');

                D.log(['Navigation.loadPage:', tab.attr('href')], 'info');

                R.push.call(this, {
                    box: box,
                    after: function () {
                        $("html, body").animate({scrollTop: 0}, 'slow');
                    },
                    url: true
                });

                Navigation.hide();
            }

            return false;

        },

        loadBlock: function (event) {

            if (!event.isPropagationStopped()) {

                event.stopPropagation();
                var tab = $(this),
                    box = tab.parents('.content-main').length ? tab.parents('.content-main') : tab.parents('.content-top');

                D.log(['Navigation.loadBlock:', tab.attr('href')], 'info');

                R.push.call(this, {
                    box: box,
                    after: function (options) {
                        $(options.findClass).addClass('slideInRight');
                        $("html, body").animate({scrollTop: 0}, 'slow');
                    },
                    url: true
                });

            }

            return false;

        },

        backBlock: function (event) {

            if (!event.isPropagationStopped()) {

                event.stopPropagation();
                var tab = $(this),
                    box = $(this).parents('.content-box');


                D.log(['Navigation.backBlock:', tab.attr('href')], 'info');

                box.prev().addClass('slideInLeft').show()
                    .find(I.Tabs + '.active').click();

                $(this).parents('.content-box').remove();
                history.back();

            }

            return false;

        },

        switchTab: function (event) {

            if (!event.isPropagationStopped()) {

                event.stopPropagation();
                var tab = $(this),
                    box = tab.parents('.content-box').find('.content-box-content'),
                    href = tab.attr('href');

                D.log(['Navigation.switchTab:', href], 'info');

                if (U.isAnchor(href)) {

                    $(I.Tabs, tab.parents('.content-box-header')).removeClass('active');
                    $(' > div', box).hide();
                    $('.content-box-item.' + href, box).show();
                    tab.addClass('active');
                }

                else {
                    R.push.call(this, {
                        box: box
                    });
                }
            }

            return false;
        },

        switchCat: function (event) {

            if (!event.isPropagationStopped()) {

                event.stopPropagation();
                var tab = $(this),
                    box = tab.parents('.with-cat');

                D.log(['switchCat:', tab.attr('href')], 'info');

                // with animation
                if ($(I.Cats, box).filter('.active').length) {

                    $(I.Cats, box).removeClass('active');
                    $('.content-box-item-content > div', box).fadeOut(200);
                    setTimeout(function () {
                        $('.content-box-item-content > div.category-' + tab.data('category'), box).fadeIn(200);
                    }, 200);

                }

                // without animation
                else {
                    $('.content-box-item-content > div', box).hide();
                    $('.content-box-item-content > div.category-' + tab.data('category'), box).show();
                }

                tab.addClass('active');

            }

            return false;
        }

    };

})();