$(function(){
var ships = [];
var game_ships = [];
var conn;
        function WebSocketAjaxClient(path, data, stop) {
            if(!conn || conn.readyState !== 1)
            {
                conn = new WebSocket(url);
                conn.onopen = function (e) {
                    if(path){
                        conn.send(JSON.stringify({'path': path, 'data': data}));
                        WebSocketStatus('<b style="color:blue">send',path+(data?JSON.stringify(data):''))
                    }
                    console.info('Socket open');
                    WebSocketStatus('<b style="color:green">socket', 'open')
                };
            } else {
                WebSocketStatus('<b style="color:blue">send',path+(data?JSON.stringify(data):''))
                conn.send(JSON.stringify({'path': path, 'data': data}));
            }

                conn.onerror = function (e) {
                    WebSocketStatus('<b style="color:red">error', JSON.stringify(e))
                    console.error('There was an un-identified Web Socket error');
                    if(stop!==true) {
                        WebSocketAjaxClient(path, data, true);
                    }
                    else {
                        ws=1;
                        /*$.ajax({
                            url: "/players/trouble/WS",
                            method: 'GET'
                        });*/
                    }

                };

                conn.onmessage = function (e) {
                    WebSocketStatus('<b style="color:purple">receive',e.data)
                    data=$.parseJSON(e.data);
                    if(data.error)
                        $("#report-popup").show().find(".txt").text(getText(data.error)).fadeIn(200);
                    else {

                        path = data.path;
                        if(data.res) {

                            if(data.res.appId && data.res.appId!=onlineGame.appId) {
                                onlineGame = {};
                            } else if(onlineGame.winner) {
                                onlineGame['winner'] = null;
                            }

                            $.each(data.res, function( index, value ) {
                                onlineGame[index]=value;
                            });

                            if (data.res.appName)
                                appName = data.res.appName;

                            if (data.res.appMode)
                                appMode = data.res.appMode;

                            if (data.res.appId) {
                                appId = data.res.appId;
                                data = null;
                            }

                            /* */

                            playAudio([appName, onlineGame.action]);
                        }

                        eval(path.replace('\\', '') + 'Callback')(data);
                        //window[data.path.replace('\\', '') + 'Callback'](data);
                    }
                };

        }

// try start websocket
WebSocketAjaxClient();

    if(!$.cookie("audio")) {
        $('.sbk-tl-bk .b-cntrl-block').parent().find('.audio').removeClass('icon-volume-2').addClass('icon-volume-off');
    }

function WebSocketStatus(action, data) {
    $("#wsStatus").html(action+': </b>'+data+'</br>'+$("#wsStatus").html());
}
/****************************************************
 *      WebSocketAjaxClient CallBack's              *
 ****************************************************/

function stackCallback() {
    if($('.ngm-bk .rls-r-t').is(':visible')) {
        /*
         *  постановка в стек
         */
        $('.ngm-bk .rls-r-ts').show();
        $('.ngm-bk .rls-r-t').hide();
        $('.ngm-bk .prc-but-cover').show();
    } else {
        /*
         *  выход из игры при другом сопернике
         */
        $('.ngm-bk .ngm-gm .gm-mx .msg.winner .re').hide();
        $('.ngm-bk .ngm-gm .gm-mx .msg.winner .ch-ot').hide();
        $('.ot-exit').html('Ожидаем соперника').show();
    }
}

function cancelCallback() {
    /*
     *  отказ от ожидания в стеке
     */
    $('.ngm-bk .rls-r-ts').hide();
    $('.ngm-bk .rls-r-t').show();
    $('.ngm-bk .prc-but-cover').hide();
}

function quitCallback() {

    appId=0;
    WebSocketAjaxClient('update/'+appName);
    $('.ngm-bk .tm').countdown('pause');


    if($('.ngm-bk .ngm-gm .gm-mx .msg.winner .button.exit').hasClass('button-disabled')) {

        /*
         *  выход из игры и возврат к правилам
         */

        $('.ngm-bk .ngm-gm .gm-mx .msg.winner .button').removeClass('button-disabled').removeAttr('disabled');

    } else if($('.ngm-bk .ngm-gm .gm-mx .players .exit').is(":visible")){

        /*
         *  выход из игры и возврат к правилам
         */

        $('.ngm-bk .ngm-gm .gm-mx .players .exit').removeClass('button-disabled');
    }

    $('.ngm-bk .rls-r-ts').hide();
    $('.ngm-bk .rls-r-t').show();
    $('.ngm-bk .prc-but-cover').hide();
    $('.ngm-rls').fadeIn(200);
}

 function backCallback() {
     /*
      *  назад к описанию игры

     $('.ngm-bk .rls-r-ts').hide();
     $('.ngm-bk .rls-r-t').show();
     $('.ngm-bk .prc-but-cover').hide();
     $('.ngm-bk .prc-l').hide();
     $('.ngm-bk .rls-l').fadeIn(200);
      */

     $('.ngm-bk').fadeOut(200);
     window.setTimeout(function(){
         $('.ch-bk').fadeIn(200);
     }, 200);
 }

    /*
     *  обновление данных
     */
    function ratingCallback(receiveData){

        $('.ngm-rls .rls-r .preloader').remove();

        if(receiveData.res.top){

            html='';
            if(receiveData.res.top.POINT)
                $.each(receiveData.res.top.POINT, function( index, value ) {
                    html+='<li><div class="prs-ph" style="background-image: url(';
                    if(value.A)
                        html += "'../filestorage/avatars/"+Math.ceil(parseInt(value.I)/100)+"/"+value.A+"'";
                    else
                        html += "'../tpl/img/default.jpg'";
                    html+=')"></div>' +
                    '<div class="prs-ifo">' +
                    '<div class="nm">'+value.N+(value.O?' <b>•</b>':'')+'</div>' +
                    '<div class="ifo">'+Math.ceil(value.R)+' <b>|</b> '+value.T+' <b>|</b> '+Math.ceil((parseInt(value.W)))+'</div>   ' +
                    '</div></li>';

                });

            $('.rls-r .rls-r-prs.top-pnt').html(html);

            html='';
            if(receiveData.res.top.MONEY)
                $.each(receiveData.res.top.MONEY, function( index, value ) {
                    html+='<li><div class="prs-ph" style="background-image: url(';
                    if(value.A)
                        html += "'../filestorage/avatars/"+Math.ceil(parseInt(value.I)/100)+"/"+value.A+"'";
                    else
                        html += "'../tpl/img/default.jpg'";
                    html+=')"></div>' +
                    '<div class="prs-ifo">' +
                    '<div class="nm">'+value.N+(value.O?' <b>•</b>':'')+'</div>' +
                    '<div class="ifo">'+Math.ceil(value.R)+' <b>|</b> '+value.T+' <b>|</b> '+Math.ceil((parseInt(value.W)))+'</div>   ' +
                    '</div></li>';

                });

            $('.rls-r .rls-r-prs.top-mon').html(html);

        }
    }

    /*
     *  обновление данных
     */
function updateCallback(receiveData)
{

    // $(".ngm-bk .ngm-rls-bk .rls-l .rls-bt-bk .r .online span").text(receiveData.res.online);
    // $(".ngm-bk .ngm-rls-bk .rls-l .rls-bt-bk .r .all span").text(receiveData.res.all);


    if(receiveData.res.points)
        updatePoints(receiveData.res.points);

    if(receiveData.res.money)
        updateMoney(receiveData.res.money);

    if(receiveData.res.modes)
        appModes[receiveData.res.key] = receiveData.res.modes;

    if(receiveData.res.audio)
        appAudio[receiveData.res.key]=receiveData.res.audio;

    if(receiveData.res.key) {

        $(".ngm-bk .ngm-rls-bk .rls-r .rls-r-t .rls-r-t-rating .rls-r-t-rating-points").text(
        (receiveData.res.count>0?Math.ceil((parseInt(receiveData.res.win))+(parseInt(receiveData.res.count))):"0"));
//        (receiveData.res.count>0?Math.ceil((parseInt(receiveData.res.win))/25):"0")+'');

        $('.ngm-bk .ngm-rls-bk .rls-r .rls-mn-bk .bt').removeClass('button-disabled').removeAttr('disabled');
        $('.ngm-bk .cell .bt').first().click();
    }

    if(receiveData.res.fund){
        $('.prz-fnd-mon').text(receiveData.res.fund.MONEY?getCurrency(receiveData.res.fund.MONEY,1):0);
        $('.prz-fnd-pnt').text(receiveData.res.fund.POINT?parseInt(receiveData.res.fund.POINT):0);
    }
}

    /*
     *  обновление запущенных игр
     */
    function nowCallback(receiveData)
    {

        $('.ngm-rls .rls-r .preloader').remove();

        html='';
        if(receiveData.res.now && receiveData.res.now.length){
            $.each(receiveData.res.now, function( index, value ) {
                mode=value.mode.split('-');
                number = mode[2];

                html += '<div class="gm-now">' +
                '<div class="gm-now-md"><b>'+(mode[0]=='MONEY'?getCurrency(mode[1],1):mode[1])+'</b><i>'+(mode[0]=='MONEY'?getCurrency():'баллов')+'</i></div>' +
                '<div class="gm-now-nmb"><span class="icon-users"></span> '+value.players.length + '/' + mode[2]+'</div>';

                players = [];
                $.each(value.players, function (i, player) {
                    players.push(player);
                });

                html += '<div class="gm-now-plr">'+players.join(', ')+(value.players.length<mode[2]?'<span  data-id="'+value.id+'" data-mode="'+value.mode+'" class="icon-control-play"></span>':'')+"</div></div>";
            });
        } else {

            html='<div class="gn-now-create">запущенных игр нет, создайте свою</div>';
        }

        $('.ngm-rls .rls-r .now-bl').html(html);

    }


// chat
function appchatCallback(receiveData)
{
    $('#chatMessage').val('').focus();
    user=receiveData.res.user;

    if(!receiveData.res.uid)
        user='<b style="color:red;">system';
    else if(receiveData.res.uid==playerId)
        user='<b style="color:green;">'+user;
    else
        user='<b style="color:blue;">'+user;


    $("#chatStatus").html(user + ': </b>' + receiveData.res.message+'</br>'+$("#chatStatus").html());
}

// game
function appSeaBattleCallback()
{

     switch (onlineGame.action) {
     case 'reply':
         $('.re').hide();
         $('.ot-exit').html('Ожидаем соперника').show();
         break;

     case 'stack':

         break;

         case 'wait':

             hideAllGames();
             if(!($('ul.mx.SeaBattle.m').find('div').length) && !($('ul.mx.SeaBattle.m').find('li.s').length)){
                 runGame();
                 $('.ngm-bk .gm-fld .place').show();
                 $('.gm-pr .pr-cl').show().html("<span>корабли</span><b></b>");
                 $('.gm-pr.r .pr-cl').hide();

                 price=onlineGame.appMode.split('-');
                 $('.gm-pr .pr-pr').show().html("<b>"+
                 (price[0]=='MONEY'?getCurrency(price[1],1):price[1])+
                 "</b><span>"+
                 (price[0]=='MONEY'?getCurrency():'баллов')+"<br>ставка</span>");

                 $.each(onlineGame.players, function( index, value ) {
                     var player=value.pid;
                     var class_player=player==playerId?'l':'r';
                     html='';
                     $.each(value.ships, function( shp, cnt ) {html+='<div class="s '+(cnt?'':'e ')+'" style="width:'+shp*20+'px"><b>'+cnt+'</b></div>'});
                     $('.gm-pr.'+class_player+' .pr-cl b').html(html);

                     var class_field=player==playerId?'m':'o';
                     $('.ngm-bk ul.mx.'+class_field+' li').each(function( index ) {
                         $(this).attr('data-cell', $(this).data('coor')+'x'+player).addClass((player==playerId?'m':''));
                     });

                     if(value.avatar)
                         value.avatar = "url('../filestorage/avatars/"+Math.ceil(parseInt(value.pid)/100)+"/"+value.avatar+"')";
                     else
                         value.avatar = "url('../tpl/img/default.jpg')";

                     $('.gm-pr.'+class_player+' .pr-ph-bk .pr-ph').css('background-image',value.avatar);

                     if(player!=playerId && value.name){
                         $('.gm-pr.r .pr-nm').html(value.name);
                     }
                 });

                 appId=onlineGame.appId;
                 updateTimeOut(onlineGame.timeout);
             }

             $('.sb-wait').show();
             $('.sb-ready.but, .sb-random.but').hide();
             $.each(onlineGame.fields, function( index, field ) {
                 class_cell = index==playerId?'m':'o';
                 $.each(field, function( x, cells ) {
                     $.each(cells, function( y, cell) {
                         $('.ngm-bk .ngm-gm .gm-mx ul.mx.SeaBattle.'+class_cell+' li.last').
                             removeClass('last');
                         $('.ngm-bk .ngm-gm .gm-mx ul.mx.SeaBattle.'+class_cell+' li[data-cell="'+x+'x'+y+'x'+index+'"]').
                             addClass((is_numeric(cell)?'s':cell)+' last').fadeIn(100);
                     });
                 });
             });
             break;

         case 'field':

             hideAllGames();
             if((!($('ul.mx.SeaBattle.m').find('div').length) && !($('ul.mx.SeaBattle.m').find('li.s').length)) || $('ul.SeaBattle.o').is(':visible')){
                 runGame();
                 window.game_ships=onlineGame.ships;
                 genFieldSeaBattle();
                 $('.ngm-bk .gm-fld .place').show();
                 $('.ngm-bk .gm-fld .mx.SeaBattle.o').hide();
                 $('.sb-wait').hide();
                 $('.sb-ready.but, .sb-random.but').show();
             }


             $('.gm-pr .pr-cl').show().html("<span>корабли</span><b></b>");
             $('.gm-pr.r .pr-cl').hide();

             updateTimeOut(onlineGame.timeout);

             price=onlineGame.appMode.split('-');
             $('.gm-pr .pr-pr').show().html("<b>"+
            (price[0]=='MONEY'?getCurrency(price[1],1):price[1])+
             "</b><span>"+
             (price[0]=='MONEY'?getCurrency():'баллов')+"<br>ставка</span>");

             $.each(onlineGame.players, function( index, value ) {
                 var player=value.pid;
                 var class_player=player==playerId?'l':'r';
                 html='';
                 $.each(value.ships, function( shp, cnt ) {html+='<div class="s '+(cnt?'':'e ')+'" style="width:'+shp*20+'px"><b>'+cnt+'</b></div>'});
                 $('.gm-pr.'+class_player+' .pr-cl b').html(html);

                 var class_field=player==playerId?'m':'o';
                 $('.ngm-bk ul.mx.'+class_field+' li').each(function( index ) {
                     $(this).attr('data-cell', $(this).data('coor')+'x'+player).addClass((player==playerId?'m':''));
                 });

                 if(value.avatar)
                     value.avatar = "url('../filestorage/avatars/"+Math.ceil(parseInt(value.pid)/100)+"/"+value.avatar+"')";
                 else
                     value.avatar = "url('../tpl/img/default.jpg')";

                 $('.gm-pr.'+class_player+' .pr-ph-bk .pr-ph').css('background-image',value.avatar);

                 if(player!=playerId && value.name){
                     $('.gm-pr.r .pr-nm').html(value.name);
                 }
             });

             $('.ngm-bk .ngm-gm .tm').css('text-align','center');
             $('.gm-pr.l').addClass('move');
             $('.gm-pr.r').removeClass('move');

             $('ul.mx.SeaBattle.m').css('opacity',1);

             break;

         case 'start':

             if(!onlineGame.winner)
                 updateTimeOut(onlineGame.timeout);
             hideAllGames();
             runGame();
             $('.ngm-bk .gm-fld .place').hide();
             $('.ngm-bk .gm-fld .mx.SeaBattle.o').show();
             $('.gm-pr .pr-cl').show().css('opacity',1).html("<span>корабли</span><b></b>");
             $('ul.mx.SeaBattle.m div').remove();

             price=onlineGame.appMode.split('-');
             $('.gm-pr .pr-pr').show().html("<b>"+
            (price[0]=='MONEY'?getCurrency(price[1],1):price[1])+
             "</b><span>"+
             (price[0]=='MONEY'?getCurrency():'баллов')+"<br>ставка</span>");

         $.each(onlineGame.players, function( index, value ) {
             var player=value.pid;
             var class_player=player==playerId?'l':'r';
             var class_field=player==playerId?'m':'o';

             html='';
             $.each(value.ships, function( shp, cnt ) {html+='<div class="s '+(cnt?'':'e ')+class_field+'" style="width:'+shp*20+'px"><b>'+cnt+'</b></div>'});
             $('.gm-pr.'+class_player+' .pr-cl b').html(html);

             $('.ngm-bk ul.mx.'+class_field+' li').each(function( index ) {
                 $(this).attr('data-cell', $(this).data('coor')+'x'+player).addClass((player==playerId?'m':''));;
             });

             if(value.avatar)
                 value.avatar = "url('../filestorage/avatars/"+Math.ceil(parseInt(player)/100)+"/"+value.avatar+"')";
                else
                 value.avatar = "url('../tpl/img/default.jpg')";

                 $('.gm-pr.'+class_player+' .pr-ph-bk .pr-ph').css('background-image',value.avatar);

             if(player!=playerId && value.name){
                 $('.gm-pr.r .pr-nm').html(value.name);
             }
         });

         if(onlineGame.current!=playerId)
         {
             $('ul.mx.SeaBattle.o').css('opacity',0.5);
             $('ul.mx.SeaBattle.m').css('opacity',1);
             $('.ngm-bk .ngm-gm .tm').css('text-align','right');
             $('.gm-pr.r').addClass('move');
             $('.gm-pr.l').removeClass('move');
         } else {
             $('ul.mx.SeaBattle.o').css('opacity',1);
             $('ul.mx.SeaBattle.m').css('opacity',0.5);
             $('.ngm-bk .ngm-gm .tm').css('text-align','left');
             $('.gm-pr.l').addClass('move');
             $('.gm-pr.r').removeClass('move');
         }

         $.each(onlineGame.fields, function( index, field ) {
             class_cell = index==playerId?'m':'o';
             $.each(field, function( x, cells ) {
                 $.each(cells, function( y, cell) {
                     $('.ngm-bk .ngm-gm .gm-mx ul.mx.SeaBattle.'+class_cell+' li.last').
                         removeClass('last');
                     $('.ngm-bk .ngm-gm .gm-mx ul.mx.SeaBattle.'+class_cell+' li[data-cell="'+x+'x'+y+'x'+index+'"]').
                         addClass((is_numeric(cell)?'s':cell)+' last').addClass(class_cell).fadeIn(100);
                 });
             });
         });

             onlineGame.winner && endGame();

         break;

         case 'move':

             if(onlineGame.cell)
             {
                 class_cell = (onlineGame.cell.coord.split("x")[2]== playerId ? 'm' : 'o');

                 if(move=onlineGame.cell.class=='e'?1:onlineGame.cell.class=='d'?2:onlineGame.cell.class=='k'?3:null)
                    playAudio([appName, 'Move-' + class_cell + '-' + move]);

                 $('.ngm-bk .ngm-gm .gm-mx ul.mx li.'+class_cell+'.last').
                     removeClass('last');

                 $('.ngm-bk .ngm-gm .gm-mx ul.mx li[data-cell="'+onlineGame.cell.coord+'"]')
                     //html(onlineGame.cell.points).
                     .addClass(onlineGame.cell.class)
                     .html('<div class="'+onlineGame.cell.class+'" style="background:'+$('.ngm-bk .ngm-gm .gm-mx ul.mx li[data-cell="'+onlineGame.cell.coord+'"]').css('background')+';width:19px;height:19px;"></div>')
                     .find('div')
                     .effect('explode', {pieces: 4 }, 500)
                     //.effect('bounce')
                     .parent().addClass(class_cell+' last')
                     .fadeIn(300).html(onlineGame.cell.class=='d'?"<img src='tpl/img/games/damage.png'>":'');

             }

             if(onlineGame.fields) {
                 $.each(onlineGame.fields, function (index, field) {
                     class_cell = (index == playerId ? 'm' : 'o');
                     $.each(field, function (x, cells) {
                         $.each(cells, function (y, cell) {
                             $('.ngm-bk .ngm-gm .gm-mx ul.mx.SeaBattle.' + class_cell + ' li.last').
                                 removeClass('last');

                             $('.ngm-bk .ngm-gm .gm-mx ul.mx.SeaBattle.' + class_cell + ' li[data-cell="' + x + 'x' + y + 'x' + index + '"]').
                                 addClass((is_numeric(cell)?'s':cell) + ' last').addClass(class_cell).fadeIn(100).html(cell=='d'?"<img src='tpl/img/games/damage.png'>":'');
                         });
                     });
                 });
             }

             if(onlineGame.extra) {
                 var equal = $('.ngm-bk .msg.equal');
                 equal.fadeIn(200);
                 window.setTimeout(function(){
                     equal.fadeOut(200);
                 }, 2000);
             }


             if(onlineGame.current)
                 if(onlineGame.current!=playerId)
                 {
                     $('ul.mx.SeaBattle.o').css('opacity',0.5);
                     $('ul.mx.SeaBattle.m').css('opacity',1);
                     $('.ngm-bk .ngm-gm .tm').css('text-align','right');
                     $('.gm-pr.r').addClass('move');
                     $('.gm-pr.l').removeClass('move');
                 } else {
                     $('ul.mx.SeaBattle.o').css('opacity',1);
                     $('ul.mx.SeaBattle.m').css('opacity',0.5);
                     $('.ngm-bk .ngm-gm .tm').css('text-align','left');
                     $('.gm-pr.l').addClass('move');
                     $('.gm-pr.r').removeClass('move');
                 }

             if(onlineGame.players)
                 $.each(onlineGame.players, function( index, value ) {
                     var class_player=value.pid==playerId?'l':'r';
                     var class_field=value.pid==playerId?'m':'o';
                     // $('.gm-pr.'+class_player+' .pr-cl b').html(value.moves);
                     html='';
                     $.each(value.ships, function( shp, cnt ) {html+='<div class="s '+(cnt?'':'e ')+class_field+'" style="width:'+shp*20+'px"><b>'+cnt+'</b></div>'});
                     $('.gm-pr.'+class_player+' .pr-cl b').html(html);$('.gm-pr.'+class_player+' .pr-cl b').html(html);
                     $('.gm-pr.'+class_player+' .pr-pt b').html(value.points).hide().fadeIn(200);
                 });

             if(!onlineGame.winner)
                 updateTimeOut(onlineGame.timeout);

             if(onlineGame.winner){
                 $('.ngm-bk .tm').countdown('pause');
                 $('.ngm-bk .msg').hide();

                 $('.gm-pr').removeClass('move');
                 $('.ngm-bk .ngm-gm .gm-pr .pr-surr').hide();

                 $('.ngm-bk .ngm-gm .gm-mx ul.mx.SeaBattle.o li.s:not(.d,.k)').effect('pulsate',{times:10});

                 setTimeout(function(){
                     $('.msg.winner').fadeIn(200);
                     class_player=onlineGame.winner==playerId?'l':'r';

                     $('.gm-pr .pr-cl, .gm-pr .pr-pr').hide();
                     $('.gm-pr.'+class_player+' .pr-cl').show().html("<b>"+
                     (onlineGame.currency=='MONEY'?getCurrency(onlineGame.price,1):onlineGame.price )+
                     "</b><span>"+
                     (onlineGame.currency=='MONEY'?getCurrency():'баллов')+"<br>выиграно</span>");

                     $('.gm-pr.'+class_player).addClass('winner');
                 }, (onlineGame.winner==playerId?1200:3600));

                 $('.ngm-bk .ngm-gm .gm-mx .msg.winner .ch-ot').show();
                 $('.ngm-bk .ngm-gm .gm-mx .msg.winner .re').show();
                 $('.ngm-bk .ot-exit').hide();
             }
         break;

         case 'quit':
                 $('.ngm-bk .re').hide();
                 $('.ngm-bk .ot-exit').html('Соперник вышел').show();

             appId=0;
             break;


         case 'error':
             errorGame();
             break;


     }
}

/****************************************************
 *         WebSocketAjaxClient Queries              *
 ****************************************************/

// send chat
    $(document).on('click', '#chatButton', function(e){
        var path='chat';
        var data={'message':$('#chatMessage').val()};
        WebSocketAjaxClient(path,data);
    });

// присоединиться в запущенную игру
    $(document).on('click', '.ngm-bk .ngm-rls-bk .rls-r .now-bl .gm-now .gm-now-plr span', function(e){

        appMode = $(this).attr('data-mode');
        appId = $(this).attr('data-id');

            price = appMode.split('-');

            if ((price[0] == 'POINT' && playerPoints < parseInt(price[1])) || (price[0] == 'MONEY' && playerMoney < getCurrency(price[1],1))) {

                $("#report-popup").show().find(".txt").text(getText('INSUFFICIENT_FUNDS')).fadeIn(200);

            } else {

                var path = 'app/' + appName + '/' + appId;
                var data = {'action': 'start', 'mode': appMode};
                WebSocketAjaxClient(path, data);

            }

    });


// записать в игровой стек
    $(document).on('click', '.ngm-bk .ngm-go', function(e){

        if(appMode = $('.ngm-bk .rls-r .new-bl .prc-sel').find('.active').attr('data-price')) {
            price = appMode.split('-');
            appMode+='-'+($('.ngm-bk .rls-r .new-bl .plr-sel').find('.active').attr('data-players')?$('.ngm-bk .rls-r .new-bl .plr-sel').find('.active').attr('data-players'):2);

            if ((price[0] == 'POINT' && parseInt(playerPoints) < parseInt(price[1])) || (price[0] == 'MONEY' && parseFloat(playerMoney) < getCurrency(price[1],1))) {

                $("#report-popup").show().find(".txt").text(getText('INSUFFICIENT_FUNDS')).fadeIn(200);

            } else {

                var path = 'app/' + appName + '/' + appId;
                var data = {'action': 'start', 'mode': appMode};
                WebSocketAjaxClient(path, data);

            }
        } else {
            $("#report-popup").show().find(".txt").text(getText('CHOICE_BET')).fadeIn(200);
        }
    });


// повторить сыгранную игру
    $(document).on('click', '.ngm-bk .ngm-gm .gm-mx .msg.winner .re', function(e) {
        var path = 'app/' + appName + '/' + appId;
        var data = {'action': 'replay'};
        WebSocketAjaxClient(path, data);
    });

// другой соперник
    $(document).on('click', '.ngm-bk .ngm-gm .gm-mx .msg.winner .ch-ot', function(e){

        if(appId) {
            var path='app/'+appName+'/'+appId;
            var data={'action':'quit'};
            WebSocketAjaxClient(path,data);
            appId=0;
        }

        var path='app/'+appName+'/'+appId;
        var data={'action':'start', 'mode':appMode};
        WebSocketAjaxClient(path,data);
    });

// выйти из сыгранной игры
    $(document).on('click', '.ngm-bk .ngm-gm .gm-mx .msg.winner .exit', function(e){

        if($(this).hasClass('button-disabled')) return false;

        $(this).parent().find('.button').addClass('button-disabled').attr('disabled','disabled');
        var path='app/'+appName+'/'+appId;
        var data={'action':'quit'};
        WebSocketAjaxClient(path,data);

    });

// выйти из неначатой игры
    $(document).on('click', '.ngm-bk .ngm-gm .gm-mx .players .exit', function(e){

        if($(this).hasClass('button-disabled')) return false;

        $(this).addClass('button-disabled');

        window.setTimeout(function () {
            $(this).removeClass('button-disabled');
        }, 1000);
        var path='app/'+appName+'/'+appId;
        var data={'action':'quit'};
        WebSocketAjaxClient(path,data);

    });

// выписаться из стека
    $(document).on('click', '.ngm-bk .ngm-rls-bk .rls-r .ngm-cncl', function(e){
        appId=0;
        var path='app/'+appName+'/'+appId;
        var data={'action':'cancel'};
        WebSocketAjaxClient(path,data);
    });


// выписаться из стека + вернуться к правилам
    $(document).on('click', '.ngm-bk .bk-bt-rl', function(e){
        appId=0;
        var path='app/'+appName+'/'+appId;
        var data={'action':'back'};
        WebSocketAjaxClient(path,data);
        WebSocketAjaxClient('update/'+appName);
    });

// выписаться из стека + вернуться к играм
    $(document).on('click', '.ngm-bk .bk-bt', function(e){

        if($('.rls-r-t').is(":visible")) {
            $('.ngm-bk').fadeOut(200);
            window.setTimeout(function () {
                $('.ch-bk').fadeIn(200);
            }, 200);
        } else {
            appId = 0;
            var path = 'app/' + appName + '/' + appId;
            var data = {'action': 'back'};
            WebSocketAjaxClient(path, data);
        }
    });

// switch game info blocks
    $(document).on('click', '.ngm-bk .cell .bt', function(e){
        if($(this).hasClass('button-disabled')) return false;
        $('.ngm-bk .cell .bt').removeClass('active');
        $(this).addClass('active');
        $('.ngm-bk .blocks > div').hide();
        $('.ngm-bk .blocks .'+$(this).data('block')+'-bl').fadeIn(200);
    });

// open price block
    $(document).on('click', '.ngm-bk .ngm-create', function(e){
        if($(this).hasClass('button-disabled')) return false;

        $('.ngm-bk .ngm-go').addClass('button-disabled').attr('disabled','disabled');
        //$('.ngm-bk .ngm-rls-bk .prc-l .prc-but-bk .prc-sel > div').removeClass('active');
        $('.ngm-bk .ngm-rls-bk .rls-r .new-bl').find('.active').removeClass('active');
        $('.ngm-bk .ngm-rls-bk .rls-r .new-bl .prc-sel').hide();

        $('.ngm-bk .ngm-rls-bk .rls-r .new-bl .plr-bt, .ngm-bk .ngm-rls-bk .rls-r .new-bl .prc-bt').removeClass('hidden').show();

        $('.ngm-bk .ngm-rls-bk .rls-r .new-bl .plr-sel .plr-vl, .ngm-bk .ngm-rls-bk .rls-r .new-bl .prc-sel .prc-vl').remove();

        if(onlineGame.maxPlayers  && onlineGame.maxPlayers > 2 || 1) {
            for(i=2;i<=onlineGame.maxPlayers ;i++)
                $('<div class="plr-vl" data-players="'+i+'">'+i+'</div>').insertAfter($('.ngm-bk .ngm-rls-bk .rls-r .new-bl .plr-sel div').first());
            $('.ngm-bk .ngm-rls-bk .rls-r .new-bl .plr-sel .plr-vl').last().click();
        } else {
            $('.ngm-bk .ngm-rls-bk .rls-r .new-bl .plr-bt').hide();
        }

        if(appModes && appModes[appName])
            $.each(appModes[appName], function (c, m) {
                $.each(m, function (i,v) {
                    if(v>0)
                        $('<div class="prc-vl" data-price="'+c+'-'+v+'">'+(c=='MONEY'?getCurrency(v,1):v)+'</div>').insertAfter($('.ngm-bk .ngm-rls-bk .rls-r .new-bl .prc-sel[data-currency="'+c+'"] div').first());
                });
            });

        $('.prc-sel').each(function() {
            if(!$(this).find('.prc-vl').length)
                if($(this).data('currency')!='FREE' || (appModes && (!appModes[appName] || !appModes[appName]['POINT'] || $.inArray(0, appModes[appName]['POINT'])<0)))
                    $(this).prev().hide();
        });

    });

// open running games block
    $(document).on('click', '.ngm-bk .ngm-games', function(e){
        if($(this).hasClass('button-disabled')) return false;

        if(!$('.ngm-rls .rls-r .preloader').length)
            $('.ngm-rls .rls-r').append('<div class="preloader"></div>').find('.preloader').show();

        WebSocketAjaxClient('now/'+appName);
    });


// open rating block
    $(document).on('click', '.ngm-bk .ngm-rating', function(e){
        if($(this).hasClass('button-disabled')) return false;

        if(!$('.ngm-rls .rls-r .preloader').length)
            $('.ngm-rls .rls-r').append('<div class="preloader"></div>').find('.preloader').show();

        WebSocketAjaxClient('rating/'+appName);
    });

// switch блоков ставок
    $(document).on('click', '.ngm-bk .ngm-rls-bk .rls-r .new-bl .prc-bt', function(e){

        $('.ngm-bk .ngm-rls-bk .rls-r .new-bl .prc-sel .prc-vl').removeClass('active');
        $('.ngm-bk .ngm-rls-bk .rls-r .new-bl .prc-sel').removeClass('active');
        $('.ngm-bk .ngm-rls-bk .rls-r .new-bl .prc-sel').hide();
        $('.ngm-bk .ngm-rls-bk .rls-r .new-bl .prc-bt.hidden').removeClass('hidden').show();
        $(this).addClass('hidden').hide().next().show().children(':not([style$="display: none;"])').last().addClass('active');
        $('.ngm-bk .ngm-go').removeClass('button-disabled').attr('disabled',false);

    });

    
// выбор ставки
    $(document).on('click', '.ngm-bk .ngm-rls-bk .rls-r .new-bl .prc-sel .prc-vl', function(e){
        $('.ngm-bk .ngm-rls-bk .rls-r .new-bl .prc-sel .prc-vl').removeClass('active');
        $(this).addClass('active');
    });

// выбор игроков
    $(document).on('click', '.ngm-bk .ngm-rls-bk .rls-r .new-bl .plr-sel .plr-vl', function(e){
        $('.ngm-bk .ngm-rls-bk .rls-r .new-bl .plr-sel .plr-vl').removeClass('active');
        $(this).addClass('active');
    });


// сдаться
$(document).on('click', '.ngm-gm .gm-pr.l .pr-surr', function(e){
    surr=$(this);
    msg = $('.ngm-bk .ngm-gm .gm-mx .msg.ca');
    surr.fadeOut(200)
    msg.fadeIn(200);

    $('.ngm-bk .ngm-gm .gm-mx .msg.ca .bt-bk .l').off('click').on('click', function(e){
        var path='app/'+appName+'/'+appId;
        var data={'action':'pass'};
        WebSocketAjaxClient(path,data);
        msg.fadeOut(200);
    });

    $('.ngm-bk .ngm-gm .gm-mx .msg.ca .bt-bk .r').off('click').on('click', function(e){
        msg.fadeOut(200);
        surr.fadeIn(200)
    });

});

// генерация поля морского боя
    $(document).on('click', '.ngm-bk .ngm-gm .gm-mx .but.sb-random', function(e){
        genFieldSeaBattle();
    });


// подтверждение игрового поля
    $(document).on('click', '.ngm-bk .ngm-gm .gm-mx .but.sb-ready', function(e){
        var path='app/'+appName+'/'+appId;
        var data={'action':'field','field':window.ships};
        WebSocketAjaxClient(path,data);
    });

// audio
    $(document).on('click', '.chance .sbk-tl-bk .b-cntrl-block .audio', function(e){
    if($.cookie("audio")){
        $.removeCookie("audio");
        $(this).parent().find('.audio').removeClass('icon-volume-2').addClass('icon-volume-off');
    } else {
        $(this).parent().find('.audio').addClass('icon-volume-2').removeClass('icon-volume-off');
        $.cookie("audio", 1, { expires : 100 });
    }
    });

// готов при наборе необходимого количества игроков
    $(document).on('click', '.ngm-gm .gm-mx .players .m .btn-ready', function(e){


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
    $(document).on('click', '.ngm-gm .gm-mx .players .m .btn-pass', function(e){

        var path='app/'+appName+'/'+appId;
        var data={'action':'pass'}
        WebSocketAjaxClient(path,data);

    });

// выбор карты
    $(document).on('click', '.ngm-gm .gm-mx .players .m .card:not(.select)', function(e){

            $('.ngm-gm .gm-mx .players .m .card').removeClass('select');
            $(this).addClass('select');
            appDurakCallback('premove');

    });

// подтверждение карты
    $(document).on('click', '.ngm-gm .gm-mx .players .m .card.select', function(e){

            var path='app/'+appName+'/'+appId;
            var data={'action':'move','cell':$(this).attr('data-card')};
            WebSocketAjaxClient(path,data);
    });

// подтверждение поля
    $(document).on('click', '.ngm-gm .gm-mx .table .cards.highlight', function(e){

        var path='app/'+appName+'/'+appId;
        var data={'action':'move','cell':$('.ngm-gm .gm-mx .players .m .card.select').attr('data-card'),'table':$(this).attr('data-table')};
        WebSocketAjaxClient(path,data);
    });

// делаем ход
$(document).on('click', '.ngm-gm .gm-mx ul.mx li', function(e){

    if(parseInt($('.gm-pr.l .pr-cl b').html())<=0){
    //    $("#report-popup").find(".txt").text(getText('ENOUGH_MOVES'));
    //    $("#report-popup").show().fadeIn(200);
    } else if(!($('.gm-pr.l').hasClass('move'))) {
    //    $("#report-popup").find(".txt").text(getText('NOT_YOUR_MOVE'));
    //    $("#report-popup").show().fadeIn(200);
    } else if($(this).hasClass('m') || $(this).hasClass('o') || $(this).hasClass('b')) {
        //    $("#report-popup").find(".txt").text(getText('CELL_IS_PLAYED'));
        //    $("#report-popup").show().fadeIn(200);
    } else {
        $(this).addClass('b');
        window.setTimeout(function(){$(this).removeClass('b');},1000);

        var path='app/'+appName+'/'+appId;
        var data={'action':'move','cell':$(this).data('cell')}
        WebSocketAjaxClient(path,data);
    }
});

<!-- NEW GAMES PREVIEW -->
$('.ch-gm-tbl .ngm-bt').click(function(){
    hideAllGames();
    appName = $(this).data('game');
    $('.ngm-bk .blocks > div').hide();
    $('.ngm-bk .cell .bt.active').removeClass('active');
    $('.ngm-bk .ngm-rls-bk .rls-r .rls-mn-bk .cell .bt').addClass('button-disabled').attr('disabled','disabled');
    $('.ngm-rls .gm-if-bk .l').text($('.ch-gm-tbl .ngm-bt[data-game="'+appName+'"]').parent().find('.l').text());
    $('.ngm-rls .rls-txt-bk').html($('#newgame-rules').find('div[data-game="'+appName+'"]').html());
    $('.ngm-bk .ngm-rls-bk .rls-l .prz-fnd b').text('...');
    if(!$('.ngm-rls .rls-r .preloader').length)
        $('.ngm-rls .rls-r').append('<div class="preloader"></div>').find('.preloader').show();
    WebSocketAjaxClient('update/'+appName);
    $('.ch-bk').fadeOut(200);
    window.setTimeout(function(){
        $('.ngm-bk').removeClass().addClass('ngm-bk '+appName).fadeIn(200);
    }, 200);
});

    /*
<!-- NEW GAME BACK -->
$('.ngm-bk .bk-bt').on('click', function() {});
*/

    function checkFieldSeaBattle(newship,id) {

        var size=$('.mx.SeaBattle:eq(1) li').last().attr('data-coor').split('x');
        var size_x = size[0];
        var size_y = size[1];

        matrix = [
            [-1, -1], [-1, 0], [-1, 1],
            [0, -1], [0, 0], [0, 1],
            [1, -1], [1, 0], [1, 1]
        ];

        var game_ships = window.game_ships;

        var field = [];
        for (y = 1; y <= size_y; y++) {
            field[y] = [];
            for (x = 1; x <= size_x; x++)
                field[y][x] = 0;
        }
        var iterration = 0;
        var count = 0;
        var ret=false;

        loop: while (iterration != game_ships.length) {

            count++;
            if (count > 100) {
                break loop;
            }

            if (iterration != id) data = window.ships[iterration];
            else data = newship;

            x = data[0][0];
            y = data[0][1];
            h = data[1];
            l = data[2];

            ship = [];

            while (ship.length != l) {

                if (x > size_x || y > size_y) {
                    return false;
                }


                $.each(matrix, function (i, v) {
                    if (y + v[0] > 0 && y + v[0] <= size_y && x + v[1] > 0 && x + v[1] <= size_x)
                        if (field[y + v[0]][x + v[1]]) {
                            ret=true;
                        }
                });

                if(ret)
                    return false;
                ship.push([x, y]);
                h ? x++ : y++;
            }

            $.each(ship, function (i, cell) {
                field[cell[1]][cell[0]] = 1;
            });

            iterration++;

        }
        return true;
    }

    function genFieldSeaBattle(){
    window.ships=[];

    var size=$('.mx.SeaBattle:eq(1) li').last().attr('data-coor').split('x');
    var size_x = size[0];
    var size_y = size[1];

    matrix=[
        [-1,-1],[-1,0], [-1,1],
        [0,-1], [0,0],  [0,1],
        [1,-1], [1,0],  [1,1]
    ];

    var game_ships = window.game_ships;
    var field = [];
    for (y = 1; y <= size_y; y++){
        field[y] = [];
        for (x = 1; x <= size_x; x++)
            field[y][x] = 0;
    }
    var iterration = 0;
    var count = 0;

    loop: while (window.ships.length != game_ships.length) {

        count++;
        if (count > 100){
            break loop;
        }

        x = Math.ceil(Math.random() * size_x);
        y = Math.ceil(Math.random() * size_y);
        h = Math.ceil(Math.random() * 2)-1;
        l = game_ships[iterration];

        ship=[];

            while(ship.length != l) {

                con = false;
                if (l != 1 && ((h && x + 1 > size_x) || (!h && y + 1 > size_y))) {
                    continue loop;
                }

                $.each(matrix, function (i, v) {
                    if (y + v[0] > 0 && y + v[0] <= size_y && x + v[1] > 0 && x + v[1] <= size_x)
                        if (field[y + v[0]][x + v[1]])
                            con = true;
                });

                if (con) continue loop;
                ship.push([x, y]);
                h ? x++ : y++;
            }

            $.each(ship, function( i,cell) {
                field[cell[1]][cell[0]] = 1;
            });

            window.ships.push([ship[0],h,l]);
            iterration++;

    }
        var wid=parseFloat($('.mx.SeaBattle:eq(1) li').last().css('width'));
        var hei=parseFloat($('.mx.SeaBattle:eq(1) li').last().css('height'));
    var html='';
    $.each(window.ships, function( index,ship) {
        html+='<div data-id="'+index+'" ' +
        'style="' +
        'top:'+ (ship[0][1]*(hei+1)-(hei+1))+'px;' +
        'left:'+(ship[0][0]*(wid+1)-(wid+1))+'px;'+(ship[1]
            ?'width: '+(ship[2]*(wid+1))+'px;height:'+(hei+1)+'px;'
            :'height: '+(ship[2]*(hei+1))+'px;width:'+(wid+1)+'px;')+'" class="s '+(ship[1]?'h':'')+' drag"></div>';
    });

    $('ul.mx.SeaBattle.m div').remove();
    $("ul.SeaBattle.m").append(html);

    $(  ".drag" ).dblclick(function() {
        var drag=$(this)
        var h=drag.css('width');
        var w=drag.css('height');
        var v=drag.hasClass('h')?0:1;

        var ship=[].concat(window.ships[drag.data('id')]);
        ship[1] = v;

        if(checkFieldSeaBattle(ship,$(this).data('id'))) {
            $(this).css('width', w).css('height', h).removeClass('h').addClass(v ? 'h' : '');
            window.ships[$(this).data('id')][1] = v;
        } else {
            $(this).removeClass('drag ui-draggable ui-draggable-handle');
            $(this).effect( "shake",{distance:5,times :1,duration:2} );
            window.setTimeout(function(){
                drag.addClass('drag ui-draggable ui-draggable-handle');
            }, 1000);
        }

    });

    $( ".drag" ).draggable({containment: "parent",  grid: [ wid+1,hei+1 ],
        revert:function() {

            var ship=[].concat(window.ships[$(this).data('id')]);
            ship[0] = [
                (parseInt($(this).css('left'))+(wid+1))/(wid+1),
                (parseInt($(this).css('top'))+(hei+1))/(hei+1)
            ];

            if(checkFieldSeaBattle(ship,$(this).data('id'))){
                window.ships[$(this).data('id')][0]=[
                    (parseInt($(this).css('left'))+(wid+1))/(wid+1),
                    (parseInt($(this).css('top'))+(hei+1))/(hei+1)
                ];
                return false
            } else return true;
        },

        start: function() {
        },
        stop: function() {


        }
    });
}

    function printField(field) {
        $("ul.SeaBattle.m li").html('');
        $.each(field, function( x,arr) {
            if(arr)
            $.each(arr, function( y,val) {
                if(val)
                $('ul.SeaBattle.m').find('li[data-coor="'+y+'x'+x+'"]').html(val);
            });
        });
    }

// FiveLine
    function appFiveLineCallback()
    {

        switch (onlineGame.action) {

            case 'reply':
                $('.re').hide();
                $('.ot-exit').html('Ожидаем соперника').show();
                break;

            case 'stack':

                break;

            case 'start':
                hideAllGames();
                runGame();

                $('.gm-pr .pr-cl').css('opacity','100').hide().html("<b>0</b><span>ходов<br>осталось</span>");
                $('.gm-pr .pr-pt').css('opacity','100').hide().html("<b>0</b><span>очков<br>набрано</span>");

                if(!onlineGame.winner)
                    updateTimeOut(onlineGame.timeout);

                $('.ngm-rls').fadeOut(200);
                $('.ngm-bk .ngm-gm .gm-mx ul.mx > li').html('').removeClass();

                price=onlineGame.appMode.split('-');
                $('.gm-pr .pr-pr').show().html("<b>"+
               (price[0]=='MONEY'?getCurrency(price[1],1):price[1])+
                "</b><span>"+
                (price[0]=='MONEY'?getCurrency():'баллов')+"<br>ставка</span>");

                $.each(onlineGame.players, function( index, value ) {
                    var class_player=value.pid==playerId?'l':'r';
                    $('.gm-pr.'+class_player+' .pr-cl b').html(value.moves);
                    $('.gm-pr.'+class_player+' .pr-pt b').html(value.points);

                    if(value.avatar)
                        value.avatar = "url('../filestorage/avatars/"+Math.ceil(parseInt(value.pid)/100)+"/"+value.avatar+"')";
                    else
                        value.avatar = "url('../tpl/img/default.jpg')";

                    $('.gm-pr.'+class_player+' .pr-ph-bk .pr-ph').css('background-image',value.avatar);

                    if(value.pid!=playerId && value.name){
                        $('.gm-pr.r .pr-nm').html(value.name);
                    }
                });

                if(onlineGame.current!=playerId) {
                    $('.ngm-bk .ngm-gm .tm').css('text-align', 'right');
                    $('.gm-pr.r').addClass('move');
                    $('.gm-pr.l').removeClass('move');
                } else {
                    $('.ngm-bk .ngm-gm .tm').css('text-align', 'left');
                    $('.gm-pr.l').addClass('move');
                    $('.gm-pr.r').removeClass('move');
                }

                $.each(onlineGame.field, function( x, cells ) {
                    $.each(cells, function( y, cell) {
                        if(cell.player==playerId) {
                            var class_cell='m';
                        }
                        else
                        {
                            var class_cell='o';
                        }

                        $('.ngm-bk .ngm-gm .gm-mx ul.mx li.'+class_cell+'.last').
                            removeClass('last');

                        $('.ngm-bk .ngm-gm .gm-mx ul.mx li[data-cell="'+cell.coord+'"]').
                            html('<div></div>').
                            addClass((is_numeric(class_cell)?'s':class_cell)+' last').fadeIn(100);
                    });
                });

                onlineGame.winner && endGame();

                break;

            case 'move':

                if(onlineGame.cell)
                {
                    if(onlineGame.cell.player==playerId)
                        var class_cell='m';
                    else
                        var class_cell='o';

                    playAudio([appName, 'Move-'+class_cell+'-1']);

                    $('.ngm-bk .ngm-gm .gm-mx ul.mx li.'+class_cell+'.last').
                        removeClass('last');

                    cell=$('.ngm-bk .ngm-gm .gm-mx ul.mx li[data-cell="'+onlineGame.cell.coord+'"]');


                    cell
                        .html('<div style="display:none;"></div>')
                        .find('div').fadeIn(200);
                        //.find('div').toggle('clip').parent()
                        cell.addClass(class_cell+' last').fadeIn(300);

                        //.html(onlineGame.cell.points)
                        //.css('background-color',cell.css('background-color')).addClass(class_cell+' last').flip({direction:'tb',color:cell.css('background-color'),onEnd:function(){cell.removeAttr('style')}});

                }

                if(onlineGame.extra) {
                    var equal = $('.ngm-bk .msg.equal');
                    window.setTimeout(function(){
                        equal.fadeIn(200);
                    }, 500);
                    window.setTimeout(function(){
                        equal.fadeOut(200);
                    }, 2500);
                }

                if(onlineGame.current)
                    if(onlineGame.current!=playerId)
                    {
                        $('.ngm-bk .ngm-gm .tm').css('text-align','right');
                        $('.gm-pr.r').addClass('move');
                        $('.gm-pr.l').removeClass('move');
                    }
                    else
                    {
                        $('.ngm-bk .ngm-gm .tm').css('text-align','left');
                        $('.gm-pr.l').addClass('move');
                        $('.gm-pr.r').removeClass('move');
                    }

                if(onlineGame.players)
                    $.each(onlineGame.players, function( index, value ) {
                        var class_player=value.pid==playerId?'l':'r';
                        $('.gm-pr.'+class_player+' .pr-cl b').html(value.moves).hide().fadeIn(200);
                        $('.gm-pr.'+class_player+' .pr-pt b').html(value.points).hide().fadeIn(200);
                    });


                if(onlineGame.line) {
                    $.each(onlineGame.line, function (x, cells) {
                        $.each(cells, function (y, cell) {
                            $('.ngm-bk .ngm-gm .gm-mx ul.mx li[data-cell="' + x + 'x' + y + '"]').
                                addClass('w').addClass('last');
                        });
                    });
                }

                onlineGame.winner && endGame() || updateTimeOut(onlineGame.timeout);

                break;

            case 'quit':
                if(onlineGame.quit!=playerId){
                    $('.ngm-bk .re').hide();
                    $('.ngm-bk .ot-exit').html('Соперник вышел').show();
                }
                appId=0;
                break;


            case 'error':
                errorGame();
                break;


        }
    }

// DurakRevert
    function appDurakRevertCallback(action)
    {
        appDurakCallback(action)
    }

// Durak
    function appDurakCallback(action)
    {

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
                console.log(onlineGame.action);
                console.log(onlineGame);

                if(($.inArray(onlineGame.action, ['move','timeout','pass']) == -1 &&
                    (onlineGame.action!='ready' || Object.size(onlineGame.players)==onlineGame.current.length))
                    || !$('.ngm-bk .ngm-gm .gm-mx .mx .players').children('div').length) {

                    hideAllGames();
                    runGame();

                    $('.ngm-bk .ngm-gm').addClass('cards');
                    $('.ngm-rls').fadeOut(200);

                    echo('обнулили поля');

                    fields = [];
                    statuses = [];

                    timestamp = null;

                    if(players = onlineGame.players){

                        if(onlineGame.action=='wait'){
                            var player = {"avatar":"","name":"ждем..."};
                            for(i=Object.size(players);i<onlineGame.playerNumbers;i++) {
                                index = 0 - i;
                                players[index] = player;
                            }
                        }


                        if(onlineGame.action=='start'){

                            var orders = Object.keys(players);
                            var order = players[playerId].order;

                            orders.sort(function (a, b) {

                                a = players[a].order;
                                b = players[b].order;

                                check = a == order ? 1 : (
                                    b == order ? -1 : (
                                        (a < order && b < order ) || (a > order && b > order ) ? (a - b) : (b - a) ))

                                return check;
                            });

                            $.each(orders, function (index, value) {
                                div = '<div class="player' + value + (value == playerId ? ' m' : ' o col' + ( Object.size(players) - 1)) + '"></div>';
                                $('.ngm-bk .ngm-gm .gm-mx .mx .players').append(div);
                            })

                        }

                        $.each(players, function( index, value ) {

                            if(onlineGame.action!='start') {
                                div = '<div class="player' + index + (index == playerId ? ' m' : ' o col' + ( Object.size(players) - 1)) + '"></div>';
                                $('.ngm-bk .ngm-gm .gm-mx .mx .players').append(div);
                            }

                            value.avatar = index < 0 ? "url(../tpl/img/preloader.gif)": (value.avatar ? "url('../filestorage/avatars/" + Math.ceil(parseInt(value.pid) / 100) + "/" + value.avatar + "')" : "url('../tpl/img/default.jpg')");

                            $('.ngm-bk .ngm-gm .gm-mx .mx .players .player' + index).append(
                                '<div class="gm-pr">' +
                                '<div class="pr-ph-bk">' +
                                '<div class="pr-ph" style="background-image: ' + value.avatar + '">' +
                                '<div class="mt"></div>' +
                                '<div class="wt"></div>' +
                                '<div class="pr-nm">' + value.name + '</div></div></div></div>');




                            if(index==playerId){

                                bet = price=onlineGame.appMode.split('-');
                                $('.ngm-bk .ngm-gm .gm-mx .mx .players .player' + index +' .gm-pr').prepend(
                                    '<div class="pr-cl">' +
                                        '<div class="btn-pass">пас</div>' +
                                        '<div class="msg-move">ваш ход</div>' +
                                    '</div>'
                                ).append(
                                    '<div class="pr-md"><i class="icon-reload"></i></div>'+
                                    '<div class="pr-pr"><b>'+(bet[0]=='MONEY'?getCurrency(bet[1],1):bet[1])+'</b><span>'+(bet[0]=='MONEY'?getCurrency(bet[1],2):'баллов')+'</span></div>'+
                                    '<div class="pr-pt"><div class="icon-wallet wallet"></div><div><span class="plMoneyHolder">'+playerMoney+'</span> '+getCurrency()+'</div><div><span class="plMoneyPoints">'+playerPoints+'</span> баллов</div></div>'

                                );
                            }


                        });

                        if(onlineGame.action=='ready'){
                            $('.ngm-bk .ngm-gm .gm-mx .mx .players .player' + playerId +' .gm-pr .btn-pass').addClass('btn-ready').removeClass('btn-pass').text('готов');
                        }

                        if(onlineGame.action=='ready' || onlineGame.action=='wait')
                            $('.ngm-bk .ngm-gm .gm-mx .mx .players').append('<div class="exit"><span class="icon-arrow-left"></span></div>');
                    }

                    if(onlineGame.trump)
                        $('.ngm-bk .ngm-gm .gm-mx .mx .deck').append(
                            '<div class="lear card' + (onlineGame.trump[0]) + '"></div>'+
                            '<div class="last"></div>'+
                            (onlineGame.fields.deck && onlineGame.fields.deck.length ? '<div class="card trump card' + onlineGame.trump + '"></div>' : '')+
                            (onlineGame.fields.deck && onlineGame.fields.deck.length > 1 ? '<div class="card"></div>' : ''));


                } else {
                }

                var sample = null;

                if($.inArray(onlineGame.action, ['ready','wait']) == -1
                    && onlineGame.fields){

                    $.each(onlineGame.fields, function( key, field ) {
                        if(!field)
                            return;
                        newLen = (field.length ? field.length : Object.size(field));
                        oldLen = (fields && fields[key] ? (fields[key].length ? fields[key].length : Object.size(fields[key])) : 0);
                        //if(is_numeric(key))
                        //console.log(key, newLen, oldLen, fields);

                        if (key == 'deck') {
                            if (field.length) {
                                $('.ngm-bk .ngm-gm .gm-mx .mx .deck .last').text(field.length);
                            } else{
                                $('.ngm-bk .ngm-gm .gm-mx .mx .deck .lear').nextAll().hide();
                            }
                            return true;
                        } else if(key == 'table' && 0) {

                        } else if(key == 'off' && newLen == oldLen) {
                           //console.log('пропускаем off');
                        } else if(is_numeric(key) && newLen == oldLen && fields && fields.deck && (fields.deck.length == onlineGame.fields.deck.length )) {
                           //console.log('пропускаем '+key);

                        } else {

                            if(is_numeric(key)){
                                $('.ngm-bk .ngm-gm .gm-mx .mx .players .player' + key + ' .card').remove();

                                if(!sample){
                                    if(newLen<oldLen) // походил
                                        sample = (key==playerId ? 'Move-m-1' : 'Move-m-2'); // я | противник
                                    else // взял
                                        sample='Move-o-2';
                                }

                            } else if(key != 'off' || newLen == 0){
                                $('.ngm-bk .ngm-gm .gm-mx .mx .' + key).html('');
                            } else if(key == 'off'){
                                sample = 'Move-o-3'; // отбой
                            }

                            var idx = 0;
                            var count = 16;

                            $.each(field, function (index, card) {
                                idx++;

                                if(idx>count && 0)
                                    return false;


                                if (is_numeric(key)) {
                                    cardsCount = (field.length ? field.length : Object.size(field));
                                    // cardsCount = count;
                                    $('.ngm-bk .ngm-gm .gm-mx .mx .players .player' + key).append(
                                        '<div style="transform: rotate(' +
                                        ( cardsCount > 1 ?
                                            (idx * ((key == playerId ? 60 : 105) - (cardsCount>4?0:(4-cardsCount)*15)) / cardsCount) -
                                                ((key == playerId ? 30 : 45))
                                            : 0 ) +
                                        'deg);' +
                                        (key == playerId ? (idx == 1 ? 'margin-left:'+(230+(cardsCount>4?(cardsCount>8?-160:''):(6-cardsCount)*30))+'px;' : '') +
                                        (cardsCount>6?('margin-right:-' + (110 - (cardsCount>8?750:400) / cardsCount) + 'px'):'')
                                         : '' ) + '"' +
                                        'class="card ' + (card ? ' card' + card + '" data-card="' + card + '' : '') + '">' +
                                        '</div>');

                                } else if (key == 'table') {
                                    var cards = '';
                                    $.each(card, function (i, c) { cards += '<div class="card' + (c ? ' card' + c : '') + '">' + '</div>';});
                                    $('.ngm-bk .ngm-gm .gm-mx .mx .' + key).append('<div data-table="'+index+'" class="cards">'+cards+'</div>');

                                } else if (key == 'off')  {
                                    if(index >= $('.ngm-bk .ngm-gm .gm-mx .mx .' + key+' .card').length)
                                        $('.ngm-bk .ngm-gm .gm-mx .mx .' + key).append('<div '+(key=='off'?'style="margin-top:'+Math.random()*160+'px;transform: scale(0.7,0.7) rotate('+Math.random()*360+'deg)" ':'')+'class="card' + (card ? ' card' + card : '') + '">' + '</div>');
                                }
                            });
                        }
                    });

                    appDurakCallback('premove');
                }


                fields = onlineGame.fields;


                $('.ngm-bk .ngm-gm .gm-mx .mx .players .mt').hide();
                $('.ngm-bk .ngm-gm .gm-mx .mx .players > div').removeClass('current beater starter');


                    $.each(onlineGame.players, function (index, player) {


                        if (index==playerId && onlineGame.action!='ready') {
                            var status = '';

                            if (index == onlineGame.beater)
                                status = 'Беру';
                            else if (
                                ($.inArray(parseInt(onlineGame.beater), onlineGame.current) != -1 || onlineGame.starter == playerId || (onlineGame.beater && onlineGame.players[onlineGame.beater].status && onlineGame.players[onlineGame.beater].status == 2))
                                && (onlineGame.players[playerId].status != 1)
                                || (onlineGame.beater && onlineGame.players[onlineGame.beater].status))
                                status = 'Пас';
                            else if ($.inArray(parseInt(onlineGame.beater), onlineGame.current) == -1
                                || (onlineGame.players[playerId].status == 1))
                                status = 'Отбой';

                            $('.ngm-bk .ngm-gm .gm-mx .mx .players .player' + playerId +' .gm-pr .btn-pass').text(status);
                        }

                        if (index == onlineGame.beater)
                            $('.ngm-bk .ngm-gm .gm-mx .mx .players .player' + index).addClass('beater');
                        else if(index == onlineGame.starter && !$('.ngm-bk .ngm-gm .gm-mx .mx .table .cards').length)
                            $('.ngm-bk .ngm-gm .gm-mx .mx .players .player' + index).addClass('starter');

                        if (!sample && (!statuses[index] || statuses[index]!=player.status) && player.status)
                            sample = (index == onlineGame.beater) ? 'Move-o-1' : 'Move-m-3';

                        statuses[index] = player.status ? player.status : null;

                        if ($.inArray(parseInt(index), onlineGame.current) != -1) {

                            $('.ngm-bk .ngm-gm .gm-mx .mx .players .player' + index).addClass('current');

                            if($.inArray(parseInt(onlineGame.beater), onlineGame.current) == -1 ||
                                ($.inArray(parseInt(onlineGame.beater), onlineGame.current) != -1 && onlineGame.beater==index)){

                                // console.log($($('#tm').countdown('getTimes')).get(-1),onlineGame.timeout);

                                if( onlineGame.timestamp && timestamp != onlineGame.timestamp // Math.abs($($('#tm').countdown('getTimes')).get(-1)-onlineGame.timeout) > 2
                                    || !$('.ngm-bk .ngm-gm .gm-mx .mx .players .player' + index + ' .gm-pr .pr-ph-bk .circle-timer').length){

                                    console.log('remove');

                                    $('.ngm-bk .ngm-gm .gm-mx .mx .players .player' + index + ' .gm-pr .pr-ph-bk .circle, .ngm-bk .ngm-gm .gm-mx .mx .players .player' + index + ' .gm-pr .pr-ph-bk .circle-timer').remove();
                                    $('.ngm-bk .ngm-gm .gm-mx .mx .players .player' + index + ' .gm-pr .pr-ph-bk').prepend('<div class="circle-timer"><div class="timer-r"></div><div class="timer-slot"><div class="timer-l"></div></div></div>').find('.timer-r,.timer-l').css('animation-duration',onlineGame.timeout+'s');
                                }

                            } else {

                                $('.ngm-bk .ngm-gm .gm-mx .mx .players .player' + index + ' .gm-pr .pr-ph-bk .circle, .ngm-bk .ngm-gm .gm-mx .mx .players .player' + index + ' .gm-pr .pr-ph-bk .circle-timer').remove();
                                $('.ngm-bk .ngm-gm .gm-mx .mx .players .player' + index +' .gm-pr .pr-ph-bk').prepend('<div class="circle"></div>');

                            }

                        } else {

                            $('.ngm-bk .ngm-gm .gm-mx .mx .players .player' + index + ' .gm-pr .pr-ph-bk .circle, .ngm-bk .ngm-gm .gm-mx .mx .players .player' + index + ' .gm-pr .pr-ph-bk .circle-timer').remove();

                            if (index == onlineGame.beater)
                                $('.ngm-bk .ngm-gm .gm-mx .mx .players .player' + index +' .gm-pr .pr-ph-bk').prepend('<div class="circle"></div>');

                            if (player.status || player.ready || onlineGame.winner) {

                                var status = '';
                                // console.log($.inArray(parseInt(index), onlineGame.current), parseInt(index), onlineGame.current);

                                if (player.status == 2 && onlineGame.beater == index)
                                    status = 'Беру';
                                else if (player.status == 1 && onlineGame.starter == index)
                                    status = 'Пас';
                                else if (player.status == 2)
                                    status = 'Отбой';
                                else if (player.ready == 1)
                                    status = 'Готов';

                                $('.ngm-bk .ngm-gm .gm-mx .mx .players .player' + index + ' .mt').show().text(status);
                            }
                        }

                        echo(timestamp);
                        if(onlineGame.timestamp)
                            timestamp = onlineGame.timestamp;
                    });

                    updateTimeOut(onlineGame.timeout);

                if(!onlineGame.winner) {

                    if(sample) {
                        console.log(sample);
                        playAudio([appName, sample]);
                    }

                } else {

                    // $('.ngm-bk .tm').countdown('pause');
                    // $('.ngm-bk .msg').hide();

                    // $('.ngm-bk .ngm-gm .gm-mx .mx .players .gm-pr .pr-ph-bk .circle, .ngm-bk .ngm-gm .gm-mx .mx .players .gm-pr .pr-ph-bk .circle-timer').remove();
                    // $('.ngm-bk .ngm-gm .gm-mx .mx .players > div .tm').remove();
                    // $('.ngm-bk .ngm-gm .gm-mx .mx .players > div').removeClass('current');


                    if(!$('.ngm-bk .ngm-gm .gm-mx .mx .players .wt').is(":visible")) {

                    playAudio([appName, ($.inArray(playerId, onlineGame.winner) != -1 ? 'Win' : 'Lose')]);

                        // $('.msg.winner').fadeIn(200);

                        $.each(onlineGame.players, function (index, value) {
                            $('.ngm-bk .ngm-gm .gm-mx .mx .players .player' + index + ' .wt').removeClass('loser').html(
                                (value.result > 0 ? 'Выигрыш' : 'Проигрыш') + '<br>' +
                                (onlineGame.currency == 'MONEY' ? getCurrency(value.win, 1) : parseInt(value.win) ) + ' ' +
                                (onlineGame.currency == 'MONEY' ? getCurrency() : 'баллов')
                            ).addClass(value.result < 0 ? 'loser' : '').fadeIn();

                            if(index==playerId){
                                onlineGame.currency == 'MONEY' ? updateMoney(playerMoney+getCurrency(value.win, 1)) : updatePoints(playerPoints+parseInt(value.win))
                            }
                        });


                        setTimeout(function () {

                            if ($('.ngm-bk .ngm-gm .gm-mx .players .exit').is(":visible")) {
                                $('.ngm-bk .ngm-gm .gm-mx .mx .card, .ngm-bk .ngm-gm .gm-mx .mx .deck').fadeOut();
                                $('.ngm-bk .ngm-gm .gm-mx .mx .players .wt').fadeOut();
                            }

                        }, 2000);
                    }
                    // setTimeout(function () {}, 200);

                    // $('.ngm-bk .ngm-gm .gm-mx .msg.winner .ch-ot').show();
                    // $('.ngm-bk .ngm-gm .gm-mx .msg.winner .re').show();
                    // $('.ngm-bk .ot-exit').hide();

                }


                break;

            case 'highlight':

                if(onlineGame.beater == playerId && $('.ngm-gm .gm-mx .table .cards').length){

                    if($('.ngm-gm').hasClass('DurakRevert') && $('.ngm-gm .gm-mx .table .cards').length==$('.ngm-gm .gm-mx .table .cards .card').length && !$('.ngm-gm .gm-mx .table .revert').length)
                        $('.ngm-gm .gm-mx .table').append('<div data-table="revert" class="cards revert"><div class="card"></div></div>');


                    if( $('.ngm-gm .gm-mx .players .m .card.select').length){
                        $('.ngm-gm .gm-mx .table .cards').each(function( index ) {
                            if($('.card',this).length==1 && !$(this).hasClass('highlight'))
                                $(this).addClass('highlight');

                        });
                    } else
                        $('.highlight').removeClass('highlight');

                }
                break;

            case 'premove':

                appDurakCallback('highlight');

                $(".table"+(playerId==onlineGame.beater?" .cards:not(:has(.card:eq(1)))":'')).droppable({
                    accept: ".card",
                    activeClass: 'active',
                    hoverClass: 'hover',
                    drop: function(event, ui) {

                        if($(this).attr('data-table'))
                            $(this).click();
                        else
                            $(ui.draggable).click();
                    }
                });

                $('.m .card').draggable({
                    zIndex: 10,
                    containment:'window',

                    revert:function(event, ui) { return !event; },

                    start: function() {

                        $('.m .card').removeClass('select');
                        $(this).addClass('select');
                        appDurakCallback('highlight');
                    },
                    stop: function() {}
                });

                break;

            case 'quit':
                if(onlineGame.quit!=playerId){
                    $('.ngm-bk .re').hide();
                    $('.ngm-bk .ot-exit').html('Соперник вышел').show();
                } else
                    appId=0;

                break;


            case 'error':

                $('.m .card').removeClass('select').css('left',0).css('top',0);

                appDurakCallback('premove');
                errorGame();
                break;


        }
    }

// WhoMore
function appWhoMoreCallback()
{

    switch (onlineGame.action) {

        case 'reply':
            $('.re').hide();
            $('.ot-exit').html('Ожидаем соперника').show();
            break;

        case 'stack':

            break;

        case 'start':
            hideAllGames();
            runGame();

            $('.gm-pr .pr-cl').css('opacity','100').show().html("<b>0</b><span>ходов<br>осталось</span>");
            $('.gm-pr .pr-pt').css('opacity','100').show().html("<b>0</b><span>очков<br>набрано</span>");

            if(!onlineGame.winner)
                updateTimeOut(onlineGame.timeout);

            $('.ngm-rls').fadeOut(200);
            $('.ngm-bk .ngm-gm .gm-mx ul.mx > li').html('').removeClass();

            price=onlineGame.appMode.split('-');
            $('.gm-pr .pr-pr').show().html("<b>"+
           (price[0]=='MONEY'?getCurrency(price[1],1):price[1])+
            "</b><span>"+
            (price[0]=='MONEY'?getCurrency():'баллов')+"<br>ставка</span>");

            $.each(onlineGame.players, function( index, value ) {
                var class_player=value.pid==playerId?'l':'r';
                $('.gm-pr.'+class_player+' .pr-cl b').html(value.moves);
                $('.gm-pr.'+class_player+' .pr-pt b').html(value.points);

                if(value.avatar)
                    value.avatar = "url('../filestorage/avatars/"+Math.ceil(parseInt(value.pid)/100)+"/"+value.avatar+"')";
                else
                    value.avatar = "url('../tpl/img/default.jpg')";

                $('.gm-pr.'+class_player+' .pr-ph-bk .pr-ph').css('background-image',value.avatar);

                if(value.pid!=playerId && value.name){
                    $('.gm-pr.r .pr-nm').html(value.name);
                }
            });

            if(onlineGame.current!=playerId)
            {
                $('.ngm-bk .ngm-gm .tm').css('text-align','right');
                $('.gm-pr.r').addClass('move');
                $('.gm-pr.l').removeClass('move');
            }
            else
            {
                $('.ngm-bk .ngm-gm .tm').css('text-align','left');
                $('.gm-pr.l').addClass('move');
                $('.gm-pr.r').removeClass('move');
            }

            $.each(onlineGame.field, function( x, cells ) {
                $.each(cells, function( y, cell) {
                    if(cell.player==playerId) {
                        var class_cell='m';
                    }
                    else
                    {
                        var class_cell='o';
                    }

                    $('.ngm-bk .ngm-gm .gm-mx ul.mx li.'+class_cell+'.last').
                        removeClass('last');

                    $('.ngm-bk .ngm-gm .gm-mx ul.mx li[data-cell="'+cell.coord+'"]').
                        html(cell.points).
                        addClass((is_numeric(class_cell)?'s':class_cell)+' last').fadeIn(100);
                });
            });

            if(onlineGame.winner){
                $('.ngm-bk .tm').countdown('pause');
                $('.ngm-bk .msg').hide();

                $('.gm-pr').removeClass('move');
                $('.ngm-bk .ngm-gm .gm-pr .pr-surr').hide();

                setTimeout(function(){
                    $('.msg.winner').fadeIn(200);
                    class_player=onlineGame.winner==playerId?'l':'r';

                    $('.gm-pr .pr-cl').css('opacity',0);
                    $('.gm-pr .pr-pr').hide();

                    $('.gm-pr.'+class_player+' .pr-cl').css('opacity','100').html("<b>"+
                    (onlineGame.currency=='MONEY'?getCurrency(onlineGame.price,1):onlineGame.price )+
                    "</b><span>"+
                    (onlineGame.currency=='MONEY'?getCurrency():'баллов')+"<br>выиграно</span>");

                    $('.gm-pr.'+class_player).addClass('winner');
                }, 1200);

                $('.ngm-bk .ngm-gm .gm-mx .msg.winner .ch-ot').show();
                $('.ngm-bk .ngm-gm .gm-mx .msg.winner .re').show();
                $('.ngm-bk .ot-exit').hide();
            }

            break;

        case 'move':

            if(onlineGame.cell)
            {
                if(onlineGame.cell.player==playerId)
                    var class_cell='m';
                else
                    var class_cell='o';

                playAudio([appName, 'Move-'+class_cell+'-1']);

                $('.ngm-bk .ngm-gm .gm-mx ul.mx li.'+class_cell+'.last').
                    removeClass('last');

                cell=$('.ngm-bk .ngm-gm .gm-mx ul.mx li[data-cell="'+onlineGame.cell.coord+'"]');

                cell
                    .html('<div style="background:'+cell.css('background')+';width:'+cell.css('width')+';height:'+cell.css('height')+';"></div>')
                    .find('div').toggle('explode', {pieces: 4 }, 500).parent()

                    .html(onlineGame.cell.points)
                    .addClass(class_cell+' last').fadeIn(300);

            }

            if(onlineGame.extra) {
                var equal = $('.ngm-bk .msg.equal');
                equal.fadeIn(200);
                window.setTimeout(function(){
                    equal.fadeOut(200);
                }, 2000);
            }

            if(onlineGame.current)
                if(onlineGame.current!=playerId)
                {
                    $('.ngm-bk .ngm-gm .tm').css('text-align','right');
                    $('.gm-pr.r').addClass('move');
                    $('.gm-pr.l').removeClass('move');
                }
                else
                {
                    $('.ngm-bk .ngm-gm .tm').css('text-align','left');
                    $('.gm-pr.l').addClass('move');
                    $('.gm-pr.r').removeClass('move');
                }

            if(onlineGame.players)
                $.each(onlineGame.players, function( index, value ) {
                    var class_player=value.pid==playerId?'l':'r';
                    $('.gm-pr.'+class_player+' .pr-cl b').html(value.moves).hide().fadeIn(200);
                    $('.gm-pr.'+class_player+' .pr-pt b').html(value.points).hide().fadeIn(200);
                });


            if(!onlineGame.winner)
                updateTimeOut(onlineGame.timeout);

            if(onlineGame.winner){
                $('.ngm-bk .tm').countdown('pause');
                $('.ngm-bk .msg').hide();

                $('.gm-pr').removeClass('move');
                $('.ngm-bk .ngm-gm .gm-pr .pr-surr').hide();

                setTimeout(function(){
                    $('.msg.winner').fadeIn(200);
                    class_player=onlineGame.winner==playerId?'l':'r';

                    $('.gm-pr .pr-cl').css('opacity',0);
                    $('.gm-pr .pr-pr').hide();

                    $('.gm-pr.'+class_player+' .pr-cl').css('opacity','100').html("<b>"+
                    (onlineGame.currency=='MONEY'?getCurrency(onlineGame.price,1):onlineGame.price )+
                    "</b><span>"+
                    (onlineGame.currency=='MONEY'?getCurrency():'баллов')+"<br>выиграно</span>");

                    $('.gm-pr.'+class_player).addClass('winner');
                }, 1200);

                $('.ngm-bk .ngm-gm .gm-mx .msg.winner .ch-ot').show();
                $('.ngm-bk .ngm-gm .gm-mx .msg.winner .re').show();
                $('.ngm-bk .ot-exit').hide();
            }
            break;

        case 'quit':
            if(onlineGame.quit!=playerId){
                $('.ngm-bk .re').hide();
                $('.ngm-bk .ot-exit').html('Соперник вышел').show();
            }
            appId=0;
            break;


        case 'error':
            errorGame();
            break;


    }
}

// Mines
    function appMinesCallback()
    {

        switch (onlineGame.action) {

            case 'reply':
                $('.re').hide();
                $('.ot-exit').html('Ожидаем соперника').show();
                break;

            case 'stack':

                break;

            case 'start':
                hideAllGames();
                runGame();

                $('.gm-pr .pr-cl').css('opacity','100').show().html("<b>0</b><span>попыток<br>осталось</span>");
                $('.gm-pr .pr-pt').css('opacity','100').show().html("<b>0</b><span>успешных<br>ходов</span>");

                if(!onlineGame.winner)
                    updateTimeOut(onlineGame.timeout);

                $('.ngm-rls').fadeOut(200);
                $('.ngm-bk .ngm-gm .gm-mx ul.mx > li').html('').removeClass();

                price=onlineGame.appMode.split('-');
                $('.gm-pr .pr-pr').show().html("<b>"+
               (price[0]=='MONEY'?getCurrency(price[1],1):price[1])+
                "</b><span>"+
                (price[0]=='MONEY'?getCurrency():'баллов')+"<br>ставка</span>");

                $.each(onlineGame.players, function( index, value ) {
                    var class_player=value.pid==playerId?'l':'r';
                    $('.gm-pr.'+class_player+' .pr-cl b').html(value.moves);
                    $('.gm-pr.'+class_player+' .pr-pt b').html(value.points);

                    if(value.avatar)
                        value.avatar = "url('../filestorage/avatars/"+Math.ceil(parseInt(value.pid)/100)+"/"+value.avatar+"')";
                    else
                        value.avatar = "url('../tpl/img/default.jpg')";

                    $('.gm-pr.'+class_player+' .pr-ph-bk .pr-ph').css('background-image',value.avatar);

                    if(value.pid!=playerId && value.name){
                        $('.gm-pr.r .pr-nm').html(value.name);
                    }
                });

                if(onlineGame.current!=playerId)
                {
                    $('.ngm-bk .ngm-gm .tm').css('text-align','right');
                    $('.gm-pr.r').addClass('move');
                    $('.gm-pr.l').removeClass('move');
                }
                else
                {
                    $('.ngm-bk .ngm-gm .tm').css('text-align','left');
                    $('.gm-pr.l').addClass('move');
                    $('.gm-pr.r').removeClass('move');
                }

                $.each(onlineGame.field, function( x, cells ) {
                    $.each(cells, function( y, cell) {
                        if(cell.player==playerId) {
                            var class_cell='m';
                        }
                        else
                        {
                            var class_cell='o';
                        }

                        $('.ngm-bk .ngm-gm .gm-mx ul.mx li.'+class_cell+'.last').
                            removeClass('last');

                        $('.ngm-bk .ngm-gm .gm-mx ul.mx li[data-cell="'+cell.coord+'"]').
                            html(cell.points).
                            addClass((is_numeric(class_cell)?'s':class_cell)+' last').fadeIn(100);
                    });
                });
                break;

            case 'move':

                if(onlineGame.cell)
                {
                    if(onlineGame.cell.player==playerId)
                        var class_cell='m';
                    else if(onlineGame.cell.player)
                        var class_cell='o';

                    playAudio([appName, 'Move-'+class_cell+'-1']);

                    $('.ngm-bk .ngm-gm .gm-mx ul.mx li.'+class_cell+'.last').
                        removeClass('last');

                    $('.ngm-bk .ngm-gm .gm-mx ul.mx li[data-cell="'+onlineGame.cell.coord+'"]')
                        .addClass(class_cell+' last')
                        .html('<div style="background:'+$('.ngm-bk .ngm-gm .gm-mx ul.mx li[data-cell="'+onlineGame.cell.coord+'"]').css('background')+';width:100%;height:100%;">'+
                        (is_numeric(onlineGame.cell.mine)?onlineGame.cell.mine:onlineGame.cell.mine=='m'?'<img src="tpl/img/games/bomb.png">':'')+'</div>')
                        /*.find('div').toggle('explode', {pieces: 4 }, 500).parent()*/
                        //.html(onlineGame.cell.mine)
                        .fadeIn(300);

                }

                if(onlineGame.field) {
                    $.each(onlineGame.field, function (x, cells) {
                        $.each(cells, function (y, cell) {
                            class_cell = (cell.player == playerId ? 'm' : 'o');

                            $('.ngm-bk .ngm-gm .gm-mx ul.mx li[data-cell="' + cell.coord + '"]')
                                .addClass(class_cell)
                                .html('<div style="background:' + $('.ngm-bk .ngm-gm .gm-mx ul.mx li[data-cell="' + cell.coord + '"]').css('background') + ';width:100%;height:100%;">' +
                                (is_numeric(cell.mine) ? cell.mine : cell.mine == 'm' ? '<img src="tpl/img/games/bomb.png">' : '') + '</div>')
                                .fadeIn(300);

                        });
                    });
                }

                if(onlineGame.extra) {
                    var equal = $('.ngm-bk .msg.equal');
                    equal.fadeIn(200);
                    window.setTimeout(function(){
                        equal.fadeOut(200);
                    }, 2000);
                }

                if(onlineGame.current)
                    if(onlineGame.current!=playerId)
                    {
                        $('.ngm-bk .ngm-gm .tm').css('text-align','right');
                        $('.gm-pr.r').addClass('move');
                        $('.gm-pr.l').removeClass('move');
                    }
                    else
                    {
                        $('.ngm-bk .ngm-gm .tm').css('text-align','left');
                        $('.gm-pr.l').addClass('move');
                        $('.gm-pr.r').removeClass('move');
                    }

                if(onlineGame.players)
                    $.each(onlineGame.players, function( index, value ) {
                        var class_player=value.pid==playerId?'l':'r';
                        $('.gm-pr.'+class_player+' .pr-cl b').html(value.moves).hide().fadeIn(200);
                        $('.gm-pr.'+class_player+' .pr-pt b').html(value.points).hide().fadeIn(200);
                    });


                if(!onlineGame.winner)
                    updateTimeOut(onlineGame.timeout);

                if(onlineGame.winner){
                    $('.ngm-bk .tm').countdown('pause');
                    $('.ngm-bk .msg').hide();

                    $('.gm-pr').removeClass('move');
                    $('.ngm-bk .ngm-gm .gm-pr .pr-surr').hide();

                    setTimeout(function(){
                        $('.msg.winner').fadeIn(200);
                        class_player=onlineGame.winner==playerId?'l':'r';

                        $('.gm-pr .pr-cl').css('opacity',0);
                        $('.gm-pr .pr-pr').hide();

                        $('.gm-pr.'+class_player+' .pr-cl').css('opacity','100').html("<b>"+
                        (onlineGame.currency=='MONEY'?getCurrency(onlineGame.price,1):onlineGame.price )+
                        "</b><span>"+
                        (onlineGame.currency=='MONEY'?getCurrency():'баллов')+"<br>выиграно</span>");

                        $('.gm-pr.'+class_player).addClass('winner');
                    }, 1200);

                    $('.ngm-bk .ngm-gm .gm-mx .msg.winner .ch-ot').show();
                    $('.ngm-bk .ngm-gm .gm-mx .msg.winner .re').show();
                    $('.ngm-bk .ot-exit').hide();
                }
                break;

            case 'quit':
                if(onlineGame.quit!=playerId){
                    $('.ngm-bk .re').hide();
                    $('.ngm-bk .ot-exit').html('Соперник вышел').show();
                }
                appId=0;
                break;

            case 'error':
                errorGame();
                break;
        }
    }

    function errorGame(){

        // $("#report-popup").show().find(".txt").text(getText(onlineGame.error)).fadeIn(200);
        // $("#report-popup").show().fadeIn(200);
        if(onlineGame.appId==0) {
            $('.ngm-bk .tm').countdown('pause');
            appId = onlineGame.appId;
            $('.ngm-bk .prc-but-cover').hide();
            $('.ngm-rls').fadeIn(200);
        }
    }

    function endGame(){

        $('.ngm-bk .tm').countdown('pause');
        $('.ngm-bk .msg').hide();

        $('.gm-pr').removeClass('move');
        $('.ngm-bk .ngm-gm .gm-pr .pr-surr').hide();

        $('.ngm-bk .ngm-gm .gm-mx ul.mx li.w div').effect('pulsate',{times:10});

        setTimeout(function(){
            $('.msg.winner').fadeIn(200);
            class_player=onlineGame.winner==playerId?'l':'r';

            $('.gm-pr .pr-cl').css('opacity',0);
            $('.gm-pr .pr-pr').hide();

            $('.gm-pr.'+class_player+' .pr-cl').css('opacity','100').show().html("<b>"+
            (onlineGame.currency=='MONEY'?getCurrency(onlineGame.price,1):onlineGame.price )+
            "</b><span>"+
            (onlineGame.currency=='MONEY'?getCurrency():'баллов')+"<br>выиграно</span>");

            $('.gm-pr.'+class_player).addClass('winner');
        }, 3600);

        $('.ngm-bk .ngm-gm .gm-mx .msg.winner .ch-ot').show();
        $('.ngm-bk .ngm-gm .gm-mx .msg.winner .re').show();
        $('.ngm-bk .ot-exit').hide();
    }
    
    function runGame(){
        $('.ngm-bk .ngm-gm').removeClass().addClass('ngm-gm').addClass(appName);
        $('.ngm-rls .gm-if-bk .l').text($('.ch-gm-tbl .ngm-bt[data-game="'+appName+'"]').parent().find('.l').text());
        $('.ngm-rls .rls-txt-bk').html($('#newgame-rules').find('div[data-game="'+appName+'"]').html());
        $('.ngm-bk .msg').hide();
        $('.ch-bk').fadeOut(200);
        $('.ngm-bk').fadeIn(200);
        $('.ngm-bk .ngm-gm .gm-mx .gm-fld').html($('#newgame-fields div[data-game="'+appName+'"]').html());
        window.setTimeout(function(){
            $("html, body").animate({scrollTop: $('.chance').offset().top-60}, 500, 'easeInOutQuint');
        }, 300);
        $('.ngm-bk .rls-r-ts').hide();
        $('.ngm-rls').hide();
        $('.ngm-bk .rls-r-t').show();
        $('.gm-pr').removeClass('winner');
        $('.ngm-bk .gm-pr.r .pr-nm').html('');
        $('.ngm-bk .pr-cl').html('').hide();
        $('.ngm-bk .pr-pt').html('').hide();
        $('.ngm-bk .ngm-gm .gm-mx ul.mx > li').html('').removeClass();
    }

    function updateTimeOut(time, format) {
        format = format || '{mnn}<span>:</span>{snn}';
        if(time) {
            if (time < 1) {
                onTimeOut();
            } else {
                $(".ngm-bk .tm").countdown({
                    until: (time),
                    layout: format
                });
                $(".ngm-bk .tm:eq(0)").countdown('option', {onExpiry: onTimeOut});
                $(".ngm-bk .tm").countdown('resume');
                $(".ngm-bk .tm").countdown('option', {until: (time)});
            }
        }
    }

    function onTimeOut() {
        $('.ngm-bk .ngm-gm .gm-mx .mx .players .gm-pr .pr-ph-bk .circle-timer').remove()
        var path='app/'+appName+'/'+appId;
        var data={'action':'timeout'};
        WebSocketAjaxClient(path,data);
    }

});