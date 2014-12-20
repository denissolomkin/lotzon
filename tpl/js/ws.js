var url = 'ws://testbed.lotzon.com:8080';
var server = './ws/run';
var conn;

        function WebSocketAjaxClient(path, data, stop) {
            if(!conn || conn.readyState !== 1)
            {
                conn = new WebSocket(url);// window['MozWebSocket'] ? new MozWebSocket(url) : new WebSocket(url);
                conn.onopen = function (e) {
                    if(path){
                        conn.send(JSON.stringify({'path': path, 'data': data}));
                        WebSocketStatus('<b style="color:blue">send',path+JSON.stringify(data))
                    }
                    console.info('Socket open');
                    WebSocketStatus('<b style="color:green">socket', 'open')
                };
            }
            else
            {
                WebSocketStatus('<b style="color:blue">send',path+JSON.stringify(data))
                conn.send(JSON.stringify({'path': path, 'data': data}));
            }

                conn.onerror = function (e) {
                    WebSocketStatus('<b style="color:red">error', JSON.stringify(e))
                    console.error('There was an un-identified Web Socket error');
                    if(stop!==true) {
                        $.ajax({url: server});
                        WebSocketAjaxClient(path, data, true);
                    }
                };

                conn.onmessage = function (e) {
                    WebSocketStatus('<b style="color:purple">receive',e.data)
                    data=$.parseJSON(e.data);
                    window[data.path.replace('\\','')+'Callback'](data);
                };

        }

// try start websocket
WebSocketAjaxClient();

function WebSocketStatus(action, data) {
    $("#wsStatus").html(action+': </b>'+data+'</br>'+$("#wsStatus").html());
}
/****************************************************
 *      WebSocketAjaxClient CallBack's              *
 ****************************************************/

