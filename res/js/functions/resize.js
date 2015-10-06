$(function () {

    /* ========================================================= */
    //                        CALLBACK FUNCTION
    /* ========================================================= */

    windowResize = function () {
        switchMobileMenu();
        setBallsMargins();
        initOwlCarousel();
    }

    /* ========================================================= */
    //                        PARTIAL FUNCTIONS
    /* ========================================================= */

    setBallsMargins = function () {

        function getBallsMargins(box, balls) {
            var boxWidth = box.outerWidth();
            var ballWidth = balls.outerWidth();
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

        if (detectDevice() === 'mobile') {
            D.log('setBallsMargins');
            var ticketBox = $('.ticket-item');
            var ticketBalls = $('.ticket-numbers li');
            var ticketActions = $('.ticket-actions li');
            var ticketNumbersBox = $('.ticket-numbers');

            var result = getBallsMargins(ticketBox, ticketBalls);
            var margin = result.margin;
            var padding = result.padding;

            ticketBox.css({
                'padding-left': padding + 'px',
                'padding-right': padding + 'px'
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
    }

    switchMobileMenu = function(){

        // MENU =================================== //
        if (isMobile()) {
            $(I.menuMore).removeClass('menu-item');

            if ($(I.menuBtn).hasClass('active')) {
                $(I.menu).show();
            } else {
                $(I.menu).hide();
            }

        } else {
            $(I.menuMore).addClass('menu-item');

            if ($(I.menuBtn).hasClass('active')) {
                $(I.menuMore).show();
            } else {
                $(I.menuMore).hide();
            }
        }
        // ======================================== //
    }

    owl = null;
    initOwlCarousel = function () {
        // OWL CAROUSEL =========================== //
        var $matchesCarousel = $('.carousel');
        if ($matchesCarousel.length) {

            if ($matchesCarousel.css('box-shadow') === 'none') {
                if (owl !== null) {
                    owl.destroy();
                    owl = null;
                }
            } else {
                if (owl === null) {
                    $matchesCarousel.owlCarousel({
                        singleItem: true,
                        autoPlay: true
                    });
                    owl = $matchesCarousel.data('owlCarousel');
                }
            }
        }
    }


});