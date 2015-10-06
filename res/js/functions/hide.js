(function () {

    /* ========================================================= */
    //                        CALLBACK FUNCTION
    /* ========================================================= */

    hideBlocks = function () {
        hideMenu();
        hideComments();
    }

    /* ========================================================= */
    //                        PARTIAL FUNCTIONS
    /* ========================================================= */

    hideMenu = function(){

        $(I.menuProfile+":visible").fadeOut(200);
        $(I.menuBalance+":visible").fadeOut(200);
        $(I.menuMore+":visible").fadeOut(200);

        if (isMobile()) {
            $(I.menu+":visible").hide();
            $(I.menuMain+":visible").fadeOut(200);
        }

        $(I.menuBtnItem+".active").removeClass('active');
    }

    hideComments = function(){

        $(I.comment).removeClass('active');

        if (!$(event.target).hasClass('c-show-notifications')) {
            if ($(I.notificationsList).is(':visible')) {
                $(I.notificationsList).slideUp('fast');
            }
        }
    }

})();