(function () {

    Profile = {

        init: function () {

            $('input[type="text"][name="bd"]').inputmask("d.m.y", {autoUnmask: false});
            $('input[type="tel"][name="billing[phone]"]').inputmasks(phoneMask);
            $('input[type="tel"][name="billing[qiwi]"]').inputmasks(phoneMask);
            $('input[type="text"][name="billing[yandexMoney]"]').inputmask({mask: '410019{7,10}', placeholder: ''});
            $('input[type="text"][name="billing[webMoney]"]').inputmask('a999999999999');
        },


        validate: {

            convert: function () {

                var $input_money = $(I.moneyConvert),
                    $calc_points = $(I.pointsConvert, $input_money.closest('form')),
                    input_money = Player.checkMoney($input_money.val()),
                    calc_points = Player.calcPoints(input_money);

                $input_money.val(input_money);
                $calc_points.text(calc_points);

                return true;

            },

            сashout: function () {

                var $input_money = $(I.moneyCashout),
                    input_money = Player.checkMoney($input_money.val());

                $input_money.val(input_money);

                return true;


            }
        },

        do: {

            changeLanguage: function (data) {

                var selectedLang = $('input', this).val(),
                    isLanguageChange = (selectedLang !== Player.language.current);

                Player.language.current = selectedLang;

                if (isLanguageChange)
                    Cache.localize();

            },

            updateAvatar: function () {

                // create form
                var form = $('<form method="POST" enctype="multipart/form-data"><input type="file" name="image"/></form>');

                var input = form.find('input[type="file"]').damnUploader({
                    url: '/profile/updateAvatar',
                    fieldName: 'image',
                    dataType: 'json',
                });

                var image = $('<img></img>');
                var holder = $(this);
                if (holder.find('img').length) {
                    image = holder.find('img');
                }

                input.off('du.add').on('du.add', function (e) {
                    e.uploadItem.completeCallback = function (succ, data, status) {
                        image.attr('src', data.res.imageWebPath);

                        holder.addClass('true');
                        holder.append(image);

                        $('form[name="profile"]').find('.pi-ph.true i').off('click').on('click', function (e) {
                            e.stopPropagation();

                            removePlayerAvatar(function (data) {
                                $('form[name="profile"]').find('.pi-ph').find('img').remove();
                                $('form[name="profile"]').find('.pi-ph').removeClass('true');
                            }, function () {
                            }, function () {
                            });
                        });
                    };

                    e.uploadItem.progressCallback = function (perc) {
                    }
                    e.uploadItem.upload();
                });

                form.find('input[type="file"]').click();
            },

            removeAvatar: function () {
                $('form[name="profile"]').find('.pi-ph').find('img').remove();
                $('form[name="profile"]').find('.pi-ph').removeClass('true');
            },

            hideFavorite: function (event) {

                if (!$(event.target).closest(".ae-current-combination").length && !$(event.target).closest(".ae-combination-box").length) {
                    $(".ae-combination-box").fadeOut(200);
                    $('.ae-current-combination li').removeClass('on');
                }

            },

            openFavorite: function () {
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
            },

            selectFavorite: function () {
                if (!$(this).hasClass('selected')) {
                    var n = $(this).text();
                    $('.ae-current-combination li.on').text(n);
                    $(this).addClass('selected');
                    $('.ae-combination-box').fadeOut(200);
                    $('.ae-current-combination li.on').removeClass('on');
                }
            }

        },

        update: {

            convert: function (data) {
                Player.init(data)
                    .updateBalance();
            },

            cashout: function (data) {
                Player.init(data)
                    .updateBalance();
            },

            billing: function (data) {
                Player.init(data);
                R.push({
                    template: 'profile-billing',
                    replace: '.ci-personal-form-values'
                });
            },

            details: function (data) {
                Player.init(data);
                R.push({
                    template: 'profile-details',
                    replace: '.ci-personal-form-values'
                });

            },

            settings: function (data) {
                Player.init(data);
                R.push({
                    template: 'profile-settings',
                    replace: '.ci-personal-form-values'
                });

            }

        }



    }

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


})();