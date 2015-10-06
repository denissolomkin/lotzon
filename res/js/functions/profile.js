(function () {

    /* ========================================================= */
    //                        CABINET
    /* ========================================================= */

    var $balanceTab = $('.cabinet-balance-tab');
    var $balanceMenu = $('.balance-menu');

    $balanceTab.on('click', function (event) {

        event.stopPropagation();

        if ($balanceMenu.is(':hidden')) {
            $balanceMenu.slideDown('fast', function () {
                $balanceTab.addClass('active');
            });
        }
        else {
            $balanceMenu.slideUp('fast', function () {
                $balanceTab.removeClass('active');
                $(this).css('display', '');
            });
        }

    });

    // REFERRALS ===================================== //
    // Active Icon ------------------------ //
    var $activeIcon = $('.r-active-icon');

    $activeIcon.hover(function () {
        $(this).parent().find('.r-active-inf').show();
    }, function () {
        $(this).parent().find('.r-active-inf').hide();
    });
    // ----------------------------------- //
    // =============================================== //


    // ACCOUNT ======================================= //
    // Favorite Combination ---------------- //
    $(document).on('click', function (event) {
        if (!$(event.target).closest(".ae-current-combination").length && !$(event.target).closest(".ae-combination-box").length) {
            $(".ae-combination-box").fadeOut(200);
            $('.ae-current-combination li').removeClass('on');
        }
        ;
    });

    $('.ae-current-combination li').on('click', function () {
        $('.ae-save-btn').addClass('save');

        if (!$(this).hasClass('on')) {
            $('.ae-current-combination li').removeClass('on');
            var n = $(this).text();

            $('.ae-combination-box li.selected').each(function () {
                if ($(this).text() == n)$(this).removeClass('selected');
            });

            $(this).text('');
            $(this).addClass('on');
            $('.ae-combination-box').fadeIn(200);
        }
        else {
            $(this).removeClass('on');
            $('.ae-combination-box').fadeOut(200);
        }
    });

    $('.ae-combination-box li').on('click', function () {
        if (!$(this).hasClass('selected')) {
            var n = $(this).text();
            $('.ae-current-combination li.on').text(n);
            $(this).addClass('selected');
            $('.ae-combination-box').fadeOut(200);
            $('.ae-current-combination li.on').removeClass('on');
        }
    });
    // ----------------------------------- //
    // =============================================== //

    /* ========================================================= */
    /* ========================================================= */

})();