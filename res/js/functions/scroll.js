$(function () {

    /* ========================================================= */
    //                        CALLBACK FUNCTION
    /* ========================================================= */

    windowScroll = function () {
        getScroll();
        switchGoTop();
        fixTopMenu();
    }

    /* ========================================================= */
    //                        PARTIAL FUNCTIONS
    /* ========================================================= */

    switchGoTop = function(){
        yScroll >= 150 ? $('.go-to-top').fadeIn(200) : $('.go-to-top').fadeOut(300);
    }

    getScroll = function(){
        if (self.pageYOffset){
            yScroll = self.pageYOffset;
            xScroll = self.pageXOffset;
        } else if (document.documentElement && document.documentElement.scrollTop){
            yScroll = document.documentElement.scrollTop;
            xScroll = document.documentElement.scrollLeft;
        } else if (document.body){
            yScroll = document.body.scrollTop;
            xScroll = document.body.scrollLeft;
        }
    };

    fixTopMenu = function(){
        (!isMobile() && yScroll > 135) || (isMobile() && yScroll > 0) ? $('body').addClass('fixed') : $('body').removeClass('fixed');
    }

});