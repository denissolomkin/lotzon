var landing = {
    step: '',

    init: function() {

        // init events
        this.events();

        // if bad social data
        if ($('.landing .popup.social').length) {
            $('html').css({ 'overflow': 'hidden' });
            $('#login-block').fadeIn(200);
        }

        // >>> init video popup
        $('.popup-youtube, .popup-vimeo, .popup-gmaps').magnificPopup({
            disableOn: 700,
            type: 'iframe',
            mainClass: 'mfp-fade',
            removalDelay: 160,
            preloader: false,
            fixedContentPos: true
        });

    },
    popup:{
        open:function(popup){
            popup = popup || '#login-block';
            $('html').css({ 'overflow': 'hidden' });
            $(popup).fadeIn(200);            
            
            console.debug('>>> popup open', popup);
        },
        close:function(popup){
            popup = popup || '#login-block';
            $('html').css({ 'overflow': 'auto' });
            $(popup).fadeOut(200);

            console.debug('>>> popup close',popup);
        },
    },
    events: function() {

        // >>> popup open
        $('.go-play').on('click', function() {
            landing.popup.open();
        });

        // >>> close popup
        $('.popup-close').on('click', function() {
            $('.popup').attr('class', 'popup');
            $('.popup form').attr('style', '');
            landing.popup.close();
        });

        // >>> close info-popup
        $('.close-box i').on('click', function() {
            landing.popup.close('.info-popup');
        });

        // >>> info-popup open
        $('.header .popup-msg, .popup .rs-sw').on('click', function() {

            // console.debug($(this).attr('class'));

            $('.info-popup .box').hide(0);

            if ($(this).hasClass('popup-msg')) {
                $('.info-popup .box.about').show(0);
            } else {
                $('.info-popup .box.license').show(0);
            }

            landing.popup.open('.info-popup');
        });

        // >>> toggle recover-pass
        $('.login-box #rec-pass, .password-recovery-box .back').on('click', function() {

            //restore form|msg
            $('#pass-rec-form-success').hide();
            $('form[name="rec-pass"]').show();

            $('.password-recovery-box').toggle();
            $('.login-box').toggle();

        });

        // >>> check empty EMAIL input !! old class
        $('.landing .m_input').on('keyup mousemove', function() {
            console.debug('>>> !! email length ', $.trim($(this).val().length));
            var val = $.trim($(this).val().length);
            if (val > 0) {
                $(this).closest('form').find('.sb_but').removeClass('disabled').prop('disabled', false);
            } else {
                $(this).closest('form').find('.sb_but').addClass('disabled').prop('disabled', true);
            }
        });


        // >>> restore password
        $('form[name="rec-pass"]').submit(function(event) {
            var form = $(this);
            var email = $(this).find('input[name="login"]').val();

            resendPassword(email, function() {

                form.find('input[name="login"]').val('');
                // form.attr('class', 'success');

                form.hide();
                $('#pass-rec-form-success').show();

                setTimeout(function() {
                    form.show();
                    $('#pass-rec-form-success').hide();
                    $('.password-recovery-box').hide();
                    $('.login-box').show();
                }, 5000);


            }, function(data) {

                landing.formError(form);
                form.find('.alert').text(data.message);

            });

            event.preventDefault();
        });

        // >>> registration handler
        $('form[name="register"]').on('submit', function(e) {
            console.debug('>>> registration handler');

            var form = $(this);
            var email = form.find('input[name="login"]').val();
            var rulesAgree = 1; //form.find('#rulcheck').prop('checked') ? 1 : 0;
            var ref = $(this).data('ref');

            registerPlayer({ 'email': email, 'agree': 1, 'ref': ref }, function(data) {
                console.debug('register success!!');

                form.find('input[name="login"]').val(''); // resset value
                form.attr('class', 'success');

                // >>>> переписать на нормальный код ...как только время будет
                // go to next step // вывод окна с переотправки пароля
                form.hide();
                var compleetForm = $('form[name="email-send"]');
                compleetForm.show();
                compleetForm.find('.current-mail').text(email);

                $('form[name="email-send"] .back').on('click', function() {
                    $('form[name="email-send"]').hide();
                    $('form[name="register"]').show();
                });

                $('form[name="email-send"] a.resend').on('click', function() {
                    resendEmail(email, function() {
                        // some callback
                    }, function(data) {
                        // some error
                    });
                });

            }, function(data) {
                console.debug('register error!!');

                landing.formError(form);
                form.find('.alert').text(data.message);

            });

            return false;
        });

        // >>> social registration handler
        $('form[name="social_register"]').on('submit', function(e) {
            // console.debug('>>> social registration handler');

            var form = $(this);
            var email = form.find('input[name="email"]').val() || form.find('input[name="email"]').attr('data-current');
            var rulesAgree = 1; //form.find('#rulcheck').prop('checked') ? 1 : 0;
            var ref = $(this).data('ref');
            // alert(email);
            registerPlayer({ 'email': email, 'agree': 1, 'ref': ref }, function(data) {
                console.debug('register success!!');

                form.find('input[name="login"]').val(''); // resset value
                form.attr('class', 'success');

                // >>>> переписать на нормальный код ...как только время будет
                // go to next step // вывод окна с переотправки пароля
                form.hide();
                var compleetForm = $('form[name="email-send"]');
                compleetForm.show();
                compleetForm.find('.current-mail').text(email);

                $('form[name="email-send"] .back').on('click', function() {
                    $('form[name="email-send"]').hide();
                    $('form[name="register"]').show();
                });

                $('form[name="email-send"] a.resend').on('click', function() {
                    resendPassword(email, function() {
                        // some callback
                    }, function(data) {
                        // some error
                    });
                });

            }, function(data) {
                console.debug('register error!!');

                landing.formError(form);
                form.find('.alert').text(data.message);

            });

            return false;
        });

        // >>> login handler
        $('form[name="login"]').on('submit', function(e) {
            var form = $(this);
            var email = form.find('input[name="login"]').val();
            var pwd = form.find('input[name="password"]').val();
            var remember = form.find("#remcheck:checked").length ? 1 : 0;

            loginPlayer({ 'email': email, 'password': pwd, 'remember': remember }, function(data) {

                form.attr('class', 'success');
                document.location.href = "/";

            }, function(data) {

                landing.formError(form);
                form.find('.alert').text(data.message);

            });

            return false;
        })
    },
    formError: function(form) {

        if (form) {
            form.addClass('error');

            setTimeout(function() {
                form.removeClass('error');
            }, 3000);

            return true;
        }

        console.log('>> form not found')
        return false;
    }
}

$(function() {
    landing.init();
});