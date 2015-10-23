$(function () {

    Navigation = {

        path: [],

        init: function () {

            window.onpopstate = function (event) {
                D.log(["location: " + document.location, "state: " + JSON.stringify(event.state)],'info');
                if(event.state)
                    R.push(event.state);
            };

            this.initPath();
        },

        initPath: function () {

            this.path = window.location.pathname.split('/');
            this.path[1] = Navigation.path[1] || 'blog';

        },

        // handler functions
        loadPage: function (event) {

            if (!event.isPropagationStopped()) {

                event.stopPropagation();
                var tab = $(this),
                    box = $('.content-top');

                D.log(['loadPage:', tab.attr('href')], 'info');

                R.push.call(this, {
                    box: box,
                    callback: function () {
                        $("html, body").animate({scrollTop: 0}, 'slow');
                    }
                });

                Menu.hide();

            }
            return false;

        },

        loadBlock: function (event) {

            if (!event.isPropagationStopped()) {

                event.stopPropagation();
                var tab = $(this),
                    box = tab.parents('.content-main').length ? tab.parents('.content-main') : tab.parents('.content-top');

                D.log(['loadBlock:', tab.attr('href')], 'info');

                R.push.call(this, {
                    box: box,
                    callback: function (rendered, findClass) {
                        $(findClass).addClass('slideInRight');
                        $("html, body").animate({scrollTop: 0}, 'slow');
                    }
                });

            }

            return false;

        },

        backBlock: function (event) {

            if (!event.isPropagationStopped()) {

                event.stopPropagation();
                var tab = $(this),
                    box = $(this).parents('.content-box');


                D.log(['backBlock:', tab.attr('href')], 'info');

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

                D.log(['switchTab:', href], 'info');

                if (U.isAnchor(href)) {

                    $(I.Tabs, tab.parents('.content-box-header')).removeClass('active');
                    $(' > div', box).hide();
                    $('.content-box-item.' + href, box).show();
                    tab.addClass('active');
                }

                else {
                    R.push.call(this, {
                        box: box,
                        url: false
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
    }

    /* ========================================================= */
    /* ========================================================= */

});