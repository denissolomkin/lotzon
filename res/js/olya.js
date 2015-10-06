/*
function ViewDurakSize() {
    full_ScreenHeight = $(window).height()*0.57;
    $(".game > .cards").height(full_ScreenHeight);   
    cardHeight = full_ScreenHeight*0.27;
    $(".card").height(cardHeight); 
    cardWidth = cardHeight*0.654;
    $(".card").width(cardWidth);
}

function viewCardSize() {

}

function viewDurak() {
    
}
$( document ).ready(function() {
   ViewDurakSize();
   console.log($(window).height());
});
$(window).resize(function() {  
    ViewDurakSize();    
});*/

$(function () {


step = 0;
appName = 'Durak';
onlineGame = appAudio = appModes = appVariations = {};
var playerPoints   = 13296;
var playerMoney   = 3.58;
var currency =  {"iso":"\u0433\u0440\u043d","one":"\u0433\u0440\u0438\u0432\u043d\u0430","few":"\u0433\u0440\u0438\u0432\u043d\u0438","many":"\u0433\u0440\u0438\u0432\u0435\u043d","coefficient":"1","rate":100};
var playerId   = 3628;

WebSocketAjaxClient = function (id) {

            if(is_numeric(id))
                step = id;
            else
                step++;

            data = Cache['games-game'][step];

            if (data.error)
                $("#report-popup").show().find(".txt").text(getText(data.error)).fadeIn(200);
            else {

                path = data.path;
                if (data.res) {

                    if (data.res.appId && data.res.appId != onlineGame.appId) {
                        onlineGame = {};
                    } else if (onlineGame.winner) {
                        onlineGame['winner'] = null;
                        onlineGame['fields'] = null;
                    }

                    $.each(data.res, function(index, value) {
                        onlineGame[index] = value;
                    });

                    if (data.res.appName)
                        appName = data.res.appName;

                    if (data.res.appMode)
                        appMode = data.res.appMode;

                    if (data.res.appId) {
                        appId = data.res.appId;
                        data = null;
                    }

                    playAudio([appName, onlineGame.action]);
                }

                eval(path.replace('\\', '') + 'Callback')(data);
return;

}
    $.getJSON("/?step="+step,
        function(data) {
            if (data.error)
                $("#report-popup").show().find(".txt").text(getText(data.error)).fadeIn(200);
            else {

                path = data.path;
                if (data.res) {

                    if (data.res.appId && data.res.appId != onlineGame.appId) {
                        onlineGame = {};
                    } else if (onlineGame.winner) {
                        onlineGame['winner'] = null;
                        onlineGame['fields'] = null;
                    }

                    $.each(data.res, function(index, value) {
                        onlineGame[index] = value;
                    });

                    if (data.res.appName)
                        appName = data.res.appName;

                    if (data.res.appMode)
                        appMode = data.res.appMode;

                    if (data.res.appId) {
                        appId = data.res.appId;
                        data = null;
                    }

                    / /

                    playAudio([appName, onlineGame.action]);
                }

                eval(path.replace('\\', '') + 'Callback')(data);
                //window[data.path.replace('\\', '') + 'Callback'](data);
        }
        // setTimeout(WebSocketAjaxClient,500);
    });

WebSocketAjaxClient();
}


function playAudio(key) {
    if (!$.cookie("audio-off")) {
        if ($.isArray(key)){
            if(appAudio && appAudio[key[0]] && (file = appAudio[key[0]][key[1]]))
                $('<audio src=""></audio>').attr('src', 'tpl/audio/' + file).trigger("play");
        } else if (key) {
            $('<audio src=""></audio>').attr('src', 'tpl/audio/' + key).trigger("play");
        }
    }
}

Object.size = function(obj) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) size++;
        }
        return size;
    };

function stackCallback() {
    if($('.rls-r-t').is(':visible')) {
        /*
         *  постановка в стек
         */
        $('.rls-r-ts').show();
        $('.rls-r-t').hide();
        $('.prc-but-cover').show();
    } else {
        /*
         *  выход из игры при другом сопернике
         */
        $('.ngm-gm .gm-mx .msg.winner .re').hide();
        $('.ngm-gm .gm-mx .msg.winner .ch-ot').hide();
        $('.ot-exit').html('Ожидаем соперника').show();
    }
}