// chat
function updateCallback(receiveData)
{
    updatePoints(receiveData.res.points);
    updateMoney(receiveData.res.money);
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
function appNewGameCallback(receiveData)
{
console.log(receiveData.res.action);
     switch (receiveData.res.action) {


     case 'ready':
         $('.re').hide();
         $('.ot-exit').html('Ожидаем соперника').show();
         break;

     case 'start':
         $('.ngm-bk').focus();
         $('.msg.winner').hide();
         $('.gm-pr').removeClass('winner');
         $('.ngm-bk .gm-pr.r .pr-nm').html();
         $('.ngm-bk .pr-cl b').html();
         $('.ngm-bk .pr-pt b').html();

         $('.gm-pr .pr-cl').css('opacity','100').html("<b>0</b><span>ходов<br>осталось</span>");



         $('.ngm-bk .ngm-gm .gm-pr .pr-surr').show();
         $('.ngm-rls').fadeOut(200);
         $('.ngm-bk .ngm-gm .gm-mx ul.mx > li').html('').removeClass();//.addClass('m');
         appId=receiveData.res.gid;
         appTimeOut=receiveData.res.timeout;
         $.each(receiveData.res.players, function( index, value ) {
             var class_player=value.pid==playerId?'l':'r';
             $('.gm-pr.'+class_player+' .pr-cl b').html(value.moves);
             $('.gm-pr.'+class_player+' .pr-pt b').html(value.points);

             if(value.avatar){
                 value.avatar = value.avatar.lenght > 0 ? value.avatar:"url('../tpl/img/bg-chanse-game-hz.png')";
                 $('.gm-pr.'+class_player+' .pr-ph-bk .pr-ph').css('background-image',value.avatar);
             }

             if(value.pid!=playerId && value.name){
                 $('.gm-pr.r .pr-nm').html(value.name);
             }
         });

         if(receiveData.res.current!=playerId)
         {
             $('.gm-pr.r').addClass('move');
             $('.gm-pr.l').removeClass('move');
         }
         else
         {
             $('.gm-pr.l').addClass('move');
             $('.gm-pr.r').removeClass('move');
         }

         $(".ngm-bk .tm").countdown({
             until: (appTimeOut),
             layout: '{mnn}<span>:</span>{snn}',
             onExpiry: NewGameTimeOut
         });
         $(".ngm-bk .tm").countdown('resume');
         $(".ngm-bk .tm").countdown('option', {until: (appTimeOut)});

         $.each(receiveData.res.field, function( x, cells ) {
             $.each(cells, function( y, cell) {
                //     $('.ngm-bk .ngm-gm .gm-mx ul.mx li#'+x+'x'+y).html(cell.points);
                 if(cell.player==playerId) {
                     var class_cell='m';
                 }
                 else
                 {
                     var class_cell='o';
                 }
                 $('.ngm-bk .ngm-gm .gm-mx ul.mx li#'+cell.coord).
                     html(cell.points).
                     addClass(class_cell).fadeIn(100);
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

                 $('.ngm-bk .ngm-gm .gm-mx ul.mx li#'+receiveData.res.cell.coord).
                     html(receiveData.res.cell.points).
                     addClass(class_cell).fadeIn(300);

             }

             if(receiveData.res.current)
                 if(receiveData.res.current!=playerId)
                 {
                     $('.gm-pr.r').addClass('move');
                     $('.gm-pr.l').removeClass('move');
                 }
                 else
                 {
                     $('.gm-pr.l').addClass('move');
                     $('.gm-pr.r').removeClass('move');
                 }

             if(receiveData.res.players)
                 $.each(receiveData.res.players, function( index, value ) {
                     var class_player=value.pid==playerId?'l':'r';
                     $('.gm-pr.'+class_player+' .pr-cl b').html(value.moves).hide().fadeIn(200);
                     $('.gm-pr.'+class_player+' .pr-pt b').html(value.points).hide().fadeIn(200);
                 });

             $(".ngm-bk .tm").countdown('option', {until: (appTimeOut)});
    /*
             $('.gm-pr.'+current_player+' .pr-cl b').html(
                 parseInt($('.gm-pr.'+current_player+' .pr-cl b').html())-1
             ).hide().fadeIn(200);

             $('.gm-pr.'+current_player+' .pr-pt b').html(
             parseInt($('.gm-pr.'+current_player+' .pr-pt b').html())+
             receiveData.res.cell.points
             ).hide().fadeIn(200);
     */


             if(receiveData.res.winner){
                 $('.ngm-bk .tm').countdown('pause');

                 $('.gm-pr').removeClass('move');
                 $('.ngm-bk .ngm-gm .gm-pr .pr-surr').hide();

                 setTimeout(function(){
                     $('.msg.winner').fadeIn(200);
                     class_player=receiveData.res.winner==playerId?'l':'r';


                     $('.gm-pr .pr-cl').css('opacity','0');
                     $('.gm-pr.'+class_player+' .pr-cl').css('opacity','100').html("<b>"+receiveData.res.price+"</b><span>"+(receiveData.res.currenсy=='MONEY'?'денег':'баллов')+"<br>выиграно</span>");

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
             $("#report-popup").find(".txt").text(receiveData.res.error);
             $("#report-popup").show().fadeIn(200);
             if(receiveData.res.appId==0) {
                 appId = receiveData.res.appId;
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
    if ($(this).is(":not(:disabled)"))
    {
        $('.ngm-bk .ngm-go').hide().prev().show();
        appMode = $('.ngm-bk .prc-bl .prc-but-bk .prc-sel').find('.active').data('price');
        var path='app/NewGame/'+appId;
        var data={'action':'start', 'mode': appMode};
        WebSocketAjaxClient(path,data);
    }
});



// выход
$(document).on('click', '.ngm-bk .ngm-gm .gm-mx .msg.winner .exit', function(e){
    $('.ngm-rls').fadeIn(200);
    $('.ngm-bk .ngm-go').show().prev().hide();// removeClass('button-disabled').attr('disabled',false);

    var path='app/NewGame/'+appId;
    var data={'action':'quit'};
    WebSocketAjaxClient(path,data);
    appId=0;
});

// отмена
$(document).on('click', '.ngm-bk .bk-bt-rl, .ngm-bk .bk-bt, .ngm-bk .ngm-rls-bk .prc-l .prc-but-bk .ngm-cncl', function(e){
    $('.ngm-bk .ngm-go').show().prev().hide();
    appId=0;
    var path='app/NewGame/'+appId;
    var data={'action':'quit'};
    WebSocketAjaxClient(path,data);
});

// switch блоков ставок
$(document).on('click', '.ngm-bk .ngm-rls-bk .prc-l .prc-but-bk .prc-bt', function(e){

    $('.ngm-bk .ngm-rls-bk .prc-l .prc-but-bk .prc-sel .prc-vl').removeClass('active');
    $('.ngm-bk .ngm-rls-bk .prc-l .prc-but-bk .prc-sel').hide();
    $('.ngm-bk .ngm-rls-bk .prc-l .prc-but-bk .prc-bt').show();
    $(this).hide().next().show().children().last().addClass('active');
    $('.ngm-bk .ngm-go').removeClass('button-disabled').attr('disabled',false);
});

// выбор ставки
$(document).on('click', '.ngm-bk .ngm-rls-bk .prc-l .prc-but-bk .prc-sel .prc-vl', function(e){
    $('.ngm-bk .ngm-rls-bk .prc-l .prc-but-bk .prc-sel .prc-vl').removeClass('active');
    $(this).addClass('active');
});


// switch rules block
$(document).on('click', '.ngm-bk .bk-bt-rl', function(e){
    $('.ngm-bk .prc-l').hide();
    $('.ngm-bk .rls-l').fadeIn(200);
});


// switch price block
$(document).on('click', '.ngm-bk .ngm-price', function(e){
    $('.ngm-bk .rls-l').hide();

    $('.ngm-bk .ngm-go').addClass('button-disabled').attr('disabled','disabled');
    $('.ngm-bk .prc-but-bk').find('active').removeClass('active');
    $('.ngm-bk .ngm-rls-bk .prc-l .prc-but-bk .prc-sel').hide();
    $('.ngm-bk .ngm-rls-bk .prc-l .prc-but-bk .prc-bt').show();

    $('.ngm-bk .prc-l').fadeIn(200);
});


// сдаться
$(document).on('click', '.ngm-gm .gm-pr.l .pr-surr', function(e){
    var path='app/NewGame/'+appId;
    var data={'action':'pass'};
    WebSocketAjaxClient(path,data);
});


// повторить
$(document).on('click', '.ngm-bk .ngm-gm .gm-mx .msg.winner .re', function(e){
    var path='app/NewGame/'+appId;
    var data={'action':'replay'};
    WebSocketAjaxClient(path,data);
});


// другой соперник
$(document).on('click', '.ngm-bk .ngm-gm .gm-mx .msg.winner .ch-ot', function(e){

    if(appId)
    {
        var path='app/NewGame/'+appId;
        var data={'action':'quit'};
        WebSocketAjaxClient(path,data);
        appId=0;
    }

    $('.ngm-bk .ngm-gm .gm-mx .msg.winner .re').hide();
    $('.ngm-bk .ngm-gm .gm-mx .msg.winner .ch-ot').hide();
    $('.ot-exit').html('Ожидаем соперника').show();
    
    var path='app/NewGame/'+appId;
    var data={'action':'start', 'mode':appMode};
    WebSocketAjaxClient(path,data);
});

// делаем ход
$(document).on('click', '.ngm-gm .gm-mx ul.mx li', function(e){

    if(parseInt($('.gm-pr.l .pr-cl b').html())<=0){
    //    $("#report-popup").find(".txt").text('ENOUGH_MOVES');
    //    $("#report-popup").show().fadeIn(200);
    }
    else if(!($('.gm-pr.l').hasClass('move')))
    {
    //    $("#report-popup").find(".txt").text('NOT_YOUR_MOVE');
    //    $("#report-popup").show().fadeIn(200);
    }
    else{
        var path='app/NewGame/'+appId;
        var data={'action':'move','cell':$(this).attr('id')}
        WebSocketAjaxClient(path,data);
    }
});

function NewGameTimeOut(){
    var path='app/NewGame/'+appId;
    var data={'action':'timeout'}
    WebSocketAjaxClient(path,data);
}