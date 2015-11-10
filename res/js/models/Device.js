(function () {

    Device = {

        do: {

            hide: function (event) {
                Navigation.menu.hide(event);
                Comments.hide(event);
                Profile.do.hideFavorite(event);

            },

            resize: function (event) {
                Navigation.menu.switch();
                Ticket.setBallsMargins();
                Carousel.initOwl();
                Cards.setupForDevices();

            },

            scroll: function (event) {
                Device.getScroll();
                Device.switchGoTop();
                Navigation.menu.fix();

            },

            goTop: function () {
                $('html, body').animate({scrollTop: 0}, 'slow');
                return false;
            }

        },

        get: function () {
            return parseFloat($('.js-detect').css('opacity')).toFixed(1);
        },

        detect: function () {

            switch (parseFloat($('.js-detect').css('opacity')).toFixed(1)) {
                case '0.2':
                    return 'mobile-small';
                case '0.3':
                    return 'mobile-landscape';
                case '0.5':
                    return 'mobile';
                case '0.6':
                    return 'tablet';
                case '0.8':
                    return 'desktop';
                case '1.0':
                    return 'desktop-hd';

            }
        },

        getScroll: function () {
            if (self.pageYOffset) {
                yScroll = self.pageYOffset;
                xScroll = self.pageXOffset;
            } else if (document.documentElement && document.documentElement.scrollTop) {
                yScroll = document.documentElement.scrollTop;
                xScroll = document.documentElement.scrollLeft;
            } else if (document.body) {
                yScroll = document.body.scrollTop;
                xScroll = document.body.scrollLeft;
            }
        },

        isMobile: function () {
            return $('.js-detect').css('opacity') < 0.8;
        },

        switchGoTop: function () {
            yScroll >= 150 ? $(I.goTop).fadeIn(200) : $(I.goTop).fadeOut(300);
        },

        toggleFullScreen: function () {

            var doc = window.document;

            var docEl = doc.documentElement;

            var requestFullScreen = docEl.requestFullscreen || docEl.mozRequestFullscreen || docEl.webkitRequestFullscreen || docEl.msRequestFullscreen;
            var cancelFullScreen = doc.exitFullscreen || doc.mozCancelFullScreen || doc.webkitExitFullscreen || doc.msExitFullscreen;


            if (!document.fullscreenElement &&    // alternative standard method
                !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {  // current working methods
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen();
                } else if (document.documentElement.msRequestFullscreen) {
                    document.documentElement.msRequestFullscreen();
                } else if (document.documentElement.mozRequestFullScreen) {
                    document.documentElement.mozRequestFullScreen();
                } else if (document.documentElement.webkitRequestFullscreen) {
                    document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
                }
                console.log("вызвался");
            }
            else {
                cancelFullScreen.call(doc);
            }
        }


    }


})();