function cancelCallback() {
    /*
     *  отказ от ожидания в стеке
     */
    $('.rls-r-ts').hide();
    $('.rls-r-t').show();
    $('.prc-but-cover').hide();
}

function quitCallback() {

    appId=0;
    WebSocketAjaxClient('update/'+appName);
    $('.tm').countdown('pause');


    if($('.ngm-gm .gm-mx .msg.winner .button.exit').hasClass('button-disabled')) {

        /*
         *  выход из игры и возврат к правилам
         */

        $('.ngm-gm .gm-mx .msg.winner .button').removeClass('button-disabled').removeAttr('disabled');

    } else if($('.ngm-gm .gm-mx .players .exit').is(":visible")){

        /*
         *  выход из игры и возврат к правилам
         */

        $('.ngm-gm .gm-mx .players .exit').removeClass('button-disabled');
    }

    $('.rls-r-ts').hide();
    $('.rls-r-t').show();
    $('.prc-but-cover').hide();
    $('.ngm-rls').fadeIn(200);
}

 function backCallback() {
     /*
      *  назад к описанию игры

     $('.rls-r-ts').hide();
     $('.rls-r-t').show();
     $('.prc-but-cover').hide();
     $('.prc-l').hide();
     $('.rls-l').fadeIn(200);
      */

     if(!$('.rls-r-t').is(':visible')){
         cancelCallback();
     }


     $('.ngm-bk').fadeOut(200);
     window.setTimeout(function(){
         $('.ch-bk').fadeIn(200);
     }, 200);
 }

function updateCallback(receiveData) {

    // $(".ngm-rls-bk .rls-l .rls-bt-bk .r .online span").text(receiveData.res.online);
    // $(".ngm-rls-bk .rls-l .rls-bt-bk .r .all span").text(receiveData.res.all);


    if(receiveData.res.points)
        updatePoints(receiveData.res.points);

    if(receiveData.res.money)
        updateMoney(receiveData.res.money);

    if(receiveData.res.modes)
        appModes[receiveData.res.key] = receiveData.res.modes;

    if(receiveData.res.variations)
        appVariations[receiveData.res.key] = receiveData.res.variations;

    if(receiveData.res.audio)
        appAudio[receiveData.res.key ? receiveData.res.key : receiveData.res.appName]=receiveData.res.audio;

    if(receiveData.res.key) {

        $(".ngm-rls-bk .rls-r .rls-r-t .rls-r-t-rating .rls-r-t-rating-points").text(
            (receiveData.res.rating && receiveData.res.rating.POINT ? receiveData.res.rating.POINT : "0"));

        $(".ngm-rls-bk .rls-r .rls-r-t .rls-r-t-rating .rls-r-t-rating-money").text(
            (receiveData.res.rating && receiveData.res.rating.MONEY ? receiveData.res.rating.MONEY : "0"));

        $('.ngm-rls-bk .rls-r .rls-mn-bk .bt').removeClass('button-disabled').removeAttr('disabled');
        $('.cell .bt').first().click();
    }

    if(receiveData.res.fund){
        $('.prz-fnd-mon').text(receiveData.res.fund && receiveData.res.fund.MONEY ? getCurrency(receiveData.res.fund.MONEY,1) : 0);
        $('.prz-fnd-pnt').text(receiveData.res.fund && receiveData.res.fund.POINT ? parseInt(receiveData.res.fund.POINT) : 0);
    }
}


// готов при наборе необходимого количества игроков
    $(document).on('click', '.mx .players .m .btn-ready', function(e){


        price = appMode.split('-');
        if ((price[0] == 'POINT' && playerPoints < parseInt(price[1])) || (price[0] == 'MONEY' && playerMoney < getCurrency(price[1],1))) {

            $("#report-popup").show().find(".txt").text(getText('INSUFFICIENT_FUNDS')).fadeIn(200);

        } else {

            var path='app/'+appName+'/'+appId;
            var data={'action':'ready'}
            WebSocketAjaxClient(path,data);

        }

    });

