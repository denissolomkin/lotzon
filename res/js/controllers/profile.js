(function() {

    Profile = {
        arrFavorite: [],
        moneyConvert: '.cc-out .cc-sum',
        pointsConvert: '.cc-income .cc-sum',
        moneyCashout: 'input.cco-sum',

        init: {
            edit: function() {
                $(document).off('change', '.ae-personal-inf input.datepicker', Profile.do.checkCalendar);

                if (Player.birthday) {
                 $('input.datepicker').daterangepicker({
                        singleDatePicker: true,
                        "showDropdowns": true,
                        startDate: new Date(Player.birthday * 1000),
                        autoUpdateInput: true
                        });
                } else {
                    $('input.datepicker').daterangepicker({
                        singleDatePicker: true,
                        "showDropdowns": true,
                        autoUpdateInput: true
                    });
                    $('input.datepicker').val('');
                }
                
                $(document).on('change', '.ae-personal-inf input.datepicker', Profile.do.checkCalendar);
        
                R.push({
                    template:'profile-edit-countries',
                    href: '/res/countries/'  + Player.language.current
                });

            },

            billing: function() {

                $('input[type="tel"][name="billing[phone]"]').inputmasks(phoneMask);
                $('input[type="tel"][name="billing[qiwi]"]').inputmasks(phoneMask);
                $('input[type="text"][name="billing[yandex]"]').inputmask({
                    mask: '410019{7,10}',
                    placeholder: ''
                });
                $('input[type="text"][name="billing[webmoney]"]').inputmask('a999999999999');

            },

        },


        validate: {

            convert: function() {

                console.log('asdasdasdsa');
                var $input_money = $(Profile.moneyConvert),
                    $calc_points = $(Profile.pointsConvert, $input_money.closest('form')),
                    input_money = Player.checkMoney($input_money.val()),
                    calc_points = Player.calcPoints(input_money);

                $input_money.val(input_money);
                $calc_points.val(calc_points);

                return true;

            },

            cashout: function() {

                var $input_money = $(Profile.moneyCashout),
                    input_money = Player.checkMoney($input_money.val());

                $input_money.val(input_money);

                return true;

            },

            passwordRepeat: function() {
                if ($('.new-pass').val() != $('.repeat-pass').val()) {
                    $('.hidden-notice').css('display', 'block');
                } else $('.hidden-notice').css('display', 'none');
            }
        },

        do: {

            changeLanguage: function(data) {

                var selectedLang = $('input', this).val(),
                    isLanguageChange = (selectedLang !== Player.language.current);

                Player.language.current = selectedLang;

                if (isLanguageChange)
                    Cache.localize();

            },

            updateAvatar: function() {

                // create form
                var form = $('<form method="POST" enctype="multipart/form-data"><input type="file" name="image"/></form>');

                var input = form.find('input[type="file"]').damnUploader({
                    url: '/profile/avatar',
                    fieldName: 'image',
                    dataType: 'json',
                });

                var image = $('<img></img>');
                var holder = $(this);
                if (holder.find('img').length) {
                    image = holder.find('img');
                }

                input.off('du.add').on('du.add', function(e) {
                    e.uploadItem.completeCallback = function(succ, data, status) {
                        Cache.init(data);
                        var url = Player.getAvatar();

                        image.attr('src', url);
                        $('.avatar-bg').css({'background' : 'url(' + url + ')  no-repeat 50%', 'background-size' : 'cover'});

                        holder.addClass('true');
                        holder.append(image);

                       /* $('form[name="profile"]').find('.pi-ph.true').off('click').on('click', function(e) {
                            e.stopPropagation();

                            removePlayerAvatar(function(data) {
                                $('form[name="profile"]').find('.pi-ph').find('img').remove();
                                $('form[name="profile"]').find('.pi-ph').removeClass('true');
                            }, function() {}, function() {});
                        });
                        */
                    };

                    e.uploadItem.progressCallback = function(perc) {}
                    e.uploadItem.upload();
                });

                form.find('input[type="file"]').click();
            },

            // removeAvatar: function() {
            //     $('form[name="profile"]').find('.pi-ph').find('img').remove();
            //     $('form[name="profile"]').find('.pi-ph').removeClass('true');
            // },

            openOption: function() {
                console.log("first");
                $(this).closest('.option-block').find('.option, .show-option .change').toggleClass('hidden');
                if ($('.hidden-notice') != undefined) {
                    console.log("second");
                    $(this).closest('.option-block').find('.hidden-notice').css('display', 'none');
                }

            },

            openFavorite: function() {
                Profile.do.openOption;
                Profile.arrFavorite = [];
                $('.ae-current-combination > *').removeClass('on');
                $('.ae-current-combination > *').each(function() {
                    // console.log($(this).value.length, '$(this).velue.length');
                    if (($(this).val() != undefined) && $(this).val().length > 0) {

                        value = $(this).val();
                        Profile.arrFavorite.push(value);
                        $(this).addClass('on');
                    }
                });

                $('.ae-combination-box li').each(function() {
                    var x = $(this);
                    $('.ae-current-combination > *').each(function() {
                        if (x.text() == $(this).val()) {
                            console.log($(this).val(), '$(this).velue()');
                            x.addClass('selected');
                        }
                    });

                });

                if ($('.ae-combination-box li.selected').length == 6) {
                    $('.ae-combination-box li:not(.selected)').addClass('unavailable');
                }

            },

            selectFavorite: function() {
                if (!$(this).hasClass('selected')) {

                    if ($('.ae-combination-box li.selected').length < 6) {
                        var n = $(this).text();
                        var first = $('.ae-current-combination >*:not(.on):first');

                        if (first) {
                            first.addClass('on').val(n);
                        } else {
                            $('.ae-current-combination >*:first-child').addClass('on').val(n).text(n);
                        }

                        $(this).addClass('selected');

                        if ($('.ae-combination-box li.selected').length == 6) {
                            $('.ae-combination-box li:not(.selected)').addClass('unavailable');
                        }
                    }
                } else {
                    $('.ae-combination-box li:not(.selected)').removeClass('unavailable');
                    var x = $(this);
                    $('.ae-current-combination >*').each(function() {
                        if (x.text() == $(this).val()) {
                            $(this).val('');
                            $(this).removeClass('on');
                        }
                    });
                    x.removeClass('selected');
                }
            },

            cancelFavorite: function() {
                $('.ae-current-combination >*').val('');
                for (var i = 0; i < Profile.arrFavorite.length; i++) {
                    $('.ae-current-combination >*').eq(i).val(Profile.arrFavorite[i]);

                }
                $('.ae-combination-box li').removeClass('selected');
                $('.ae-combination-box li:not(.selected)').removeClass('unavailable');
            },

            checkCalendar : function(e) {

                console.log(e);

                var placeholder = $('div.placeholder');

                if ($(this).val() != "") {
                    placeholder.css('display','none');
                      console.log(placeholder.css('display'));
                }
                else {
                    placeholder.css('display','block');
                }
            }

        },

        after: {

            cashout: function() {
                Player.updateBalance();
                $('.cco-enter-sum-box').innerHTML = i18n("button-user-remove-request");
            }
        },

        update: {

            billing: function() {
                R.push('profile-billing');
            },

            details: function() {
                R.push('profile-edit');
            },

            settings: function() {
                R.push('profile-settings');
            }

        }

    };

})();