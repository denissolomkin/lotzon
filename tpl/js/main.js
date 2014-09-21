$(function(){


    /* ==========================================================================
                    Navigations scroll functional
     ========================================================================== */

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
    var rules = $('.rules').offset().top;
    var profile = $('.profile').offset().top;
    if($(document).scrollTop() >= 0 && $(document).scrollTop() < (prizes - 300)){
        $('.tn-mbk_li').removeClass('now');
        $('#tickets-but').addClass('now');
    }else if($(document).scrollTop() > (prizes - 300) && $(document).scrollTop() < (news - 300)){
        $('.tn-mbk_li').removeClass('now');
        $('#prizes-but').addClass('now');
    }else if($(document).scrollTop() > (news - 300) && $(document).scrollTop() < (rules - 300)){
        $('.tn-mbk_li').removeClass('now');
        $('#news-but').addClass('now');
    }else if($(document).scrollTop() > (rules - 300) && $(document).scrollTop() < (profile - 300)){
        $('.tn-mbk_li').removeClass('now');
        $('#rules-but').addClass('now');
    }else if($(document).scrollTop() > (profile - 300)){
        $('.tn-mbk_li').removeClass('now');
        $('#profile-but').addClass('now');
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
        rules = $('.rules').offset().top;
        profile = $('.profile').offset().top;
        if($(document).scrollTop() >= 0 && $(document).scrollTop() < (prizes - 300)){
            $('.tn-mbk_li').removeClass('now');
            $('#tickets-but').addClass('now');
        }else if($(document).scrollTop() > (prizes - 300) && $(document).scrollTop() < (news - 300)){
            $('.tn-mbk_li').removeClass('now');
            $('#prizes-but').addClass('now');
        }else if($(document).scrollTop() > (news - 300) && $(document).scrollTop() < (rules - 300)){
            $('.tn-mbk_li').removeClass('now');
            $('#news-but').addClass('now');
        }else if($(document).scrollTop() > (rules - 300) && $(document).scrollTop() < (profile -300)){
            $('.tn-mbk_li').removeClass('now');
            $('#rules-but').addClass('now');
        }else if($(document).scrollTop() > (profile - 300)){
            $('.tn-mbk_li').removeClass('now');
            $('#profile-but').addClass('now');
        }


        if($(document).scrollTop() > navPos){
            $('nav.top-nav').addClass('fixed');
        }else{
            $('nav.top-nav').removeClass('fixed');
        }
    });


    /* ==========================================================================
                            Tickets sliders functional
     ========================================================================== */
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
            var sel = 5 - lim;
            if(lim < 6){
                $(this).addClass('select');
                $(this).closest('.tb-slide').find('.tb-ifo b').html(sel);
                if(lim == 5){
                    $(this).closest('.tb-slide').find('.tb-ifo').hide();
                    $(this).closest('.tb-slide').find('.sm-but').addClass('on');
                }
            }
        }else{
            var lim = $(this).closest('ul').find('.select').length;
            var sel = 6 - lim + 1;
            $(this).removeClass('select');
            $(this).closest('.tb-slide').find('.tb-ifo b').html(sel);
            $(this).closest('.tb-slide').find('.tb-ifo').show();
            $(this).closest('.tb-slide').find('.sm-but').removeClass('on');
        }
    });
    $('.sm-but').on('click', function(){
        if($(this).hasClass('on')){
            $(this).closest('.bm-pl').find('.tb-fs-tl').remove();
            $(this).closest('.tb-slide').addClass('done');
            $(this).closest('.tb-st-bk').html('<div class="tb-st-done">подвержден и принят к розыгрышу</div>');
            if($('.tb-slides .done').length == 5){
                $('.tb-tabs, .tb-slides').remove();
                $('.atd-bk').show();
            }
        }
    });



    /* ==========================================================================
                        Prizes sliders functional
     ========================================================================== */
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



    /* ==========================================================================
                                Info block functional
     ========================================================================== */
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


    /* ==========================================================================
                        Profile block functional
     ========================================================================== */

    // PROFILE INFORMATIONS //

    $('.profile aside li').on('click', function(){
        var link = $(this).attr('data-link');
        $('.profile aside li').removeClass('now');
        $(this).addClass('now');
        $('.profile ._section').hide();
        console.log($('.'+link));
        $('.'+link).show();
    });

    $('.pi-inp-bk input').on('focus', function(){
        $(this).closest('.pi-inp-bk').addClass('focus')
        if($(this).attr('name') == 'date')$(this).attr('type','date');
    });

    $('.pi-inp-bk input').on('blur', function(){
        $(this).closest('.pi-inp-bk').removeClass('focus')
        if($(this).attr('name') == 'date')$(this).attr('type','text');
    });

    $('.fc-nrch-bk li').on('click', function(){
        if(!$(this).hasClass('on')){
            $('.fc-nrch-bk li').removeClass('on');
            var n = $(this).find('span').text();
            $('.fc-nbs-bk li.dis').each(function(){
                if($(this).text() == n)$(this).removeClass('dis');
            })
            $(this).find('span').text('');
            $(this).addClass('on');
            $('.fc-nbs-bk').show();
        }else{
            $(this).removeClass('on');
            $('.fc-nbs-bk').hide();
        }
    });
    $('.fc-nbs-bk li').on('click', function(){
        if(!$(this).hasClass('dis')){
            var n = $(this).text();
            $('.fc-nrch-bk li.on span').text(n);
            $(this).addClass('dis');
            $('.fc-nbs-bk').hide();
            $('.fc-nrch-bk li.on').removeClass('on');
        }
    });

    // PROFILE HISTORY //
    $('.profile-history .mr-bt, .profile-history .mr').on('click', function(){
        var cash = $('#results-cash').html();
        $('.ht-bk').append(cash);
        $('.ht-bk').append(cash);
        $('.mr-bt').hide();
        $('.mr-cl-bt-bl').show();
    });
    $('.profile-history .cl').on('click', function(){
        var cash = $('#results-cash').html();
        $('.ht-bk').html(cash);
        $('.mr-bt').show();
        $('.mr-cl-bt-bl').hide();
    });

})