// пасуем в картах
$(document).on('click', '.mx .players .m .btn-pass', function(e) {

    var path = 'app/' + appName + '/' + appId;
    var data = {
        'action': 'pass'
    }
    WebSocketAjaxClient(path, data);

});
// выбор карты
    $(document).on('click', '.players .m .card:not(.select)', function(e){
        D.log('2asdasd');
         $('.players .m .card').removeClass('select').next().removeClass('select-next')
            cardsCount > 6 ? RihtMargin = '-'+ deltaMargin : RihtMargin = '-' + cardsLess6 ;
            $('.players .m .card').css({'margin-top': '0', 'margin-right': RihtMargin });
            
            $this = $(this).addClass('select');

            if ($this.next().length>0) {
                $this.next().addClass('select-next');
                $card =   $('.players .m .card:not(.select), .players .m .card:not(.select-next)');
                smallestRihtMargin =  cardsCount > 6 ? ( ((cardsCount-2) * RihtMargin + RihtMargin*2)/(cardsCount-2)) : RihtMargin;
                $card.animate({marginRight:  + smallestRihtMargin + 'px'},{ duration: 200, queue: false });
                $('.select').animate({marginTop: '-20px', marginRight: marginRightSelect },{ duration: 200, queue: false });
                $('.select').next().animate({marginRight: marginRightSelect },{ duration: 200, queue: false });
            }
            else {
                $card =   $('.players .m .card:not(.select)'); 
                smallestRihtMargin = ((cardsCount-1) * RihtMargin + RihtMargin)/(cardsCount-1);
                $card.animate({marginRight: + smallestRihtMargin + 'px'}, 250);
            } 

              
            
            // $card.css({marginRight: + smallestRihtMargin + 'px'});
            appDurakCallback('premove');

    });


// подтверждение карты
$(document).on('click', '.mx .players .m .card.select', function(e) {
    D.log('asdasd');

    var path = 'app/' + appName + '/' + appId;
    var data = {
        'action': 'move',
        'cell': $(this).attr('data-card')
    };
    WebSocketAjaxClient(path, data);
});

// подтверждение поля
$(document).on('click', '.mx .table .cards.highlight', function(e) {

    var path = 'app/' + appName + '/' + appId;
    var data = {
        'action': 'move',
        'cell': $('.mx .players .m .card.select').attr('data-card'),
        'table': $(this).attr('data-table')
    };
    WebSocketAjaxClient(path, data);
});

function getCurrency(value, part) {
    function round(a,b) {
        b=b||0;
        return parseFloat(a.toFixed(b));
    }

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
                return (!part || part==1 ? value : '') + (part==1 ? null : (!part ? ' ' : '') + currency['many']);
            } else if((format=='few' || (!format && (value>1 || value<1))) && currency['few']){
                return (!part || part==1 ? value : '') + (part==1 ? null : (!part ? ' ' : '') + currency['few']);
            } else if((format=='one' || (!format && value == 1)) && currency['one']){
                return (!part || part==1 ? value : '') + (part==1 ? null : (!part ? ' ' : '') + currency['one']);
            } else {
                return (!part || part==1 ? value : '') + (part==1 ? null : (!part ? ' ' : '') + currency['iso']);
            }
            break;
    }
}

function hideAllGames() {

}

    function errorGame(){

        // $("#report-popup").show().find(".txt").text(getText(onlineGame.error)).fadeIn(200);
        // $("#report-popup").show().fadeIn(200);

        if(onlineGame.appId==0) {
            $('.mx .tm').countdown('pause');
            appId = onlineGame.appId;
            $('.mx .prc-but-cover').hide();
            $('.ngm-rls').fadeIn(200);
        }
    }
function runGame() {
 $('.mx').html($('.mx-tmpl').html());
}
       var scale,
        scaleO,
        scaleOf,
        rightMargin24,
        rightMargin8,
        marginIndex ,
        indexMargin,
        cardsLess6,
        marginRightSelect;
