(function () {

    Apps.SeaBattle = {
        
        ships       : [],
        game_ships  : [],

        run: function () {

            Apps.SeaBattle.drawField();

        },


        action: function () {

            if (Game.hasField()) {
                Game.run() && Apps.SeaBattle.run();

                Apps.SeaBattle.initStatuses();
                Apps.SeaBattle.initTimers();
                Apps.SeaBattle.paintCell();
                Game.end() && Apps.WhoMore.end();
            }

        },

        error: function () {

        },

        'wait': function () {

        },

        field: function () {

        },

        start: function () {

        },
        
        do: {
            ready: function(e) {

                e.preventDefault();
                e.stopPropagation();

                var path = 'app/' + App.key + '/' + App.Uid,
                    data = {
                        'action': 'field', 
                        'field': Apps.SeaBattle.ships
                    };
                
                WebSocketAjaxClient(path, data);
            }
        },

        drawField: function(){

        },

        checkFieldSeaBattle: function(newship,id) {

            var size=$('.mx.SeaBattle:eq(1) li').last().attr('data-coor').split('x');
            var size_x = size[0];
            var size_y = size[1];

            matrix = [
                [-1, -1], [-1, 0], [-1, 1],
                [0, -1], [0, 0], [0, 1],
                [1, -1], [1, 0], [1, 1]
            ];

            var game_ships = Apps.SeaBattle.game_ships;

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

                if (iterration != id) data = Apps.SeaBattle.ships[iterration];
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
        },

        genFieldSeaBattle: function (){
            Apps.SeaBattle.ships=[];

            var size=$('.mx.SeaBattle:eq(1) li').last().attr('data-coor').split('x');
            var size_x = size[0];
            var size_y = size[1];

            matrix=[
                [-1,-1],[-1,0], [-1,1],
                [0,-1], [0,0],  [0,1],
                [1,-1], [1,0],  [1,1]
            ];

            var game_ships = Apps.SeaBattle.game_ships;
            var field = [];
            for (y = 1; y <= size_y; y++){
                field[y] = [];
                for (x = 1; x <= size_x; x++)
                    field[y][x] = 0;
            }
            var iterration = 0;
            var count = 0;

            loop: while (Apps.SeaBattle.ships.length != game_ships.length) {

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

                Apps.SeaBattle.ships.push([ship[0],h,l]);
                iterration++;

            }
            var wid=parseFloat($('.mx.SeaBattle:eq(1) li').last().css('width'));
            var hei=parseFloat($('.mx.SeaBattle:eq(1) li').last().css('height'));
            var html='';
            $.each(Apps.SeaBattle.ships, function( index,ship) {
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

                var ship=[].concat(Apps.SeaBattle.ships[drag.data('id')]);
                ship[1] = v;

                if(checkFieldSeaBattle(ship,$(this).data('id'))) {
                    $(this).css('width', w).css('height', h).removeClass('h').addClass(v ? 'h' : '');
                    Apps.SeaBattle.ships[$(this).data('id')][1] = v;
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

                    var ship=[].concat(Apps.SeaBattle.ships[$(this).data('id')]);
                    ship[0] = [
                        (parseInt($(this).css('left'))+(wid+1))/(wid+1),
                        (parseInt($(this).css('top'))+(hei+1))/(hei+1)
                    ];

                    if(checkFieldSeaBattle(ship,$(this).data('id'))){
                        Apps.SeaBattle.ships[$(this).data('id')][0]=[
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

    }



})();