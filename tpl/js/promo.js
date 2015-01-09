$(function(){
    var dHeight = $(window).height();
    $('.display-slide').height(dHeight);
    $(window).on('resize', function(){
        var dHeight = $(window).height();
        $('.display-slide').height(dHeight);
    });
    $('.to-slide').on('click', function(e){
        var toSlide = $(e.currentTarget).attr('data-slide');
        var point = $('#slide'+toSlide).offset().top;
        $('html, body').animate({scrollTop : point},900, 'easeInOutQuint');
    });
    $('.go-play').on('click', function(){
        if (!$('#cl-check').hasClass('login')) {
            $('#login-block .tb_a-r').click();
            //$('#pass-rec-form').show();
            //$('#pass-rec-txt').hide();
            $('#cl-check').removeAttr('class').addClass('b-m registration');
        }
        
        $('#login-block').fadeIn(200);
    });
    $('#lb-close').on('click', function(){
        $('#login-block').fadeOut(200);
    });
    $('#login-block').click(function(event) {
        if ($(event.target).closest("#cl-check").length){
            $('#reg-succ-txt, #pass-rec-txt').addClass('hidden');
            if($(event.target).hasClass('tb_a-l') && !$(event.target).closest('#cl-check').hasClass('login')){
                $('#cl-check').removeAttr('class').addClass('b-m login');
            }else if($(event.target).hasClass('tb_a-r') && !$(event.target).closest('#cl-check').hasClass('registration')){
                $('#reg-form').removeClass('hidden');
                $('#cl-check').removeAttr('class').addClass('b-m registration');
            }else if($(event.target).hasClass('r-p')){
                $('#cl-check').toggleClass('login rec-pass');
            }else if($(event.target).hasClass('rs-sw')){
                $('.login-popup .lp-b').css('transform','translate(-560px, 0)');
                $('.rules-bk').css('display' , 'block');
            }else if($(event.target).hasClass('rs-sw')){

            }else if($(event.target).hasClass('rb-cs-bt')) {
                $('.login-popup .lp-b, .rules-bk').removeAttr('style');
            }else if($(event.target).hasClass('rb-cs-bt')) {
                $('.login-popup .lp-b, .rules-bk').removeAttr('style');
            }
        }else{
            $('#login-block').fadeOut(200);
        }
    });



    $('.login-popup .m_input,.mail-popup .m_input').on('keyup', function(){
        var val = $.trim($(this).val().length);
        if(val > 0){
            $(this).closest('form').find('.sb_but').removeClass('disabled').prop('disabled', false);
        }else{
            $(this).closest('form').find('.sb_but').addClass('disabled').prop('disabled', true);
        }
    });

    $('.login-popup form,.mail-popup .m_input').on('mousemove', function(){
        //var val = $.trim($(this).find('input').val().length);
        $(this).find('.m_input').each(function(){
            var val = $.trim($(this).val().length);
            if(val > 0){
                $(this).closest('form').find('.sb_but').removeClass('disabled').prop('disabled', false);
            }else{
                $(this).closest('form').find('.sb_but').addClass('disabled').prop('disabled', true);
            }
        });
    });

    $('form[name="rec-pass"]').submit(function(event){
        var email = $(this).find('input[name="login"]').val();
        resendPassword(email, function() {
            $('form[name="rec-pass"]').find('input[name="login"]').val('');
            $('#pass-rec-txt').removeClass('hidden');
            $('#cl-check').removeAttr('class').addClass('b-m rec-txt');
        },function(data){
            $("#cl-check").addClass('error');
            $('#pass-rec-form .e-t').text(data.message);
        }, function(){});
        event.preventDefault();
    });

    $('#login-block .m_input,.mail-popup .m_input').on('focus', function(){
        $(this).parent().addClass('focus');
    });
    $('#login-block .m_input,.mail-popup .m_input').on('blur', function(){
        $(this).parent().removeClass('focus');
    });



    // Contact form functional //
    $('#cf-ab').on('click', function(){
        if(!$(this).hasClass('ct-on')){
            $(this).addClass('ct-on');
            $('.fb-p-b').css('left',5000);
            setTimeout(function(){
                $('.fb-p-b, .fb-f-b').addClass('ct-on');
                setTimeout(function(){
                    $('.fb-f-b').css('left',0);
                }, 50);
            }, 200);
        }else{
            $(this).removeClass('ct-on');
            $('.fb-f-b').css('left',-5000);
            setTimeout(function(){
                $('.fb-p-b, .fb-f-b').removeClass('ct-on');
                setTimeout(function(){
                    $('.fb-p-b').css('left',0);
                }, 50);
            }, 200);
        }


    });

    $('#cti').val('');
    $('#cti').on('keyup', function(){
        $(this).height($(this).get(0).scrollHeight);
        var $this = $(this);
        $this.height(1);
        $this.height(this.scrollHeight);
    });

    // registration handler
    $('#login-block form[name="register"]').on('submit', function(e) {
        var form = $(this);
        var email = form.find('input[name="login"]').val();
        var rulesAgree = 1;//form.find('#rulcheck').prop('checked') ? 1 : 0;
        var ref = $(this).data('ref');
        registerPlayer({'email':email, 'agree':1, 'ref':ref}, function(data){
            $('#login-block form[name="register"]').find('input[name="login"]').val('');
            $("#reg-succ-txt").removeClass('hidden');
            $("#reg-form").removeClass('error').addClass('hidden');
        }, function(data){
            $("#reg-form").addClass('error');
            form.find('.e-t').text(data.message);
        }, function(data) {});
        return false;
    });


    $('.mail-popup .ib-l .m_input[name="login"]').on('click', function(e) {

        $('.mail-popup form[name="login"]').hide();
        $('.mail-popup form[name="mail"]').fadeIn(200);
        $('.mail-popup form[name="mail"]').find('input[name="login"]').focus();

    })


    // mail confirm login handler
    $('.mail-popup form[name="login"]').on('submit', function(e) {
        var form = $(this);

        var email = form.find('input[name="login"]').val();
        var pwd   = form.find('input[name="password"]').val();
        loginPlayer({'email':email, 'password':pwd}, function(data){
            document.location.href = "/";
        }, function(data){
            form.find('.e-t').text(data.message);
        }, function(data) {});

        return false;
    })

    // mail confirm handler
    $('.mail-popup form[name="mail"]').on('submit', function(e) {
        var form = $(this);
        var email = form.find('input[name="login"]').val();
        var rulesAgree = 1;//form.find('#rulcheck').prop('checked') ? 1 : 0;
        var ref = $(this).data('ref');
        registerPlayer({'email':email, 'agree':1, 'ref':ref}, function(data){
            document.location.href = "/";
        }, function(data){
            if(data.message=='PROFILE_EXISTS_NEED_LOGIN'){
                $('.mail-popup form[name="login"]').addClass('error');
                $('.mail-popup form[name="mail"]').hide();
                $('.mail-popup t-b.switch').show();
                $('.mail-popup form[name="login"]').fadeIn(200);
                $('.mail-popup form[name="login"]').find('input[name="login"]').val(email)
                $('.mail-popup form[name="login"]').find('.e-t').text('Данный email уже зарегистрирован, для привязки к нему введите Ваш пароль на Lotzon');
            }
            else{
                $('.mail-popup form[name="mail"]').addClass('error');
                form.find('.e-t').text(data.message);
            }
        }, function(data) {});
        return false;
    });

    $('#rulcheck').on('change', function(){
        if($(this).prop('checked')){
            $("#reg-form").removeClass('rul-error');
            $("#reg-form .sl-bk").removeClass('disabled');
        }else{
            $("#reg-form .sl-bk").addClass('disabled');
        }
    });

    $("#reg-form .sl-bk a").on('click', function(){
        var rulesAgree = 1;//$('#reg-form').find('#rulcheck').prop('checked') ? 1 : 0;
        if(rulesAgree == 0){
            $("#reg-form").addClass('rul-error');
        }
    });

    // login handler
    $('#login-block form[name="login"]').on('submit', function(e) {
        var form = $(this);
        var email = form.find('input[name="login"]').val();
        var pwd   = form.find('input[name="password"]').val();
        var remember = form.find("#remcheck:checked").length ? 1 : 0;        
        loginPlayer({'email':email, 'password':pwd, 'remember':remember}, function(data){
            document.location.href = "/";
        }, function(data){
            $("#login-form").addClass('error');
            form.find('.e-t').text(data.message);
        }, function(data) {});

        return false;
    })

    $('form[name="feed-back-form"]').on('submit', function(e) {
        e.preventDefault();

        var post = {
            email: $(this).find('input[name="mail"]').val(),
            text: $(this).find('textarea').val(),
        }

        sendPartnersFeedback(post, function(data) {
            $('#cf-ab').click();
        }, function(data) {
            alert(data.message);
        })
        return false;
    });
});
