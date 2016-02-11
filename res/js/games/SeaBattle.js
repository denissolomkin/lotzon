(function () {

    Apps.SeaBattle = {

        ships: [],
        game_ships: [],
        buttons:{
            random: {
                class: 'btn-primary btn-sb-ready',
                action: 'random',
                title: 'button-game-random'
            },
            ready: {
                class: 'btn-secondary btn-sb-random',
                action: 'ready',
                title: 'button-game-ready'
            }
        },

        action: {

            default: function () {

                if (Game.hasField()) {

                    Game.run() && Apps.SeaBattle.drawField();
                    Apps.SeaBattle.initStatuses();
                    Apps.SeaBattle.initTimers();

                    switch(App.action) {

                        case 'field':
                            Apps.SeaBattle.game_ships = Apps.SeaBattle.ships;
                            Apps.SeaBattle.genFieldSeaBattle();
                            Game.drawButtons([
                                Apps.SeaBattle.buttons.random,
                                Apps.SeaBattle.buttons.ready
                            ]);
                            break;

                        case 'start':
                            $('ul.mx.SeaBattle.m div').remove();
                            $('.mx.place').hide();
                            $('.mx.SeaBattle.o').show();
                            break;

                        case 'wait':
                            Game.drawButtons('game-waiting-for-opponent');
                            break;

                        case 'move':
                            break;
                    }

                    Apps.SeaBattle.drawPlayerFields();
                    Apps.SeaBattle.paintCell();
                    Game.end() && Apps.SeaBattle.end();
                }

            },

            error: function () {

            },
        },

        do: {

            ready: function (e) {

                e.preventDefault();
                e.stopPropagation();

                var path = 'app/' + App.id + '/' + App.uid,
                    data = {
                        'action': 'field',
                        'field': Apps.SeaBattle.ships
                    };

                WebSocketAjaxClient(path, data);
            }
        },

        drawField: function () {

            var field = Game.field.getElementsByClassName('mx')[0];

            field.innerHTML +=
                '<ul class="mx SeaBattle m"></ul>' +
                '<div class="mx place">Расставьте корабли в необходимом порядке.<br><br>Что бы изменить ориентацию корабля, кликните по нему дважды.' +
                '<ul class="mx SeaBattle o"></ul>';

            if(App.variation && App.variation.field){

                var field = App.variation.field.split('x'),
                    width = height = Math.min( Math.floor((220 - (field[0]-1)) / field[0]), Math.floor((440 - field[1]) / field[0]))+'px;',
                    font = (parseInt(width) / 1.6) + 'px/'+ (parseInt(width))+'px Handbook-bold;',
                    html = '';

                for(i=1;i<=field[1];i++)
                    for(j=1;j<=field[0];j++)
                        html+="<li style='width:"+width+"height:"+height+"font:"+font+(j==field[0]?"margin-right: 0px;":"")+"' data-coor='"+j+"x"+i+"'></li>";

                $('ul.mx', $(Game.field)).html(html);
            }
        },

        drawPlayerFields: function(){
            if (App.fields) {
                $.each(App.fields, function (index, field) {
                    class_cell = (index == Player.id ? 'm' : 'o');
                    $.each(field, function (x, cells) {
                        $.each(cells, function (y, cell) {
                            cell.coord = x+'x'+y+'x'+index;
                            Apps.SeaBattle.paintCell(cell);
                        });
                    });
                });
            }
        },

        paintCell: function (cell) {

            if ((cell = cell || App.cell)) {

                class_cell = (cell.coord.split("x")[2] == Player.id ? 'm' : 'o');

                if (move = cell.class == 'e' ? 1 : cell.class == 'd' ? 2 : cell.class == 'k' ? 3 : null)
                    Apps.playAudio([App.key, 'Move-' + class_cell + '-' + move]);

                $('ul.mx.' + class_cell + ' li.last', $(Game.field)).removeClass('last');

                var $cell = $('ul.mx li[data-cell="' + cell.coord + '"]', $(Game.field));

                if(cell!=App.cell){

                    $cell
                        .addClass((isNumeric(cell) ? 's' : cell) + ' last')
                        .addClass(class_cell)
                        .fadeIn(100)
                        .html(cell == 'd' ? "<img src='tpl/img/games/damage.png'>" : '');

                } else {

                    $cell
                        .addClass(cell.class)
                        .html(
                            '<div class="' + cell.class + '" style="background:'
                            + $('.ngm-bk .ngm-gm .gm-mx ul.mx li[data-cell="' + cell.coord + '"]')
                                .css('background') + ';width:' + cell.css('width') + ';height:' + cell.css('height') + '"></div>')
                        .find('div')
                        .effect('explode', {pieces: 4}, 500)
                        .parent().addClass(class_cell + ' last')
                        .fadeIn(300)
                        .html(cell.class == 'd' ? "<img src='tpl/img/games/damage.png'>" : '');
                }
            }
        },

        checkFieldSeaBattle: function (newship, id) {

            var size = $('.mx.SeaBattle:eq(1) li').last().attr('data-coor').split('x');
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
            var ret = false;

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
                                ret = true;
                            }
                    });

                    if (ret)
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

        genFieldSeaBattle: function () {

            Apps.SeaBattle.ships = [];

            var size = $('.mx.SeaBattle:eq(1) li').last().attr('data-coor').split('x');
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

            loop: while (Apps.SeaBattle.ships.length != game_ships.length) {

                count++;
                if (count > 100) {
                    break loop;
                }

                x = Math.ceil(Math.random() * size_x);
                y = Math.ceil(Math.random() * size_y);
                h = Math.ceil(Math.random() * 2) - 1;
                l = game_ships[iterration];

                ship = [];

                while (ship.length != l) {

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

                $.each(ship, function (i, cell) {
                    field[cell[1]][cell[0]] = 1;
                });

                Apps.SeaBattle.ships.push([ship[0], h, l]);
                iterration++;

            }
            var wid = parseFloat($('.mx.SeaBattle:eq(1) li').last().css('width'));
            var hei = parseFloat($('.mx.SeaBattle:eq(1) li').last().css('height'));
            var html = '';
            $.each(Apps.SeaBattle.ships, function (index, ship) {
                html += '<div data-id="' + index + '" ' +
                    'style="' +
                    'top:' + (ship[0][1] * (hei + 1) - (hei + 1)) + 'px;' +
                    'left:' + (ship[0][0] * (wid + 1) - (wid + 1)) + 'px;' + (ship[1]
                        ? 'width: ' + (ship[2] * (wid + 1)) + 'px;height:' + (hei + 1) + 'px;'
                        : 'height: ' + (ship[2] * (hei + 1)) + 'px;width:' + (wid + 1) + 'px;') + '" class="s ' + (ship[1] ? 'h' : '') + ' drag"></div>';
            });

            $('ul.mx.SeaBattle.m div').remove();
            $("ul.SeaBattle.m").append(html);

            $(".drag").dblclick(function () {
                var drag = $(this)
                var h = drag.css('width');
                var w = drag.css('height');
                var v = drag.hasClass('h') ? 0 : 1;

                var ship = [].concat(Apps.SeaBattle.ships[drag.data('id')]);
                ship[1] = v;

                if (checkFieldSeaBattle(ship, $(this).data('id'))) {
                    $(this).css('width', w).css('height', h).removeClass('h').addClass(v ? 'h' : '');
                    Apps.SeaBattle.ships[$(this).data('id')][1] = v;
                } else {
                    $(this).removeClass('drag ui-draggable ui-draggable-handle');
                    $(this).effect("shake", {distance: 5, times: 1, duration: 2});
                    window.setTimeout(function () {
                        drag.addClass('drag ui-draggable ui-draggable-handle');
                    }, 1000);
                }

            });

            $(".drag").draggable({
                containment: "parent", grid: [wid + 1, hei + 1],
                revert: function () {

                    var ship = [].concat(Apps.SeaBattle.ships[$(this).data('id')]);
                    ship[0] = [
                        (parseInt($(this).css('left')) + (wid + 1)) / (wid + 1),
                        (parseInt($(this).css('top')) + (hei + 1)) / (hei + 1)
                    ];

                    if (checkFieldSeaBattle(ship, $(this).data('id'))) {
                        Apps.SeaBattle.ships[$(this).data('id')][0] = [
                            (parseInt($(this).css('left')) + (wid + 1)) / (wid + 1),
                            (parseInt($(this).css('top')) + (hei + 1)) / (hei + 1)
                        ];
                        return false
                    } else return true;
                },

                start: function () {
                },
                stop: function () {


                }
            });
        }

    }


})();