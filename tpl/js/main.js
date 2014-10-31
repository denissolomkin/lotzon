var currentShowedItem = 0;
var winChance = false;
var filledTicketsCount = 0;
var wactive = true;

$(window).on("blur focus", function (e) {

    var prevType = $(this).data("prevType");

    if (prevType != e.type) { //  reduce double fire issues
        switch (e.type) {
            case "blur":
                wactive = false;
            case "focus":
                wactive = true;
        }
    }

    $(this).data("prevType", e.type);
});
$(function(){
    /* ==========================================================================
                        Start Slide functional
     ========================================================================== */
    $(document).ready(function(){
        var hash = window.location.hash;
        if(hash){
            var id = hash.split('#')[1];
            $('.tn-mbk li[data-href='+id+']').click();
        }
    });


    /* ==========================================================================
                        Header slider functional
     ========================================================================== */
    $('#hr-io-slider').slick({
        dots: true,
        arrows : false,
        autoplay : true,
        autoplaySpeed : 4000
    });
    /* ==========================================================================
                        Popups shoe/hide functional
     ========================================================================== */
    $('.popup .cs').on('click', function(){
        $('.popup').fadeOut(200);
    });
    $('.popup').click(function(event) {
        if (!$(event.target).closest(".pop-box").length){
            if($(event.target).closest(".popup").find('#game-process:visible').length)return false;
            if($(event.target).closest("#mchance").length)return false;
            if($(event.target).closest("#game-itself").length)document.location.reload();
            if($(event.target).closest(".popup").hasClass('chance'))return false;
            $('.popup').fadeOut(200);
        };
    });



    /* ==========================================================================
                                Logout functional
     ========================================================================== */
    $('#logout a, .profile .p-exit-bt').on('click', function(){
        $('#logout-popup').fadeIn(300);
    });

    $('#logout-popup .exit').on('click', function(){
        document.location.href = '/players/logout';
    });

    $('#logout-popup .back').on('click', function(){
        $(this).closest('.popup').fadeOut(300);
    });


    /* ==========================================================================
                            Tickets sliders functional
     ========================================================================== */
    $('.tb-tabs_li').on('click', function(){
        var tn = $(this).attr('data-ticket');
        var st = $('#tb-slide'+tn);
        if(!$(this).hasClass('now')){
            $('.tb-tabs_li').removeClass('now').find('span').hide();
            $(this).addClass('now').find('span').show();
            $('.tb-slides .tb-slide').fadeOut(300);
            setTimeout(function(){
                st.fadeIn(300);
            }, 300);
        }
    });
    var filledTickets = [];
    var ticketCache = [];
    $('.ticket-random').on('click', function(e) {
        if($(e.target).hasClass('after'))return false;
        if (!$(this).hasClass('select')) {
            var after = $(this).find('.after');
            after.fadeIn(300);
            setTimeout(function(){
                after.fadeOut(300);
            }, 2000);
            if($('.ticket-favorite .after:visible').length)$('.ticket-favorite .after').fadeOut(150);
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
            if($('.ticket-random .after:visible').length)$('.ticket-random .after').fadeOut(150);
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
            }else{
                if($(this).find('.after:hidden').length){
                    $(this).find('.after').fadeIn(200);
                }else{
                    $(this).find('.after').fadeOut(200);
                }
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

    $('.ticket-favorite .after i').on('click', function(){
        $('.profile .ul_li[data-link="profile-info"]').click();
    });

    $('.tb-loto-tl li.loto-tl_li').on('click', function() {
        $('.ticket-favorite .after:visible').fadeOut(300);
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
                $('.tb-tabs_li[data-ticket="'+tickNum+'"]').addClass('done');
                button.closest('.tb-slide').addClass('done');
                button.closest('.tb-st-bk').html('<div class="tb-st-done">подвержден и принят к розыгрышу</div>');
                $('.tb-slide.done').find('.loto-tl_li').off('click');
                $('.tb-slide.done').find('.ticket-random').off('click');
                $('.tb-slide.done').find('.ticket-favorite').off('click');
                
                if ($('.tb-tabs .done').length == 5) {
                    $('.tb-slide').each(function(id, slide) {
                        var comb = [];
                        $(slide).find('li.select').each(function(id, num){
                            comb.push($(num).text());
                        });
                        filledTickets.push(comb);
                    })
                    $('.tb-tabs, .tb-slides').remove();
                    var html = '<ul class="yr-tb">';
                    $(filledTickets).each(function(id, ticket) {
                        console.log(ticket);
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
                filledTicketsCount++;
            }, function(data){
                if (data.message == 'ALREADY_FILLED') {
                    button.closest('.bm-pl').find('.tb-fs-tl').remove();
                    button.closest('section.tickets').find('li.now').addClass('done');
                    button.closest('.tb-slide').addClass('done');
                    button.closest('.tb-st-bk').html('<div class="tb-st-done" style="color:red">Этот билет уже был заполнен</div>');
                }

            }, function(){});
        }
    });

    $(".send-invite").on('click', function() {
        var email = $(this).parent().find('input[name="email"]').val();
        var button = $(this);
        addEmailInvite(email, function(data){
            button.parents('.if-bk').find('.invites-count').text(data.res.invitesCount);
            button.parent().find('input[name="email"]').hide();
            button.parent().find('.inp-bk').append('<span class="it-msg-bk'+(data.status == 0 ? ' error':'')+'">Приглашение отправлено</span>');
            setTimeout(function(){
                button.parent().find('.it-msg-bk').fadeOut(300);
                button.parent().find('input[name="email"]').val("");
                setTimeout(function(){
                    button.parent().find('.it-msg-bk').remove();
                    button.parent().find('input[name="email"]').fadeIn(300);
                }, 300);
            }, 3000);
        }, function(data){
            //alert(data.message);
            button.parent().find('input[name="email"]').hide();
            button.parent().find('.inp-bk').append('<span class="it-msg-bk'+(data.status == 0 ? ' error':'')+'">'+data.message+'</span>');
            setTimeout(function(){
                button.parent().find('.it-msg-bk').fadeOut(300);
                setTimeout(function(){
                    button.parent().find('.it-msg-bk').remove();
                    button.parent().find('input[name="email"]').fadeIn(300);
                }, 300);
            }, 3000);
        }, function(){})
    });



    /* ==========================================================================
                        Prizes sliders functional
     ========================================================================== */

    $('.prizes .pz-more-bt, .prizes .mr-cl-bt-bk .mr').on('click', function(){
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
            $('.prizes .pz-more-bt').hide();
        } else {
            $('.prizes .pz-more-bt').show();
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
            chanceWin: winChance ? 1 : 0,
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
            if($('#shop-items-popup').hasClass('chance')){
                $('.game-bk .bk-bt').click();
            }
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
            $('.rules .faq').show();
            $('.r-add-but.show').hide();
            $('.r-add-but.close').show();
        }else{
            $('.rules .faq').hide();
            $('.r-add-but.show').show();
            $('.r-add-but.close').hide();
            rulesBlock.removeClass('b-ha');
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
        if (playerMoney > 10) {
            $('#cash-output-popup').fadeIn(200);    
        } else {
            $('.pz-ifo-bk').hide();
            $('.pz-rt-bk').text("Недостаточно средств для вывода!").show().parents('#shop-items-popup').show();
        }
        
    });





    // PROFILE INFORMATIONS //

    $('.profile aside li').on('click', function(){
        var link = $(this).attr('data-link');
        $('.profile aside li').removeClass('now');
        $(this).addClass('now');
        $('.profile ._section').hide();
        $('.'+link).show();
    });

    /*
     Функционал валидации формы, пока закаментил, мал ли может захотят вернуть
    $('.profile-info .pi-inp-bk input').on('blur', function(){
        $('.profile-info .pi-inp-bk input').each(function(){
            var val = $(this).val();
            var valid = $(this).attr('data-valid');
            if(val != valid){
                $('.profile-info .save-bk .sb-ch-td .but').addClass('save');
                return false;
            }else{
                $('.profile-info .save-bk .sb-ch-td .but').removeClass('save');
            };
        });
    });*/

    $('.pi-inp-bk input').on('focus', function(){
        $(this).closest('.pi-inp-bk').addClass('focus')
        if($(this).attr('name') == 'date')$(this).attr('type','date');
        $('.profile-info .save-bk .sb-ch-td .but').addClass('save');
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
            /*
            Функционал валидации формы, пока закаментил, мал ли может захотят вернуть
             var liChack = false;
             var inputChack = false;
            $('.fc-nrch-bk li').each(function(){
                var val = $.trim($(this).find('span').text());
                var valid = $.trim($(this).find('span').attr('data-valid'));
                if(val != valid){
                    liChack = true;
                    return false;
                };
            });
            $('.profile-info .pi-inp-bk input').each(function(){
                var val = $(this).val();
                var valid = $(this).attr('data-valid');
                if(val != valid){
                    inputChack = true;
                    return false;
                };
            });
            if(liChack || inputChack){
                $('.profile-info .but').addClass('save');
            }else{
                $('.profile-info .save-bk .sb-ch-td .but').removeClass('save');
            }
             */
        };
    });

    $('.profile-info #rulcheck').on('change', function(){
        $('.profile-info .but').addClass('save');
    });

    $('.fc-nrch-bk li').on('click', function(){
        $('.profile-info .but').addClass('save');
        if(!$(this).hasClass('on')){
            $('.fc-nrch-bk li').removeClass('on');
            var n = $(this).find('span').text();
            console.log(n);
            $('.fc-nbs-bk li.dis').each(function(){
                if($(this).text() == n)$(this).removeClass('dis');
            })
            $(this).find('span').text('');
            $(this).addClass('on');
            $('.fc-nbs-bk').fadeIn(200);
        }else{
            $(this).removeClass('on');
            $('.fc-nbs-bk').fadeOut(200);

            /*
             Функционал валидации формы, пока закаментил, мал ли может захотят вернуть
            $('.fc-nrch-bk li').each(function(){
                var val = $.trim($(this).find('span').text());
                var valid = $.trim($(this).find('span').attr('data-valid'));
                if(val != valid){
                    $('.profile-info .but').addClass('save');
                    return false;
                }else{
                    $('.profile-info .but').removeClass('save');
                }
            });
            */
        }
    });
    $('.fc-nbs-bk li').on('click', function(){
        if(!$(this).hasClass('dis')){
            var n = $(this).text();
            $('.fc-nrch-bk li.on span').text(n);
            $(this).addClass('dis');
            $('.fc-nbs-bk').fadeOut(200);
            $('.fc-nrch-bk li.on').removeClass('on');
            /*
            scooterok
            Функционал валидации формы, пока закаментил, мал ли может захотят вернуть
            $('.fc-nrch-bk li').each(function(){
                var valid = $.trim($(this).find('span').attr('data-valid'));
                if($.trim(n) != valid){
                    $('.profile-info .but').addClass('save');
                    return false;
                }else{
                    $('.profile-info .but').removeClass('save');
                    return false;
                }
            });*/
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
                if (lottery.winnersCount > 0 || lottery.iPlayed || onlyMineLotteryResults) {
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

        form.find('.pi-inp-bk input').each(function(){
            var val = $(this).val();
            $(this).attr('data-valid', val);
        });

        /*
        form.find('.fc-nrch-bk li span').each(function(){
            var val = $.trim($(this).text());
            $(this).attr('data-valid', val);
        });
        */

        form.find('.save').removeClass('save');

        form.find('.pi-inp-bk').removeClass('error');
        form.find('.ph').each(function(id, ph){
            $(ph).text($(ph).data('default'));
        });

        form.find('input').each(function(id, input) {
            if($(input).attr('name') != 'plug')playerData[$(input).attr('name')] = $(input).val();
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
        var yourId = '';
        $(data.res.winners).each(function(id, winner){
            if (winner.you) {
                yourId = winner.id;
            }
            winnerHtml += '<li data-id="'+winner.id+'" '+(winner.you ? 'class="you"' : '')+'><div class="tl"><div class="ph"><img src="'+(winner.avatar ? winner.avatar : '/tpl/img/default.jpg' )+'" /></div><div class="nm">'+(winner.name && winner.surname ? winner.name + ' ' + winner.surname : winner.nick)+'</div></div></li>';
        });



        $('#profile-history').find('.ws-lt').html(winnerHtml);
        if (yourId) {
            $('#profile-history').find('.ws-pf-rt-bk').show();
            $('#profile-history').find('.ws-dt.ch-hide').hide();
            $('#profile-history').find('.wr-pf-ph img').attr('src', $('li[data-id="'+yourId+'"]').find('.ph img').attr('src'));
            var tickets = data.res.tickets[yourId];
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
                    ticketsHtml += '<li class="null">не заполнен</li></ul>';
                }
                
                ticketsHtml += '</li>';
            }
            $('#profile-history').find('.yr-tb').html(ticketsHtml);
            $(data.res.lottery.combination).each(function(id, num){
                $('#profile-history').find('.yr-tb').find('li[data-num="'+num+'"]').addClass('won');
            });
        } else {
            $('#profile-history').find('.ws-pf-rt-bk').hide();
        }
        $('#profile-history').find('.ws-lt').find('li').off('click').on('click', function(e) {
            e.stopPropagation();
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
    $('body').show();

    /* ==========================================================================
     Navigations scroll functional
     ========================================================================== */

    $('.tn-mbk_li, .scrollto, #exchange, .ticket-favorite .after i').on('click', function(event){
        var pn = $(this).attr('data-href');
        if(pn == 'logout')return false;
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
        chance = $('.chance').offset().top;
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
});
function showGameProccessPopup(){
    if (filledTicketsCount > 0 && wactive) {
        $("#game-won").hide();
        $("#game-end").hide();
        $("#mchance").hide();
        $("#game-process").show();
        $("#game-itself").show();

        proccessResult();    
    } else {
        location.reload();
    }
    
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
            ticketsHtml += "<li class='null'>Не заполнен</li>"
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
            ticketsHtml += "<li class='null'>Не заполнен</li>"
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
                    ticketsHtml += "<li class='null'>Не заполнен</li>"
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

$('.ch-gm-tbl .gm-bt').click(function(){
    var gi = $(this).data('game');
    $('.msg-tb.won').hide();
    $('.msg-tb.los').hide();
    // hide all games;
    $('.game-bk .gm-tb').hide();
    $('.game-bk .rw-b .tb').hide();
    $('.game-bk .play').show();
    $('.game-bk li').removeClass('won').removeClass('los');
    $('.game-bk li').removeClass('true').removeClass('blink');
    // show current game
    
    $('.game-bk .gm-tb[data-game="'+gi+'"]').show();
    $('.game-bk .rw-b .tb[data-game="'+gi+'"]').show();

    if (gi == 55) {
        $('.game-bk .rw-b .tb[data-game="'+gi+'"]').find('.td').removeClass('sel').first().addClass('sel');
    }
    $('.game-bk .l-bk-txt').html($('.game-bk').find("#game-rules").find('div[data-game="'+gi+'"]').html());
    $('.game-bk').find('.gm-if-bk .l').html($(this).parent().find('.gm-if-bk .l').html());
    $('.game-bk').find('.gm-if-bk .r').html($(this).parent().find('.gm-if-bk .r').html());
    $('.ch-bk').fadeOut(200);
    window.setTimeout(function(){
        $('.game-bk').fadeIn(200);
    }, 200);

    $('.game-bk .play .bt').off('click').on('click', function() {
        winChance = false;
        var btn = $(this);
        startChanceGame(gi, function(data) {
            updatePoints(playerPoints - parseInt($('.game-bk').find('.gm-if-bk .r b').text()));
            btn.parents('.play').hide();
        }, function(data) {
            if (data.message=="INSUFFICIENT_FUNDS") {
                $('.pz-ifo-bk').hide();
                $('.pz-rt-bk').text("Недостаточно баллов для игры в шанс!").show().parents('#shop-items-popup').show();
            }
        }, function() {});
    })
});

$('li[data-coord]').on('click', function() {
    var cell = $(this);
    playChanceGame($(this).parent().data('game'), $(this).data('coord'), function(data) {
        if (data.res.status == 'win') {
            winChance = true;
            $('.msg-tb.won').show();
            $('.msg-tb.won').find('.pz-ph img').attr('src', '/filestorage/shop/' + data.res.prize.image);
            $('.msg-tb.won').find('.tl b').text(data.res.prize.title);
            $('.msg-tb.won').find('.bt').off('click').on('click', function() {
                currentShowedItem = data.res.prize.id;
                $('.pz-ifo-bk').hide();
                $('.pz-fm-bk').show();
                $('.pz-rt-bk').hide();

                $('#shop-items-popup').show().addClass('chance');
                $('#shop-items-popup').find('.cs').hide();
                /*$('#shop-items-popup').find('.cs').off('click').on('click', function() {
                    location.reload();
                })*/
            });

        } else if (data.res.status == 'loose') {
            for (var i in data.res.field) {
                for (var j in data.res.field[i]) {
                    if(data.res.field[i][j] == 1) {
                        $('li[data-coord="' + i + 'x' +j+ '"]').addClass('blink');
                    }
                }
            }
            var blinkCount = 3;
            var blinkInterval = window.setInterval(function() {
                if (blinkCount == 0) {
                    window.clearInterval(blinkInterval);
                    $('.msg-tb.los').show();
                    return;
                }
                blinkCount--;

                $('li.blink').toggleClass('true');
            }, 600);
            $('.msg-tb.los').find('.bt span').text(cell.parents('.gm-tb').data('price'));
            $('.msg-tb.los').find('.bt').off('click').on('click', function() {
                var btn = $(this);
                startChanceGame(cell.parent().data('game'), function(data) {
                    btn.parents('.msg-tb').hide();
                    updatePoints(playerPoints - parseInt($('.game-bk').find('.gm-if-bk .r b').text()));
                    $('li[data-coord]').removeClass('won').removeClass('los');
                    $('li[data-coord]').removeClass('true').removeClass('blink');
                    $('.game-bk .rw-b .tb:visible').find('.td').removeClass('sel').first().addClass('sel');
                }, function(data) {
                    if (data.message=="INSUFFICIENT_FUNDS") {
                        $('.pz-ifo-bk').hide();
                        $('.pz-rt-bk').text("Недостаточно баллов для игры в шанс!").show().parents('#shop-items-popup').show();
                    }
                }, function() {});
            });
        } else if (data.res.status == 'process') {
            if (data.res.cell == 0) {
                if (!data.res.dublicate) {
                    $('.rw-b').find('.tb:visible').find('.td.sel').removeClass('sel').next().addClass('sel');    
                }
            }
        }
        if (data.res.cell == 1) {
            cell.addClass('won');
        } else {
            cell.addClass('los');
        }
    }, function() {

    }, function() {});
});

$('.game-bk .bk-bt').on('click', function() {
    $('.game-bk').fadeOut(200);
    window.setTimeout(function(){
        $('.ch-bk').fadeIn(200);
    }, 200); 
});
$('#mchance').find('.cs').on('click', function() {
    location.reload();
});

$('.st-hy-bt').on('click', function(){
    $('#ta-his-popup').fadeIn(200);

    $('#ta-his-popup').find('.pz-more-bt, .mr').off('click').on('click', function() {
        var currency = $(this).parents('.bblock').data('currency');
        button = $(this);
        getTransactions($(this).parents('.bblock').find('.rw').length, currency, function(data) {
            if (data.res.length) {
                var html = '';
                $(data.res).each(function(id, tr) {
                    html += '<div class="rw"><div class="nm td"><span>'+tr.description+'</span></div><div class="if td">'+tr.quantity+'</div><div class="dt td"><span>'+tr.date+'</span></div></div>';
                });

                button.parents('.bblock').find('.tb').append($(html));

                if (button.hasClass('pz-more-bt')) {
                    button.hide();    
                }
                button.parents('.bblock').find('.mr-cl-bt-bl').show();

                if (data.res.length < 6) {
                    button.parents('.bblock').find('.mr-cl-bt-bl').find('.mr').hide();
                }
            }
        }, function(data) {}, function() {})
    });

    $('#ta-his-popup').find('.cl').on('click', function() {
        $(this).parents('.bblock').find('.tb').find('.rw').each(function(id, rw) {
            if (id > 5) {
                $(rw).remove();
            }
        });
        $(this).parents('.bblock').find('.mr-cl-bt-bl').hide();
        $(this).parents('.bblock').find('.pz-more-bt').show();
    });
});

function updatePoints(points) {
    playerPoints = points;
    $('.plPointHolder').text(playerPoints);
}
