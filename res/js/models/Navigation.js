(function () {

    typeof I === 'undefined' && (I = {});
    Object.deepExtend(I, {
        /* menu */
        menuMobile: '.menu-mobile',
        menu: '.menu',
        menuMain: '.menu-main',
        menuMore: '.menu-more',
        menuProfile: '.menu-profile',
        menuBalance: '.menu-balance',
        menuLogout: '.menu-logout',
        menuMobileBalance: '.menu-balance-inf',
        menuItem: '.menu-item',
        menuBtn: '.menu-btn',
        menuProfileBtn: '.menu-profile-btn',
        menuBalanceBtn: '.menu-balance-btn',
        menuBtnItem: '.menu-btn-item',
        balanceBtn: '.balance-btn',
        
        /* navigation and tabs */
        Tabs: '.content-box-tabs a',
        Cats: '.content-box-cat a'
    });

    Navigation = {

        path: [],
        loadedBlocks: 0,
        requiredBlocks: null,
        body: null,

        init: function (init) {
            D.log('Navigation.init', 'menu');
            Object.deepExtend(this, init);

            this.requiredBlocks = Device.mobile ? 2 : 3;

            window.addEventListener('popstate', function(event) {
                event.stopPropagation();
                event.preventDefault();
                D.log(["location: " + document.location, "state: " + JSON.stringify(event.state)], 'info');
                var state = event.state; // || {href: document.location.pathname};
                if(state && state.hasOwnProperty('href')){
                    state.state = true;
                    R.push(state);
                }
            }, false);

            return this;

        },

        load: function () {

            this.loadedBlocks = 0;

            // menu buttons
            R.push({
                'template': 'menu-buttons',
                'json': Player
            });

            if(Device.mobile) {

                // Navigation menu mobile
                R.push({
                    'template': 'menu-navigation-mobile',
                    'json': {
                        navigation: this.navigation,
                        balance:  this.balance,
                    }
                });

            } else {

                // Balance menu
                R.push({
                    'template': 'menu-balance',
                    'json': this.balance
                });

                // Navigation menu desktop
                R.push({
                    'template': 'menu-navigation',
                    'json': this.navigation
                });
            }
        },

        ready: function () {
            
            if (++Navigation.loadedBlocks === Navigation.requiredBlocks) {

                D.log('Navigation.ready', 'menu');
                $(I.menuBtnItem).off().on('click', Navigation.menu.click);

                var pathname = window.location.pathname === '/' ? (Config.page || '/blog') : window.location.pathname,
                    selector = 'a[href="/' + U.parse(pathname, 'url') + '"]',
                    anchor = document.querySelector(selector);

                if (anchor) {
                    console.info('byClick:', pathname);
                    DOM.click(anchor);
                } else {
                    console.info('byPush:', pathname);
                    R.push({
                        href: pathname,
                        url: true
                    });
                }

                Navigation.menu.switch();
                Content.updateBanners();
                Player.ping();
                Player.updateBalance();
            }

        },

        menu: {

            click: function (event) {
                D.log(['Navigation.menu.click'], 'menu');
                event.stopPropagation();
                var isActive = $(this).hasClass('active'),
                    isMobile = Device.isMobile(),
                    menuClass = '.' + $(this).attr('class').replace(/ |menu-btn-item|active/g, '');

                Navigation.menu.hide();

                if (isActive){
                    return false;
                }
                else{
                    $('header .active[href]').removeClass('active');
                    $(this).addClass('active');
                }
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
                D.log('Navigation.menu.switch', 'menu');
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
//                console.error(this);
                Navigation.menu.hide();

            },

            hide: function () {
                D.log('Navigation.menu.hide', 'menu');
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
                !this.body && (this.body = document.getElementsByTagName('body')[0]);

                if ((!Device.isMobile() && yScroll >= $('header').offset().top ) || (Device.isMobile() && yScroll > 0)) {
                    if (!this.body.classList.contains('fixed')) {
                        D.log('Navigation.menu.fix.add', 'menu');
                        this.body.classList.add('fixed');
                    }
                } else if (this.body.classList.contains('fixed')) {
                    D.log('Navigation.menu.fix.remove', 'menu');
                    this.body.classList.remove('fixed');
                }
            }
        },

        'do': {

            backBlock: function (event) {

                if (!event.isPropagationStopped()) {

                    event.stopPropagation();
                    D.log(['Navigation.backBlock:', this.parentNode.id], 'info');
                    DOM.remove(DOM.up('.content-box', this));
                    history.back();

                }

                return false;

            }

        }

    };

})();