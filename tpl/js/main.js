$(function(){

    /* ==========================================================================
                        Popups shoe/hide functional
     ========================================================================== */
    $('.popup .cs').on('click', function(){
        $('.popup').fadeOut(200);
    });
    $('.popup').click(function(event) {
        if (!$(event.target).closest(".pop-box").length){
            $('.popup').fadeOut(200);
        };
    });


    /* ==========================================================================
                    Navigations scroll functional
     ========================================================================== */

    $('.tn-mbk_li, #exchange').on('click', function(){
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
        var tn = $(this).attr('data-ticket');
        var st = $('#tb-slide'+tn);
        $('.tb-tabs_li').removeClass('now').find('span').hide();
        $(this).addClass('now').find('span').show();
        $('.tb-slides .tb-slide').fadeOut(300);
        setTimeout(function(){
            st.fadeIn(300);
        }, 300);

    });
    var filledTickets = [];
    var ticketCache = [];
    $('.ticket-random').on('click', function(e) {
        if (!$(this).hasClass('select')) {
            if ($(this).parents('.tb-slide').find('li.select').length > 0) {
                $(this).parents('.tb-slide').find('li.select').removeClass('select');
            }
            ticketCache = [];
            for (var i = 1; i <= 6; ++i) {
                ticketCache.push(randomCachedNum());
            }        
            var button = $(this);
            $(ticketCache).each(function(id, num) {
                button.parents('.tb-slide').find('.loto-' + num).addClass('select');    
            });
            
            $(this).addClass('select');
            $(this).parents('.tb-slide').find('.tb-ifo b').html(0);
            $(this).parents('.tb-slide').find('.sm-but').addClass('on');
        } else {
            $(this).parents('.tb-slide').find('li.select').removeClass('select');
        }
        if ((6 - $(this).parents('.tb-slide').find('.tb-loto-tl li.select').length) > 0) {
            $(this).parents('.tb-slide').find('.tb-ifo').show();
            $(this).parents('.tb-slide').find('.tb-ifo b').html(6 - $(this).parents('.tb-slide').find('.tb-loto-tl li.select').length);    
            $(this).parents('.tb-slide').find('.add-ticket').removeClass('on');
        } else {

            $(this).parents('.tb-slide').find('.tb-ifo').hide();
            $(this).parents('.tb-slide').find('.add-ticket').addClass('on');
        }
    });    
    function randomCachedNum() {
        var rand = Math.floor((Math.random() * 49) + 1); 
        $(ticketCache).each(function(id, num) {
            if (num == rand) {
                rand = randomCachedNum();
            }
        });
        return rand;
    }
    $('.ticket-favorite').on('click', function() {
        if (!$(this).hasClass('select')) {
            if ($(this).parents('.tb-slide').find('li.select').length > 0) {
                $(this).parents('.tb-slide').find('li.select').removeClass('select');
            }
            if (playerFavorite.length) {
                for (var i = 0; i <= 5; ++i) {
                    $(this).parents('.tb-slide').find('.loto-' + playerFavorite[i]).addClass('select');
                }
                $(this).addClass('select');
                $(this).parents('.tb-slide').find('.tb-ifo b').html(0);
                $(this).parents('.tb-slide').find('.sm-but').addClass('on');
            }
        } else {
            $(this).parents('.tb-slide').find('li.select').removeClass('select');   
        }        
        if ((6 - $(this).parents('.tb-slide').find('.tb-loto-tl li.select').length) > 0) {
            $(this).parents('.tb-slide').find('.tb-ifo').show();
            $(this).parents('.tb-slide').find('.tb-ifo b').html(6 - $(this).parents('.tb-slide').find('.tb-loto-tl li.select').length);    
            $(this).parents('.tb-slide').find('.add-ticket').removeClass('on');
        } else {

            $(this).parents('.tb-slide').find('.tb-ifo').hide();
            $(this).parents('.tb-slide').find('.add-ticket').addClass('on');
        }
        
    });

    $('.tb-loto-tl li.loto-tl_li').on('click', function() {
        if ($(this).parents('.tb-slide').find('.tb-loto-tl li.select').length == 6) {
            if (!$(this).hasClass('select')) {
                return;    
            }
            
        }
        if (!$(this).hasClass('ticket-random') && !$(this).hasClass('ticket-favorite')) {    
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
        }else{
            var lim = $(this).closest('ul').find('.select').length;
            var sel = 6 - lim + 1;
            $(this).removeClass('select');
            $(this).closest('.tb-slide').find('.tb-ifo b').html(sel);
            $(this).closest('.tb-slide').find('.tb-ifo').show();
            $(this).closest('.tb-slide').find('.sm-but').removeClass('on');
        }
    });

    $('.add-ticket').on('click', function(){
        if($(this).hasClass('on')){
            var combination = [];
            $(this).parents('.tb-slide').find('li.select').each(function(id, li) {
                if (!$(li).hasClass('ticket-favorite') && !$(li).hasClass('ticket-random')) {
                    combination.push($(li).text());
                }
            });
            var button = $(this);
            addTicket(combination, function() {
                button.closest('.bm-pl').find('.tb-fs-tl').remove();
                button.closest('.tb-slide').addClass('done');
                button.closest('.tb-st-bk').html('<div class="tb-st-done">подвержден и принят к розыгрышу</div>');
                button.closest('.tb-slide').find('.ticket-random').off('click');
                button.closest('.tb-slide').find('.ticket-favorite').off('click');
                button.closest('.tb-slide').find('.loto-tl_li').off('click');
                filledTickets.push(combination);
                console.log(filledTickets.length);
                if (filledTickets.length == 5) {
                    $('.tb-tabs, .tb-slides').remove();
                    var html = '<ul class="yr-tb">';
                    $(filledTickets).each(function(id, ticket) {
                        html += '<li class="yr-tt"><div class="yr-tt-tn">Билет #' + (id + 1) + '</div><ul class="yr-tt-tr">';
                        $(ticket).each(function(tid, num) {
                            html += '<li class="yr-tt-tr_li">' + num + '</li>';
                        });
                        html += '</ul></li>';
                    });
                    html += '</ul>';
                    $('.atd-bk').prepend($(html));
                    $('.atd-bk').show();
                }

            }, function(){}, function(){});
            
            $(this).closest('.bm-pl').find('.tb-fs-tl').remove();
            $(this).closest('section.tickets').find('li.now').addClass('done');
            $(this).closest('.tb-slide').addClass('done');
            $(this).closest('.tb-st-bk').html('<div class="tb-st-done">подвержден и принят к розыгрышу</div>');
            if($('.tb-slides .done').length == 5){
                $('.tb-tabs, .tb-slides').remove();
                
            }
        }
    });



    /* ==========================================================================
                        Prizes sliders functional
     ========================================================================== */

    $('.pz-more-bt, .mr-cl-bt-bk .mr').on('click', function(){
        loadShop($('.shop-category.now').data('id'), $('.shop-category-items:visible .pz-cg_li').length, function(data) {
            if (data.res.items.length) {
                var html = '';    
                $(data.res.items).each(function(id, item) {
                    html += '<li class="pz-cg_li">';
                    if (item.quantity > 0) {
                        html += '<div class="pz-lim"><span>ограниченное количество</span><b>'+item.quantity+' шт</b></div>';
                    }
                    html += '<div class="im-ph"><img src="/filestorage/shop/'+item.img+'"></div><div class="im-tl">'+item.title+'</div><div class="im-bn"><b>'+item.price+'</b><span>обменять на баллов</span></div></li>'
                });
                $('.shop-category-items:visible').append(html);
                //reinit listeners
                $('.shop-category-items li').off('click').on('click', showItemDetails);
            }
            if (!data.res.keepButtonShow) {
                $(".mr-cl-bt-bk .mr").hide();
                $(".pz-more-bt").hide();
            } else {
                $(".mr-cl-bt-bk .mr").show();
            }
        }, function() {}, function() {});

        if($(this).hasClass('pz-more-bt'))$(this).hide();
        $('.prizes .mr-cl-bt-bk').show();
    });

    $('.mr-cl-bt-bk .cl').on('click', function(){
        $('.shop-category-items:visible').find('.pz-cg_li').each(function(id, item) {
            if (id >= 6) {
                $(item).remove();
            }
        })
        $(this).closest('.mr-cl-bt-bk').hide();
        $('.pz-more-bt').show();
    });

    $('.shop-category').on('click', function() {
        $('.shop-category').removeClass('now');
        $(this).addClass('now');

        $('.shop-category-items').hide();
        $('.shop-category-items[data-category="' + $(this).data('id') + '"]').show();
    });

    $('.shop-category-items li').on('click', showItemDetails);

    function showItemDetails() {
        $('#shop-items-popup').find('.item-preview').attr('src', $(this).find('.im-ph img').attr('src'));
        $('#shop-items-popup').find('.item-title').text($(this).find('.im-tl').text());
        $('#shop-items-popup').find('.item-price').text ($(this).find('.im-bn b').text());
        $('#shop-items-popup').fadeIn(200);
    }


    $('.pz-ifo-bk .pz-ifo-bt').on('click', function(){
        $('.pz-ifo-bk').hide();
        $('.pz-fm-bk').show();
    });

    $('.pz-fm-bk .pz-ifo-bt').on('click', function(){
        $('.pz-fm-bk').hide();
        $('.pz-rt-bk').show();
    });

    /* ==========================================================================
                                Info block functional
     ========================================================================== */
    $('.n-add-but').on('click', function(){
        var newsBlock = $('.news');
        var button = $(this);
        if(!newsBlock.hasClass('b-ha')){
            loadNews($(this).parent().find('.n-item').length, function(data) {
                if (data.res.news.length) {
                    var html = '';
                    $(data.res.news).each(function(id, news) {
                        html += '<div class="n-item"><div class="n-i-tl">'+news.title+' • '+news.date+'</div><div class="n-i-txt">'+news.text+'</div></div>';
                    });
                    $(html).insertBefore(button);
                    $('.n-items').append('<div class="n-ic-bk"></div>');
                    newsBlock.addClass('b-ha');
                    $('.n-add-but').html('спрятать');
                }
            }, function() {}, function() {});
        } else {
            $('.n-items').find('.n-item').each(function(id, news){
                if (id >= 6) {
                    $(news).remove();
                }
            });
            $('.n-ic-bk').remove();
            $('.n-add-but').html('загрузить еще');
            newsBlock.removeClass('b-ha');
        }
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

    // CASE OUTPUT POPUP //
    $('#cash-output').on('click', function(){
        $('#cash-output-popup').fadeIn(200);
    });





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

    $('.ph-fr-bk li').on('click', function(){
        $(this).closest('ul.ph-fr-bk').find('li').removeClass('sel');
        $(this).addClass('sel');
    });

    var onlyMineLotteryResults = false;
    $('.bt-om').on('click', function() {
        onlyMineLotteryResults = true;
        $(this).parents('.profile-history').find('.ht-bk').find('li.lot-container').remove();
        loadLotteries(0, onlyMineLotteryResults, processLotteryResults, function(){}, function(){});
    });

    $('.bt-all').on('click', function() {
        onlyMineLotteryResults = false;
        $(this).parents('.profile-history').find('.ht-bk').find('li.lot-container').remove();
        loadLotteries(0, onlyMineLotteryResults, processLotteryResults, function(){}, function(){}); 
    })

    function processLotteryResults (data) {
        if (data.res.offset > 0) {
            $('.mr-cl-bt-bl').show();
            if (!data.res.keepShowButton) {
                $('mr-cl-bt-bl').find('.mr').hide();
            }    
        }
        var html = '';
        if (data.res.lotteries) {            
            for (var i in data.res.lotteries) {
                var lottery = data.res.lotteries[i];
                html += '<li class="lot-container'+(onlyMineLotteryResults ? " win" : '')+'"><div class="dt">' + lottery.date + '</div><ul class="ht-ct">';
                $(lottery.combination).each(function(d, num) {
                    html += '<li>' + num + '</li>';
                });
                html += '</ul><div class="nw">' + lottery.winnersCount + '</div><div class="aw-bt"><a href="javascript:void(0)"></a></div></li>';
            };

            $('.profile-history').find('.ht-bk').append(html);                
        }
    }

    $('.profile-history .mr-bt, .profile-history .mr').on('click', function(){
        var button = $(this);
        button.hide();
        var offset = button.parents('.profile-history').find('.ht-bk').find('li.lot-container').length;
        loadLotteries(offset, onlyMineLotteryResults, processLotteryResults, function(){}, function(){});
    });
    $('.profile-history .cl').on('click', function(){
        var counter = 0;
        $(this).parents('.profile-history').find('.ht-bk').find('li.lot-container').each(function(id, element) {
            if (counter < 6) {
                counter++;
            } else {
                $(element).remove();
            }
        })

        $('.mr-bt').show();
        $('.mr-cl-bt-bl').find('.mr').show();
        $('.mr-cl-bt-bl').hide();
    });

    $('form[name="profile"]').on('submit', function() {
        var form = $(this);
        var playerData = {};

        form.find('input').each(function(id, input) {
            playerData[$(input).attr('name')] = $(input).val();
        });
        playerData.email=$("#profile_email").text();
        // favorite
        playerData.favs = [];
        playerFavorite = [];
        $(".fc-nbs-bk").find('li.dis').each(function(id, li) {
            playerFavorite.push($(li).text());
            playerData.favs.push($(li).text());
        });
        playerData.visible = form.find('input[name="visible"]:checked').length ? 1 : 0;
        updatePlayerProfile(playerData,
            function(data) {

            }, 
            function(data) {

            },
            function (data) {

            }
        );
        return false;
    });

    $('.profile-history .ht-bk .aw-bt').on('click', function(){
        $('#profile-history').fadeIn(200);
    });


    /* ==========================================================================
                    Cash popup functional
     ========================================================================== */
    $('.csh-ch-bk .m_input').on('focus', function(){
        $(this).parent().addClass('focus');
    });
    $('.csh-ch-bk .m_input').on('blur', function(){
        $(this).parent().removeClass('focus');
    });
    $('.csh-ch-bk .f_input').hover(
        function(){
            if(!$('.inp-fl-bt').hasClass('check'))$('.inp-fl-bt').addClass('hover');
        },
        function(){
            $('.inp-fl-bt').removeClass('hover');
        }
    )
    $('.csh-ch-bk .f_input').on('change', function(){
        $('.inp-fl-bt').addClass('check').html('заменить');
    });

    $('input[name="cash"]').on('change', function(){
        var id = $(this).attr('id');
        $('#csh-ch-txt').hide();
        $('.csh-ch-bk .form').hide();
        $('.csh-ch-bk .'+id).show();
    });
    $("input").keydown(function(e){
        if($(this).attr('data-type') == 'number'){
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                    // Allow: Ctrl+A
                (e.keyCode == 65 && e.ctrlKey === true) ||
                    // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
            }else if($(this).attr('data-type') == 'phone'){
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                    // Allow: Ctrl+A
                (e.keyCode == 65 && e.ctrlKey === true) ||
                    // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105) && e.keyCode != 61 && e.keyCode != 107) {
                e.preventDefault();
            }
        };
    });
    
});
function showGameProccessPopup(){
    $("#game-won").hide();
    $("#game-end").hide();
    $("#game-process").show();
    $("#game-itself").show();

    proccessResult();
}

function showFailPopup(data)
{
    $("#game-process").hide();
    $("#game-end").show();
    var ticketsHtml = '';
    for (var i = 0; i < 5; ++i) {
        ticketsHtml += '<li class="yr-tt"><div class="yr-tt-tn">Билет #'+ (i+1) + '</div><ul class="yr-tt-tr">';
        if (data.res.tickets[i]) {
            $(data.res.tickets[i]).each(function(id, num) {
                ticketsHtml += '<li class="yr-tt-tr_li" data-num="' + num + '">' + num + '</li>';
            });
        } else {
            ticketsHtml += "<li>Не заполнен.</li>"
        }
        ticketsHtml += '</ul></li>';
    }
    $("#game-end").find('.yr-tb').html(ticketsHtml);   
    var lotteryHtml = '';

    $(data.res.lottery.combination).each(function(id, num) {
        lotteryHtml += '<li class="g-oc_li"><span class="g-oc_span">' + num + '</span></li>';
    });

    $("#game-end").find('.g-oc-b').html(lotteryHtml);   
}

function showWinPopup(data)
{
    $("#game-process").hide();
    $("#game-won").show();
    var ticketsHtml = '';
    for (var i = 0; i < 5; ++i) {
        ticketsHtml += '<li class="yr-tt"><div class="yr-tt-tn">Билет #'+ (i+1) + '</div><ul class="yr-tt-tr">';
        if (data.res.tickets[i]) {
            $(data.res.tickets[i]).each(function(id, num) {
                ticketsHtml += '<li class="yr-tt-tr_li" data-num="' + num + '">' + num + '</li>';
            });
        } else {
            ticketsHtml += "<li>Не заполнен.</li>"
        }
        ticketsHtml += '</ul>';
        if (data.res.ticketWins[i] != 0) {
            ticketsHtml += '<div class="yr-tt-tc">' + data.res.ticketWins[i] + '</div>'
        }
        ticketsHtml += '</li>';
    }
    $("#game-won").find('.yr-tb').html(ticketsHtml);   
    var lotteryHtml = '';

    $(data.res.lottery.combination).each(function(id, num) {
        lotteryHtml += '<li class="g-oc_li"><span class="g-oc_span">' + num + '</span></li>';
        $("#game-won").find('li[data-num="' + num + '"]').addClass('won')
    });

    $("#game-won").find('.g-oc-b').html(lotteryHtml);   
    $("#game-won").find('.player-points').text(data.res.player.points);
    $("#game-won").find('.player-money').text(data.res.player.money);
}

function proccessResult()
{
    getLotteryData(function(data) {
        if (!data.res.content) {
            if (data.res.wait) {
                window.setTimeout(proccessResult, data.res.wait);
            }
        } else {
            if (!data.res.tickets.length) {
                $("#game-itself").hide();                
            }
            var ticketsHtml = '';
            for (var i = 0; i < 5; ++i) {
                ticketsHtml += '<li class="yr-tt"><div class="yr-tt-tn">Билет #'+ (i+1) + '</div><ul class="yr-tt-tr">';
                if (data.res.tickets[i]) {
                    $(data.res.tickets[i]).each(function(id, num) {
                        ticketsHtml += '<li class="yr-tt-tr_li" data-num="' + num + '">' + num + '</li>';
                    });
                } else {
                    ticketsHtml += "<li>Не заполнен.</li>"
                }
                ticketsHtml += '</ul></li>';
            }
            $("#game-process").find('.yr-tb').html(ticketsHtml);
            var ball = '';
            var combination = $(data.res.lottery.combination).get();
            var lotInterval = window.setInterval(function() {

                if (!combination.length) {
                    window.clearInterval(lotInterval);
                    if (data.res.player.win) {
                        showWinPopup(data);
                    } else {
                        showFailPopup(data);
                    }                    
                } else {
                    ball = combination.shift();
                    var spn = $("#game-process .g-oc_span.unfilled:first");
                    
                    spn.text(ball);
                    var li = spn.parents('.g-oc_li');
                    li.find('.goc_li-nb').addClass('goc-nb-act');
                    spn.removeClass('unfilled');
                    $("#game-process").find('li[data-num="' + ball + '"]').addClass('won')
                    setTimeout(function(){
                        li.removeClass('goc-tm');
                    }, 1000);
                }
            }, 1000);
        }
    }, function(){}, function(){});
}


