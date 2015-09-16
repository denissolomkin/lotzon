(function() {

	var $body = $('body');

	// DETECT DEVICE ========================== //
	$body.append('<span class="js-detect"></span>');
	function detectDevice() {
		switch ($('.js-detect').css('opacity')) {
			case '0.1': return 'mobile';
			case '0.2': return 'tablet';
			case '0.3': return 'desktop';
			case '0.4': return 'desktop-hd';
		}
	}
	// ======================================= //



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



	// OWL CAROUSEL =========================== //
	var $matchesCarousel = $('.matches-inf-wrapper.carousel');
	var owl = null;

	if ( $matchesCarousel.css('box-shadow') !== 'none' ) {
		$matchesCarousel.owlCarousel({
			singleItem: true,
			autoPlay : false
		});

		var owl = $matchesCarousel.data('owlCarousel');
	}
	// ======================================== //










	/* ========================================================= */
	//                        MENU
	/* ========================================================= */

	var $menu = $('.menu');
	var $moreMenu = $('.menu-more');
	var $menuBtn = $('.menu-btn');
	var $moreMenuBtn = $('.more-menu-btn');

	// MAIN MENU -------------------------------- //
	$menuBtn.on('click', function(event) {
		if ( $menu.hasClass('active') ) {
			$menu.removeClass('active');
			$menuBtn.removeClass('active');
		}
		else {
			$menu.addClass('active');
			$menuBtn.addClass('active');
		}
	});
	// ----------------------------------------- //

	// MORE MENU -------------------------------- //
	$moreMenuBtn.on('click', function() {
		if ( $moreMenu.hasClass('active') ) {
			$moreMenu.removeClass('active');
			$moreMenuBtn.removeClass('active');
		}
		else {
			$moreMenu.addClass('active');
			$moreMenuBtn.addClass('active');
		}
	});
	// ----------------------------------------- //

	$menu.on('click', function(event) {
		event.stopPropagation();
	});
	$moreMenu.on('click', function(event) {
		event.stopPropagation();
	});

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










	/* ========================================================= */
	//                         BONUSES
	/* ========================================================= */

	// POPUP BANNER ---------------------- //
	var $popupBanner = $('.popup-banner');
	var $openPopup = $('.bonus-banner-view-btn');
	var $closePopup = $('.popup-banner .close');

	$openPopup.on('click', function() {
		$popupBanner.css({
			top: ($(window).outerHeight() / 2 - $popupBanner.outerHeight() / 2) + 'px',
			left: ($(window).outerWidth() / 2 - $popupBanner.outerWidth() / 2) + 'px'
		}).fadeIn('fast');
	});

	$closePopup.on('click', function() {
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

	$balanceTab.on('click', function(event) {

		event.stopPropagation();

		if ( $balanceMenu.is(':hidden') ) {
			$balanceMenu.slideDown('fast', function() {
				$balanceTab.addClass('active');
			});
		}
		else {
			$balanceMenu.slideUp('fast', function() {
				$balanceTab.removeClass('active');
				$(this).css('display', '');
			});
		}

	});

	// REFERRALS ===================================== //
	// Active Icon ------------------------ //
	var $activeIcon = $('.r-active-icon');

	$activeIcon.hover(function() {
		$(this).parent().find('.r-active-inf').show();
	}, function() {
		$(this).parent().find('.r-active-inf').hide();
	});
	// ----------------------------------- //
	// =============================================== //



	// ACCOUNT ======================================= //
	// Favorite Combination ---------------- //
	$(document).on('click', function(event) {
		if (!$(event.target).closest(".ae-current-combination").length && !$(event.target).closest(".ae-combination-box").length) {
			$(".ae-combination-box").fadeOut(200);
			$('.ae-current-combination li').removeClass('on');
		};
	});

	$('.ae-current-combination li').on('click', function() {
		$('.ae-save-btn').addClass('save');

		if(!$(this).hasClass('on')) {
			$('.ae-current-combination li').removeClass('on');
			var n = $(this).text();

			$('.ae-combination-box li.selected').each(function() {
				if($(this).text() == n)$(this).removeClass('selected');
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

	$('.ae-combination-box li').on('click', function() {
		if(!$(this).hasClass('selected')) {
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

	$comment.on('click', function(event) {
		event.stopPropagation();
		$comment.removeClass('active');
		if ( detectDevice() === 'mobile' ) {
			$(this).addClass('active');
		}
	});

	$hideNotifications.on('click', function() {
		$notifications.fadeOut('fast', function() {
			$notifications.remove();
		});
	});

	$closeList.on('click', function() {
		$notifications.slideUp('fast', function() {
			$notifications.remove();
		});
	});

	$closeNotification.on('click', function() {
		if ($notificationsList.find('.c-notification').length < 2) {
			$notifications.slideUp('fast', function() {
				$notifications.remove();
			});
		}
		else {
			$(this).parent().slideUp('fast', function() {
				$(this).remove();
			});
		}
	});

	$showNotifications.on('click', function(event) {
		$notificationsList.slideDown('fast');
	});

	$notificationsList.on('click', function(event) {
		event.stopPropagation();
	});

	// $notifications.on('click', function(event) {
	// 	event.stopPropagation();
	// });

	// TEXTAREA ------------------------- //
	function h(e) {
		$(e).css({'height':'auto','overflow-y':'hidden'}).height(e.scrollHeight);
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

	$(document).on('click', function(event) {

		// MENU ==================================== //
		if ( detectDevice() === 'mobile' || detectDevice() === 'tablet' ) {
			if ( $menu.hasClass('active') && !$(event.target).hasClass('menu-btn') && !$(event.target).hasClass('menu') ) {
				$menu.removeClass('active');
				$menuBtn.removeClass('active');
			}
		}
		else {
			if ( $moreMenu.hasClass('active') && !$(event.target).hasClass('menu-more') && !$(event.target).hasClass('more-menu-btn') ) {
				$moreMenu.removeClass('active');
				$moreMenuBtn.removeClass('active');
			}
		}
		// ======================================== //



		// COMMENTS ========================= ===== //
		$(document).on('click', function() {
			$comment.removeClass('active');
		});

		if ( !$(event.target).hasClass('c-show-notifications') ) {
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

	$(window).on('resize', function() {
		// TICKET ================================= //
		if ( detectDevice() === 'mobile' ) {
			setBallsMargins();
		}
		// ======================================== //



		// OWL CAROUSEL =========================== //
		if ( $matchesCarousel.css('box-shadow') === 'none' ) {
			if ( owl !== null ) {
				owl.destroy();
				owl = null;
			}
		}
		else {
			if ( owl === null ) {
				$matchesCarousel.owlCarousel({
					singleItem: true,
					autoPlay : false
				});
				owl = $matchesCarousel.data('owlCarousel');
			}
		}
		// ======================================== //
	});

	/* ========================================================= */
	/* ========================================================= */

})();