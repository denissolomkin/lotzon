(function () {

    Device = {

        jsdetect: null,
        gototop: null,
        mobile: false,
        width: 768,

        init: function(init){

            Object.deepExtend(this, init);
            this.jsdetect = document.querySelector('.js-detect');
            this.gototop = document.querySelector('.go-to-top');
            if(this.mobile){
                this.setWidth();
                window.addEventListener('resize', Device.setWidth, true);
            }
        },

        setWidth: function(){

            var width = screen.width;
            switch (true){

                case width <= 320:
                    Device.width = 320;
                    break;

                case width <= 360:
                    Device.width = 360;
                    break;

                case width <= 480:
                    Device.width = 480;
                    break;

                case width <= 640:
                    Device.width = 640;
                    break;

                default:
                    Device.width = 768;
                    break;
            }
        },

        do: {

            hide: function (event) {
                Navigation.menu.hide(event);
                Comments.do.hideNotifications(event);
            },

            resize: function (event) {
                Carousel.initOwl();
                Cards.setupForDevices();

            },

            scroll: function (event) {
                Device.getScroll();
                Device.switchGoTop();
                Navigation.menu.fix();
                Content.infiniteScrolling();
            },

            goTop: function () {
                $('html, body').animate({scrollTop: 0}, 'slow');
                return false;
            }

        },

        get: function () {
            return parseFloat(window.getComputedStyle(this.jsdetect).opacity).toFixed(1);
        },

        detect: function () {

            switch (this.get()) {
                case '0.2':
                    return 'mobile-small';
                case '0.3':
                    return 'mobile-landscape';
                case '0.5':
                    return 'mobile';
                case '0.6':
                    return 'tablet';
                /* todo add
                case '0.7':
                    return 'tablet-landscape';
                 */
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
            return Device.get() < 0.8;
        },

        switchGoTop: function () {
            return true;
            yScroll >= 600
                ? (!this.gototop.style.opacity || this.gototop.style.opacity === 0 || !this.gototop.style.display || this.gototop.style.display === 'none') && fadeIn(this.gototop)
                : this.gototop.style.opacity === "1" && fadeOut(this.gototop);
        },

        onScreen: function (y) {

            y = y || 0;
            var bounds = this.getBoundingClientRect();
            return bounds.top + y < window.innerHeight && bounds.bottom - y > 0;

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
            }
            else {
                cancelFullScreen.call(doc);
            }
        }


    }


})();