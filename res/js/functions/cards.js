$(function() {

    scale = 1;
    scaleO = 0.7;
    scaleOf = 0.7;
    rightMargin24 = 12;
    rightMargin8 = 40;
    marginIndex = 12;
    indexMargin = 0;
    cardsLess6 = 40;
    indexLess6 = 60;
    marginRightSelect = '-10px';

    Cards = {
        drawFields: function() {

            if ($.inArray(onlineGame.action, ['ready', 'wait']) == -1 || onlineGame.fields) {
                $.each(onlineGame.fields, function(key, field) {
                    if (!field)
                        return;
                    newLen = (field.length ? field.length : Object.size(field));
                    oldLen = (fields && fields[key] ? (fields[key].length ? fields[key].length : Object.size(fields[key])) : 0);
                    console.log(oldLen,'oldLen');

                    if (key == 'deck') {
                        if (field.length) {
                            if (field.length == 1)
                                $('.mx .deck .card:not(.trump )').remove();
                            $('.mx .deck .last').text(field.length);
                        } else {
                            $('.mx .deck .lear').nextAll().hide();
                        }
                        return true;
                    } else if (key == 'table' && 0) {

                    } else if (key == 'off' && newLen == oldLen) {

                    } else if (isNumeric(key) && newLen == oldLen && fields && fields.deck && (fields.deck.length == onlineGame.fields.deck.length)) {

                    } else {
                        if (isNumeric(key)) {
                            $('.mx .players .player' + key + ' .card').remove();
                            if (typeof sample == 'undefined' && !sample) {
                                if (newLen < oldLen) // походил
                                    sample = (key == Player.id ? 'Move-m-1' : 'Move-m-2'); // я | противник
                                else // взял
                                    sample = 'Move-o-2';
                            }

                        } else if (key != 'off' || newLen == 0) {
                            $('.mx .' + key).html('');

                        } else if (key == 'off') {
                            sample = 'Move-o-3'; // отбой
                        }

                        var idx = 0;
                        var count = 16;

                        $.each(field, function(index, card) {
                            idx++;

                            if (idx > count && 0)
                                return false;


                            if (isNumeric(key)) {
                                cardsCount = (field.length ? field.length : Object.size(field));

                                if (key == Player.id) {
                                    myCount = (field.length ? field.length : Object.size(field));
                                }
                                var deg = (cardsCount > 1 
                                    ? (idx * ((key == Player.id
                                        ? 22 
                                        : 105) 
                                    - (cardsCount > 4 
                                        ? 0 
                                        : (4 - cardsCount) * 15)) / cardsCount) 
                                    - ((key == Player.id 
                                        ? 11 
                                        : 45)) 
                                    : 0);


                                $('.mx .players .player' + key + (key == Player.id ? ' .game-cards' : '')).append(
                                    '<div style="transform: rotate(' + deg + 'deg)' + ' ' +

                                    (key == Player.id ? 'scale(' + scale + ',' + scale + ')' : 'scale(' + scaleO + ',' + scaleO + ')') +
                                    '; -webkit-transform: rotate(' + deg + 'deg)' +

                                    (key == Player.id ? 'scale(' + scale + ',' + scale + ')' : 'scale(' + scaleO + ',' + scaleO + ')') +
                                    ';' + '\"' + 'class="card ' + (card ? ' card' + card + '" data-card="' + card + '' : '') + '">' +
                                    '</div>');

                                durakSpaceWidth = $(".game-cards").width();
                                cardsBlock = $(".game-cards  > div");
                                cardsWrapper = $(".game-cards");
                                cardWidth = $(".cards .players .m .card").width();
                                cardHeight = $(".cards .players .m .card").height();
                                marginValue = 0;
                                marginLeftValue = 0;
                                if (cardWidth) {
                                    allCardWidth = myCount * cardWidth;
                                    deltaWidth = (allCardWidth - durakSpaceWidth);
                                    if (deltaWidth > 0) {
                                        deltaMargin = deltaWidth / myCount * 1.02
                                    } else {
                                        deltaMargin = myCount / deltaWidth
                                    }
                                    marginRightValue = (deltaMargin < cardsLess6) ? (-cardsLess6) : (-(myCount > 6 ? deltaMargin : cardsLess6));

                                    rotatedWidth = Cards.getRotateSize(idx);
                                    newWidth = rotatedWidth.Width * scale;
                                    newAllwidth = newWidth * myCount;

                                }  

                                if (key == Player.id) {
                                    Cards.marginsDraw();
                                }

                            } else if (key == 'table') {
                                var cards = '';
                                $.each(card, function(i, c) {
                                    cards += '<div class="card' + (c ? ' card' + c : '') + '">' + '</div>';
                                });
                                $('.mx .' + key).append('<div data-table="' + index + '" class="cards">' + cards + '</div>');

                            } else if (key == 'off') {
                                if (index >= $('.mx .' + key + ' .card').length) {
                                    var deg = Math.random() * 360;
                                    $('.mx .' + key).append('<div ' + (key == 'off' ? 'style="margin-top:' + Math.random() * 160 + 'px;transform: scale(' + scaleOf + ',' + scaleOf + ') rotate(' + deg + 'deg);-webkit-transform: scale(' + scaleOf + ',' + scaleOf + ') rotate(' + deg + 'deg)" ' : '') + 'class="card' + (card ? ' card' + card : '') + '">' + '</div>');
                                }
                            }


                        });
                    }
                });
            }
             
            Cards.animateClass();
            fields = onlineGame.fields;
           
        },

        animateClass: function() {

            if ($.inArray(onlineGame.action, ['move']) != -1 || onlineGame.fields) {


                $.each(onlineGame.fields, function(key, field) {

                    if (!field)
                        return;
                    console.log($.inArray(onlineGame.action, ['move']) != -1 || onlineGame.fields, 'first');
                    newLen = (field.length ? field.length : Object.size(field));
                    oldLen = (fields && fields[key] ? (fields[key].length ? fields[key].length : Object.size(fields[key])) : 0);
                    console.log(oldLen,'oldLen2');

                    if (isNumeric(key) && newLen == oldLen && fields && fields.key && (fields.key.length == onlineGame.fields.key.length)) {

                    } 

                    else  {
   
                        $.each(field, function(index, card) {

                            if ( oldLen == 0 ) {

                                newCard = $('.card' + card).addClass('animatedCard');
                                   
                            }

                            else if (fields && fields[key] && fields[key] != undefined ) {

                                if (fields[key][index] != onlineGame.fields[key][index]) {
                                    console.log(key, 'key');

                                   if (fields.table[index] != onlineGame.fields.table[index])
                                    
                                    $('.card' + fields[key][index]).addClass('animatedCard') ;
                                }

                            }  

                           
                            // }

                        });




                    }

                }); 
                Cards.animateMove($('.deck'), $('.game-cards div.animatedCard'));

            }       
        },
        animateMove: function(elem, newElem){


  

        $.each(newElem, function(index) {

                if (elem && elem[0] != undefined && newElem && newElem.parent()[0] != undefined && newElem.hasClass('animatedCard')) {

                    var startPosY = $(newElem[index]).css('top');
                    var startPosX = $(newElem[index]).css('left');

                    var box = elem[0].getBoundingClientRect();
                    var top = box.top - $(newElem[index]).parent()[0].getBoundingClientRect().top ;
                    var left = box.left - $(newElem[index]).parent()[0].getBoundingClientRect().left -200;

                    $(newElem[index]).css({
                        'display': 'none',
                        'top': top + 'px',
                        'left': left + 'px'
                    });
                    $(newElem[index]).css({'display': 'block',});


                    var j = index;

                        setTimeout(function() {
                            console.log('timeout');

                            $(newElem[j]).addClass('transition');
                            $(newElem[j]).css({
                                'top': startPosY,
                                'left': startPosX
                                
                            });
                            
                            


                            $(newElem[j]).removeClass('animatedCard');


                        }, j* 100);

                }


            $(newElem[j]).removeClass('transition');
            });
 













            // if (elem && elem[0] != undefined && newElem && newElem.parent()[0] != undefined && newElem.hasClass('animatedCard')) {
        
            //     for (var i = 0; i<newElem.length; i++) {
            //         var startPosY = $(newElem[i]).css('top');
            //         var startPosX =$(newElem[i]).css('left');

            //         var box = elem[0].getBoundingClientRect();
            //         var top  = box.top  - $(newElem[i]).parent()[0].getBoundingClientRect().top;
            //         var left = box.left - $(newElem[i]).parent()[0].getBoundingClientRect().left;
   
            //         $(newElem[i]).css({
            //             'display' : 'none',
            //             'top' :top + 'px',
            //             'left' : left + 'px'
            //         });

                    
            //             var j = i;
            //         (function(j){
            //         setTimeout(function(){
            //             console.log('timeout');
                         
            //             $(newElem[j]).css({
            //                 'display' : 'block',
            //                 'top' :startPosY,
            //                 'left' : startPosX ,
            //                 'transition' : "all 1s"
            //             })
            //         }, j*100);
            //         })(j);
                   
            //     }

                
                
            //     console.log('top', top, 'left', left);
            // }    
        },
        marginsDraw: function() {

            marginLeftValue =
                (myCount > 6 ? (deltaWidth > 0 ? (0) : (durakSpaceWidth - newAllwidth) / 2) : (durakSpaceWidth - allCardWidth + myCount * cardsLess6) / 2)

            $(cardsBlock).each(function(indx) {
                a = (myCount > 6 ? marginLeftValue + indx * (newWidth - ((newAllwidth - durakSpaceWidth) / myCount)) * 0.9 : marginLeftValue + indx * indexLess6);

                var rotatedHeight = Cards.getRotateSize(indx);
                var newIdxrotatedHeight = Cards.getRotateSize(indx-1);

                topFordeg = (((newIdxrotatedHeight.Height - rotatedHeight.Height) > 0 
                    ? ((newIdxrotatedHeight.Height - rotatedHeight.Height) ) 
                    : (rotatedHeight.Height - newIdxrotatedHeight.Height ))  + 
                 (newIdxrotatedHeight.Deg > 0 
                    ? newIdxrotatedHeight.Deg 
                    : (-newIdxrotatedHeight.Deg))) ;
                $(this).css({
                    'left': a + 'px',
                    'top': topFordeg + 'px'
                });

                
            });

        },

        getRotateSize: function(indx) {
            var Deg = (
                myCount > 1 ? ((indx) *
                    (18 -
                        (myCount > 4 ? 0 : (4 - myCount) * 15)
                    ) / myCount) - 9 : 0
            );

            var angle = Deg * Math.PI / 180;
            var sin = Math.sin(angle);
            var cos = Math.cos(angle);
            var width = $(cardsBlock).width();
            var height = $(cardsBlock).height();
            var x1 = cos * width;
            y1 = sin * width;
            var x2 = -sin * height;
            y2 = cos * height;
            var x3 = cos * width - sin * height;
            y3 = sin * width + cos * height;
            var minX = Math.min(0, x1, x2, x3);
            maxX = Math.max(0, x1, x2, x3);
            minY = Math.min(0, y1, y2, y3);
            maxY = Math.max(0, y1, y2, y3);
            var Width = maxX - minX * scale;
            var Height = maxY - minY * scale;
            return {
                Height: Height,
                Width: Width,
                Deg: Deg
            }

        },

        emptyFields: function() {

            D.log('обнулили поля');
            fields = [];
            statuses = [];
            timestamp = null;
        },

        drawTrump: function() {
            if (onlineGame.trump) {
                console.log('onlineGame.trump')
                $('.mx .deck').append(
                    '<div class="lear card' + (onlineGame.trump[0]) + '"></div>' +
                    '<div class="last"></div>' +
                    (onlineGame.fields.deck && onlineGame.fields.deck.length ? '<div class="card trump card' + onlineGame.trump + '"></div>' : '') +
                    (onlineGame.fields.deck && onlineGame.fields.deck.length > 1 ? '<div class="card"></div>' : ''));
            }
        },

        createCardsWrapper: function() {

            $('.game > div ').addClass('cards');
            $.each(players, function(index, value) {
                if (index == Player.id) {
                    $('.mx .players .player' + index).append('<div class="game-cards"></div>');

                }
            });
        },

        initStatuses: function() {
            if (onlineGame.action == 'ready') {
                $('.mx .players .player' + Player.id + ' .gm-pr .btn-pass').addClass('btn-ready').removeClass('btn-pass').text('готов');
            }

            if (onlineGame.action == 'ready' || onlineGame.action == 'wait') {
                $('.mx .players').append('<div class="exit"><span class="icon-arrow-left"></span></div>');
            }

            $('.mx .players .mt').hide();
            $('.mx .players > div').removeClass('current beater starter');
            $.each(onlineGame.players, function(index, player) {
                if (index == Player.id && onlineGame.action != 'ready') {
                    var status = '';
                    if (index == onlineGame.beater) {
                        status = 'Беру';
                    } else if (
                        ($.inArray(parseInt(onlineGame.beater), onlineGame.current) != -1 || onlineGame.starter == Player.id || (onlineGame.beater && onlineGame.players[onlineGame.beater].status && onlineGame.players[onlineGame.beater].status == 2)) && (onlineGame.players[Player.id].status != 1) || (onlineGame.beater && onlineGame.players[onlineGame.beater].status))
                        status = 'Пас';
                    else if ($.inArray(parseInt(onlineGame.beater), onlineGame.current) == -1 || (onlineGame.players[Player.id].status == 1))
                        status = 'Отбой';
                    $('.mx .players .player' + Player.id + ' .gm-pr .btn-pass').text(status);
                }
                if (index == onlineGame.beater)
                    $('.mx .players .player' + index).addClass('beater');
                else if (index == onlineGame.starter && !$('.mx .table .cards').length)
                    $('.mx .players .player' + index).addClass('starter');
                if (!sample && (!statuses[index] || statuses[index] != player.status) && player.status)
                    sample = (index == onlineGame.beater) ? 'Move-o-1' : 'Move-m-3';
                statuses[index] = player.status ? player.status : null;
                if ($.inArray(parseInt(index), onlineGame.current) != -1) {
                    $('.mx .players .player' + index).addClass('current');
                    if ($.inArray(parseInt(onlineGame.beater), onlineGame.current) == -1 ||
                        ($.inArray(parseInt(onlineGame.beater), onlineGame.current) != -1 && onlineGame.beater == index)) {

                        // D.log($($('#tm').countdown('getTimes')).get(-1),onlineGame.timeout);
                        if (onlineGame.timestamp && timestamp != onlineGame.timestamp // Math.abs($($('#tm').countdown('getTimes')).get(-1)-onlineGame.timeout) > 2
                            || !$('.mx .players .player' + index + ' .gm-pr .pr-ph-bk .circle-timer').length) {
                            D.log('remove');
                            $('.mx .players .player' + index + ' .gm-pr .pr-ph-bk .circle, .mx .players .player' + index + ' .gm-pr .pr-ph-bk .circle-timer').remove();
                            $('.mx .players .player' + index + ' .gm-pr .pr-ph-bk').prepend('<div class="circle-timer"><div class="timer-r"></div><div class="timer-slot"><div class="timer-l"></div></div></div>').find('.timer-r,.timer-l').css('animation-duration', onlineGame.timeout + 's');
                        }

                    } else {

                        $('.mx .players .player' + index + ' .gm-pr .pr-ph-bk .circle, .mx .players .player' + index + ' .gm-pr .pr-ph-bk .circle-timer').remove();
                        $('.mx .players .player' + index + ' .gm-pr .pr-ph-bk').prepend('<div class="circle"></div>');

                    }

                } else {
                    $('.mx .players .player' + index + ' .gm-pr .pr-ph-bk .circle, .mx .players .player' + index + ' .gm-pr .pr-ph-bk .circle-timer').remove();

                    if (index == onlineGame.beater)
                        $('.mx .players .player' + index + ' .gm-pr .pr-ph-bk').prepend('<div class="circle"></div>');

                    if (player.status || player.ready || onlineGame.winner) {

                        var status = '';
                        // D.log($.inArray(parseInt(index), onlineGame.current), parseInt(index), onlineGame.current);

                        if (player.status == 2 && onlineGame.beater == index)
                            status = 'Беру';
                        else if (player.status == 1 && onlineGame.starter == index)
                            status = 'Пас';
                        else if (player.status == 2)
                            status = 'Отбой';
                        else if (player.ready == 1)
                            status = 'Готов';

                        $('.mx .players .player' + index + ' .mt').show().text(status);
                    }
                }

                D.log(timestamp);
                if (onlineGame.timestamp)
                    timestamp = onlineGame.timestamp;
            });
        },

        premove: function() {
            Cards.highlight();
            Listeners.clear();


            tableObj = $(".Durak .table" + (Player.id == onlineGame.beater ? " .cards:not(:has(.card:eq(1)))" : ''));


            if (tableObj != undefined && tableObj.length > 0) {
                tableObj = tableObj.get(0);
            } else {
                tableObj = $(".Durak .table").get(0);
            }
            Listeners.options.droppable = '.' + tableObj.className;
            Listeners.init();
        },

        highlight: function() {

            if (onlineGame.beater == Player.id && $('.mx .table .cards').length) {
                if ($('.ngm-gm').hasClass('Revert') && $('.mx .table .cards').length == $('.mx .table .cards .card').length && !$('.mx .table .revert').length)
                    $('.mx .table').append('<div data-table="revert" class="cards revert"><div class="card"></div></div>');

                if ($('.mx .players .m .card.select').length) {
                    $('.mx .table .cards').each(function(index) {
                        if ($('.card', this).length == 1 && !$(this).hasClass('highlight'))
                            $(this).addClass('highlight');
                    });
                } else
                    $('.highlight').removeClass('highlight');

            }
        },

        setupForDevices: function() {

            var gameHeight = $(window).height()-50;
            var orientation = ($(window).width() > $(window).height());

            if (Device.get() > 0.6) {
                scale = 1;
                scaleO = 0.7;
                scaleOf = 0.7;
                rightMargin24 = 12;
                rightMargin8 = 40;
                marginIndex = 12;
                indexMargin = 0;
                cardsLess6 = 40;
                indexLess6 = 60;
                marginRightSelect = '-10px';

            }

            else if (Device.get() <= 0.6 ) {
               console.log("dddddddddddddddddddddddddddddddddddddddddddddddddddddd", gameHeight <= 768);
               $('.game.single-game, .game > .cards').css({
                'height' : gameHeight + 'px'
               });


               //  .content-main.single-game
               //  css({
               //  'height' : gameHeight,
               //  'width' : '100%',
               //  'position' : 'absolute',
               //  'top' : 0,
               //  'left' :0

               // })

             if (Device.detect() == 'mobile') {
            
                scale = 1;
                scaleO = 0.5;
                scaleOf = 0.5;
                rightMargin24 = 0;
                rightMargin8 = 40;
                marginIndex = 13;
                indexMargin = 1;
                cardsLess6 = 60;
                indexLess6 = 40;
                marginRightSelect = 0;
                // $('.game.single-game, .game > .cards').height(gameHeight);

            } else if (Device.detect() == 'mobile-small') {
            
                scale = 0.8;
                scaleO = 0.5;
                scaleOf = 0.5;
                rightMargin24 = 0;
                marginIndex = 13;
                indexMargin = 1;
                cardsLess6 = 60;
                indexLess6 = 40;
                marginRightSelect = -20 + 'px';
                // $('.game.single-game, .game > .cards').height(gameHeight);


            } else if (Device.detect() == 'mobile-landscape') {
                scale = 0.6;
                scaleO = 0.3;
                scaleOf = 0.3;
                rightMargin24 = 0;
                marginIndex = 0;
                indexMargin = 2;
                cardsLess6 = 70;
                gameHeight += 50;
                indexLess6 = 30;
                // $('.game.single-game, .game > .cards').height(gameHeight);
                marginRightSelect = -40 + 'px';
                // $('.content-box-header').css({
                //     display: 'none'
                // });
            }           

            }


        }

    }

});