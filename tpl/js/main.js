var currentShowedItem = 0;
$(function(){
    /* ==========================================================================
                        Header slider functional
     ========================================================================== */
    $('#hr-io-slider').slick({
        dots: true,
        arrows : false
    });
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
        var pnPos = $('.'+pn).offset().top - 65;
        if(pn == 'tickets')pnPos = 0;
        $('html, body').animate({scrollTop : pnPos},900, 'easeInOutQuint');
    });


    var navPos = $('nav.top-nav').offset().top;
    var tikets = $('.tickets').offset().top;
    var prizes = $('.prizes').offset().top;
    var news = $('.news').offset().top;
    var rules = $('.rules').offset().top;
    var profile = $('.profile').offset().top;
    var chance = $('.chance').offset().top;
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
    }else if($(document).scrollTop() > (profile - 300) && $(document).scrollTop() < (chance - 300)){
        $('.tn-mbk_li').removeClass('now');
        $('#profile-but').addClass('now');
    }else if($(document).scrollTop() > (chance - 300)){
        $('.tn-mbk_li').removeClass('now');
        $('#chance-but').addClass('now');
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
        }else if($(document).scrollTop() > (profile - 300) && $(document).scrollTop() < (chance - 300)){
            $('.tn-mbk_li').removeClass('now');
            $('#profile-but').addClass('now');
        }else if($(document).scrollTop() > (chance - 300)){
            $('.tn-mbk_li').removeClass('now');
            $('#chance-but').addClass('now');
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
        if ($('.tb-tabs_li[data-ticket="' + $(this).parents('.tb-slide').data('ticket') + '"]').hasClass('done')) {
            return;
        }
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
            var tickNum = $(this).parents('.tb-slide').data('ticket');
            addTicket(tickNum,combination, function() {
                button.closest('.bm-pl').find('.tb-fs-tl').remove();
                button.closest('.tb-slide').addClass('done');
                button.closest('.tb-st-bk').html('<div class="tb-st-done">подвержден и принят к розыгрышу</div>');
                $('.tb-slide.done').find('.loto-tl_li').off('click');
                $('.tb-slide.done').find('.ticket-random').off('click');
                $('.tb-slide.done').find('.ticket-favorite').off('click');
                
                filledTickets.push(combination);
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

    $(".if-bt").on('click', function() {
        var email = $(this).parent().find('input[name="email"]').val();
        var button = $(this);
        addEmailInvite(email, function(data){
            button.parents('.if-bk').find('.invites-count').text(data.res.invitesCount);
            button.parent().find('input[name="email"]').val("");
        }, function(data){
            alert(data.message);
        }, function(){})
    });



    /* ==========================================================================
                        Prizes sliders functional
     ========================================================================== */

    $('.pz-more-bt, .mr-cl-bt-bk .mr').on('click', function(){
        var button = $(this);
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
            if(button.hasClass('pz-more-bt'))button.hide();
            $('.prizes .mr-cl-bt-bk').show();

            if (!data.res.keepButtonShow) {
                $(".mr-cl-bt-bk .mr").hide();
                $(".pz-more-bt").hide();
            } else {
                $(".mr-cl-bt-bk .mr").show();
            }
        }, function() {}, function() {});
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
        var catButt = $(this);
        $('.mr-cl-bt-bk').hide();
        
        $('.shop-category-items').hide();
        $('.shop-category-items[data-category="' + $(catButt).data('id') + '"]').show();
        if ($('.shop-category-items[data-category="' + $(catButt).data('id') + '"]').find('.pz-cg_li').length < 6) {
            $('.pz-more-bt').hide();
        } else {
            $('.pz-more-bt').show();
            $('.shop-category-items[data-category="' + $(catButt).data('id') + '"]').find('.pz-cg_li').each(function(id, item) {
                if (id >= 6) {
                    $(item).remove();
                }
            });
        }
    });

    $('.shop-category-items li').on('click', showItemDetails);

    function showItemDetails() {
        currentShowedItem = $(this).data('itemId');

        $('#shop-items-popup').find('.item-preview').attr('src', $(this).find('.im-ph img').attr('src'));
        $('#shop-items-popup').find('.item-title').text($(this).find('.im-tl').text());
        $('#shop-items-popup').find('.item-price').text ($(this).find('.im-bn b').text());        
        $('#shop-items-popup').fadeIn(200);
        $('.pz-ifo-bk').show();
        $('.pz-fm-bk').hide();
        $('.pz-rt-bk').hide();
    }


    $('.pz-ifo-bk .pz-ifo-bt').on('click', function(){
        var price =  parseInt($('#shop-items-popup').find('.item-price').text().replace(/\s*/g, ""));
        console.log(price);
        if (price > playerPoints) {
            $('.pz-ifo-bk').hide();
            $('.pz-rt-bk').text("Недостаточно баллов для заказа товара!").show();
        } else {
            $('.pz-ifo-bk').hide();
            $('.pz-fm-bk').show();
        }
    });

    $('.pz-fm-bk .pz-ifo-bt').on('click', function(){
        form = $(this).parent().find('.fm-inps-bk');
        form.find('.pi-inp-bk').removeClass('error');
        var order = {
            itemId: currentShowedItem,
            name: form.find('input[name="name"]').val(),
            surname: form.find('input[name="surname"]').val(),
            phone: form.find('input[name="phone"]').val(),
            region: form.find('input[name="region"]').val(),
            city: form.find('input[name="city"]').val(),
            addr: form.find('input[name="addr"]').val()
        }

        createItemOrder(order, function(data){
            $('.pz-fm-bk').hide();

            var text = $('.pz-rt-bk').data('default');
            $('.pz-rt-bk').text(text).show();
        }, function(data){
            if (data.message == 'INSUFFICIENT_FUNDS') {
                $('.pz-fm-bk').hide();
                $('.pz-rt-bk').text("Недостаточно баллов для заказа товара!").show();    
            }
            switch (data.message) {
                case 'ORDER_INVALID_NAME' :
                    form.find('input[name="name"]').parent().addClass('error');                    
                    form.find('input[name="name"]').focus();
                break;
                case 'ORDER_INVALID_SURNAME' :
                    form.find('input[name="surname"]').parent().addClass('error');                    
                    form.find('input[name="surname"]').focus();
                break;
                case 'ORDER_INVALID_PHONE' :
                case 'INVALID_PHONE_FORMAT' :
                    form.find('input[name="phone"]').parent().addClass('error');                    
                    form.find('input[name="phone"]').focus();
                break;
                case 'ORDER_INVALID_CITY' :
                    form.find('input[name="city"]').parent().addClass('error');                    
                    form.find('input[name="city"]').focus();
                break; 
                case 'ORDER_INVALID_ADRESS' :
                    form.find('input[name="addr"]').parent().addClass('error');                    
                    form.find('input[name="addr"]').focus();
                break; 
            }
        }, function(){})

    });

    /* ==========================================================================
                                Info block functional
     ========================================================================== */
    $('.n-add-but, .n-mr-cl-bt-bk .mr').on('click', function(){
        var newsBlock = $('.news');
        loadNews(newsBlock.find('.n-item').length, function(data) {
            if (data.res.news.length) {
                newsBlock.addClass('b-ha');
                var html = '';
                $(data.res.news).each(function(id, news) {
                    html += '<div class="n-item"><div class="n-i-tl">'+news.title+' • '+news.date+'</div><div class="n-i-txt">'+news.text+'</div></div>';
                });
                $('.n-items .h-ch').append(html);

                $('.n-items').height($('.n-items .h-ch').height());
                $('.n-add-but').hide();
                if (!data.res.keepButtonShow) {
                    $('.n-mr-cl-bt-bk .mr').hide();
                }
                newsBlock.find('.n-mr-cl-bt-bk').show();
            }
        }, function() {}, function() {});
    });

    $('.n-mr-cl-bt-bk .cl').on('click', function(){
        var newsBlock = $('.news');
        $('.n-items').find('.n-item').each(function(id, news){
            if (id >= 6) {
                $(news).remove();
            }
        });
        $('.n-items').removeAttr('style');
        $('.n-mr-cl-bt-bk .mr').show();
        $(this).closest('.n-mr-cl-bt-bk').hide();
        $('.n-add-but').show();
        newsBlock.removeClass('b-ha');
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



    //if (!$(event.target).closest(".pop-box").length){
      //  $('.popup').fadeOut(200);
    //};

    $('section.profile .p-bk').on('click', function(event){
        if (!$(event.target).closest(".fc-nrch-bk").length && !$(event.target).closest(".fc-nbs-bk").length){
            $(".fc-nbs-bk").fadeOut(200);
            $('.fc-nrch-bk li').removeClass('on');

        };
    })

    $('.fc-nrch-bk li').on('click', function(){
        if(!$(this).hasClass('on')){
            $('.fc-nrch-bk li').removeClass('on');
            var n = $(this).find('span').text();
            $('.fc-nbs-bk li.dis').each(function(){
                if($(this).text() == n)$(this).removeClass('dis');
            })
            $(this).find('span').text('');
            $(this).addClass('on');
            $('.fc-nbs-bk').fadeIn(200);
        }else{
            $(this).removeClass('on');
            $('.fc-nbs-bk').fadeOut(200);
        }
    });
    $('.fc-nbs-bk li').on('click', function(){
        if(!$(this).hasClass('dis')){
            var n = $(this).text();
            $('.fc-nrch-bk li.on span').text(n);
            $(this).addClass('dis');
            $('.fc-nbs-bk').fadeOut(200);
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
            if (!data.res.keepButtonShow) {               
                $('.mr-cl-bt-bl').find('.mr').hide();
            } else {               
                $('.mr-cl-bt-bl').find('.mr').show();
            }
        }
        var html = '';
        if (data.res.lotteries) {
            for (var i in data.res.lotteries) {
                var lottery = data.res.lotteries[i];
                html += '<li class="lot-container'+(onlyMineLotteryResults || lottery.iPlayed ? " win" : '')+'"><div class="dt">' + lottery.date + '</div><ul class="ht-ct">';
                $(lottery.combination).each(function(d, num) {
                    html += '<li>' + num + '</li>';
                });
                html += '</ul><div class="nw">' + lottery.winnersCount + '</div>';
                if (lottery.winnersCount > 0) {
                    html += '<div class="aw-bt" data-lotid="'+lottery.id+'"><a href="javascript:void(0)"></a></div>';
                }
                html += '</li>';
            };  

            $('.profile-history').find('.ht-bk').append(html);

            $('.profile-history .ht-bk .aw-bt').off('click').on('click', showLotteryDetails);
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

        form.find('.pi-inp-bk').removeClass('error');
        form.find('.ph').each(function(id, ph){
            $(ph).text($(ph).data('default'));
        });

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
                switch (data.message) {
                    case 'NICKNAME_BUSY' :
                        form.find('input[name="nick"]').parent().addClass('error');
                        form.find('input[name="nick"]').parent().find('.ph').text('Ник уже занят');
                    break;
                    case 'INVALID_PHONE_FORMAT' :
                        form.find('input[name="phone"]').parent().addClass('error');
                        form.find('input[name="phone"]').parent().find('.ph').text('Неверный формат');
                    break;
                    case 'INVALID_DATE_FORMAT' :
                        form.find('input[name="bd"]').parent().addClass('error');
                        form.find('input[name="bd"]').parent().find('.ph').text('Неверный формат даты');
                    break;
                }
            },
            function (data) {

            }
        );
        return false;
    });
    $('form[name="profile"]').find('.pi-ph.true i').off('click').on('click', function(e) {
        e.stopPropagation();

        removePlayerAvatar(function(data) {
            $('form[name="profile"]').find('.pi-ph').find('img').remove();
            $('form[name="profile"]').find('.pi-ph').removeClass('true');
        }, function() {}, function() {});
    });
    $('form[name="profile"]').find('.pi-ph').on('click', function(){
        // create form
        var form = $('<form method="POST" enctype="multipart/form-data"><input type="file" name="image"/></form>');

        var input = form.find('input[type="file"]').damnUploader({
            url: '/players/updateAvatar',
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
                image.attr('src', data.res.imageWebPath);

                holder.addClass('true');
                holder.append(image);

                $('form[name="profile"]').find('.pi-ph.true i').off('click').on('click', function(e) {
                    e.stopPropagation();

                    removePlayerAvatar(function(data) {
                        $('form[name="profile"]').find('.pi-ph').find('img').remove();
                        $('form[name="profile"]').find('.pi-ph').removeClass('true');
                    }, function() {}, function() {});
                });
            }; 

            e.uploadItem.progressCallback = function(perc) {}
            e.uploadItem.upload();
        });

        form.find('input[type="file"]').click();    
    })
    $('.profile-history .ht-bk .aw-bt').on('click', showLotteryDetails);

    function nextLotteryDetails() {
        loadLotteryDetails($('#profile-history').data('lotid'), 'next', function(data) {
            renderLotteryDetails(data)
        }, function() {
            $('#profile-history').hide();
        }, function(){});
    }

    function prevLotteryDetails() {
        loadLotteryDetails($('#profile-history').data('lotid'), 'prev', function(data) {
            renderLotteryDetails(data)
        }, function() {
            $('#profile-history').hide();
        }, function(){});
    }

    function showLotteryDetails() {
        loadLotteryDetails($(this).data('lotid'), 'current',function(data) {
            renderLotteryDetails(data)
        }, function() {
            $('#profile-history').hide();
        }, function(){});
    }

    function renderLotteryDetails(data) {
        $('#profile-history').data('lotid', data.res.lottery.id);
        $('#profile-history').find('.ws-dt').text(data.res.lottery.date);
        var combHtml = winnerHtml = '';
        $(data.res.lottery.combination).each(function(id, num){
            combHtml += '<li class="yr-tt-tr_li">' + num + '</li>';
        });
        $('#profile-history').find('.loto-holder').html(combHtml);
        $(data.res.winners).each(function(id, winner){
            winnerHtml += '<li data-id="'+winner.id+'"><div class="tl"><div class="ph"><img src="'+(winner.avatar ? winner.avatar : 'default.jpg' )+'" /></div><div class="nm">'+(winner.name && winner.surname ? winner.name + ' ' + winner.surname : winner.nick)+'</div></div></li>';
        });
        $('#profile-history').find('.ws-lt').html(winnerHtml);
        $('#profile-history').find('.ws-lt').find('li').off('click').on('click', function(e) {
            e.stopPropagation();
            $('#profile-history').find('.ws-lt').find('li').removeClass('you');
            $('#profile-history').find('.wr-pf-ph img').attr('src', $(this).find('.ph img').attr('src'));
            var tickets = data.res.tickets[$(this).data('id')];
            var ticketsHtml = '';
            for (var i=1; i<=5; ++i) {
                ticketsHtml += '<li class="yr-tt">';
                
                ticketsHtml += '<div class="yr-tt-tn">Билет #'+i+'</div><ul class="yr-tt-tr">';

                if (tickets[i]) {
                    $(tickets[i].combination).each(function(id, num){
                        ticketsHtml += '<li class="yr-tt-tr_li" data-num="'+num+'">' + num + '</li>';
                    });
                    ticketsHtml += '</ul><div class="yr-tt-tc">' + tickets[i].win + '</div>';
                } else {
                    ticketsHtml += '<li>не заполнен</li></ul>';
                }
                
                ticketsHtml += '</li>';
            }
            $('#profile-history').find('.yr-tb').html(ticketsHtml);
            $(data.res.lottery.combination).each(function(id, num){
                $('#profile-history').find('.yr-tb').find('li[data-num="'+num+'"]').addClass('won');
            });
            $(this).addClass('you');
        });
        $('#profile-history').find('.ws-lt').find('li:first').click();
        $('#profile-history').fadeIn(200); 

        $('#profile-history .ar-r').off('click').on('click', nextLotteryDetails);
        $('#profile-history .ar-l').off('click').on('click', prevLotteryDetails);
    }

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

    /* ==========================================================================
                        Footer functional
     ========================================================================== */
    $('#terms-bt').on('click', function(){
        $('#terms').fadeIn(200);
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
    for (var i = 1; i <= 5; ++i) {
        ticketsHtml += '<li class="yr-tt"><div class="yr-tt-tn">Билет #'+ (i) + '</div><ul class="yr-tt-tr">';
        if (data.res.tickets[i]) {
            $(data.res.tickets[i]).each(function(id, num) {
                ticketsHtml += '<li class="yr-tt-tr_li" data-num="' + num + '">' + num + '</li>';
            });
        } else {
            ticketsHtml += "<li>Не заполнен.</li>"
        }
        ticketsHtml += '</ul>';
        if (data.res.ticketWins[i] && data.res.ticketWins[i] != 0) {
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
            if (!data.res.tickets) {
                $("#game-itself").hide();
            }
            var ticketsHtml = '';
            for (var i = 1; i <= 5; ++i) {
                ticketsHtml += '<li class="yr-tt"><div class="yr-tt-tn">Билет #'+ (i) + '</div><ul class="yr-tt-tr">';
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
                ball = combination.shift();
                var spn = $("#game-process .g-oc_span.unfilled:first");

                spn.text(ball);
                var li = spn.parents('.g-oc_li');
                li.find('.goc_li-nb').addClass('goc-nb-act');
                spn.removeClass('unfilled');
                $("#game-process").find('li[data-num="' + ball + '"]').addClass('won')

                if (!combination.length) {
                    window.clearInterval(lotInterval);
                    window.setTimeout(function() {
                        if (data.res.player.win) {
                            showWinPopup(data);
                        } else {
                            showFailPopup(data);
                        }
                    }, 1200);
                }
            }, 5000);
        }
    }, function(){}, function(){});
}


