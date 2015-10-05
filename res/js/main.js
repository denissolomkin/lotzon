(function () {


    var $body = $('body');

    // DETECT DEVICE ========================== //
    $body.append('<span class="js-detect"></span>');
    detectDevice = function () {
        switch ($('.js-detect').css('opacity')) {
            case '0.1':
                return 'mobile';
            case '0.2':
                return 'tablet';
            case '0.3':
                return 'desktop';
            case '0.4':
                return 'desktop-hd';
            case '0.5':
                return 'smallMobile';
            case '0.6':
                return 'landscape';
        }
    }
    // ======================================= //

    function menuMobile() {
        var device = detectDevice();
        var menuMobile = (device === 'mobile' || device === 'tablet') ? true : false;
        return menuMobile;
    }


    // OWL CAROUSEL =========================== //
    var $matchesCarousel = $('.matches-inf-wrapper.carousel');
    var owl = null;

    if ($matchesCarousel.css('box-shadow') !== 'none') {
        $matchesCarousel.owlCarousel({
            singleItem: true,
            autoPlay: false
        });

        var owl = $matchesCarousel.data('owlCarousel');
    }
    // ======================================== //



    // CALENDAR ================================ //
    $('input[name="daterange"]').daterangepicker(
        {
            locale: {
                format: 'DD.MM.YYYY'
            },
            "autoApply": true
        }
    );
    // ======================================== //

    /* ========================================================= */
    //                        MENUS
    /* ========================================================= */


    /* ========================================================= */
    /* ========================================================= */


	/* ========================================================= */
	//                        TICKETS
	/* ========================================================= */

	// TICKET ================================= //
	var $ticketBox = $('.ticket-item');
	var $ticketBalls = $('.ticket-numbers li');
	var $ticketActions = $('.ticket-actions li');
	var $ticketNumbersBox = $('.ticket-numbers');

	if ( detectDevice() === 'mobile' ) {
		setBallsMargins();
	}

	function setBallsMargins() {
		var result = getBallsMargins($ticketBox, $ticketBalls);
		var margin = result.margin;
		var padding = result.padding;

		$ticketBox.css({
			'padding-left': padding+'px',
			'padding-right': padding+'px',
		});

		$ticketNumbersBox.css('margin-right', -margin+'px');

		$ticketBalls.css({
			'margin-right': margin + 'px',
			'margin-bottom': 0.7*margin + 'px'
		});

		$ticketActions.css({
			'margin-right': margin + 'px'
		});
	}

	function getBallsMargins($box, $balls) {
		var boxWidth = $box.outerWidth();
		var ballWidth = $balls.outerWidth();
		var margin, padding, count;

		if ( boxWidth < 7*1.8*ballWidth || boxWidth / 1.8*ballWidth < 8 ) {
			margin = Math.floor((boxWidth - 7*ballWidth) / 7);
			padding = margin/2 + (boxWidth - 7*ballWidth - 7*margin) / 2;
		}
		else {
			count = Math.floor(boxWidth / (1.8*ballWidth));
			margin = Math.floor((boxWidth - count * ballWidth) / (count));
			padding = margin/2 + (boxWidth - count*ballWidth - (count)*margin) / 2;
		}

		return {
			margin: margin,
			padding: padding
		};
	}
	// ======================================== //

	/* ========================================================= */
	/* ========================================================= */

    var $menuItem = $('.menu-item');

    var $menuBtn = $('.menu-btn');
    var $menuProfileBtn = $('.menu-profile-btn');
    var $menuBalanceBtn = $('.menu-balance-btn');

    var $menuBtnItem = $('.menu-btn-item');

    var $balanceBtn = $('.balance-btn');

    if ( !menuMobile() ) {
        $menuMore.addClass('menu-item');
    }

    // MENU
    $menuBtn.on('click', function() {
        var mobile = menuMobile();

        if ( $(this).hasClass('active') ) {
            $(this).removeClass('active');

            if ( mobile ) {
                $menu.fadeOut(200);
            }
            else {
                $menuMore.fadeOut(200);
            }
        }
        else {
            $menuBtnItem.removeClass('active');
            $(this).addClass('active');

            $menuBalance.hide();
            $menuProfile.hide();

            if ( mobile ) {
                $menu.fadeIn(200);
            }
            else {
                $menuMore.fadeIn(200);
            }
        }
    });

    // PROFILE MENU
    $menuProfileBtn.on('click', function() {
        var mobile = menuMobile();

        if ( $(this).hasClass('active') ) {
            $(this).removeClass('active');
            $menuProfile.fadeOut(200);
        }
        else {
            $menuBtnItem.removeClass('active');
            $(this).addClass('active');

            $menuBalance.hide();

            if ( mobile ) {
                $menu.hide();
            }
            else {
                $menuMore.hide();
            }

            $menuProfile.fadeIn(200);
        }
    });

    // BALANCE MENU
    $menuBalanceBtn.on('click', function() {
        var mobile = menuMobile();

        if ( $(this).hasClass('active') ) {
            $(this).removeClass('active');
            $menuBalance.fadeOut(200);
        }
        else {
            $menuBtnItem.removeClass('active');
            $(this).addClass('active');

            $menuProfile.hide();

            if ( mobile ) {
                $menu.hide();
            }
            else {
                $menuMore.hide();
            }

            $menuBalance.fadeIn(200);
        }
    });

    $balanceBtn.on('click', function() {
        if ( $(this).hasClass('active') ) {
            $(this).removeClass('active');
            $menuBalance.fadeOut(200);
        }
        else {
            $menuBtnItem.removeClass('active');
            $(this).addClass('active');

            $menuProfile.hide();
            $menuBalance.fadeIn(200);
            $menuMore.hide();

            $menuBalance.fadeIn(200);
        }
    });

    // Stop Propogation
    $menuItem.on('click', function(event) {
        event.stopPropagation();
    });

    $menuBtnItem.on('click', function(event) {
        event.stopPropagation();
    });


    /* ========================================================= */
    /* ========================================================= */


    /* ========================================================= */
    //                        TICKETS
    /* ========================================================= */

    // TICKET ================================= //
    var $ticketBox = '.ticket-item';
    var $ticketBalls = '.ticket-numbers li';
    var $ticketActions = '.ticket-actions li';
    var $ticketNumbersBox = '.ticket-numbers';

    if (detectDevice() === 'mobile') {
        setBallsMargins();
    }

    function setBallsMargins() {

        console.log('setBallsMargins');
        var ticketBox = $($ticketBox);
        var ticketBalls = $($ticketBalls);
        var ticketActions = $($ticketActions);
        var ticketNumbersBox = $($ticketNumbersBox);

        var result = getBallsMargins(ticketBox, ticketBalls);
        var margin = result.margin;
        var padding = result.padding;

        ticketBox.css({
            'padding-left': padding + 'px',
            'padding-right': padding + 'px',
        });

        ticketNumbersBox.css('margin-right', -margin + 'px');

        ticketBalls.css({
            'margin-right': margin + 'px',
            'margin-bottom': 0.7 * margin + 'px'
        });

        ticketActions.css({
            'margin-right': margin + 'px'
        });
    }

    function getBallsMargins($box, $balls) {
        var boxWidth = $box.outerWidth();
        var ballWidth = $balls.outerWidth();
        var margin, padding, count;

        if (boxWidth < 7 * 1.8 * ballWidth || boxWidth / 1.8 * ballWidth < 8) {
            margin = Math.floor((boxWidth - 7 * ballWidth) / 7);
            padding = margin / 2 + (boxWidth - 7 * ballWidth - 7 * margin) / 2;
        }
        else {
            count = Math.floor(boxWidth / (1.8 * ballWidth));
            margin = Math.floor((boxWidth - count * ballWidth) / (count));
            padding = margin / 2 + (boxWidth - count * ballWidth - (count) * margin) / 2;
        }

        return {
            margin: margin,
            padding: padding
        };
    }

    // ======================================== //

    /* ========================================================= */
    /* ========================================================= */


    /* ========================================================= */
    //                         BONUSES
    /* ========================================================= */

    // POPUP BANNER ---------------------- //
    var $popupBanner = $('.popup-banner');
    var $openPopup = $('.bonus-banner-view-btn');
    var $closePopup = $('.popup-banner .close');

    $openPopup.on('click', function () {
        $popupBanner.css({
            top: ($(window).outerHeight() / 2 - $popupBanner.outerHeight() / 2) + 'px',
            left: ($(window).outerWidth() / 2 - $popupBanner.outerWidth() / 2) + 'px'
        }).fadeIn('fast');
    });

    $closePopup.on('click', function () {
        $popupBanner.fadeOut('fast');
    });
    //  --------------------------------- //

    /* ========================================================= */
    /* ========================================================= */


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


    /* ========================================================= */
    //                     COMMUNICATION
    /* ========================================================= */

    // COMMENTS ============================== //
    var $comment = $('.comment');
    var $notifications = $('.c-notifications');
    var $showNotifications = $('.c-show-notifications');
    var $hideNotifications = $('.c-hide-notifications');
    var $notificationsList = $('.c-notifications-list');
    var $closeList = $('.c-notifications-list .close-list');
    var $closeNotification = $('.c-notification .close-notification');
    var $textArea = $('.message-form-area');

    $comment.on('click', function (event) {
        event.stopPropagation();
        $comment.removeClass('active');
        if (detectDevice() === 'mobile') {
            $(this).addClass('active');
        }
    });

    $hideNotifications.on('click', function () {
        $notifications.fadeOut('fast', function () {
            $notifications.remove();
        });
    });

    $closeList.on('click', function () {
        $notifications.slideUp('fast', function () {
            $notifications.remove();
        });
    });

    $closeNotification.on('click', function () {
        if ($notificationsList.find('.c-notification').length < 2) {
            $notifications.slideUp('fast', function () {
                $notifications.remove();
            });
        }
        else {
            $(this).parent().slideUp('fast', function () {
                $(this).remove();
            });
        }
    });

    $showNotifications.on('click', function (event) {
        $notificationsList.slideDown('fast');
    });

    $notificationsList.on('click', function (event) {
        event.stopPropagation();
    });

    // $notifications.on('click', function(event) {
    // 	event.stopPropagation();
    // });

    // TEXTAREA ------------------------- //
    function h(e) {
        $(e).css({'height': 'auto', 'overflow-y': 'hidden'}).height(e.scrollHeight);
    }

    $textArea.each(function () {
        h(this);
    }).on('input', function () {
        h(this);
    });
    // --------------------------------- //
    // ======================================= //

    /* ========================================================= */
    /* ========================================================= */


    /* ========================================================= */
    //                        HIDE BLOCKS
    /* ========================================================= */

    $(document).on('click', function (event) {

        // MENU ==================================== //
        var mobile = menuMobile();

        if ( mobile ) {
            $menuItem.fadeOut(200);
        }
        else {
            $menuProfile.fadeOut(200);
            $menuBalance.fadeOut(200);
            $menuMore.fadeOut(200);
        }


        $menuBtnItem.removeClass('active');
        // ======================================== //


        // COMMENTS ========================= ===== //
        $(document).on('click', function () {
            $comment.removeClass('active');
        });

        if (!$(event.target).hasClass('c-show-notifications')) {
            if ($notificationsList.is(':visible')) {
                $notificationsList.slideUp('fast');
            }
        }
        // ======================================== //

    });

    /* ========================================================= */
    /* ========================================================= */


    /* ========================================================= */
    //                        ON RESIZE
    /* ========================================================= */

    $(window).on('resize', function () {
        var device = detectDevice();

        // MENU =================================== //
        var mobile  = menuMobile();
        if ( mobile ) {
            $menuMore.removeClass('menu-item');

            if ( $menuBtn.hasClass('active') ) {
                $menu.show();
            }
            else {
                $menu.hide();
            }
        }
        else {
            $menuMore.addClass('menu-item');

            if ( $menuBtn.hasClass('active') ) {
                $menuMore.show();
            }
            else {
                $menuMore.hide();
            }
        }
        // ======================================== //



        // TICKET ================================= //
        if (device === 'mobile') {
            setBallsMargins();
        }
        // ======================================== //


        // OWL CAROUSEL =========================== //
        if ($matchesCarousel.css('box-shadow') === 'none') {
            if (owl !== null) {
                owl.destroy();
                owl = null;
            }
        }
        else {
            if (owl === null) {
                $matchesCarousel.owlCarousel({
                    singleItem: true,
                    autoPlay: false
                });
                owl = $matchesCarousel.data('owlCarousel');
            }
        }
        // ======================================== //
    });

    /* ========================================================= */
    /* ========================================================= */

})();