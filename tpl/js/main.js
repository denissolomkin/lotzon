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
            if($(event.target).hasClass('tb_a-l') && $(event.target).closest('#cl-check').hasClass('registration')){
                $('#cl-check').toggleClass('registration login')
            }else if($(event.target).hasClass('tb_a-r') && $(event.target).closest('#cl-check').hasClass('login')){
                $('#cl-check').toggleClass('login registration')
            }else if($(event.target).hasClass('r-p')){
                $('.login .m_input').val('');
                $(this).removeClass('error');
            }
            return;
        }else{
            $('#login-block').css('opacity','0');
            setTimeout(function(){
                $('#login-block').css('display','none');
            },500)

        }
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


    // Tickets sliders functional //
    $('.tb-tabs_li').on('click', function(){
        $('.tb-tabs_li').removeClass('now');
        $(this).addClass('now');
        var tn = $(this).attr('data-ticket');
        var st = tn * 750-750;
        $('.tb-sl-scroller').css('transform' , 'translate('+-st+'px, 0)')
    });
    $('.loto-tl_li').on('click', function(){
        if(!$(this).hasClass('select')){
            var lim = $(this).closest('ul').find('.select').length;
            var sel = 6 - lim - 1;
            if(lim < 6){
                $(this).addClass('select');
                $(this).closest('.tb-slide').find('.tb-ifo b').html(sel);
            }
        }else{
            var lim = $(this).closest('ul').find('.select').length;
            var sel = 6 - lim + 1;
            $(this).removeClass('select');
            $(this).closest('.tb-slide').find('.tb-ifo b').html(sel);
        }
    });

    // Prizes sliders functional //
    $('.pz-nav .pz-nav_li').on('click', function(){
        $('.pz-nav .pz-nav_li').removeClass('now');
        $(this).addClass('now');
    });

    $('.pz-more-bt').on('click', function(){
        if($(this).attr('data-status') == 'close'){
            $('.pz-cg.more').show(300);
            $(this).html('спрятать').attr('data-status','open');
        }else{
            $('.pz-cg.more').hide(300);
            $(this).html('загрузить еще').attr('data-status','close');
        }
    });
})
