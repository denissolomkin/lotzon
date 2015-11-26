(function () {

    Navigation = {

        path: [],
        loadedBlocks: 0,
        requiredBlocks: 2,

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

            // Balance menu
            R.push({
                'box': '.balance',
                'template': 'menu-balance',
                'json': Player
            });

            // Navigation menu
            R.push({
                'box': '.menu',
                'template': 'menu-navigation',
                'json': this.navigation
            });

            // Navigation menu mobile
            R.push({
                'box': 'nav-mobile',
                'template': 'menu-navigation-mobile',
                'json': this.navigation
            });
        },

        ready: function () {


            if (++Navigation.loadedBlocks === Navigation.requiredBlocks) {

                Navigation.path = window.location.pathname.split('/');
                Navigation.path[1] = Navigation.path[1] || 'blog';

                D.log('Navigation.ready', 'func');
                $("header a, .menu-mobile a").off().on('click', Navigation.do.loadPage);

                $(I.menuBtnItem).off().on('click', Navigation.menu.click);

                var element = [];

                while (Navigation.path.length > 1 && !element.length) {
                    element = $('[href="' + Navigation.path.join("/") + '"]').first().click();
                    Navigation.path.pop();
                }

                Navigation.menu.switch();
            }

        },

        menu: {

            click: function (event) {
                // alert("click works");
                D.log(['Navigation.menu.click'], 'func');
                event.stopPropagation();
                // alert("click works after stopPropagation");
                var isActive = $(this).hasClass('active'),
                    isMobile = Device.isMobile(),
                    menuClass = '.' + $(this).attr('class').replace(/ |menu-btn-item|active/g, '');

                Navigation.menu.hide();
                // return;
                if (isActive)
                    // alert("click works aisActive");
                    return false;
                else
                    $(this).addClass('active');

                switch (menuClass) {
                    case I.menuBtn:
                        if (isMobile) {
                            $(I.menuMobile).removeClass('pushy-left').addClass('pushy-open');
                            $('.wrapper').addClass('wrapper-push');
                            $(I.menuMain).show();
                            $(I.menuMore).show();
                            $(I.menuLogout).show();
                            $(I.menuProfile).hide();
                            $(I.menuMobileBalance).show();
                            $('body').addClass('pushy-active');
                        } else {
                            $(I.menuMore).fadeIn(200);
                            $(I.menuLogout).fadeIn(200);
                        }
                        break;

                    case I.menuProfileBtn:
                        if (isMobile) {
                            $(I.menuMobile).removeClass('pushy-left').addClass('pushy-open');
                            $('.wrapper').addClass('wrapper-push');
                            $(I.menuProfile).show();
                            $(I.menuMain).hide();
                            $(I.menuMore).hide();
                            $(I.menuLogout).hide();
                            $(I.menuMobileBalance).hide();
                            $('body').addClass('pushy-active');
                            
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

                D.log('Navigation.menu.switch', 'func');
                if (Device.isMobile()) {
                    $(I.menuMore).removeClass('menu-item');
                    $(I.menuProfile).removeClass('menu-item');
                    $(I.menuLogout).removeClass('menu-item');
                    $(I.balanceBtn).hide();
                } else {
                    $(I.menuMore).addClass('menu-item');
                    $(I.menuProfile).addClass('menu-item');
                    $(I.menuLogout).addClass('menu-item');
                    $(I.balanceBtn).show();
                }

                Navigation.menu.hide();

            },

            hide: function () {
                // alert("hide works");
                D.log('Navigation.menu.hide', 'func');
                $(I.menuProfile + ":visible").fadeOut(200);
                $(I.menuBalance + ":visible").fadeOut(200);
                $(I.menuMore + ":visible").fadeOut(200);
                $(I.menuLogout + ":visible").fadeOut(200);

                if (Device.isMobile()) {
                    $(I.menuMobile).removeClass('pushy-open').addClass('pushy-left');
                    $('.wrapper').removeClass('wrapper-push');
                    $('body').removeClass('pushy-active');
                }

                $(I.menuBtnItem + ".active").removeClass('active');
            },

            fix: function () {
                D.log('Navigation.menu.fix', 'func');
                (!Device.isMobile() && yScroll > 135) || (Device.isMobile() && yScroll > 0)
                    ? $('body').addClass('fixed')
                    : $('body').removeClass('fixed');
            }
        },

        'do': {

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

                    Navigation.menu.hide();
                    Content.updateBanners();
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
                        box = tab.parents('.with-cat'),
                        href = this.href;

                    D.log(['switchCat:', this.href], 'info');

                    if (U.isAnchor(href)) {

                        href = U.parse(href, 'anchor')

                        // with animation
                        if ($(I.Cats, box).filter('.active').length) {

                            $(I.Cats, box).removeClass('active');
                            $('.content-box-item-content > div', box).fadeOut(200);
                            setTimeout(function () {
                                $('.content-box-item-content > div.category-' + href, box).fadeIn(200);
                            }, 200);

                        }

                        // without animation
                        else {
                            $('.content-box-item-content > div', box).hide();
                            $('.content-box-item-content > div.category-' + href, box).show();
                        }

                        tab.addClass('active');

                    } else {

                        R.push.call(this, {
                            box: box,
                            href: href,
                            tab: tab
                        });
                    }

                }

                return false;
            }
        }

    };

})();