function setup_for_devices() {

    console.warn('setup_for_devices');
    var gameHeight = $(window).height() - 50;
    var orientation = ($(window).width() > $(window).height());
     
    if($( window ).width() > 767) {
         scale = 1;
         scaleO = 0.7;
         scaleOf = 0.7;
         rightMargin24 = 12;
         rightMargin8 = 40; 
         marginIndex = 12;
         indexMargin = 0
         cardsLess6 = 40;
         marginRightSelect = 0;


    } else if($( window ).width() < 767) {

            scale = 0.7;
            scaleO = 0.5;
            scaleOf = 0.5;
            rightMargin24 = 0;
            marginIndex = 13;
            indexMargin = 1;
            $('.game.single-game, .game > .cards').height(gameHeight);
            $('.content-box-header').css({
                display: 'none'});
            if ($( window ).height() < 550) {
                scale = 0.4;
                scaleO = 0.4;
                scaleOf = 0.4;
                rightMargin24 = 0;
                marginIndex = 0;
                indexMargin = 2;
                cardsLess6 = 80;
                $('.game.single-game, .game > .cards').height(gameHeight);
                marginRightSelect = -40 +'px';

            }

    } else if ($( window ).width() < 480) {

            console.log('small');
            scale = 0.7;
            scaleO = 0.5;
            scaleOf = 0.5;
            rightMargin24 = 0;
            marginIndex = 13;
            indexMargin = 1;
            cardsLess6 = 60;
            marginRightSelect = -20 +'px';
            $('.game.single-game, .game > .cards').height(gameHeight);

   
}   
}


Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

