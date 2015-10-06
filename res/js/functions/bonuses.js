(function () {

    /* ========================================================= */
    //                         BONUSES
    /* ========================================================= */

    // POPUP BANNER ---------------------- //

    $(document).on('click','.bonus-banner-view-btn', function () {

        var $popupBanner = $('.popup-banner');

        $popupBanner.css({
            top: ($(window).outerHeight() / 2 - $popupBanner.outerHeight() / 2) + 'px',
            left: ($(window).outerWidth() / 2 - $popupBanner.outerWidth() / 2) + 'px'
        }).fadeIn('fast');
    });

    $(document).on('click', '.popup-banner .close', function () {
        $('.popup-banner').fadeOut('fast');
    });
    //  --------------------------------- //

    /* ========================================================= */
    /* ========================================================= */

})();