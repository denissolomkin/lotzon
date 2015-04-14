var currentShowedItem = 0;
var winChance = false;
var filledTicketsCount = 0;
jQuery.fn.random = function() {
    var randomIndex = Math.floor(Math.random() * this.length);
    return jQuery(this[randomIndex]);
};
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

    $(document).on('click','.notifications .cs', function(){
        badge=$(this).parent().parent().parent();
        badge.fadeOut(1200);
        if(badge.attr('id')!='qgame'){
            setTimeout(function(){badge.remove()},1200);
        }
    });


    $(document).on('click','.notifications .badge#notice div div:not(.cs)', function() {
        $("li#profile-but").click();
        $('section.profile .p-cnt li[data-link="profile-notice"]').click();
        $(this).prev().prev().click();
    });



    $('.popup').click(function(event) {
        if (!$(event.target).closest(".pop-box").length){
            if($(event.target).closest(".popup").find('#game-process:visible').length)return false;
            if($(event.target).closest("#mchance").length)return false;
            if($(event.target).closest("#QuickGame-holder").length)return false;
            if($(event.target).closest("#mail-conf").length)return false;
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

    activateTicket();
    $('.tb-tabs_li').on('click', function(){
        var tn = $(this).attr('data-ticket');
        var st = $('#tb-slide'+tn);

        if(!$(this).hasClass('now') && !$('.tb-slides').find('#ticket_video').length){
            activateTicket();
            $('.tb-tabs_li').removeClass('now').find('span').hide();
            $(this).addClass('now').find('span').show();
            $('.tb-slides .tb-slide').fadeOut(300);

            setTimeout(function(){
                st.fadeIn(300);
            }, 300);

           if((filledTicketsCount==bannerTicketLastNum && !$(this).hasClass('done'))){
               $.ajax({
                   url: "/content/banner/TicketLast",
                   method: 'POST',
                   async: true,
                   dataType: 'json',
                   success: function(data) {
                       st.parent().prepend(data.res.block);
                   },
                   error: function(xhr, status, error) {
                       console.log(xhr.responseText);
                   }
               });
            }
        }
    });

    var filledTickets = [];
    var ticketCache = [];

    $('.add-ticket').on('click', function(){
        if($(this).hasClass('on') && !$(this).data('disabled')){
            $(this).data('disabled',true);
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
                button.closest('.tb-st-bk').html('<div class="tb-st-done">подтвержден и принят к розыгрышу</div>');
                $('.tb-slide.done').find('.loto-tl_li').off('click');
                $('.tb-slide.done').find('.ticket-random').off('click');
                $('.tb-slide.done').find('.ticket-favorite').off('click');

                filledTicketsCount++;
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
                        html += '<li class="yr-tt"><div class="yr-tt-tn">Билет #' + (id + 1) + '</div><ul class="yr-tt-tr">';
                        $(ticket).each(function(tid, num) {
                            html += '<li class="yr-tt-tr_li">' + num + '</li>';
                        });
                        html += '</ul></li>';
                    });
                    html += '</ul>';
                    $('.atd-bk').prepend($(html));
                    $('.atd-bk').show();
                } else {
                    $('.tb-tabs_li:not(.done)').first().click();
                }
                $(this).data('disabled',false);
            }, function(data){
                if (data.message == 'ALREADY_FILLED') {
                    button.closest('.bm-pl').find('.tb-fs-tl').remove();
                    button.closest('section.tickets').find('li.now').addClass('done');
                    button.closest('.tb-slide').addClass('done');
                    button.closest('.tb-st-bk').html('<div class="tb-st-done" style="color:red">Этот билет уже был заполнен</div>');
                }
                $(this).data('disabled',false);

            }, function(){
                $(this).data('disabled',false);
            });
        }
    });

    $(".send-invite").on('click', function() {
        var email = $(this).parent().find('input[name="email"]').val();
        var button = $(this);

        addEmailInvite(email, function(data){
            button.parent().find('.it-msg-bk').remove();
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
            button.parent().find('.it-msg-bk').remove();
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
                    html += '<li class="pz-cg_li" data-item-id="'+item.id+'">';
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

    $('.prizes .mr-cl-bt-bk .cl').on('click', function(){
        $('.shop-category-items:visible').find('.pz-cg_li').each(function(id, item) {
            if (id >= 6) {
                $(item).remove();
            }
        })
        $(this).closest('.mr-cl-bt-bk').hide();
        $('.prizes .pz-more-bt').show();
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
            $('.pz-rt-bk').text(getText('INSUFFICIENT_FUNDS')).show();
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
                $('.pz-rt-bk').text(getText(data.message)).show();
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

    $('.reviews .rv-upld-img').on('click', initReviewUpload);

    var currentReview = {
        image: '',
        text: '',
        id: 0,
    };

    var answerReview = {
        image: '',
        text: '',
        id: 0,
        reviewId: null,
    };

    $(".reviews .rv-but-add").on('click', function() {

        currentReview.text=$('.reviews .rv-txt .textarea').text();
        if(currentReview.text && !$(this).data('disabled')) {
            $(this).data('disabled',true);
            $.ajax({
                url: "/review/save/",
                method: 'POST',
                async: true,
                data: currentReview,
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        currentReview.image = null;

                        $('.reviews .rv-form').hide();
                        $('.reviews .rv-txt .textarea').html('');
                        $('.reviews .rv-image').css('background', '#e1ecee').css('opacity', '1');
                        $('.reviews .rv-upld-img img').attr('src', '/tpl/img/but-upload-review.png');
                        $('.rv-sc').fadeIn(200);

                        window.setTimeout(function () {
                            $('.rv-sc').hide();
                            $('.reviews .rv-form').fadeIn(200);
                        }, 2400);
                        $(this).data('disabled',false);

                    } else {
                        $(this).data('disabled',false);
                        alert(data.message);
                    }
                },
                error: function () {
                    alert('Unexpected server error');
                }
            });
        }
    });
        function initReviewUpload() {

            var image = $('.reviews .rv-image');
            if(!currentReview.image)
            {
                // create form
                var form = $('<form method="POST" enctype="multipart/form-data"><input type="file" name="image"/></form>');
                //$(button).parents('.photoalbum-box').prepend(form);

                var input = form.find('input[type="file"]').damnUploader({
                    url: '/review/uploadImage',
                    fieldName: 'image',
                    data: currentReview,
                    dataType: 'json',
                });



                input.off('du.add').on('du.add', function(e) {

                    e.uploadItem.completeCallback = function(succ, data, status) {
                        // image.attr('src', data.res.imageWebPath).show();
                        image.css('background', 'url("'+data.res.imageWebPath+'") no-repeat').css('opacity','0.5').css('background-size', 'cover');
                        $('.reviews .rv-upld-img img').attr('src','/tpl/img/but-delete-review.png');
                        currentReview.image = data.res.imageName;
                    };

                    e.uploadItem.progressCallback = function(perc) {
                        console.log(perc)
                    }

                    e.uploadItem.addPostData('Id', currentReview.id);
                    e.uploadItem.addPostData('Image', currentReview.image);
                    e.uploadItem.upload();
                });

                form.find('input[type="file"]').click();
            } else {


                $.ajax({
                    url: "/review/removeImage/",
                    method: 'POST',
                    async: true,
                    data: {image:currentReview.image},
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == 1) {

                            image.css('background', '#e1ecee').css('opacity', '1');
                            $('.reviews .rv-upld-img img').attr('src','/tpl/img/but-upload-review.png');
                            currentReview.image = null;

                        } else {
                            alert(data.message);
                        }
                    },
                    error: function() {
                        alert('Unexpected server error');
                    }
                });


            }
    }


    $(document).on('click', '.reviews .rv-ans .btn-ans', function() {

        holder=$(this).parents('.rv-ans').first();

        answerReview.text=$('#answer-review-text').text();
        if(answerReview.text && !$(this).attr('data-disabled')) {
            $(this).attr('data-disabled',true);
            $.ajax({
                url: "/review/save/",
                method: 'POST',
                async: true,
                data: answerReview,
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        answerReview.image = null;

                        $('.rv-form',holder).hide();
                        $('.rv-image',holder).css('background', '#e1ecee').css('opacity', '1');
                        $('.rv-upld-img img',holder).attr('src', '/tpl/img/but-upload-review.png');
                        $('.rv-sc', holder).fadeIn(200);

                        window.setTimeout(function () {
                            holder.fadeOut(200)
                            window.setTimeout(function () {
                                holder.remove();
                            }, 200);
                        }, 2400);

                    } else {
                        $(this).data('disabled',false);
                        alert(data.message);
                    }
                },
                error: function () {
                    alert('Unexpected server error');
                }
            });
        }
    });

    $(document).on('click', '.rv-i-ans', function(){
        var review = $(this).parents('.rv-item').first();
        var answer = $('.rv-ans-tmpl').clone();
        answerReview.reviewId=review.attr('data-id');
        $('.rv-ans').remove();
        $('.rv-usr-avtr').clone().prependTo($('.rv-form',answer));
        answer.attr('class',review.attr('class')).removeClass('rv-item').addClass('rv-ans rv-answer');
        answer.find('[contenteditable]').attr('id','answer-review-text').html(review.find('.rv-i-pl').text()+',&nbsp;');
        answer.insertAfter(review);
        moveToEnd(document.getElementById("answer-review-text"));
    });

    $('.rv-add-but, .rv-mr-cl-bt-bk .mr').on('click', function(){
        var reviewsBlock = $('.reviews');
        loadReviews(reviewsBlock.find('.rv-item:not(.rv-answer)').length, function(data) {
            reviewsBlock.addClass('b-ha');
            $('.rv-add-but').hide();
            if (data.res.reviews.length) {
                var html = '';
                var textAnswer = $('.rv-i-ans').first().text();

                $(data.res.reviews).each(function(id, review) {
                    html += '<div class="rv-item'+(review.answer?" rv-answer":"")+'"><div class="rv-i-avtr">';

                    if(review.playerAvatar)
                        html += '<img src="/filestorage/avatars/'+(Math.ceil(review.playerId/100))+'/'+review.playerAvatar+'">';
                    else
                        html += '<img src="/tpl/img/default.jpg">';
                    html+='</div><div class="rv-i-tl"><span class="rv-i-pl">'+review.playerName+'</span> • <span class="rv-i-dt">'+review.date+'</span> <span class="rv-i-ans">'+textAnswer+'</span></div><div class="rv-i-txt">'+review.text+'</div>';

                    if(review.image)
                        html += '<div class="rv-i-img"><img src="/filestorage/reviews/'+review.image+'"></div>';

                    html+='</div>';
                });
                $('.rv-items .h-ch').append(html);

            }

            if (!data.res.keepButtonShow) {
                $('.rv-mr-cl-bt-bk .mr').hide();
            }
            reviewsBlock.find('.rv-mr-cl-bt-bk').show();

            $('.rv-items').height($('.rv-items .h-ch').height());

        }, function() {}, function() {});
    });


    $('.reviews .rv-mr-cl-bt-bk .cl').on('click', function(){
        var reviewsBlock = $('.reviews');
        $('.rv-items').find('.rv-item').each(function(id, review){
            if (id >= 6) {
                $(review).remove();
            }
        });
        $('.rv-items').removeAttr('style');
        $('.rv-mr-cl-bt-bk .mr').show();
        $(this).closest('.rv-mr-cl-bt-bk').hide();
        $('.rv-add-but').show();
        reviewsBlock.removeClass('b-ha');
    });


    $('.news .n-add-but, .n-mr-cl-bt-bk .mr').on('click', function(){
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

    $('.news .n-mr-cl-bt-bk .cl').on('click', function(){
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
            $('.r-add-but.more').hide();
            $('.r-add-but.less').show();
        }else{
            $('.rules .faq').hide();
            $('.r-add-but.more').show();
            $('.r-add-but.less').hide();
            rulesBlock.removeClass('b-ha');
        };
    });


    /* ==========================================================================
                        Profile block functional
     ========================================================================== */

    // CASE OUTPUT POPUP //
    $('#cash-output').on('click', function(){
        if (playerMoney >= 10) {
            $('#cash-output-popup').fadeIn(200);
        } else {
            $('.pz-ifo-bk').hide();
            $('.pz-rt-bk').text("Недостаточно средств для вывода!").show().parents('#shop-items-popup').show();
        }

    });


    // CASE EXCHANGE POPUP //
    $('#cash-exchange').on('click', function(){
        if (playerMoney > 0) {
            $("#exchange-submit").prop('disabled', true).addClass('button-disabled');
            $("#summ_exchange").val('');
            $("#points").html('');
            $('#cash-exchange-popup').fadeIn(200);
            $('#cash-exchange-popup div.form').show();
            $('#cash-exchange-popup input').focus();
        } else {
            $("#report-popup").find(".txt").text(getText('INSUFFICIENT_FUNDS'));
            $("#report-popup").show();
        }

    });

    // PROFILE INFORMATIONS //

    $(".pi-cs-bk .cs-int-bt.int").on('click', function() {
        btn=$(this);

        provider=btn.data('provider');
        if(btn.hasClass('int'))
            $.ajax({
            url: "/players/disableSocial/"+provider,
            method: 'GET',
            async: true,
            data: null,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    btn.removeClass('int');
                    btn.wrapAll('<a href="./auth/'+provider+'?method=link"></a>');
                } else {
                    alert(data.message);
                }
            },
            error: function() {
                alert('Unexpected server error');
            }
        });
    });

    $('.profile aside li').on('click', function(){
        var link = $(this).attr('data-link');
        $('.profile aside li').removeClass('now');
        $(this).addClass('now');
        $('.profile ._section').hide();
        $('.'+link).fadeIn(200);
        if(link == 'profile-info'){
            $('.pi-inp-bk input').each(function(){
                $(this).val($(this).attr('data-valid'))
            });
        }
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
            $('.fc-nbs-bk li.dis').each(function(){
                if($(this).text() == n)$(this).removeClass('dis');
            });
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

    $('.multilanguage').on('input', function(){
        var btn = $(this);
        changeLanguage(btn.val(),
            function (data) {
                location.reload();
            },
            function (data) {
                $("#report-popup").find(".txt").text(getText(data.message));
                $("#report-popup").show();
            },
            function (data) {
                alert('error')
            });

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
                if ((lottery.winnersCount > 0 || lottery.iPlayed || onlyMineLotteryResults)) {
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
            if($(input).attr('name') != 'plug') playerData[$(input).attr('name')] = $(input).val();
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
                form.find('input[name="password"]').val('');
            },
            function(data) {
                switch (data.message) {
                    case 'NICKNAME_BUSY' :
                        form.find('input[name="nick"]').parent().addClass('error');
                        form.find('input[name="nick"]').parent().find('.ph').text(getText(data.message));
                    break;
                    case 'INVALID_PHONE_FORMAT' :
                        form.find('input[name="phone"]').parent().addClass('error');
                        form.find('input[name="phone"]').parent().find('.ph').text(getText(data.message));
                    break;
                    case 'INVALID_DATE_FORMAT' :
                        form.find('input[name="bd"]').parent().addClass('error');
                        form.find('input[name="bd"]').parent().find('.ph').text(getText(data.message));
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
        loadLotteryDetails($(this).data('lotid'), 'current', function(data) {
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
            winnerHtml += '<li data-id="'+winner.id+'" '+(winner.you ? 'class="you"' : '')+'>' +
            '<div class="tl">' +
                '<div class="ph" data-img="'+(winner.avatar ? winner.avatar : '/tpl/img/default.jpg' )+'" style="background-image:url('+(winner.avatar ? winner.avatar : '/tpl/img/default.jpg' )+')"></div>' +
                '<div class="nm">'+(winner.name && winner.surname ? winner.name + ' ' + winner.surname : winner.nick)+'</div>' +
            '</div>' +
            '</li>';
        });

        yourId=playerId;


        if(winnerHtml)
            $('#profile-history').find('.ws-lt').html(winnerHtml).show();
        else
            $('#profile-history').find('.ws-lt').hide();

        if (data.res.tickets[yourId]) {
            $('#profile-history').find('.ws-yr-tks-bk').show();
            //$('#profile-history').find('.ws-dt.ch-hide').hide();
            //$('#profile-history').find('.wr-pf-ph img').attr('src', $('li[data-id="'+yourId+'"]').find('.ph img').attr('src'));
            //$('#profile-history').find('.wr-pf-ph').css('backgroundImage','url('+$('li[data-id="'+yourId+'"]').find('.ph').attr('data-img')+')');
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
            //$('#profile-history').find('.ws-dt.ch-hide').hide();
            ticketsHtml='<li class="yr-tt"><div class="yr-tt-tn none">Вы не принимали участие в данном розыгрыше</div></li>';
            $('#profile-history').find('.yr-tb').html(ticketsHtml);
            //$('#profile-history').find('.ws-yr-tks-bk').show();
        }

        $('#profile-history').find('.wr-pf-pr').hide();

        $('#profile-history').find('.ws-lt').find('li').off('click').on('click', function(e) {
            e.stopPropagation();
        });
        $('#profile-history').find('.ws-lt').find('li:first').click();

        $('#profile-history .ar-r').off('click').on('click', prevLotteryDetails);
        $('#profile-history .ar-l').off('click').on('click', nextLotteryDetails);
    }

    /* ==========================================================================
                    Cash popup functional
     ========================================================================== */
    /* CALC POINTS */
    $("#cash-exchange-popup input").on('keyup', function(e){
        var tmp_money;
        $(this).val($(this).val().replaceArray(['ю','Ю'],'.'));
        if(new_input=$(this).val().match(/\d*[,.]\d{2}/))
            $(this).val(new_input);
//        if($(this).val().indexOf(".") !== -1)
  //          $(this).val(Math.round($(this).val()*100)/100);

        input_money=parseFloat($(this).val());
        if(!is_numeric($(this).val()) && $(this).val() && tmp_money>0)  {$(this).val(tmp_money);}
        if(input_money>playerMoney && $(this).val()) {
            //alert(input_money+' '+playerMoney);
                input_money=playerMoney;
                $(this).val(playerMoney);
        }

        $("#cash-exchange-popup #points").html(parseInt(input_money * $('#cash-exchange-popup #rate').html()));
        if(parseInt($('#cash-exchange-popup #points').html())>0)
            $("#exchange-submit").prop('disabled', false).removeClass('button-disabled');
        else
            $("#exchange-submit").prop('disabled', true).addClass('button-disabled');

        tmp_money=$(this).val();
    });

    /* FOCUS PLACEHOLDER */
    $('.csh-ch-bk .m_input').on('focus', function(){
        $(this).parent().addClass('focus');
    });
    $('.csh-ch-bk .m_input').on('blur', function(){
        $(this).parent().removeClass('focus');
    });

    /* FILE FIELD */
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

    /* SWITCH SECTION */
    $('input[name="cash"]').on('change', function(){
        var id = $(this).attr('id');
        $('#csh-ch-txt').hide();
        $('.csh-ch-bk .form').hide();
        $('.csh-ch-bk .'+id).show();
    });

    /* CHECK TEXT INPUT */
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
    window.setTimeout(function(){o=(!Boolean($('img#ad:visible').length));},5*1000);

    /* ==========================================================================
                        Navigations scroll functional
     ========================================================================== */
    if (!page) {
        $('#logo-gotop').on('click', function () {
            $('html, body').animate({scrollTop: 0}, 900, 'easeInOutQuint');
        });

        $('.tn-mbk_li, .scrollto, #exchange, .ticket-favorite .after i').on('click', function (event) {
            var pn = $(this).attr('data-href');
            if (pn == 'logout')return false;
            var pnPos = $('.' + pn).offset().top - 65;
            if (pn == 'tickets')pnPos = 0;
            $('html, body').animate({scrollTop: pnPos}, 900, 'easeInOutQuint');
        });


        var navPos = $('nav.top-nav').offset().top;
        var tikets = $('.tickets').offset().top;
        var prizes = $('.prizes').offset().top;
        // var news = $('.news').offset().top;
        var reviews = $('.reviews').offset().top;
        var rules = $('.rules').offset().top;
        var profile = $('.profile').offset().top;
        var chance = $('.chance').offset().top;
        if ($(document).scrollTop() >= 0 && $(document).scrollTop() < (prizes - 300)) {
            $('.tn-mbk_li').removeClass('now');
            $('#tickets-but').addClass('now');
        } else if ($(document).scrollTop() > (prizes - 300) && $(document).scrollTop() < (reviews - 300)) {
            $('.tn-mbk_li').removeClass('now');
            $('#prizes-but').addClass('now');
        } else if ($(document).scrollTop() > (reviews - 300) && $(document).scrollTop() < (rules - 300)) {
            $('.tn-mbk_li').removeClass('now');
            $('#reviews-but').addClass('now');
            /*}else if($(document).scrollTop() > (prizes - 300) && $(document).scrollTop() < (news - 300)){
             $('.tn-mbk_li').removeClass('now');
             $('#prizes-but').addClass('now');
             }else if($(document).scrollTop() > (news - 300) && $(document).scrollTop() < (rules - 300)){
             $('.tn-mbk_li').removeClass('now');
             $('#news-but').addClass('now');*/
        } else if ($(document).scrollTop() > (rules - 300) && $(document).scrollTop() < (profile - 300)) {
            $('.tn-mbk_li').removeClass('now');
            $('#rules-but').addClass('now');
        } else if ($(document).scrollTop() > (profile - 300) && $(document).scrollTop() < (chance - 300)) {
            $('.tn-mbk_li').removeClass('now');
            $('#profile-but').addClass('now');
        } else if ($(document).scrollTop() > (chance - 300)) {
            $('.tn-mbk_li').removeClass('now');
            $('#chance-but').addClass('now');
        }

        if ($(document).scrollTop() > navPos) {
            $('nav.top-nav').addClass('fixed');
        } else {
            $('nav.top-nav').removeClass('fixed');
        }
        $(document).on('scroll', function () {
            tikets = $('.tickets').offset().top;
            prizes = $('.prizes').offset().top;
            //news = $('.news').offset().top;
            reviews = $('.reviews').offset().top;
            rules = $('.rules').offset().top;
            profile = $('.profile').offset().top;
            chance = $('.chance').offset().top;
            if ($(document).scrollTop() >= 0 && $(document).scrollTop() < (prizes - 300)) {
                $('.tn-mbk_li').removeClass('now');
                $('#tickets-but').addClass('now');
            } else if ($(document).scrollTop() > (prizes - 300) && $(document).scrollTop() < (reviews - 300)) {
                $('.tn-mbk_li').removeClass('now');
                $('#prizes-but').addClass('now');
            } else if ($(document).scrollTop() > (reviews - 300) && $(document).scrollTop() < (rules - 300)) {
                $('.tn-mbk_li').removeClass('now');
                $('#reviews-but').addClass('now');
                /*
                 }else if($(document).scrollTop() > (prizes - 300) && $(document).scrollTop() < (news - 300)){
                 $('.tn-mbk_li').removeClass('now');
                 $('#prizes-but').addClass('now');
                 }else if($(document).scrollTop() > (news - 300) && $(document).scrollTop() < (rules - 300)){
                 $('.tn-mbk_li').removeClass('now');
                 $('#news-but').addClass('now');*/
            } else if ($(document).scrollTop() > (rules - 300) && $(document).scrollTop() < (profile - 300)) {
                $('.tn-mbk_li').removeClass('now');
                $('#rules-but').addClass('now');
            } else if ($(document).scrollTop() > (profile - 300) && $(document).scrollTop() < (chance - 300)) {
                $('.tn-mbk_li').removeClass('now');
                $('#profile-but').addClass('now');
            } else if ($(document).scrollTop() > (chance - 300)) {
                $('.tn-mbk_li').removeClass('now');
                $('#chance-but').addClass('now');
            }


            if ($(document).scrollTop() > navPos) {
                $('nav.top-nav').addClass('fixed');
            } else {
                $('nav.top-nav').removeClass('fixed');
            }
        });
    } else {

        var navPos = $('nav.top-nav').offset().top;

        if ($(document).scrollTop() > navPos) {
            $('nav.top-nav').addClass('fixed');
        } else {
            $('nav.top-nav').removeClass('fixed');
        }

        $(document).on('scroll', function () {
        if ($(document).scrollTop() > navPos) {
            $('nav.top-nav').addClass('fixed');
        } else {
            $('nav.top-nav').removeClass('fixed');
        }
        });
    }

});


function moneyOutput(type, form) {
    form = $(form);
    var data = {};

    data.type = type;

    form.find('input').each(function(id, input) {
        if (!$(input).hasClass('sb_but')) {
            if ($(input).attr('type') != 'radio') {
                data[$(input).attr('name')] = {
                    title: $(input).data('title'),
                    value: $(input).val()
                }
            } else {
                if ($(input).is(":checked")) {
                    data[$(input).attr('name')] = {
                        title: $(input).data('title'),
                        value: $(input).data('currency')
                    }
                }
            }

        }
    });

    requestForMoney(data, function(){
        updateMoney(playerMoney-parseFloat($("#cash-output-popup section.form:visible input[name=summ]").val()));
        $("#cash-output-popup").hide();
        $("#report-popup").find(".txt").text(getText('MONEY_ORDER_COMPLETE'));
        $("#report-popup").show();
    }, function(data){
        alert(data.message);
    }, function(){});
    return false;
}


function moneyExchange() {
    var data = {};

    data['type']='points';
    data['summ']={
        title: 'summ',
        value: $("#summ_exchange").val()
    };

    requestForMoney(data, function(){
        updateMoney(playerMoney-parseFloat($("#cash-exchange-popup input[name=summ]").val()));
        updatePoints(parseInt(playerPoints)+parseInt($("#cash-exchange-popup #points").html()));
        $("#cash-exchange-popup input[name=summ]").val('');
        $("#cash-exchange-popup #points").html('');
        $("#exchange-submit").prop('disabled', true).addClass('button-disabled').hide();
        $("#exchange-input").hide();
        $('#exchange-result').fadeIn(100);
        window.setTimeout(function(){
            $('#exchange-result').hide();
            $("#exchange-input").fadeIn(200);
            $("#exchange-submit").fadeIn(200);
        }, 2400);

    }, function(data){
        alert(data.message);
    }, function(){});
    return false;

}

function showGameProccessPopup(){
    if (filledTicketsCount > 0) {
        $('.popup').hide();
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
        var won = 0
        ticketsHtml += '<li class="yr-tt"><div class="yr-tt-tn">Билет #'+ (i+1) + '</div><ul data-ticket="'+i+'" class="yr-tt-tr">';

        if (data.tickets[i]) {
            $(data.tickets[i]).each(function(id, num) {
                ticketsHtml += '<li class="yr-tt-tr_li" data-num="' + num + '">' + num + '</li>';
            });
        } else {
            ticketsHtml += "<li class='null'>Не заполнен</li>"
        }
        ticketsHtml += '</ul></li>';
    }
    $("#game-end").find('.yr-tb').html(ticketsHtml);
    var lotteryHtml = '';

    $(data.c).each(function(id, num) {
        lotteryHtml += '<li class="g-oc_li"><span class="g-oc_span">' + num + '</span></li>';
    });

    $("#game-end").find('.g-oc-b').html(lotteryHtml);

    return;

    $("#game-process").hide();
    $("#game-end").show();
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

    var nominals=[];
    $('.win-tbl .c-r .c-r_li').each(function( index ) { nominals[index]=$(this).find('.tb-t').html(); });
    nominals.reverse();

    var ticketsHtml = '';
    var wonMoney=0;
    var wonPoints=0;
    for (var i = 0; i < 5; ++i) {
        var won = 0
        ticketsHtml += '<li class="yr-tt"><div class="yr-tt-tn">Билет #'+ (i+1) + '</div><ul data-ticket="'+i+'" class="yr-tt-tr">';

        if (data.tickets[i]) {
            $(data.tickets[i]).each(function(id, num) {
                ticketsHtml += '<li class="yr-tt-tr_li'+($.inArray(parseInt(num),data.c)>=0?' won':'')+'" data-num="' + num + '">' + num + '</li>';
            });
        } else {
            ticketsHtml += "<li class='null'>Не заполнен</li>"
        }
        ticketsHtml += '</ul>';

        var nominal=[];
        if(won=$(ticketsHtml).find('ul[data-ticket="'+i+'"] li.won').length) {
            ticketsHtml += '<div class="yr-tt-tc">' + nominals[won - 1] + '</div>';
            nominal=nominals[won - 1].split(" ");
            if(nominal[1]=getCurrency())
                wonMoney+=parseFloat(nominal[0]);
            else
                wonPoints+=parseInt(nominal[0]);
        }
        ticketsHtml += '</li>';
    }


    $("#game-won").find('.yr-tb').html(ticketsHtml);
    var lotteryHtml = '';

    $(data.c).each(function(id, num) {
        lotteryHtml += '<li class="g-oc_li"><span class="g-oc_span">' + num + '</span></li>';
    });

    $("#game-won").find('.g-oc-b').html(lotteryHtml);
    $("#game-won").find('.plPointHolder').text(wonPoints);
    $("#game-won").find('.plMoneyHolder').text(wonMoney);

    return false;

    updateMoney(playerMoney+wonMoney);
    updatePoints(playerPoints+wonPoints);
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
        $('#game-process .g-oc-b .goc_li-nb').removeClass('goc-nb-act');
        var tickets=[];
        if($('.tb-slide').length>0)
            $('.tb-slide').each(function( index, el ) {
                var ticket=[];
                $(el).find('.loto-tl_li.select').each(function( i, val ) {
                    ticket.push($(val).text());
                });
                if(ticket.length==6)
                tickets.push(ticket);
            });
        else if($('.yr-tb').length>0)
            $('.yr-tb .yr-tt .yr-tt-tr').each(function( index, el ) {
                var ticket=[];
                $(el).find('.yr-tt-tr_li').each(function( i, val ) {
                    if(parseInt($(val).text())>0)
                        ticket.push($(val).text());
                });
                if(ticket.length==6)
                    tickets.push(ticket);
            });

        if(!tickets.length)
            $("#game-itself").hide();
        else
            data.tickets=tickets;

        var ticketsHtml = '';
        for (var i = 0; i < 5; ++i) {
            ticketsHtml += '<li class="yr-tt"><div class="yr-tt-tn">Билет #'+ (i+1) + '</div><ul class="yr-tt-tr">';
            if (tickets[i]) {
                $(tickets[i]).each(function(id, num) {
                    ticketsHtml += '<li class="yr-tt-tr_li" data-num="' + num + '">' + num + '</li>';
                });
            } else {
                ticketsHtml += "<li class='null'>Не заполнен</li>"
            }
            ticketsHtml += '</ul></li>';
        }

        $("#game-process").find('.yr-tb').html(ticketsHtml);


        if (data.i == parseInt($('._section.profile-history .ht-bk .lot-container').first().data('lotid'))+1) {

            var ball = '';
            var lotInterval;
            var combination = $(data.c).get();
            var lotAnimation = function(){
                ball = combination.shift();
                var spn = $("#game-process .g-oc_span.unfilled:first");

                spn.text(ball);
                var li = spn.parents('.g-oc_li');
                li.find('.goc_li-nb').addClass('goc-nb-act');
                spn.removeClass('unfilled');

                window.setTimeout(function(){
                    $("#game-process").find('li[data-num="' + ball + '"]').addClass('won')
                }, 1000);

                if (!combination.length) {
                    window.clearInterval(lotInterval);
                    window.setTimeout(function() {
                        if ($("#game-process").find('li.won').length) {
                            showWinPopup(data);
                        } else {
                            showFailPopup(data);
                        }
                    }, 2000);
                }
            }
            window.setTimeout(function(){
                lotAnimation();
                lotInterval = window.setInterval(lotAnimation, 5000);}
                , 2000
            );


        } else if(!data || data.i == parseInt($('._section.profile-history .ht-bk .lot-container').first().data('lotid'))){
            window.setTimeout(proccessResult, (Math.floor(Math.random() * 5)+1)*1000);
        } else{
            location.reload();
        }

        /*

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

                window.setTimeout(function(){
                    $("#game-process").find('li[data-num="' + ball + '"]').addClass('won')
                }, 1*1000);

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
        */
    }, function(){
        window.setTimeout(proccessResult, (Math.floor(Math.random() * 5)+1)*1000);
    }, function(){
        window.setTimeout(proccessResult, (Math.floor(Math.random() * 5)+1)*1000);
    });
}

$(document).on('click','#qgame .start',function () {
    startQuickGame('QuickGame', null,
        buildQuickGame,
        function(data) {
            $('#report-popup .cs').on('click', function() {location.reload();});
            $('#report-popup').show().find('.txt').html(getText(data.message));},
        function() {alert('error')});

});

function buildQuickGame(data) {
    quickGame=data.res;
    var holder=$('#'+quickGame.Key+'-holder');
    holder.show().find('.qg-msg').hide().find('.td').children().hide();
    $('.qg-tbl',holder).removeClass('wait');
    //holder.find('.qg-msg').hide().find('.txt').next().hide();

    var html='';
    for(y1=1;y1<=quickGame.Field.y;y1++)
        for(x1=1;x1<=quickGame.Field.x;x1++)
            html+="<li data-cell='"+x1+"x"+y1+"' style='width: "+quickGame.Field.w+"px;height: "+quickGame.Field.h+"px;margin: 0 "+(x1!=quickGame.Field.x?quickGame.Field.r:0)+"px "+(y1!=quickGame.Field.y?quickGame.Field.b:0)+"px 0;'></li>";
    width=((parseInt(quickGame.Field.w)+parseInt(quickGame.Field.r))*parseInt(quickGame.Field.x)-parseInt(quickGame.Field.r));
    quickGame.Field.p = quickGame.Field.p || 0;
    $('.qg-bk-tl',holder).html(quickGame.Title);
    $('.qg-txt', holder).html(quickGame.Description);
    $('.qg-tbl', holder).css('width',width).html(html).parents('section.pop-box').css('width',width+80);
    $('.qg-bk-pr',holder).html(quickGame.Field.p);
    $('.qg-msg .bt', holder).html(getText('PLAY_ONE_MORE_TIME').format(quickGame.Field.p)).attr('data-game',quickGame.Id).attr('data-key',quickGame.Key);

    if (quickGame.Timeout) {
        window.setTimeout(function(){ location.reload(); },(quickGame.Timeout>0 ? quickGame.Timeout * 1000 : 1));
    }

    playAudio(quickGame.Audio.start);

    if (quickGame.GameField) {
        $.each(quickGame.GameField, function (index, prize) {
            var cell = $('.qg-tbl li[data-cell="' + index + '"]',holder);

            if (prize)
                cell.addClass('m '+prize.t).html(genQuickGamePrize(prize));
            else
                cell.addClass('m los');
        })
    }

    if (quickGame.Prizes) {
        $('.qg-prz', holder).html('');
        $.each(quickGame.Prizes, function (index, prize) {
            $('.qg-prz', holder).append(previewQuickGamePrize(prize));
        })
    }

    activateQuickGame(quickGame.Key);

    if (data.res.block) {
        holder.find('.block').show().html('<div class="tl">Реклама</div>' + data.res.block);
    }
}

function activateQuickGame(key)
{
    key = key || 'QuickGame';

    var holder = $('#'+key+'-holder');
    $('li[data-cell]', holder).off('click').on('click', function () {
        var cell = $(this);
        if (quickGame.Field.c < 1) {
            console.log('MOVES_IS_OUT');
            return false;
        } else if (cell.hasClasses('w, m')) {
            console.log('CELL_IS_PLAYED');
            return false;
        } else if (cell.parent().hasClass('wait')) {
            console.log('WAITING_FOR_SERVER');
            return false;
        } else {
            cell.addClass('w').parent().addClass('wait');
            window.setTimeout(function(){
                cell.removeClass('w');
                cell.parent().removeClass('wait');
            }, 5000);

        }

        cell.html('<div class="preloader"></div>');

        playQuickGame(key,$(this).data('cell'), function (data) {
            var game = data.res;
            if (game.error) {
                cell.html('');
                return;
            }

            playAudio(quickGame.Audio.move);

            quickGame.Field.c = game.Moves;
            cell.parents('ul').removeClass('wait');
            //quickGame.Field.c--;

            var cell_class='los';
            var cell_prize='';

            if (game.Prize) {
                playAudio(quickGame.Audio.hit);
                holder.find('.prize-holder.'+game.Prize.t+'-holder:not(.w):contains("'+game.Prize.v+'")').first().addClass('w');
                cell_class = (game.Prize.t);
                cell_prize = genQuickGamePrize(game.Prize);
            } else
                playAudio(quickGame.Audio.miss);

            quickGame.Field.e = quickGame.Field.e || 'clip';
            quickGame.Field.s = quickGame.Field.s || 300;
            var options = quickGame.Field.e === "scale" ? { percent: 0 } : (( quickGame.Field.e === "size" ) ? { to: { width: 0, height: 0 } } : {} );
            cell.html($('<div></div>').css('background',cell.css('background')).css('height','100%')).addClass('m '+cell_class).find('div').effect(quickGame.Field.e,options,quickGame.Field.s,function(){this.remove();cell.html(cell_prize)})

            if (game.GameField) {
                window.setTimeout(function () {
                    $.each(game.GameField, function (index, prize) {
                        var cell = $('.qg-tbl li[data-cell="' + index + '"]',holder);
                        if (cell.html() || !prize)
                            return;
                        cell.addClass('blink '+prize.t).html(genQuickGamePrize(prize));
                    });
                    holder.find('li.blink').css('opacity', 0.5);

                    var blinkCount = holder.find('li.blink').length ? 2 : 0;
                    var blinkInterval = window.setInterval(function () {
                        if (blinkCount == 0) {
                            window.clearInterval(blinkInterval);
                            holder.find('.qg-msg').css('height',holder.find('.qg-tbl').css('height')).show().find('.txt').first().show().parent().find('.preloader').hide();
                            if (game.GamePrizes.MONEY || game.GamePrizes.POINT || game.GamePrizes.ITEM) {
                                holder.find('.qg-msg').addClass('win').find('.txt').html('Поздравляем с выигрышем!' + (game.GamePrizes.MONEY ? '<br>' + getCurrency(game.GamePrizes.MONEY): '') + (game.GamePrizes.POINT ? '<br> ' + game.GamePrizes.POINT+' баллов' : '') + (game.GamePrizes.ITEM ? '<br>Приз: ' + game.GamePrizes.ITEM : ''));
                                if(game.GamePrizes.MONEY)
                                    updateMoney(playerMoney+parseFloat(game.GamePrizes.MONEY*coefficient));
                                if(game.GamePrizes.POINT)
                                    updatePoints(playerPoints+parseInt(game.GamePrizes.POINT));
                                playAudio(quickGame.Audio.win);
                            } else {
                                holder.find('.qg-msg').removeClass('win').find('.txt').text('В этот раз не повезло');
                                playAudio(quickGame.Audio.lose);
                            }

                            if(game.Price)
                                $('.qg-msg .bt', holder).show();

                            return;
                        }
                        blinkCount--;
                        holder.find('li.blink').toggleClass('prize');
                    }, 600);
                }, 600);
            }

        },
            function(data) {
                cell.html('');
                if(data.message=='CHEAT_GAME' || data.message=='TIME_NOT_YET')
                    $('#report-popup .cs').on('click', function() {location.reload();});
                    $('#report-popup').show().find('.txt').html(getText(data.message));},
            function() {
                cell.html('');
                alert('error')}
        );
    });
}

function previewQuickGamePrize(prize) {
    switch(prize.t){
        case 'item':
            return '<div class="'+(prize.w?'w ':'')+prize.t+'-holder prize-holder"><img src="/filestorage/shop/' + prize.s + '"></div>';
            break;
        default:
            return '<div class="'+(prize.w?'w ':'')+prize.t+'-holder prize-holder"><span>' + (prize.v ? (prize.t=='money' ? getCurrency(prize.v,1) : prize.v.replaceArray(["[*]", "\/"], ["x", "÷"])) : 0) + (prize.t=='money' ? '<small> '+getCurrency()+'</small>':'' )+'</span></div>';
            break;
    }
}

function genQuickGamePrize(prize) {
    switch(prize.t){
        case 'hit':
            return '<div></div>';
            break;
        case 'item':
            return '<div><img src="/filestorage/shop/' + prize.s + '"></div>';
            break;
        default:
            return '<div style="margin: 0 0 -' + parseInt(quickGame.Field.h) / 15 + 'px 0;font-size:' + parseInt(quickGame.Field.h) / (prize.t == 'math' ? 1.7 : 2) + 'px;">' + (prize.v ? (prize.t=='money' ? getCurrency(prize.v,1) : prize.v.replaceArray(["[*]", "\/"], ["x", "÷"])) : 0) + '</div>' +
            '<div style="margin-top:-' + parseInt(quickGame.Field.h) / 10 + 'px;font-size:' + parseInt(quickGame.Field.h) / 5 + 'px;">' + (prize.t == 'points' ? 'баллов' : prize.t == 'money' ? getCurrency(prize.v,2) : '') + '</div>';
            break;
    }
}

<!-- CHANCE PREVIEW -->

$(function(){
    if($('.slide-list').length) {
        $('.slide-list').each(function(){
            var slider = $(this);
            var slideWrap =  slider.find('.slide-wrap'),
                nextLink = slider.find('.next-slide'),
                prevLink = slider.find('.prev-slide'),
                slideWidth = slider.find('.slide-item').outerWidth(),
                scrollSlider = slideWrap.position().left - slideWidth;

            nextLink.click(function(){
                if(!slideWrap.is(':animated')) {
                    slideWrap.animate({left: scrollSlider}, 500, function(){
                        slideWrap
                            .find('.slide-item:first')
                            .appendTo(slideWrap)
                            .parent()
                            .css({'left': 0});
                    });
                }
            });

            prevLink.click(function(){
                if(!slideWrap.is(':animated')) {
                    slideWrap
                        .css({'left': scrollSlider})
                        .find('.slide-item:last')
                        .prependTo(slideWrap)
                        .parent()
                        .animate({left: 0}, 500);
                }
            });
        });
    }
});

$('.ch-gm-tbl .gm-bt').click(function(){
    hideAllGames();
    $('.game-bk .play').show();
    var gi = $(this).data('game');
    var quick = $(this).data('quick');
    $('#ChanceGame-holder .qg-msg').hide();
    $('#ChanceGame-holder .ul-hld').html($('.game-bk .gm-tb[data-game="'+gi+'"]').last().clone().addClass('qg-tbl').show());
    $('.game-bk .rw-b .tb[data-game="'+gi+'"]').show();

    if (gi == 55) {
        $('.game-bk .rw-b .tb[data-game="'+gi+'"]').find('.td').removeClass('sel').first().addClass('sel');
    }
    $('.game-bk .l-bk-txt.qg-txt').html($('.game-bk').find("#game-rules").find('div[data-game="'+gi+'"]').html());
    $('.game-bk .l-bk-txt.qg-prz').html($('.game-bk').find("#game-prizes").find('div[data-game="'+gi+'"]').html());
    $('.game-bk').find('.gm-if-bk .l').html($(this).parent().find('.gm-if-bk .l').html());
    $('.game-bk').find('.gm-if-bk .r').html($(this).parent().find('.gm-if-bk .r').html());
    $('.ch-bk').fadeOut(200);
    window.setTimeout(function(){
        $('.game-bk').fadeIn(200);
    }, 200);

    $('.game-bk .play .bt').off('click').on('click', function() {

        var btn = $(this);

        if(quick) {

            btn.parents('.play').hide();
            startQuickGame('ChanceGame', gi,
                function (data) {
                    updatePoints(playerPoints - parseInt($('.game-bk').find('.gm-if-bk .r b').text()));
                    buildQuickGame(data)
                },
                function (data) {
                    btn.parents('.play').show();
                    $('.pz-ifo-bk').hide();
                    $('.pz-rt-bk').text(getText(data.message)).show().parents('#shop-items-popup').show();
                    /*$('#report-popup').show().find('.txt').text(getText(data.message));*/
                },
                function () {
                    alert('error')
                });

        } else {

            winChance = false;
            var btn = $(this);
            startChanceGame(gi, function (data) {
                updatePoints(playerPoints - parseInt($('.game-bk').find('.gm-if-bk .r b').text()));
                btn.parents('.play').hide();
            }, function (data) {


            }, function () {
            });

        }
    });
});

$(document).on('click','.quickgame .qg-msg .bt', function() {
    var btn = $(this);
        startQuickGame(btn.attr('data-key'),btn.attr('data-game'),
            function (data) {
                updatePoints(playerPoints - parseInt($('.game-bk').find('.gm-if-bk .r b').text()));
                buildQuickGame(data)
            },
            function (data) {
                btn.parents('.quickgame').find('.play').show();
                $('.pz-ifo-bk').hide();
                $('.pz-rt-bk').text(getText(data.message)).show().parents('#shop-items-popup').show();
            },
            function () {
                alert('error')
            });

});

<!-- CHANCE GAME COORD CLICK -->
$(document).on('click', 'li[data-coord]', function() {
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
                startChanceGame(cell.parent().data('game'), function (data) {
                    btn.parents('.msg-tb').hide();
                    updatePoints(playerPoints - parseInt($('.game-bk').find('.gm-if-bk .r b').text()));
                    $('li[data-coord]').removeClass('won').removeClass('los');
                    $('li[data-coord]').removeClass('true').removeClass('blink');
                    $('.game-bk .rw-b .tb:visible').find('.td').removeClass('sel').first().addClass('sel');
                }, function (data) {

                    $('.pz-ifo-bk').hide();
                    $('.pz-rt-bk').text(getText(data.message)).show().parents('#shop-items-popup').show();

                }, function () {
                });
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

<!-- CHANCE GAME -->
$('.game-bk .bk-bt').on('click', function() {
    $('.game-bk').fadeOut(200);
    window.setTimeout(function(){
        $('.ch-bk').fadeIn(200);
    }, 200);
});

<!-- NOTICES -->
$('.p-cnt aside .ul_li[data-link="profile-notice"]').on('click', function(){
        var div=$( '.profile-notice .notices .n-items' );
        getNotices(0, function(data) {
            if (data.res.length) {
                var html = '';
                var unread = 0;
                $(data.res).each(function(id, tr) {
                    unread+=tr.unread;
                    html += '<div class="n-item'+(tr.unread?'':' read')+'">' +
                    '<div class="n-i-tl"><div class="n-i-dt">'+tr.date+' • </div>' +
                    '<div class="n-i-ttl">'+tr.title+'</div></div>' +
                    '<div class="n-i-txt">'+tr.text+'</div>' +
                    '</div>';
                });

                updateNotices(unread);
                div.html($(html));
            }
        }, function(data) {}, function() {});

    setTimeout(function(){
        $('.notice-unread').fadeOut(200);
        $('.n-item:not(.read)').addClass('read');
    }, 4500);


});

<!-- HISTORY OF TRANSACTIONS -->
$('.st-hy-bt').on('click', function(){
    $('#ta-his-popup').fadeIn(200);

    // update history on open popup
    $( "div.bblock" ).each(function( index ) {
        var currency=$( this ).data('currency');
        var div=$( this );
        getTransactions(0, currency, function(data) {
            if (data.res.length) {
                var html = '';
                $(data.res).each(function(id, tr) {
                    html += '<div class="rw"><div class="nm td"><span>'+tr.description+'</span></div><div class="if td">'+tr.quantity+'</div><div class="dt td"><span>'+tr.date.replace(' ','<br><span class="tm">')+'</span></div></div>';
                });
                div.find('.tb').html($(html));
            }
        }, function(data) {}, function() {});
    });

    $('#ta-his-popup').find('.cl').click();
    $('#ta-his-popup').find('.pz-more-bt, .mr').off('click').on('click', function() {
        var currency = $(this).parents('.bblock').data('currency');
        button = $(this);
        getTransactions($(this).parents('.bblock').find('.rw').length, currency, function(data) {
            if (data.res.length) {
                var html = '';
                $(data.res).each(function(id, tr) {
                    html += '<div class="rw"><div class="nm td"><span>'+tr.description+'</span></div><div class="if td">'+tr.quantity+'</div><div class="dt td"><span>'+tr.date.replace(' ','<br><span class="tm">')+'</span></div></div>';
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


$('.fb-share').on('click', function() {
    fbPost(posts.fb);
});
$('.vk-share').on('click', function() {
    vkPost(posts.vk);
});


function updateNotices(notices) {
    unreadNotices = notices;
    if (unreadNotices>0) {
        $('#profile-but a span.notice-unread').html(notices);
        $('#notice-unread').html(notices);
        $('.notice-unread').show();
    }else{
        $('.notice-unread').hide();
    }
}

function updatePoints(points) {
    playerPoints = points;
    points=points.toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
    $('.plPointHolder').text(points);
}


function updateMoney(money) {
    playerMoney = parseFloat(money).toFixed(2);
    money=parseFloat(money).toFixed(2).toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
    $('.plMoneyHolder').text(money.replace('.00',''));
}

function hideAllGames() {
    $('.msg-tb.won').hide();
    $('.msg-tb.los').hide();
    $('.game-bk .gm-tb').hide();
    $('.game-bk .rw-b .tb').hide();
    $('.game-bk .play').hide();
    $('.game-bk li').removeClass('won').removeClass('los');
    $('.game-bk li').removeClass('true').removeClass('blink');
}

function activateTicket() {
    $('.ticket-random').off().on('click', function(e) {
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

    $('.ticket-favorite').off().on('click', function() {
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

    $('.ticket-favorite .after i').off().on('click', function(){
        $('.profile .ul_li[data-link="profile-info"]').click();
    });

    $('.tb-loto-tl li.loto-tl_li').off().on('click', function () {
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
            if (!$(this).hasClass('select')) {
                var lim = $(this).closest('ul').find('.select').length;
                var sel = 5 - lim;
                if (lim < 6) {
                    $(this).addClass('select');
                    $(this).closest('.tb-slide').find('.tb-ifo b').html(sel);
                    if (lim == 5) {
                        $(this).closest('.tb-slide').find('.tb-ifo').hide();
                        $(this).closest('.tb-slide').find('.sm-but').addClass('on');
                    }
                }
            } else {
                var lim = $(this).closest('ul').find('.select').length;
                var sel = 6 - lim + 1;
                $(this).removeClass('select');
                $(this).closest('.tb-slide').find('.tb-ifo b').html(sel);
                $(this).closest('.tb-slide').find('.tb-ifo').show();
                $(this).closest('.tb-slide').find('.sm-but').removeClass('on');
            }
        } else {
            var lim = $(this).closest('ul').find('.select').length;
            var sel = 6 - lim + 1;
            $(this).removeClass('select');
            $(this).closest('.tb-slide').find('.tb-ifo b').html(sel);
            $(this).closest('.tb-slide').find('.tb-ifo').show();
            $(this).closest('.tb-slide').find('.sm-but').removeClass('on');
        }
    });
};

function getCurrency(value, part) {

    var format=null;

    if ($.inArray(part, ["iso","one","few","many"])>=0){
        var format=part;
        part=null;
    }

    if(!value || value=='' || value=='undefined')
        value=null;


    switch (value){
        case null:
            return currency['iso'];
            break;
        case 'coefficient':
        case 'rate':
            return (currency[value]?currency[value]:1);
            break;
        case 'iso':
        case 'one':
        case 'few':
        case 'many':
            return (currency[value]?currency[value]:currency['iso']);
            break;
        default:
            value = round((parseFloat(value)*currency['coefficient']),2);
            if((format=='many' || (!format && value>=5)) && currency['many']){
                return (!part || part==1?value:'') + (!part?' ':'') + (!part || part==2 ? currency['many']:'');
            } else if((format=='few' || (!format && (value>1 || value<1))) && currency['few']){
                return (!part || part==1?value:'') + (!part?' ':'') + (!part || part==2 ? currency['few']:'');
            } else if((format=='one' || (!format && value == 1)) && currency['one']){
                return (!part || part==1?value:'') + (!part?' ':'') + (!part || part==2 ? currency['one']:'');
            } else {
                return (!part || part==1?value:'') + (!part?' ':'') + (!part || part==2 ? currency['iso']:'');
            }
            break;
    }
}

function getText(key) {
    return(texts[key]?texts[key]:key);
}

function playAudio(key) {
    if ($.cookie("audio")==1) {
        if ($.isArray(key)){
            if(appAudio && appAudio[key[0]] && (file = appAudio[key[0]][key[1]]))
                $('<audio src=""></audio>').attr('src', 'tpl/audio/' + file).trigger("play");
        } else if (key) {
            $('<audio src=""></audio>').attr('src', 'tpl/audio/' + key).trigger("play");
        }
    }
}

function randomCachedNum() {
    var rand = Math.floor((Math.random() * 49) + 1);
    $(ticketCache).each(function(id, num) {
        if (num == rand) {
            rand = randomCachedNum();
        }
    });
    return rand;
}

function moveToEnd(target) {
    var rng, sel;
    if ( document.createRange ) {
        rng = document.createRange();
        rng.selectNodeContents(target);
        rng.collapse(false); // схлопываем в конечную точку
        sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange( rng );
    } else { // для IE нужно использовать TextRange
        var rng = document.body.createTextRange();
        rng.moveToElementText( target );
        rng.collapseToEnd();
        rng.select();
    }
}

String.prototype.replaceArray = function(find, replace) {
    var replaceString = this;
    var replaceMatch = replace;
    var replaceFind = find;
    var regex;
    for (var i = 0; i < find.length; i++) {
        if($.isArray(find))
            replaceFind = find[i];
        regex = new RegExp(replaceFind, "g");
        if($.isArray(replace))
            replaceMatch=replace[i];
        replaceString =  replaceString.replace(regex, replaceMatch);
        if(!$.isArray(find))
            break;
    }
    return replaceString;
};
