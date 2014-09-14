$(function(){

    // Navigations scroll functional //

    $('.tn-mbk_li').on('click', function(){
        var pn = $(this).attr('data-href');
        var pnPos = $('.'+pn).offset().top - 100;
        if(pn == 'tickets')pnPos = 0;
        $('html, body').animate({scrollTop : pnPos},900, 'easeInOutQuint');
    });


    var navPos = $('nav.top-nav').offset().top;
    var tikets = $('.tickets').offset().top;
    var prizes = $('.prizes').offset().top;
    var news = $('.news').offset().top;
    if($(document).scrollTop() >= 0 && $(document).scrollTop() < (prizes - 300)){
        $('.tn-mbk_li').removeClass('now');
        $('#tickets-but').addClass('now');
    }else if($(document).scrollTop() > (prizes - 300) && $(document).scrollTop() < (news - 300)){
        $('.tn-mbk_li').removeClass('now');
        $('#prizes-but').addClass('now');
    }else if($(document).scrollTop() > (news - 300)){
        $('.tn-mbk_li').removeClass('now');
        $('#news-but').addClass('now');
    }
    if($(document).scrollTop() > navPos){
        $('nav.top-nav').addClass('fixed');
    }else{
        $('nav.top-nav').removeClass('fixed');
    }
    $(document).on('scroll', function(){
        tikets = $('.tickets').offset().top;
        prizes = $('.prizes').offset().top;
        news = $('.news').offset().top;
        if($(document).scrollTop() >= 0 && $(document).scrollTop() < (prizes - 300)){
            $('.tn-mbk_li').removeClass('now');
            $('#tickets-but').addClass('now');
        }else if($(document).scrollTop() > (prizes - 300) && $(document).scrollTop() < (news - 300)){
            $('.tn-mbk_li').removeClass('now');
            $('#prizes-but').addClass('now');
        }else if($(document).scrollTop() > (news - 300)){
            $('.tn-mbk_li').removeClass('now');
            $('#news-but').addClass('now');
        };


        if($(document).scrollTop() > navPos){
            $('nav.top-nav').addClass('fixed');
        }else{
            $('nav.top-nav').removeClass('fixed');
        }
    });

    // Tickets sliders functional //
    $('.tb-tabs_li').on('click', function(){
        $('.tb-tabs_li').removeClass('now');
        $(this).addClass('now');
        var tn = $(this).attr('data-ticket');
        var st = $('#tb-slide'+tn);
        $('.tb-slides .tb-slide').hide();
        st.show();
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


    // Info block functional //
    $('.n-add-but').on('click', function(){
        var newsBlock = $('.news');
        if(!newsBlock.hasClass('b-ha')){
            var cash = $('#news-cash').html();
            newsBlock.addClass('b-ha');
            $('.n-items').append('<div class="n-ic-bk"></div>');
            $('.n-ic-bk').append(cash).show(500);
            setTimeout(function(){
                $('.n-add-but').html('спрятать');
            }, 500);
        }else{
            $('.n-ic-bk').hide(500);
            setTimeout(function(){
                $('.n-ic-bk').remove();
                $('.n-add-but').html('загрузить еще');
                newsBlock.removeClass('b-ha');
            }, 500);

        };
    });

    $('.r-add-but').on('click', function(){
        var rulesBlock = $('.rules');
        if(!rulesBlock.hasClass('b-ha')){
            rulesBlock.addClass('b-ha');
            $('.rules .faq').show(500);
            setTimeout(function(){
                $('.r-add-but').html('спрятать');
            }, 500);
        }else{
            $('.rules .faq').hide(500);
            setTimeout(function(){
                $('.r-add-but').html('загрузить еще');
                rulesBlock.removeClass('b-ha');
            }, 500);
        };


        if(!newsBlock.hasClass('b-ha')){
            var cash = $('#news-cash').html();
            newsBlock.addClass('b-ha');
            $('.n-items').append('<div class="n-ic-bk"></div>');
            $('.n-ic-bk').append(cash).show(500);
            setTimeout(function(){
                $('.n-add-but').html('спрятать');
            }, 500);
        }else{
            $('.n-ic-bk').hide(500);
            setTimeout(function(){
                $('.n-ic-bk').remove();
                $('.n-add-but').html('загрузить еще');
                newsBlock.removeClass('b-ha');
            }, 500);

        };
    });


})
