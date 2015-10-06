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

        if ( isMobile() ) {
            $(I.menuItem).fadeOut(200);
        }
        else {
            $(I.menuProfile).fadeOut(200);
            $(I.menuBalance).fadeOut(200);
            $(I.menuMore).fadeOut(200);
        }
        $(I.menuBtnItem).removeClass('active');

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