(function () {

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

        index: null,

        drawFields: function () {

            if ($.inArray(App.action, ['ready', 'wait']) == -1 || App.fields) {
                $.each(App.fields, function (key, field) {
                    if (!field)
                        return;
                    newLen = (field.length ? field.length : Object.size(field));
                    oldLen = (fields && fields[key] ? (fields[key].length ? fields[key].length : Object.size(fields[key])) : 0);
                    //console.log(oldLen, 'oldLen');

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

                    } else if (isNumeric(key) && newLen == oldLen && fields && fields.deck && (fields.deck.length == App.fields.deck.length)) {

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

                        $.each(field, function (index, card) {
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
                                $.each(card, function (i, c) {
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
            fields = App.fields;

        },

        setVariation: function () {

            D.log('Cards.variations','game');
            if (App.variation && App.variation.type && App.variation.type == 'revert')
                $('.cards > .mx').addClass('Revert');
            if (App.variation && App.variation.cards)
                $('.pr-md .cards-number').html(App.variation.cards);
        },

        animateClass: function () {

            if ($.inArray(App.action, ['move']) != -1 || App.fields) {

                $.each(App.fields, function (key, field) {

                    if (!field)
                        return;

                    //console.log($.inArray(App.action, ['move']) != -1 || App.fields, 'first');
                    newLen = (field.length ? field.length : Object.size(field));
                    oldLen = (fields && fields[key] ? (fields[key].length ? fields[key].length : Object.size(fields[key])) : 0);
                    //console.log(oldLen, 'oldLen2');

                    if (isNumeric(key) && newLen == oldLen && fields && fields.key && (fields.key.length == App.fields.key.length)) {

                    }

                    else {

                        $.each(field, function (index, card) {

                            if (oldLen == 0) {
                                newCard = $('.card' + card).addClass('animatedCard');
                            } else if (fields && fields[key] && fields[key] != undefined) {

                                if (fields[key][index] != App.fields[key][index]) {
                                    //console.log(key, 'key');

                                    if (fields.table[index] != App.fields.table[index])
                                        $('.card' + fields[key][index]).addClass('animatedCard');
                                }
                            }
                        });
                    }
                });

                Cards.animateMove($('.deck'), $('.game-cards div.animatedCard'));

            }
        },

        animateMove: function (elem, newElem) {

            $.each(newElem, function (index) {

                if (elem && elem[0] != undefined && newElem && newElem.parent()[0] != undefined && newElem.hasClass('animatedCard')) {

                    var startPosY = $(newElem[index]).css('top');
                    var startPosX = $(newElem[index]).css('left');

                    var box = elem[0].getBoundingClientRect();
                    var top = box.top - $(newElem[index]).parent()[0].getBoundingClientRect().top;
                    var left = box.left - $(newElem[index]).parent()[0].getBoundingClientRect().left - 200;

                    $(newElem[index]).css({
                        'display': 'none',
                        'top': top + 'px',
                        'left': left + 'px'
                    });
                    $(newElem[index]).css({'display': 'block',});


                    var j = index;

                    setTimeout(function () {
                        //console.log('timeout');

                        $(newElem[j]).addClass('transition');
                        $(newElem[j]).css({
                            'top': startPosY,
                            'left': startPosX

                        });


                        $(newElem[j]).removeClass('animatedCard');


                    }, j * 100);

                }


                $(newElem[j]).removeClass('transition');
            });

        },

        removeClassFromAll : function (classname) {

            var classList = document.getElementsByClassName(classname);
            for (i = 0; i < classList.length; i++) {
                classList[i].classList.remove(classname);
            }

        },

        eachCardLeft : function (target) {

            var next = target.nextElementSibling,
                selectedIndex = $('.players .m .card').index(target),
                nextIndex = $('.players .m .card').index(next),
                cards = document.querySelectorAll('.players .m .card');

            for (i = 0; i < cards.length; i++) {
                if (i < selectedIndex) {
                    showedCardsWidth = 0;
                    newTop = 0;
                } else if (i == selectedIndex) {
                    showedCardsWidth = 0;
                    newTop = -20;
                } else if (nextIndex > 0 && i == nextIndex) {
                    showedCardsWidth = cardWidth * scale * 0.5;
                    newTop = 0;
                } else if (nextIndex > 0 && i > nextIndex) {
                    showedCardsWidth = cardWidth * 2 * scale * 0.5;
                    newTop = 0;
                }

                newLeft = cards[i].style.left;
                oldTop = cards[i].style.top;
                oldTop = Number(oldTop.replace("px", ""));
                if (myCount > 6) {
                    var newLeft = (parseInt(newLeft.slice(0, newLeft.length - 2), 10) + showedCardsWidth).toFixed();
                } else {
                    var newLeft = parseInt(newLeft.slice(0, newLeft.length - 2), 10).toFixed();
                }
                cards[i].style.left = newLeft + 'px';
                cards[i].style.top = Number(oldTop + newTop) + 'px';
            }
        },

        marginsDraw: function () {

            var marginLeftValue =
                (myCount > 6 ? (deltaWidth > 0 ? (0) : (durakSpaceWidth - newAllwidth) / 2) : (durakSpaceWidth - allCardWidth + myCount * cardsLess6) / 2);

            $(cardsBlock).each(function (indx) {
                var a = (myCount > 6 ? marginLeftValue + indx * (newWidth - ((newAllwidth - durakSpaceWidth) / myCount)) * 0.9 : marginLeftValue + indx * indexLess6),
                    rotatedHeight = Cards.getRotateSize(indx),
                    newIdxrotatedHeight = Cards.getRotateSize(indx - 1),
                    topFordeg = (
                    ((newIdxrotatedHeight.Height - rotatedHeight.Height) > 0
                        ? (newIdxrotatedHeight.Height - rotatedHeight.Height)
                        : (rotatedHeight.Height - newIdxrotatedHeight.Height )) +
                    (newIdxrotatedHeight.Deg > 0
                        ? newIdxrotatedHeight.Deg
                        : (-newIdxrotatedHeight.Deg))
                    );

                $(this).css({
                    'left': a + 'px',
                    'top': topFordeg + 'px'
                });


            });

        },

        getRotateSize: function (indx) {

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

        emptyFields: function () {

            D.log('обнулили поля');
            fields = [];
            statuses = [];
            timestamp = null;
        },

        drawTrump: function () {
            if (App.trump) {
                console.log('App.trump')
                $('.mx .deck').append(
                    '<div class="lear card' + (App.trump[0]) + '"></div>' +
                    '<div class="last"></div>' +
                    (App.fields.deck && App.fields.deck.length ? '<div class="card trump card' + App.trump + '"></div>' : '') +
                    (App.fields.deck && App.fields.deck.length > 1 ? '<div class="card"></div>' : ''));
            }
        },

        createCardsWrapper: function () {

            var field = Game.field.getElementsByClassName('mx')[0];

            field.innerHTML +=
                '<div class="deck"></div>' +
                '<div class="table"></div>' +
                '<div class="off"></div>';

            $('.game > div ').addClass('cards');
            $('.mx .players .player' + Player.id).append('<div class="game-cards"></div>');
        },

        initStatuses: function () {

            if (App.action == 'ready') {
                $('.mx .players .player' + Player.id + ' .gm-pr .btn-pass')
                    .addClass('btn-ready')
                    .removeClass('btn-pass')
                    .text('готов');
            }

            $('.mx .players .mt').hide();
            $('.mx .players > div').removeClass('current beater starter');

            $.each(App.players, function (index, player) {
                if (index == Player.id && App.action != 'ready') {
                    var status = '';
                    if (index == App.beater) {
                        status = 'Беру';
                    } else if (
                        ($.inArray(parseInt(App.beater), App.current) != -1 || App.starter == Player.id || (App.beater && App.players[App.beater].status && App.players[App.beater].status == 2)) && (App.players[Player.id].status != 1) || (App.beater && App.players[App.beater].status))
                        status = 'Пас';
                    else if ($.inArray(parseInt(App.beater), App.current) == -1 || (App.players[Player.id].status == 1))
                        status = 'Отбой';
                    $('.mx .players .player' + Player.id + ' .gm-pr .btn-pass').text(status);
                }

                if (index == App.beater)
                    $('.mx .players .player' + index).addClass('beater');
                else if (index == App.starter && !$('.mx .table .cards').length)
                    $('.mx .players .player' + index).addClass('starter');
                if (!sample && (!statuses[index] || statuses[index] != player.status) && player.status)
                    sample = (index == App.beater) ? 'Move-o-1' : 'Move-m-3';
                statuses[index] = player.status ? player.status : null;

                if ($.inArray(parseInt(index), App.current) != -1) {

                    $('.mx .players .player' + index).addClass('current');

                    if ($.inArray(parseInt(App.beater), App.current) == -1 ||
                        ($.inArray(parseInt(App.beater), App.current) != -1 && App.beater == index)) {

                        Game.playerTimer
                            .add(index);

                    } else {

                        Game.playerTimer
                            .remove(index)
                            .circle();
                    }

                } else {

                    Game.playerTimer
                        .remove(index);

                    if (index == App.beater)
                        Game.playerTimer
                            .circle(index);

                    if (player.status || player.ready || App.winner) {

                        var status = '';
                        // D.log($.inArray(parseInt(index), App.current), parseInt(index), App.current);

                        if (player.status == 2 && App.beater == index)
                            status = 'Беру';
                        else if (player.status == 1 && App.starter == index)
                            status = 'Пас';
                        else if (player.status == 2)
                            status = 'Отбой';
                        else if (player.ready == 1)
                            status = 'Готов';

                        $('.mx .players .player' + index + ' .mt')
                            .show()
                            .text(status);
                    }
                }

                D.log(timestamp);
                if (App.timestamp)
                    timestamp = App.timestamp;
            });
        },

        premove: function () {

            Cards.highlight();
            Listeners.clear();

            tableObj = $(".Durak .table" + (Player.id == App.beater ? " .cards:not(:has(.card:eq(1)))" : ''));

            if (tableObj != undefined && tableObj.length > 0) {
                tableObj = tableObj.get(0);
            } else {
                tableObj = $(".Durak .table").get(0);
            }

            Listeners.options.droppable = '.mx .' + tableObj.className;
            Listeners.init();
        },

        highlight: function () {


            if (App.beater == Player.id && $('.mx .table .cards').length) {
                if ($('.ngm-gm').hasClass('Revert') && $('.mx .table .cards').length == $('.mx .table .cards .card').length && !$('.mx .table .revert').length)
                    $('.mx .table').append('<div data-table="revert" class="cards revert"><div class="card"></div></div>');

                return true;

                if ($('.mx .players .m .card.select').length) {
                    $('.mx .table .cards').each(function (index) {
                        if ($('.card', this).length == 1 && !$(this).hasClass('highlight'))
                            $(this).addClass('highlight');
                    });
                } else
                    $('.highlight').removeClass('highlight');

            }
        },

        setupForDevices: function () {

            var gameHeight = $(window).height(),
                orientation = ($(window).width() > $(window).height());

            if(Device.isMobile()){
                $('.game > .cards').css({
                    'height': gameHeight + 'px'
                });
            }

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

            } else if (Device.get() <= 0.6) {

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
                    marginRightSelect = -40 + 'px';

                }

            }

        }

    }

})();