$(function () {

    // handler functions
    loadPage = function (event) {

        if (!event.isPropagationStopped()) {

            event.stopPropagation();
            $Box = $('.content-top');
            $Tab = $(this);

            R.render({
                "json": {},
                "callback": function(){
                    $("html, body").animate({scrollTop: 0}, 'slow');
                }
            });

        }
        return false;

    }

    loadBlock = function (event) {

        if (!event.isPropagationStopped()) {

            event.stopPropagation();
            $Box = $(this).parents('.content-main');
            $Tab = $(this);
            D.log(['loadBlock:', $Tab.attr('href')]);

            R.render({
                "callback": function (rendered, findClass) {
                    $(findClass).addClass('slideInRight');
                    $("html, body").animate({scrollTop: 0}, 'slow');
                }
            });

        }
        return false;

    }

    backBlock = function (event) {

        if (!event.isPropagationStopped()) {

            event.stopPropagation();
            $Box = $(this).parents('.content-box');
            $Tab = $(this);

            D.log(['backBlock:', $Tab.attr('href')]);

            $Box.prev().addClass('slideInLeft').show()
                .find(I.Tabs + '.active').click();

            $(this).parents('.content-box').remove();

        }

        return false;

    }

    switchTab = function (event) {

        if (!event.isPropagationStopped()) {

            event.stopPropagation();
            $Box = $(this).parents('.content-box').find('.content-box-content');
            $Tab = $(this);
            $Href = $Tab.attr('href');

            D.log(['switchTab:', $Href]);

            if (U.isAnchor($Href)) {

                $(I.Tabs, $Tab.parents('.content-box-header')).removeClass('active');
                $(' > div', $Box).hide();
                $('.content-box-item.' + $Href, $Box).show();
                $Tab.addClass('active');

            } else {
                R.render();
            }
        }

        return false;
    }

    switchCat = function (event) {

        if (!event.isPropagationStopped()) {

            event.stopPropagation();
            $Cat = $(this);
            D.log(['switchCat:', $Cat.attr('href')]);

            // with animation
            if ($(I.Cats, $Box).filter('.active').length) {

                $(I.Cats, $Box).removeClass('active');
                $('.content-box-item-content > div', $Box).fadeOut(200);
                setTimeout(function () {
                    $('.content-box-item-content > div.category-' + $Cat.data('category'), $Box).fadeIn(200);
                }, 200);

                // without animation
            } else {
                $('.content-box-item-content > div', $Box).hide();
                $('.content-box-item-content > div.category-' + $Cat.data('category'), $Box).show();
            }

            $Cat.addClass('active');

        }

        return false;

    }

    /* ========================================================= */
    /* ========================================================= */

});