function appDurakCallback(action) {
    setup_for_devices(); 
/*    small.addListener(setup_for_devices);
    medium.addListener(setup_for_devices);
    smallHeight.addListener(setup_for_devices);*/
    // $(window).resize(function() {
    //     setup_for_devices(); 
    //     small.addListener(setup_for_devices);
    //     medium.addListener(setup_for_devices);
    //     smallHeight.addListener(setup_for_devices);
    // });



    $('main').addClass('active_small_devices');
    action = action && action.res && action.res.action ? action.res.action : action || onlineGame.action;

    switch (action) {

        case 'reply':
            $('.re').hide();
            $('.ot-exit').html('Ожидаем соперника').show();
            break;

        case 'stack':

            break;

        case 'start':
        case 'timeout':
        case 'pass':
        case 'move':
        case 'wait':
        case 'ready':


            $(".ui-droppable").droppable("destroy");
            $('.m .card.draggable').draggable("destroy");
            D.log(onlineGame.action);
            D.log(onlineGame);

            if (($.inArray(onlineGame.action, ['move', 'timeout', 'pass']) == -1 &&
                    (onlineGame.action != 'ready' || Object.size(onlineGame.players) == onlineGame.current.length)) || !$('.mx .players').children('div').length) 
            {

                        hideAllGames();
                        runGame();

                        $('game > div ').addClass('cards');

                        if (onlineGame.variation && onlineGame.variation.type && onlineGame.variation.type == 'revert')
                            $('game > div').addClass('Revert');

                            $('.ngm-rls').fadeOut(200);

                D.log('обнулили поля');

                            fields = [];
                            statuses = [];

                            timestamp = null;

                        if (players = onlineGame.players) {

                            if (onlineGame.action == 'wait') {
                                var player = {
                                    "avatar": "",
                                    "name": "ждем..."
                                };
                                for (i = Object.size(players); i < onlineGame.playerNumbers; i++) {
                                    index = 0 - i;
                                    players[index] = player;
                                }
                            }


                        if (onlineGame.action == 'start') {

                            var orders = Object.keys(players);
                            var order = players[playerId].order;

                            orders.sort(function(a, b) {

                                a = players[a].order;
                                b = players[b].order;

                                check = a == order ? 1 : (
                                    b == order ? -1 : (
                                        (a < order && b < order) || (a > order && b > order) ? (a - b) : (b - a)))

                                return check;
                            });

                        $.each(orders, function(index, value) {
                            div = '<div class="player' + value + (value == playerId ? ' m' : ' o col' + (Object.size(players) - 1)) + '"></div>';


                            $('.mx .players').append(div);
                           
                        })

                    }

                    $.each(players, function(index, value) {

                        if (onlineGame.action != 'start') {
                            div = '<div class="player' + index + (index == playerId ? ' m' : ' o col' + (Object.size(players) - 1)) + '"></div>';
                            $('.mx .players').append(div);
                        }

                        value.avatar = index < 0 ? "url(../tpl/img/preloader.gif)" : (value.avatar ? "url('../filestorage/avatars/" + Math.ceil(parseInt(value.pid) / 100) + "/" + value.avatar + "')" : "url('../tpl/img/default.jpg')");

                        $('.mx .players .player' + index).append(
                            '<div class="gm-pr">' +
                            '<div class="pr-ph-bk">' +
                            '<div class="pr-ph" style="background-image: ' + value.avatar + '">' +
                            '<div class="mt"></div>' +
                            '<div class="wt"></div>' +
                            '<div class="pr-nm">' + value.name + '</div></div></div></div>');



                        if (index == playerId) {
                            $('.mx .players .player' + index).append('<div class="game-cards"></div>');
                            bet = price = onlineGame.appMode.split('-');
                            $('.mx .players .player' + index + ' .gm-pr').prepend(
                                '<div class="pr-cl">' +
                                '<div class="btn-pass">пас</div>' +
                                '<div class="msg-move">ваш ход</div>' +
                                '</div>'
                            ).append(
                                '<div class="pr-md"><span class="cards-number"></span><i class="icon-reload"></i></div>' +
                                '<div class="pr-pr"><b>' + (bet[0] == 'MONEY' ? getCurrency(bet[1], 1) : bet[1]) + '</b><span>' + (bet[0] == 'MONEY' ? getCurrency(bet[1], 2) : 'баллов') + '</span></div>' +
                                '<div class="pr-pt"><div class="icon-wallet wallet"></div><div><span class="plMoneyHolder">' + playerMoney + '</span> ' + getCurrency() + '</div><div><span class="plPointHolder">' + playerPoints + '</span> баллов</div></div>'

                            );

                            if (onlineGame.variation && onlineGame.variation.cards)
                                $('.ngm-gm .cards-number').html(onlineGame.variation.cards);
                        }


                    });

                    if (onlineGame.action == 'ready') {
                        $('.mx .players .player' + playerId + ' .gm-pr .btn-pass').addClass('btn-ready').removeClass('btn-pass').text('готов');
                    }

                    if (onlineGame.action == 'ready' || onlineGame.action == 'wait')
                        $('.mx .players').append('<div class="exit"><span class="icon-arrow-left"></span></div>');
                    }

                    if (onlineGame.trump)
                        $('.mx .deck').append(
                            '<div class="lear card' + (onlineGame.trump[0]) + '"></div>' +
                            '<div class="last"></div>' +
                            (onlineGame.fields.deck && onlineGame.fields.deck.length ? '<div class="card trump card' + onlineGame.trump + '"></div>' : '') +
                            (onlineGame.fields.deck && onlineGame.fields.deck.length > 1 ? '<div class="card"></div>' : ''));


            } else {}

            var sample = null;

            if ($.inArray(onlineGame.action, ['ready', 'wait']) == -1 || onlineGame.fields) {

                $.each(onlineGame.fields, function(key, field) {
                    if (!field)
                        return;
                    newLen = (field.length ? field.length : Object.size(field));
                    oldLen = (fields && fields[key] ? (fields[key].length ? fields[key].length : Object.size(fields[key])) : 0);

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
                        //D.log('пропускаем off');
                    } else if (is_numeric(key) && newLen == oldLen && fields && fields.deck && (fields.deck.length == onlineGame.fields.deck.length)) {
                        //D.log('пропускаем '+key);

                    } else {

                        if (is_numeric(key)) {
                            $('.mx .players .player' + key + ' .card').remove();

                            if (!sample) {
                                if (newLen < oldLen) // походил
                                    sample = (key == playerId ? 'Move-m-1' : 'Move-m-2'); // я | противник
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
                        // var margin = windowWidth 

                        $.each(field, function(index, card) {
                            idx++;

                            if (idx > count && 0)
                                return false;


                            if (is_numeric(key)) {
                                cardsCount = (field.length ? field.length : Object.size(field));

                                var deg = (cardsCount > 1 ?
                                    (idx * ((key == playerId ? 60 : 105) - (cardsCount > 4 ? 0 : (4 - cardsCount) * 15)) / cardsCount) -
                                    ((key == playerId ? 30 : 45)) : 0);

                                     

                                  $('.mx .players .player' + key + (key == playerId ? ' .game-cards' :'')).append(
                                '<div style="transform: rotate(' + deg + 'deg)' + ' ' + 

                                 (key == playerId 
                                    ? 'scale(' + scale + ',' + scale +')'
                                    : 'scale('+ scaleO + ','+ scaleO +')' ) +
                                    '; -webkit-transform: rotate(' + deg + 'deg)' + 

                                (key == playerId 
                                    ? 'scale(' + scale + ',' + scale +')'
                                    : 'scale('+ scaleO + ','+ scaleO +')' ) + 
                                    ';' + '\"' + 'class="card ' + (card 
                                    ? ' card' + card + '" data-card="' + card + '' 
                                    : '') + '">' +
                                    '</div>');


                                durakSpaceWidth = $(".game-cards").width();
                                cardWidth = $(".cards .players .m .card").width();
                                marginValue = 0;
                                marginLeftValue = 0;
                                if (cardWidth) {
                                    allCardWidth = cardsCount*cardWidth;
                                    deltaWidth = (allCardWidth - durakSpaceWidth);
                                    if( deltaWidth > 0) {deltaMargin = deltaWidth/cardsCount*1.02} else {
                                     deltaMargin = cardsCount/deltaWidth
                                    };
                                }

                                ((key == playerId)

                                    ? (idx == 1 

                                        ?(marginLeftValue =
                                                     
                                                (indexMargin == 0)
                                                    ?(deltaWidth > 0) 
                                                        ? (0 + '%')
                                                        : (cardsCount > 6 
                                                            ?(durakSpaceWidth - allCardWidth) / 2 + 'px'
                                                            :(durakSpaceWidth - allCardWidth + cardsCount * cardsLess6 ) / 2 + 'px')
                                                    :((indexMargin != 2)
                                                        ?(deltaWidth > 0
                                                            ?(cardsCount > 6 
                                                                ?(0 + '%')
                                                                :(durakSpaceWidth - allCardWidth + cardsCount * cardsLess6 ) / 2 + 'px')
                                                            :(durakSpaceWidth - allCardWidth + cardsCount * cardsLess6 ) / 2 + 'px')
                                                        :(durakSpaceWidth -  cardWidth*scale*cardsCount) / 2 + 'px')  
                                                        
                                                )

                                        : '')

                                    : '');



                                key == playerId && idx == 1 && $('.mx .players .player'+ key +' .card').css
                                    ({
                                        'margin-left': marginLeftValue , 
                                        'margin-right' :- (cardsCount > 6 
                                            ? deltaMargin 
                                            : cardsLess6 ) + 'px'     
                                    });


                                key == playerId  && $('.mx .players .player'+ key +' .card').css
                                    ({           
                                        'margin-right' :- (cardsCount > 6 
                                            ? deltaMargin 
                                            : cardsLess6 ) + 'px'     
                                    });

                            } else if (key == 'table') {
                                var cards = '';
                                $.each(card, function(i, c) {
                                    cards += '<div class="card' + (c ? ' card' + c : '') + '">' + '</div>';
                                });
                                $('.mx .' + key).append('<div data-table="' + index + '" class="cards">' + cards + '</div>');

                            } else if (key == 'off') {
                                if (index >= $('.mx .' + key + ' .card').length) {
                                    var deg = Math.random() * 360;
                                    $('.mx .' + key).append('<div ' + (key == 'off' ? 'style="margin-top:' + Math.random() * 160 + 'px;transform: scale('+scaleOf+',' + scaleOf+') rotate(' + deg + 'deg);-webkit-transform: scale('+scaleOf+',' + scaleOf+') rotate(' + deg + 'deg)" ' : '') + 'class="card' + (card ? ' card' + card : '') + '">' + '</div>');
                                }
                            }


                        });
                    }
                });

                appDurakCallback('premove');
            }


            fields = onlineGame.fields;


            $('.mx .players .mt').hide();
            $('.mx .players > div').removeClass('current beater starter');


            $.each(onlineGame.players, function(index, player) {


                if (index == playerId && onlineGame.action != 'ready') {
                    var status = '';

                    if (index == onlineGame.beater) {
                        status = 'Беру';
                    

/*                        var game_cards = $('.game-cards');
                        var wrapper = $('.table')
                            .contents()
                            .wrap($('<div>').css('position','absolute'))
                            .parent();

                        wrapper
                        .animate(
                            game_cards.offset(), 1000, function() {
                        $(this).contents().appendTo(game_cards);
                        wrapper.remove();
                            }
                        );*/
                    }
                    else if (
                        ($.inArray(parseInt(onlineGame.beater), onlineGame.current) != -1 || onlineGame.starter == playerId || (onlineGame.beater && onlineGame.players[onlineGame.beater].status && onlineGame.players[onlineGame.beater].status == 2)) && (onlineGame.players[playerId].status != 1) || (onlineGame.beater && onlineGame.players[onlineGame.beater].status))
                        status = 'Пас';
                    else if ($.inArray(parseInt(onlineGame.beater), onlineGame.current) == -1 || (onlineGame.players[playerId].status == 1))
                        status = 'Отбой';

                    $('.mx .players .player' + playerId + ' .gm-pr .btn-pass').text(status);
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

            updateTimeOut(onlineGame.timeout);

            if (!onlineGame.winner) {

                sample && playAudio([appName, sample]);

            } else {

                if (!$('.mx .players .wt').is(":visible")) {

                    playAudio([appName, ($.inArray(playerId, onlineGame.winner) != -1 ? 'Win' : 'Lose')]);

                    $.each(onlineGame.players, function(index, value) {
                        $('.mx .players .player' + index + ' .wt').removeClass('loser').html(
                            (value.result > 0 ? 'Выигрыш' : 'Проигрыш') + '<br>' +
                            (onlineGame.currency == 'MONEY' ? getCurrency(value.win, 1) : parseInt(value.win)) + ' ' +
                            (onlineGame.currency == 'MONEY' ? getCurrency() : 'баллов')
                        ).addClass(value.result < 0 ? 'loser' : '').fadeIn();

                        //if (index == playerId) {
                        //    onlineGame.currency == 'MONEY' ? updateMoney(playerMoney + getCurrency(value.win, 1)) : updatePoints(playerPoints + parseInt(value.win))
                        //}
                    });


                    setTimeout(function() {

                        if ($('.mx .players .exit').is(":visible")) {
                            $('.mx .card, .mx .deck').fadeOut();
                            $('.mx .players .wt').fadeOut();
                        }

                    }, 5000);
                }

            }


            break;

        case 'highlight':

            if (onlineGame.beater == playerId && $('.mx .table .cards').length) {

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
            break;

        case 'premove':

            appDurakCallback('highlight');

            $(".table" + (playerId == onlineGame.beater ? " .cards:not(:has(.card:eq(1)))" : '')).droppable({
                accept: ".card",
                activeClass: 'active',
                hoverClass: 'hover',
                drop: function(event, ui) {

                    if ($(this).attr('data-table')) {
                        $(this).click();
                    } else {
                        a = ui.draggable;
                        $(a).click().click();
                    }
                }
            });

            $('.m .card').draggable({
                zIndex: 10,
                containment: 'window',

                revert: function(event, ui) {
                    return !event;
                },

                start: function() {

                    $('.m .card').removeClass('select');
                    $(this).addClass('select');
                    appDurakCallback('highlight');
                },
                stop: function() {}
            });

            break;

        case 'quit':
            if (onlineGame.quit != playerId) {
                $('.re').hide();
                $('.ot-exit').html('Соперник вышел').show();
            } else
                appId = 0;

            break;


        case 'error':

            $('.m .card').removeClass('select').css('left', 0).css('top', 0);

            appDurakCallback('premove');
            errorGame();
            break;


    }
}

$(window).on('resize', function(){ 
        D.log('setup_for_devices','error');
        fields = [];
        appDurakCallback();
    }
    );

function updatePoints(points) {
    playerPoints = parseInt(points) || playerPoints;
    points=playerPoints.toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
    $('.plPointHolder').text(points);
}
function updateMoney(money) {
    // money = money || playerMoney;
    playerMoney = parseFloat(money).toFixed(2) || playerMoney;
    money=parseFloat(playerMoney).toFixed(2).toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
    $('.plMoneyHolder').text(money.replace('.00',''));
}


function updateTimeOut(time, format) {
 
}

function is_numeric(mixed_var) {
    //   example 1: is_numeric(186.31); returns 1: true
    //   example 2: is_numeric('Kevin van Zonneveld'); returns 2: false
    //   example 3: is_numeric(' +186.31e2'); returns 3: true
    //   example 4: is_numeric(''); returns 4: false
    //   example 5: is_numeric([]); returns 5: false
    //   example 6: is_numeric('1 '); returns 6: false
    var whitespace =
        " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
    return (typeof mixed_var === 'number' || (typeof mixed_var === 'string' && whitespace.indexOf(mixed_var.slice(-1)) === -
        1)) && mixed_var !== '' && !isNaN(mixed_var);
}

});