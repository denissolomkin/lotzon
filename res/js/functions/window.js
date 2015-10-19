(function () {

    W = {

        hide: function (event) {
            Menu.hide();
            Comments.hide(event);
        },

        resize: function (event) {
            Menu.switch();
            Ticket.setBallsMargins();
            Carousel.initOwl();
        },

        scroll: function (event) {
            W.getScroll();
            W.switchGoTop();
            Menu.fix();
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

        switchGoTop: function () {
            yScroll >= 150 ? $(I.goTop).fadeIn(200) : $(I.goTop).fadeOut(300);
        },

        goTop: function () {
            $('html, body').animate({scrollTop: 0}, 'slow');
            return false;
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
        },
    }

})();