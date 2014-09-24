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
        $('#login-block').css('display','block');
        setTimeout(function(){
            $('#login-block').css('opacity','1');
        },1)
    });
    $('#lb-close').on('click', function(){
        $('#login-block').css('opacity','0');
        setTimeout(function(){
            $('#login-block').css('display','none');
        },500)
    });
    $('#login-block').click(function(event) {
        if ($(event.target).closest("#cl-check").length){
            if($(event.target).hasClass('tb_a-l') && !$(event.target).closest('#cl-check').hasClass('login')){
                $('#cl-check').removeAttr('class').addClass('b-m login');
            }else if($(event.target).hasClass('tb_a-r') && !$(event.target).closest('#cl-check').hasClass('registration')){
                $('#cl-check').removeAttr('class').addClass('b-m registration');
            }else if($(event.target).hasClass('r-p')){
                $('#cl-check').toggleClass('login rec-pass');
            }else if($(event.target).hasClass('rs-sw')){
                $('.login-popup .lp-b').css('transform','translate(-560px, 0)');
                $('.rules-bk').css('display' , 'block');
            }else if($(event.target).hasClass('rs-sw')){

            }else if($(event.target).hasClass('rb-cs-bt')) {
                $('.login-popup .lp-b, .rules-bk').removeAttr('style');
            }
            }else{
                $('#login-block').css('opacity','0');
                setTimeout(function(){
                    $('#login-block').css('display','none');
                },500)
            }

            console.log("test");
    });

    $('#login-block .m_input').on('focus', function(){
        $(this).parent().addClass('focus');
    });
    $('#login-block .m_input').on('blur', function(){
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
        var email = $(this).find('input[name="login"]').val();
        if (!email) {
            $(this).addClass('error');
        }
        var rulesAgree = $(this).find("#rulcheck:checked").length ? 1 : 0;
        registerPlayer({'email':email, 'agree':rulesAgree}, function(data){
            console.log("success");            
            console.log(data);            
        }, function(data){
            console.log("fail");
            console.log($(this));            
        }, function(data) {
            console.log("error");
            console.log($(this));            
        })
        return false;
    })

});
