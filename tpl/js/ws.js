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

                        if(data.res.appName)
                            appName=data.res.appName;

                        if(data.res.appMode)
                            appMode=data.res.appMode;

                        if(data.res.appId)
                            appId=data.res.appId;

                        playAudio([appName, data.res.action]);

                        eval(data.path.replace('\\', '') + 'Callback')(data);
                        //window[data.path.replace('\\', '') + 'Callback'](data);
                    }
                };

        }

// try start websocket
WebSocketAjaxClient();

    if(!$.cookie("audio")) {
        $('.sbk-tl-bk .b-cntrl-block').parent().find('.glyphicon').removeClass('glyphicon-volume-up').addClass('glyphicon-volume-off');
    }

function WebSocketStatus(action, data) {
    $("#wsStatus").html(action+': </b>'+data+'</br>'+$("#wsStatus").html());
}
/****************************************************
 *      WebSocketAjaxClient CallBack's              *
 ****************************************************/



function stackCallback(receiveData) {
    $('.ngm-bk .rls-r-ts').show();
    $('.ngm-bk .rls-r-t').hide();
    $('.ngm-bk .prc-but-cover').show();
}

// chat
function updateCallback(receiveData)
{
    if(receiveData.res.points)
        updatePoints(receiveData.res.points);
    if(receiveData.res.money)
        updateMoney(receiveData.res.money);
    if(receiveData.res.top){
        $(".ngm-bk .ngm-rls-bk .rls-l .rls-bt-bk .r .online span").text(receiveData.res.online);
        $(".ngm-bk .ngm-rls-bk .rls-l .rls-bt-bk .r .all span").text(receiveData.res.all);
        $(".ngm-bk .ngm-rls-bk .rls-r .rls-r-t").html('ВЫ<b>:</b> '+
        (receiveData.res.count>0?Math.ceil((parseInt(receiveData.res.win))+(parseInt(receiveData.res.count))):"0")+' <b>|</b> '+receiveData.res.count+' <b>|</b> '+
        (receiveData.res.count>0?Math.ceil((parseInt(receiveData.res.win))/25):"0")+'');


        html='';
        $.each(receiveData.res.top, function( index, value ) {
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
        $('.rls-r .rls-r-prs').html(html);
    }
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

    $.each( "play pause".split(" "), function( i, name ) {

        // Handle event binding
        $.fn[ name ] = function( data, fn ) {
            if ( fn == null ) {
                fn = data;
                data = null;
            }

            return arguments.length > 0 ?
                this.bind( name, data, fn ) :
                this.trigger( name );
        };

        if ( $.attrFn ) {
            $.attrFn[ name ] = true;
        }
    });

// game
function appSeaBattleCallback(receiveData)
{

     switch (receiveData.res.action) {
     case 'ready':
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

                 price=receiveData.res.appMode.split('-');
                 $('.gm-pr .pr-pr').show().html("<b>"+
                 (price[0]=='MONEY'?parseFloat(price[1]*coefficient).toFixed(2):price[1])+
                 "</b><span>"+
                 (price[0]=='MONEY'?playerCurrency:'баллов')+"<br>ставка</span>");

                 $.each(receiveData.res.players, function( index, value ) {
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

                 appId=receiveData.res.appId;
                 updateTimeOut(receiveData.res.timeout);
             }

             $('.sb-wait').show();
             $('.sb-ready.but, .sb-random.but').hide();
             $.each(receiveData.res.fields, function( index, field ) {
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
                 window.game_ships=receiveData.res.ships;
                 genFieldSeaBattle();
                 $('.ngm-bk .gm-fld .place').show();
                 $('.ngm-bk .gm-fld .mx.SeaBattle.o').hide();
                 $('.sb-wait').hide();
                 $('.sb-ready.but, .sb-random.but').show();
             }


             $('.gm-pr .pr-cl').show().html("<span>корабли</span><b></b>");
             $('.gm-pr.r .pr-cl').hide();

             updateTimeOut(receiveData.res.timeout);

             price=receiveData.res.appMode.split('-');
             $('.gm-pr .pr-pr').show().html("<b>"+
             (price[0]=='MONEY'?parseFloat(price[1]*coefficient).toFixed(2):price[1])+
             "</b><span>"+
             (price[0]=='MONEY'?playerCurrency:'баллов')+"<br>ставка</span>");

             $.each(receiveData.res.players, function( index, value ) {
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

             updateTimeOut(receiveData.res.timeout);
             hideAllGames();
             runGame();
             $('.ngm-bk .gm-fld .place').hide();
             $('.ngm-bk .gm-fld .mx.SeaBattle.o').show();
             $('.gm-pr .pr-cl').show().html("<span>корабли</span><b></b>");
             $('ul.mx.SeaBattle.m div').remove();

             price=receiveData.res.appMode.split('-');
             $('.gm-pr .pr-pr').show().html("<b>"+
             (price[0]=='MONEY'?parseFloat(price[1]*coefficient).toFixed(2):price[1])+
             "</b><span>"+
             (price[0]=='MONEY'?playerCurrency:'баллов')+"<br>ставка</span>");

         $.each(receiveData.res.players, function( index, value ) {
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

         if(receiveData.res.current!=playerId)
         {
             $('ul.mx.SeaBattle.o').css('opacity',0.5);
             $('ul.mx.SeaBattle.m').css('opacity',1);
             $('.ngm-bk .ngm-gm .tm').css('text-align','right');
             $('.gm-pr.r').addClass('move');
             $('.gm-pr.l').removeClass('move');
         }
         else
         {
             $('ul.mx.SeaBattle.o').css('opacity',1);
             $('ul.mx.SeaBattle.m').css('opacity',0.5);
             $('.ngm-bk .ngm-gm .tm').css('text-align','left');
             $('.gm-pr.l').addClass('move');
             $('.gm-pr.r').removeClass('move');
         }

         $.each(receiveData.res.fields, function( index, field ) {
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
         break;

         case 'move':

             if(receiveData.res.cell)
             {
                 class_cell = (receiveData.res.cell.coord.split("x")[2]== playerId ? 'm' : 'o');

                 if(move=receiveData.res.cell.class=='e'?1:receiveData.res.cell.class=='d'?2:receiveData.res.cell.class=='k'?3:null)
                    playAudio([appName, 'Move-' + class_cell + '-' + move]);

                 $('.ngm-bk .ngm-gm .gm-mx ul.mx li.'+class_cell+'.last').
                     removeClass('last');

                 $('.ngm-bk .ngm-gm .gm-mx ul.mx li[data-cell="'+receiveData.res.cell.coord+'"]')
                     //html(receiveData.res.cell.points).
                     .addClass(receiveData.res.cell.class)
                     .html('<div class="'+receiveData.res.cell.class+'" style="background:'+$('.ngm-bk .ngm-gm .gm-mx ul.mx li[data-cell="'+receiveData.res.cell.coord+'"]').css('background')+';width:19px;height:19px;"></div>')
                     .find('div')
                     .effect('explode', {pieces: 4 }, 500)
                     //.effect('bounce')
                     .parent().addClass(class_cell+' last')
                     .fadeIn(300).html(receiveData.res.cell.class=='d'?"<img src='tpl/img/games/damage.png'>":'');

             }

             if(receiveData.res.fields) {
                 $.each(receiveData.res.fields, function (index, field) {
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

             if(receiveData.res.extra) {
                 var equal = $('.ngm-bk .msg.equal');
                 equal.fadeIn(200);
                 window.setTimeout(function(){
                     equal.fadeOut(200);
                 }, 2000);
             }


             if(receiveData.res.current)
                 if(receiveData.res.current!=playerId)
                 {
                     $('ul.mx.SeaBattle.o').css('opacity',0.5);
                     $('ul.mx.SeaBattle.m').css('opacity',1);
                     $('.ngm-bk .ngm-gm .tm').css('text-align','right');
                     $('.gm-pr.r').addClass('move');
                     $('.gm-pr.l').removeClass('move');
                 }
                 else
                 {
                     $('ul.mx.SeaBattle.o').css('opacity',1);
                     $('ul.mx.SeaBattle.m').css('opacity',0.5);
                     $('.ngm-bk .ngm-gm .tm').css('text-align','left');
                     $('.gm-pr.l').addClass('move');
                     $('.gm-pr.r').removeClass('move');
                 }

             if(receiveData.res.players)
                 $.each(receiveData.res.players, function( index, value ) {
                     var class_player=value.pid==playerId?'l':'r';
                     var class_field=value.pid==playerId?'m':'o';
                     // $('.gm-pr.'+class_player+' .pr-cl b').html(value.moves);
                     html='';
                     $.each(value.ships, function( shp, cnt ) {html+='<div class="s '+(cnt?'':'e ')+class_field+'" style="width:'+shp*20+'px"><b>'+cnt+'</b></div>'});
                     $('.gm-pr.'+class_player+' .pr-cl b').html(html);$('.gm-pr.'+class_player+' .pr-cl b').html(html);
                     $('.gm-pr.'+class_player+' .pr-pt b').html(value.points).hide().fadeIn(200);
                 });

             if(!receiveData.res.winner) {
                 updateTimeOut(receiveData.res.timeout);
             }


             if(receiveData.res.winner){
                 $('.ngm-bk .tm').countdown('pause');
                 $('.ngm-bk .msg').hide();

                 $('.gm-pr').removeClass('move');
                 $('.ngm-bk .ngm-gm .gm-pr .pr-surr').hide();

                 $('.ngm-bk .ngm-gm .gm-mx ul.mx.SeaBattle.o li.s:not(.d,.k)').effect('pulsate',{times:10});

                 setTimeout(function(){
                     $('.msg.winner').fadeIn(200);
                     class_player=receiveData.res.winner==playerId?'l':'r';

                     $('.gm-pr .pr-cl, .gm-pr .pr-pr').hide();
                     $('.gm-pr.'+class_player+' .pr-cl').show().html("<b>"+
                     (receiveData.res.currency=='MONEY'?parseFloat(receiveData.res.price*coefficient).toFixed(2):receiveData.res.price )+
                     "</b><span>"+
                     (receiveData.res.currency=='MONEY'?playerCurrency:'баллов')+"<br>выиграно</span>");

                     $('.gm-pr.'+class_player).addClass('winner');
                 }, (receiveData.res.winner==playerId?1200:3600));

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
             $("#report-popup").show().find(".txt").text(getText(receiveData.res.error)).fadeIn(200);
             $("#report-popup").show().fadeIn(200);
             if(receiveData.res.appId==0) {
                 $('.ngm-bk .tm').countdown('pause');
                 appId = receiveData.res.appId;
                 $('.ngm-bk .prc-but-cover').hide();
                 $('.ngm-rls').fadeIn(200);
             }
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

// game new
$(document).on('click', '.ngm-bk .ngm-go', function(e){

    if(appMode = $('.ngm-bk .prc-bl .prc-but-bk .prc-sel').find('.active').attr('data-price')) {
        price = appMode.split('-');
        if ((price[0] == 'POINT' && playerPoints < parseInt(price[1])) || (price[0] == 'MONEY' && playerMoney < parseFloat(price[1]).toFixed(2) * coefficient)) {

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

// выход
$(document).on('click', '.ngm-bk .ngm-gm .gm-mx .msg.winner .exit', function(e){
    //$('.ngm-bk .ngm-go').show();//.prev().hide();
    $('.ngm-bk .rls-r-ts').hide();
    $('.ngm-bk .rls-r-t').show();
    $('.ngm-bk .prc-but-cover').hide();
    $('.ngm-rls').fadeIn(200);

    var path='app/'+appName+'/'+appId;
    var data={'action':'quit'};
    WebSocketAjaxClient(path,data);
    appId=0;
    WebSocketAjaxClient('update/'+appName);
});

// отмена, назад
$(document).on('click', '.ngm-bk .bk-bt-rl, .ngm-bk .ngm-rls-bk .rls-r .ngm-cncl', function(e){
    //$('.ngm-bk .ngm-go').show();//.prev().hide();
    $('.ngm-bk .rls-r-ts').hide();
    $('.ngm-bk .rls-r-t').show();
    $('.ngm-bk .prc-but-cover').hide();
    appId=0;
    var path='app/'+appName+'/'+appId;
    var data={'action':'cancel'};
    WebSocketAjaxClient(path,data);
});

// switch блоков ставок
$(document).on('click', '.ngm-bk .ngm-rls-bk .prc-l .prc-but-bk .prc-bt', function(e){

    $('.ngm-bk .ngm-rls-bk .prc-l .prc-but-bk .prc-sel .prc-vl').removeClass('active');
    $('.ngm-bk .ngm-rls-bk .prc-l .prc-but-bk .prc-sel').removeClass('active');
    $('.ngm-bk .ngm-rls-bk .prc-l .prc-but-bk .prc-sel').hide();
    $('.ngm-bk .ngm-rls-bk .prc-l .prc-but-bk .prc-bt.hidden').removeClass('hidden').show();
    $(this).addClass('hidden').hide().next().show().children(':not([style$="display: none;"])').last().addClass('active');
    $('.ngm-bk .ngm-go').removeClass('button-disabled').attr('disabled',false);
/*
    $('.prc-sel').each(function() {
        if( $(this).find('.prc-vl').length){
            var mode = $(this);
            $(this).find('.prc-vl').each(function() {
                if ($.inArray($(this).data('price'), appModes[appName])>0)
                {
                    $(this).show();
                }
            });
            if(!(mode.find('.prc-vl:not([style$="display: none;"])').length))
                mode.prev().hide();
        }
    });
*/


});

// выбор ставки
$(document).on('click', '.ngm-bk .ngm-rls-bk .prc-l .prc-but-bk .prc-sel .prc-vl', function(e){
    $('.ngm-bk .ngm-rls-bk .prc-l .prc-but-bk .prc-sel .prc-vl').removeClass('active');
    $(this).addClass('active');
});


// switch rules block
$(document).on('click', '.ngm-bk .bk-bt-rl', function(e){
    $('.ngm-bk .prc-l').hide();
    $('.ngm-bk .rls-r-ts').hide();
    $('.ngm-bk .rls-r-t').show();
    $('.ngm-bk .rls-l').fadeIn(200);
    WebSocketAjaxClient('update/'+appName);
});


// open price block
$(document).on('click', '.ngm-bk .ngm-price', function(e){
    $('.ngm-bk .rls-l').hide();
    $('.ngm-bk .ngm-go').addClass('button-disabled').attr('disabled','disabled');
    $('.ngm-bk .ngm-rls-bk .prc-l .prc-but-bk .prc-sel > div').removeClass('active');
    $('.ngm-bk .ngm-rls-bk .prc-l .prc-but-bk').find('active').removeClass('active');
    $('.ngm-bk .ngm-rls-bk .prc-l .prc-but-bk .prc-sel').hide();
    $('.ngm-bk .ngm-rls-bk .prc-l .prc-but-bk .prc-sel .prc-vl').hide();
    $('.ngm-bk .ngm-rls-bk .prc-l .prc-but-bk .prc-bt').removeClass('hidden').show();

    $('.prc-sel .prc-vl').remove();

    if(appModes[appName])
    $.each(appModes[appName], function (c, m) {
        $.each(m, function (i,v) {
            if(v>0)
                $('<div class="prc-vl" data-price="'+c+'-'+v+'">'+v+'</div>').insertAfter($('.prc-sel[data-currency="'+c+'"] div').first());
        });
    });

    $('.prc-sel').each(function() {
        console.log($(this).data('currency'));
        if(!$(this).find('.prc-vl').length)
            if($(this).data('currency')!='FREE' || !appModes[appName] || !appModes[appName]['POINT'] || $.inArray(0, appModes[appName]['POINT'])<0)
                $(this).prev().hide();
    });

    $('.ngm-bk .prc-l').fadeIn(200);
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

// повторить
    $(document).on('click', '.ngm-bk .ngm-gm .gm-mx .msg.winner .re', function(e){
        var path='app/'+appName+'/'+appId;
        var data={'action':'replay'};
        WebSocketAjaxClient(path,data);
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
        $(this).parent().find('.glyphicon').removeClass('glyphicon-volume-up').addClass('glyphicon-volume-off');
    } else {
        $(this).parent().find('.glyphicon').addClass('glyphicon-volume-up').removeClass('glyphicon-volume-off');
        $.cookie("audio", 1, { expires : 100 });
    }
    });

// другой соперник
$(document).on('click', '.ngm-bk .ngm-gm .gm-mx .msg.winner .ch-ot', function(e){

    if(appId)
    {
        var path='app/'+appName+'/'+appId;
        var data={'action':'quit'};
        WebSocketAjaxClient(path,data);
        appId=0;
    }

    $('.ngm-bk .ngm-gm .gm-mx .msg.winner .re').hide();
    $('.ngm-bk .ngm-gm .gm-mx .msg.winner .ch-ot').hide();
    $('.ot-exit').html('Ожидаем соперника').show();

    var path='app/'+appName+'/'+appId;
    var data={'action':'start', 'mode':appMode};
    WebSocketAjaxClient(path,data);
});

// делаем ход
$(document).on('click', '.ngm-gm .gm-mx ul.mx li', function(e){

    if(parseInt($('.gm-pr.l .pr-cl b').html())<=0){
    //    $("#report-popup").find(".txt").text(getText('ENOUGH_MOVES'));
    //    $("#report-popup").show().fadeIn(200);
    }
    else if(!($('.gm-pr.l').hasClass('move')))
    {
    //    $("#report-popup").find(".txt").text(getText('NOT_YOUR_MOVE'));
    //    $("#report-popup").show().fadeIn(200);
    }
    else if($(this).hasClass('m') || $(this).hasClass('o') || $(this).hasClass('b'))
    {
        //    $("#report-popup").find(".txt").text(getText('CELL_IS_PLAYED'));
        //    $("#report-popup").show().fadeIn(200);
    }
    else{
        $(this).addClass('b');
        var path='app/'+appName+'/'+appId;
        var data={'action':'move','cell':$(this).data('cell')}
        WebSocketAjaxClient(path,data);
    }
});

<!-- NEW GAMES PREVIEW -->
$('.ch-gm-tbl .ngm-bt').click(function(){
    hideAllGames();
    appName=$(this).data('game');
    $('.ngm-rls .gm-if-bk .l').text($(this).parent().find('.l').text());
    $('.ngm-rls .rls-txt-bk').html($('#newgame-rules').find('div[data-game="'+appName+'"]').html());

    $('.ngm-rls .rls-r .rls-r-prs').html('<div class="preloader"></div>').find('div').show();
    WebSocketAjaxClient('update/'+appName);
    $('.ch-bk').fadeOut(200);
    window.setTimeout(function(){
        $('.ngm-bk').fadeIn(200);
    }, 200);
});

<!-- NEW GAME BACK -->
$('.ngm-bk .bk-bt').on('click', function() {
    $('.ngm-bk').fadeOut(200);
    window.setTimeout(function(){
        $('.ch-bk').fadeIn(200);
    }, 200);
});


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
    function appFiveLineCallback(receiveData)
    {

        playAudio([appName, receiveData.res.action]);

        switch (receiveData.res.action) {

            case 'ready':
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

                appTimeOut=receiveData.res.timeout;


                $('.ngm-rls').fadeOut(200);
                $('.ngm-bk .ngm-gm .gm-mx ul.mx > li').html('').removeClass();

                price=receiveData.res.appMode.split('-');
                $('.gm-pr .pr-pr').show().html("<b>"+
                (price[0]=='MONEY'?parseFloat(price[1]*coefficient).toFixed(2):price[1])+
                "</b><span>"+
                (price[0]=='MONEY'?playerCurrency:'баллов')+"<br>ставка</span>");

                $.each(receiveData.res.players, function( index, value ) {
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

                if(receiveData.res.current!=playerId)
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

                if(appTimeOut<1)
                {
                    onTimeOut();
                } else{
                    $(".ngm-bk .tm").countdown({
                        until: (appTimeOut),
                        layout: '{mnn}<span>:</span>{snn}',
                        onExpiry: onTimeOut
                    });
                    $(".ngm-bk .tm").countdown('resume');
                    $(".ngm-bk .tm").countdown('option', {until: (appTimeOut)});
                }

                $.each(receiveData.res.field, function( x, cells ) {
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
                break;

            case 'move':

                if(receiveData.res.cell)
                {
                    if(receiveData.res.cell.player==playerId)
                        var class_cell='m';
                    else
                        var class_cell='o';

                    playAudio([appName, 'Move-'+class_cell+'-1']);

                    $('.ngm-bk .ngm-gm .gm-mx ul.mx li.'+class_cell+'.last').
                        removeClass('last');

                    cell=$('.ngm-bk .ngm-gm .gm-mx ul.mx li[data-cell="'+receiveData.res.cell.coord+'"]');


                    cell
                        .html('<div style="display:none;"></div>')
                        .find('div').fadeIn(200);
                        //.find('div').toggle('clip').parent()
                        cell.addClass(class_cell+' last').fadeIn(300);

                        //.html(receiveData.res.cell.points)
                        //.css('background-color',cell.css('background-color')).addClass(class_cell+' last').flip({direction:'tb',color:cell.css('background-color'),onEnd:function(){cell.removeAttr('style')}});

                }

                if(receiveData.res.extra) {
                    var equal = $('.ngm-bk .msg.equal');
                    window.setTimeout(function(){
                        equal.fadeIn(200);
                    }, 500);
                    window.setTimeout(function(){
                        equal.fadeOut(200);
                    }, 2500);
                }

                if(receiveData.res.current)
                    if(receiveData.res.current!=playerId)
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

                if(receiveData.res.players)
                    $.each(receiveData.res.players, function( index, value ) {
                        var class_player=value.pid==playerId?'l':'r';
                        $('.gm-pr.'+class_player+' .pr-cl b').html(value.moves).hide().fadeIn(200);
                        $('.gm-pr.'+class_player+' .pr-pt b').html(value.points).hide().fadeIn(200);
                    });

                if(!receiveData.res.winner) {
                    if (receiveData.res.timeout < 1) {
                        onTimeOut();
                    } else
                        $(".ngm-bk .tm").countdown('option', {until: (receiveData.res.timeout)});
                }

                if(receiveData.res.line) {
                    $.each(receiveData.res.line, function (x, cells) {
                        $.each(cells, function (y, cell) {
                            $('.ngm-bk .ngm-gm .gm-mx ul.mx li[data-cell="' + x + 'x' + y + '"]').
                                addClass('w').addClass('last');
                        });
                    });
                }

                if(receiveData.res.winner){
                    $('.ngm-bk .tm').countdown('pause');
                    $('.ngm-bk .msg').hide();

                    $('.gm-pr').removeClass('move');
                    $('.ngm-bk .ngm-gm .gm-pr .pr-surr').hide();

                    $('.ngm-bk .ngm-gm .gm-mx ul.mx li.w div').effect('pulsate',{times:10});

                    setTimeout(function(){
                        $('.msg.winner').fadeIn(200);
                        class_player=receiveData.res.winner==playerId?'l':'r';

                        $('.gm-pr .pr-cl').css('opacity',0);
                        $('.gm-pr .pr-pr').hide();

                        $('.gm-pr.'+class_player+' .pr-cl').css('opacity','100').html("<b>"+
                        (receiveData.res.currency=='MONEY'?parseFloat(receiveData.res.price*coefficient).toFixed(2):receiveData.res.price )+
                        "</b><span>"+
                        (receiveData.res.currency=='MONEY'?playerCurrency:'баллов')+"<br>выиграно</span>");

                        $('.gm-pr.'+class_player).addClass('winner');
                    }, 3600);

                    $('.ngm-bk .ngm-gm .gm-mx .msg.winner .ch-ot').show();
                    $('.ngm-bk .ngm-gm .gm-mx .msg.winner .re').show();
                    $('.ngm-bk .ot-exit').hide();
                }
                break;

            case 'quit':
                if(receiveData.res.quit!=playerId){
                    $('.ngm-bk .re').hide();
                    $('.ngm-bk .ot-exit').html('Соперник вышел').show();
                }
                appId=0;
                break;


            case 'error':
                $("#report-popup").show().find(".txt").text(getText(receiveData.res.error)).fadeIn(200);
                $("#report-popup").show().fadeIn(200);
                if(receiveData.res.appId==0) {
                    $('.ngm-bk .tm').countdown('pause');
                    appId = receiveData.res.appId;
                    $('.ngm-bk .prc-but-cover').hide();
                    $('.ngm-rls').fadeIn(200);
                }
                break;


        }
    }

// WhoMore
function appWhoMoreCallback(receiveData)
{

    playAudio([appName, receiveData.res.action]);

    switch (receiveData.res.action) {

        case 'ready':
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

            appTimeOut=receiveData.res.timeout;


            $('.ngm-rls').fadeOut(200);
            $('.ngm-bk .ngm-gm .gm-mx ul.mx > li').html('').removeClass();

            price=receiveData.res.appMode.split('-');
            $('.gm-pr .pr-pr').show().html("<b>"+
            (price[0]=='MONEY'?parseFloat(price[1]*coefficient).toFixed(2):price[1])+
            "</b><span>"+
            (price[0]=='MONEY'?playerCurrency:'баллов')+"<br>ставка</span>");

            $.each(receiveData.res.players, function( index, value ) {
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

            if(receiveData.res.current!=playerId)
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

            if(appTimeOut<1)
            {
                onTimeOut();
            } else{
                $(".ngm-bk .tm").countdown({
                    until: (appTimeOut),
                    layout: '{mnn}<span>:</span>{snn}',
                    onExpiry: onTimeOut
                });
                $(".ngm-bk .tm").countdown('resume');
                $(".ngm-bk .tm").countdown('option', {until: (appTimeOut)});
            }

            $.each(receiveData.res.field, function( x, cells ) {
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

            if(receiveData.res.cell)
            {
                if(receiveData.res.cell.player==playerId)
                    var class_cell='m';
                else
                    var class_cell='o';

                playAudio([appName, 'Move-'+class_cell+'-1']);

                $('.ngm-bk .ngm-gm .gm-mx ul.mx li.'+class_cell+'.last').
                    removeClass('last');

                $('.ngm-bk .ngm-gm .gm-mx ul.mx li[data-cell="'+receiveData.res.cell.coord+'"]')
                    .html('<div style="background:'+$('.ngm-bk .ngm-gm .gm-mx ul.mx li[data-cell="'+receiveData.res.cell.coord+'"]').css('background')+';width:60px;height:60px;"></div>')
                    .find('div').toggle('explode', {pieces: 4 }, 500).parent()

                    .html(receiveData.res.cell.points)
                    .addClass(class_cell+' last').fadeIn(300);

            }

            if(receiveData.res.extra) {
                var equal = $('.ngm-bk .msg.equal');
                equal.fadeIn(200);
                window.setTimeout(function(){
                    equal.fadeOut(200);
                }, 2000);
            }

            if(receiveData.res.current)
                if(receiveData.res.current!=playerId)
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

            if(receiveData.res.players)
                $.each(receiveData.res.players, function( index, value ) {
                    var class_player=value.pid==playerId?'l':'r';
                    $('.gm-pr.'+class_player+' .pr-cl b').html(value.moves).hide().fadeIn(200);
                    $('.gm-pr.'+class_player+' .pr-pt b').html(value.points).hide().fadeIn(200);
                });

            if(!receiveData.res.winner) {
                if (receiveData.res.timeout < 1) {
                    onTimeOut();
                } else
                    $(".ngm-bk .tm").countdown('option', {until: (receiveData.res.timeout)});
            }

            if(receiveData.res.winner){
                $('.ngm-bk .tm').countdown('pause');
                $('.ngm-bk .msg').hide();

                $('.gm-pr').removeClass('move');
                $('.ngm-bk .ngm-gm .gm-pr .pr-surr').hide();

                setTimeout(function(){
                    $('.msg.winner').fadeIn(200);
                    class_player=receiveData.res.winner==playerId?'l':'r';

                    $('.gm-pr .pr-cl').css('opacity',0);
                    $('.gm-pr .pr-pr').hide();

                    $('.gm-pr.'+class_player+' .pr-cl').css('opacity','100').html("<b>"+
                    (receiveData.res.currency=='MONEY'?parseFloat(receiveData.res.price*coefficient).toFixed(2):receiveData.res.price )+
                    "</b><span>"+
                    (receiveData.res.currency=='MONEY'?playerCurrency:'баллов')+"<br>выиграно</span>");

                    $('.gm-pr.'+class_player).addClass('winner');
                }, 1200);

                $('.ngm-bk .ngm-gm .gm-mx .msg.winner .ch-ot').show();
                $('.ngm-bk .ngm-gm .gm-mx .msg.winner .re').show();
                $('.ngm-bk .ot-exit').hide();
            }
            break;

        case 'quit':
            if(receiveData.res.quit!=playerId){
                $('.ngm-bk .re').hide();
                $('.ngm-bk .ot-exit').html('Соперник вышел').show();
            }
            appId=0;
            break;


        case 'error':
            $("#report-popup").show().find(".txt").text(getText(receiveData.res.error)).fadeIn(200);
            $("#report-popup").show().fadeIn(200);
            if(receiveData.res.appId==0) {
                $('.ngm-bk .tm').countdown('pause');
                appId = receiveData.res.appId;
                $('.ngm-bk .prc-but-cover').hide();
                $('.ngm-rls').fadeIn(200);
            }
            break;


    }
}

// Mines
    function appMinesCallback(receiveData)
    {
        playAudio([appName, receiveData.res.action]);

        switch (receiveData.res.action) {

            case 'ready':
                $('.re').hide();
                $('.ot-exit').html('Ожидаем соперника').show();
                break;

            case 'stack':

                break;

            case 'start':
                hideAllGames();
                runGame();

                $('.gm-pr .pr-cl').css('opacity','100').show().html("<b>0</b><span>жизней<br>осталось</span>");
                $('.gm-pr .pr-pt').css('opacity','100').show().html("<b>0</b><span>ячеек<br>разминировано</span>");

                appTimeOut=receiveData.res.timeout;

                $('.ngm-rls').fadeOut(200);
                $('.ngm-bk .ngm-gm .gm-mx ul.mx > li').html('').removeClass();

                price=receiveData.res.appMode.split('-');
                $('.gm-pr .pr-pr').show().html("<b>"+
                (price[0]=='MONEY'?parseFloat(price[1]*coefficient).toFixed(2):price[1])+
                "</b><span>"+
                (price[0]=='MONEY'?playerCurrency:'баллов')+"<br>ставка</span>");

                $.each(receiveData.res.players, function( index, value ) {
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

                if(receiveData.res.current!=playerId)
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

                if(appTimeOut<1)
                {
                    onTimeOut();
                } else{
                    $(".ngm-bk .tm").countdown({
                        until: (appTimeOut),
                        layout: '{mnn}<span>:</span>{snn}',
                        onExpiry: onTimeOut
                    });
                    $(".ngm-bk .tm").countdown('resume');
                    $(".ngm-bk .tm").countdown('option', {until: (appTimeOut)});
                }

                $.each(receiveData.res.field, function( x, cells ) {
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

                if(receiveData.res.cell)
                {
                    if(receiveData.res.cell.player==playerId)
                        var class_cell='m';
                    else
                        var class_cell='o';

                    playAudio([appName, 'Move-'+class_cell+'-1']);

                    $('.ngm-bk .ngm-gm .gm-mx ul.mx li.'+class_cell+'.last').
                        removeClass('last');

                    $('.ngm-bk .ngm-gm .gm-mx ul.mx li[data-cell="'+receiveData.res.cell.coord+'"]')
                        .html('<div style="background:'+$('.ngm-bk .ngm-gm .gm-mx ul.mx li[data-cell="'+receiveData.res.cell.coord+'"]').css('background')+';width:100%;height:100%;"></div>')
                        /*.find('div').toggle('explode', {pieces: 4 }, 500).parent()*/

                        .html(receiveData.res.cell.mine)
                        .addClass(class_cell+' last').fadeIn(300);

                }

                if(receiveData.res.extra) {
                    var equal = $('.ngm-bk .msg.equal');
                    equal.fadeIn(200);
                    window.setTimeout(function(){
                        equal.fadeOut(200);
                    }, 2000);
                }

                if(receiveData.res.current)
                    if(receiveData.res.current!=playerId)
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

                if(receiveData.res.players)
                    $.each(receiveData.res.players, function( index, value ) {
                        var class_player=value.pid==playerId?'l':'r';
                        $('.gm-pr.'+class_player+' .pr-cl b').html(value.moves).hide().fadeIn(200);
                        $('.gm-pr.'+class_player+' .pr-pt b').html(value.points).hide().fadeIn(200);
                    });

                if(!receiveData.res.winner) {
                    if (receiveData.res.timeout < 1) {
                        onTimeOut();
                    } else
                        $(".ngm-bk .tm").countdown('option', {until: (receiveData.res.timeout)});
                }

                if(receiveData.res.winner){
                    $('.ngm-bk .tm').countdown('pause');
                    $('.ngm-bk .msg').hide();

                    $('.gm-pr').removeClass('move');
                    $('.ngm-bk .ngm-gm .gm-pr .pr-surr').hide();

                    setTimeout(function(){
                        $('.msg.winner').fadeIn(200);
                        class_player=receiveData.res.winner==playerId?'l':'r';

                        $('.gm-pr .pr-cl').css('opacity',0);
                        $('.gm-pr .pr-pr').hide();

                        $('.gm-pr.'+class_player+' .pr-cl').css('opacity','100').html("<b>"+
                        (receiveData.res.currency=='MONEY'?parseFloat(receiveData.res.price*coefficient).toFixed(2):receiveData.res.price )+
                        "</b><span>"+
                        (receiveData.res.currency=='MONEY'?playerCurrency:'баллов')+"<br>выиграно</span>");

                        $('.gm-pr.'+class_player).addClass('winner');
                    }, 1200);

                    $('.ngm-bk .ngm-gm .gm-mx .msg.winner .ch-ot').show();
                    $('.ngm-bk .ngm-gm .gm-mx .msg.winner .re').show();
                    $('.ngm-bk .ot-exit').hide();
                }
                break;

            case 'quit':
                if(receiveData.res.quit!=playerId){
                    $('.ngm-bk .re').hide();
                    $('.ngm-bk .ot-exit').html('Соперник вышел').show();
                }
                appId=0;
                break;


            case 'error':
                $("#report-popup").show().find(".txt").text(getText(receiveData.res.error)).fadeIn(200);
                $("#report-popup").show().fadeIn(200);
                if(receiveData.res.appId==0) {
                    $('.ngm-bk .tm').countdown('pause');
                    appId = receiveData.res.appId;
                    $('.ngm-bk .prc-but-cover').hide();
                    $('.ngm-rls').fadeIn(200);
                }
                break;


        }
    }


    function runGame(){
        $('.ngm-rls .gm-if-bk .l').text($(this).parent().find('.l').text());
        $('.ngm-rls .rls-txt-bk').html($('#newgame-rules').find('div[data-game="'+appName+'"]').html());
        $('.ch-bk').fadeOut(200);
        $('.ngm-bk').fadeIn(200);
        $('.ngm-bk .ngm-gm .gm-mx .gm-fld').html($('#newgame-fields div[data-game="'+appName+'"]').html());
        window.setTimeout(function(){
            $("html, body").animate({scrollTop: $('.chance').offset().top-60}, 500, 'easeInOutQuint');
            $('.ngm-bk .msg').hide();
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

    function updateTimeOut(time) {
        if (time < 1) {
            onTimeOut();
        } else {
            $(".ngm-bk .tm").countdown({
                until: (time),
                layout: '{mnn}<span>:</span>{snn}',
                onExpiry: onTimeOut
            });
            $(".ngm-bk .tm").countdown('resume');
            $(".ngm-bk .tm").countdown('option', {until: (time)});
        }
    }

    function onTimeOut(){
        var path='app/'+appName+'/'+appId;
        var data={'action':'timeout'}
        WebSocketAjaxClient(path,data);
    }
});