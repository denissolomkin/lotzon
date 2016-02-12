(function () {

    Apps.SeaBattle = {

        ships: [],
        game_ships: [],
        buttons: {
            random: {
                class: 'btn-primary btn-sb-random',
                title: 'button-game-random'
            },
            ready: {
                class: 'btn-secondary btn-sb-ready',
                action: 'ready',
                title: 'button-game-ready'
            }
        },

        action: {

            default: function () {

                if (Game.hasField()) {

                    Game.run() && Apps.SeaBattle.drawField();
                    Game.drawButtons();

                    switch (App.action) {

                        case 'field':
                            App.current = Player.id;
                            Apps.SeaBattle.game_ships = App.ships;
                            Apps.SeaBattle.genFieldSeaBattle();
                            Game.drawButtons([
                                Apps.SeaBattle.buttons.random,
                                Apps.SeaBattle.buttons.ready
                            ]);
                            break;

                        case 'start':
                            $('.SeaBattle ul.table.m div').remove();
                            $('.SeaBattle .place').hide();
                            $('.SeaBattle ul.table.o').show();
                            break;

                        case 'ready':
                            for (var playerId in App.players)
                                if (App.players.hasOwnProperty(playerId) && playerId != Player.id && (App.current = playerId))
                                    break;
                            Game.drawButtons('title-game-waiting-player');
                            break;

                        case 'wait':
                            for (var playerId in App.players)
                                if (App.players.hasOwnProperty(playerId) && playerId != Player.id && (App.current = playerId))
                                    break;
                            Game.drawButtons('title-game-waiting-player');
                            break;

                        case 'move':
                            $('ul', $(Game.field)).css('opacity', 1).css('border', '1px solid red');
                            $('ul.player' + App.current, $(Game.field)).css('opacity', 0.7).css('border', 'none');
                            Apps.SeaBattle.paintCell();
                            break;

                        case 'stack':
                            break;
                    }

                    Apps.SeaBattle.drawShips();
                    Game.initTimers();
                    Game.end() && Apps.SeaBattle.end();

                    //>>>>>> dont forget remove this!!!!
                    Game.destroyTimeOut();
                }

            },

            error: function () {
                alert(App.error);
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

            if (App.variation && App.variation.field) {
                alert('drawField:');
                var fieldSize = App.variation.field.split('x'),
                    width = height = Math.min(Math.floor((220 - (fieldSize[0] - 1)) / fieldSize[0]), Math.floor((440 - fieldSize[1]) / fieldSize[0])) + 'px;',
                    font = (parseInt(width) / 1.6) + 'px/' + (parseInt(width)) + 'px Handbook-bold;',
                    html = '';

                for (i = 1; i <= fieldSize[1]; i++)
                    for (j = 1; j <= fieldSize[0]; j++)
                        html += "<li style='width:" + width + "height:" + height + "font:" + font + (j == fieldSize[0] ? "margin-right: 0px;" : "")
                            + "' data-coor='" + j + "x" + i + "'></li>";

                for (var playerId in App.players) {

                    var playerClass = 'player' + playerId + ' ' + (Player.id == playerId ? 'm' : 'o');

                    field.innerHTML +=
                        '<ul class="table ' + playerClass + '">' + html + '</ul>';

                    var li = field.querySelectorAll('.player' + playerId + ' li');

                    for (var index = 0; index < li.length; index++) {
                        var cellAttr = li[index].getAttribute('data-coor') + 'x' + playerId;
                        li[index].setAttribute('data-cell', cellAttr);
                        playerId == Player.id && li[index].classList.add('m');
                    }
                }

                field.innerHTML += '<div class="place" style="inline-block">' + i18n("text-game-place-ships") + '</div>';

            }
        },

        drawShips: function () {
            if (App.fields) {
                $.each(App.fields, function (index, field) {
                    $.each(field, function (x, cells) {
                        $.each(cells, function (y, cell) {
                            Apps.SeaBattle.paintCell({
                                coord: x + 'x' + y + 'x' + index,
                                class: cell
                            });
                        });
                    });
                });
            }
        },

        paintCell: function (cell) {
            if ((cell = cell || App.cell)) {

                var playerId = (cell.coord.split("x")[2]),
                    classCell = playerId == Player.id ? 'm' : 'o',
                    $gameField = $(Game.field),
                    $cell = $('ul li[data-cell="' + cell.coord + '"]', $gameField);

                if (move = cell.class == 'e' ? 1 : cell.class == 'd' ? 2 : cell.class == 'k' ? 3 : null)
                    Apps.playAudio([App.key, 'Move-' + classCell + '-' + move]);

                $('ul.player' + playerId + ' li.last', $gameField).removeClass('last');

                $cell
                    .addClass((isNumeric(cell.class) ? 's' : cell.class) + ' last')
                    .addClass(classCell)
                    .html(cell.class == 'd' ? "<img src='tpl/img/games/damage.png'>" : '');

                if (cell != App.cell) {

                    $cell.fadeIn(100);

                } else {

                    var div = '<div class="' + cell.class + '" style="background:'
                        + $cell.css('background')
                        + ';width:' + $cell.css('width')
                        + ';height:' + $cell.css('height') + '"></div>';

                    $cell
                        .html(div)
                        .find('div')
                        .effect('explode', {pieces: 4}, 500)
                        .parent()
                        .fadeIn(300);
                }
            }
        },


        'end': function () {

            $('.SeaBattle .mx ul.o li.s:not(.d,.k)').effect('pulsate', {times: 10});
            Game.drawWinButtons([
                Game.buttons.replay,
                Game.buttons.exit
            ]);

        },

        checkFieldSeaBattle: function (newship, id) {
            alert('checkFieldSeaBattle:');
            var size = $('.SeaBattle .mx .table:eq(1) li').last().attr('data-coor').split('x');
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
            alert('genFieldSeaBattle:');

            var size = $('.SeaBattle .table:eq(1) li').last().attr('data-coor').split('x'),
                size_x = size[0],
                size_y = size[1],
                matrix = [
                    [-1, -1], [-1, 0], [-1, 1],
                    [0, -1], [0, 0], [0, 1],
                    [1, -1], [1, 0], [1, 1]
                ],
                game_ships = Apps.SeaBattle.game_ships,
                field = [],
                iterration = 0,
                count = 0;

            for (y = 1; y <= size_y; y++) {
                field[y] = [];
                for (x = 1; x <= size_x; x++)
                    field[y][x] = 0;
            }

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

            var wid = parseFloat($('.SeaBattle ul:eq(1) li').last().css('width')),
                hei = parseFloat($('.SeaBattle ul:eq(1) li').last().css('height')),
                html = '';

            $.each(Apps.SeaBattle.ships, function (index, ship) {
                html += '<div data-id="' + index + '" ' +
                    'style="' +
                    'top:' + (ship[0][1] * (hei + 1) - (hei + 1)) + 'px;' +
                    'left:' + (ship[0][0] * (wid + 1) - (wid + 1)) + 'px;' + (ship[1]
                        ? 'width: ' + (ship[2] * (wid + 1)) + 'px;height:' + (hei + 1) + 'px;'
                        : 'height: ' + (ship[2] * (hei + 1)) + 'px;width:' + (wid + 1) + 'px;') + '" class="s ' + (ship[1] ? 'h' : '') + ' drag"></div>';
            });

            $(".SeaBattle ul.m div").remove();
            $(".SeaBattle ul.m").append(html);

            $(".drag").dblclick(function () {
                var drag = $(this)
                var h = drag.css('width');
                var w = drag.css('height');
                var v = drag.hasClass('h') ? 0 : 1;

                var ship = [].concat(Apps.SeaBattle.ships[drag.data('id')]);
                ship[1] = v;

                if (Apps.SeaBattle.checkFieldSeaBattle(ship, $(this).data('id'))) {
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
                alert('drag');

                    var ship = [].concat(Apps.SeaBattle.ships[$(this).data('id')]);
                    ship[0] = [
                        (parseInt($(this).css('left')) + (wid + 1)) / (wid + 1),
                        (parseInt($(this).css('top')) + (hei + 1)) / (hei + 1)
                    ];

                    if (Apps.SeaBattle.checkFieldSeaBattle(ship, $(this).data('id'))) {
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


// (function () {

//     Apps.SeaBattle = {

//         ships: [],
//         game_ships: [],
//         buttons: {
//             random: {
//                 class: 'btn-primary btn-sb-random',
//                 title: 'button-game-random'
//             },
//             ready: {
//                 class: 'btn-secondary btn-sb-ready',
//                 action: 'ready',
//                 title: 'button-game-ready'
//             }
//         },

//         action: {

//             default: function () {

//                 if (Game.hasField()) {

//                     Game.run() && Apps.SeaBattle.drawField();
//                     Game.drawButtons();

//                     switch (App.action) {

//                         case 'field':
//                             App.current = Player.id;
//                             Apps.SeaBattle.game_ships = App.ships;
//                             Apps.SeaBattle.genFieldSeaBattle();
//                             Game.drawButtons([
//                                 Apps.SeaBattle.buttons.random,
//                                 Apps.SeaBattle.buttons.ready
//                             ]);
//                             break;

//                         case 'start':
//                             $('.SeaBattle ul.table.m div').remove();
//                             $('.SeaBattle .place').hide();
//                             $('.SeaBattle ul.table.o').show();
//                             break;

//                         case 'ready':
//                             for (var playerId in App.players)
//                                 if (App.players.hasOwnProperty(playerId) && playerId != Player.id && (App.current = playerId))
//                                     break;
//                             Game.drawButtons('title-game-waiting-player');
//                             break;

//                         case 'wait':
//                             for (var playerId in App.players)
//                                 if (App.players.hasOwnProperty(playerId) && playerId != Player.id && (App.current = playerId))
//                                     break;
//                             Game.drawButtons('title-game-waiting-player');
//                             break;

//                         case 'move':
//                             $('ul', $(Game.field)).css('opacity', 1).css('border', '1px solid red');
//                             $('ul.player' + App.current, $(Game.field)).css('opacity', 0.7).css('border', 'none');
//                             Apps.SeaBattle.paintCell();
//                             break;

//                         case 'stack':
//                             break;
//                     }

//                     Apps.SeaBattle.drawShips();
//                     Game.initTimers();
//                     Game.end() && Apps.SeaBattle.end();

//                     //>>>>>> dont forget remove this!!!!
//                     Game.destroyTimeOut();
//                 }

//             },

//             error: function () {
//                 alert(App.error);
//             },
//         },

//         do: {

//             ready: function (e) {

//                 e.preventDefault();
//                 e.stopPropagation();

//                 var path = 'app/' + App.id + '/' + App.uid,
//                     data = {
//                         'action': 'field',
//                         'field': Apps.SeaBattle.ships
//                     };

//                 WebSocketAjaxClient(path, data);
//             }
//         },

//         drawField: function () {

//             var field = Game.field.getElementsByClassName('mx')[0];

//             if (App.variation && App.variation.field) {
//                 alert('drawField:');
//                 var fieldSize = App.variation.field.split('x'),
//                     width = height = Math.min(Math.floor((220 - (fieldSize[0] - 1)) / fieldSize[0]), Math.floor((440 - fieldSize[1]) / fieldSize[0])) + 'px;',
//                     font = (parseInt(width) / 1.6) + 'px/' + (parseInt(width)) + 'px Handbook-bold;',
//                     html = '';

//                 for (i = 1; i <= fieldSize[1]; i++)
//                     for (j = 1; j <= fieldSize[0]; j++)
//                         html += "<li style='width:" + width + "height:" + height + "font:" + font + (j == fieldSize[0] ? "margin-right: 0px;" : "")
//                             + "' data-coor='" + j + "x" + i + "'></li>";

//                 for (var playerId in App.players) {

//                     var playerClass = 'player' + playerId + ' ' + (Player.id == playerId ? 'm' : 'o');

//                     field.innerHTML +=
//                         '<ul class="table ' + playerClass + '">' + html + '</ul>';

//                     var li = field.querySelectorAll('.player' + playerId + ' li');

//                     for (var index = 0; index < li.length; index++) {
//                         var cellAttr = li[index].getAttribute('data-coor') + 'x' + playerId;
//                         li[index].setAttribute('data-cell', cellAttr);
//                         playerId == Player.id && li[index].classList.add('m');
//                     }
//                 }

//                 field.innerHTML += '<div class="place" style="inline-block">' + i18n("text-game-place-ships") + '</div>';

//             }
//         },

//         drawShips: function () {
//             if (App.fields) {
//                 $.each(App.fields, function (index, field) {
//                     $.each(field, function (x, cells) {
//                         $.each(cells, function (y, cell) {
//                             Apps.SeaBattle.paintCell({
//                                 coord: x + 'x' + y + 'x' + index,
//                                 class: cell
//                             });
//                         });
//                     });
//                 });
//             }
//         },

//         paintCell: function (cell) {
//             if ((cell = cell || App.cell)) {

//                 var playerId = (cell.coord.split("x")[2]),
//                     classCell = playerId == Player.id ? 'm' : 'o',
//                     $gameField = $(Game.field),
//                     $cell = $('ul li[data-cell="' + cell.coord + '"]', $gameField);

//                 if (move = cell.class == 'e' ? 1 : cell.class == 'd' ? 2 : cell.class == 'k' ? 3 : null)
//                     Apps.playAudio([App.key, 'Move-' + classCell + '-' + move]);

//                 $('ul.player' + playerId + ' li.last', $gameField).removeClass('last');

//                 $cell
//                     .addClass((isNumeric(cell.class) ? 's' : cell.class) + ' last')
//                     .addClass(classCell)
//                     .html(cell.class == 'd' ? "<img src='tpl/img/games/damage.png'>" : '');

//                 if (cell != App.cell) {

//                     $cell.fadeIn(100);

//                 } else {

//                     var div = '<div class="' + cell.class + '" style="background:'
//                         + $cell.css('background')
//                         + ';width:' + $cell.css('width')
//                         + ';height:' + $cell.css('height') + '"></div>';

//                     $cell
//                         .html(div)
//                         .find('div')
//                         .effect('explode', {pieces: 4}, 500)
//                         .parent()
//                         .fadeIn(300);
//                 }
//             }
//         },


//         'end': function () {

//             $('.SeaBattle .mx ul.o li.s:not(.d,.k)').effect('pulsate', {times: 10});
//             Game.drawWinButtons([
//                 Game.buttons.replay,
//                 Game.buttons.exit
//             ]);

//         },

//         checkFieldSeaBattle: function (newship, id) {
//             alert('checkFieldSeaBattle:');
//             var size = $('.SeaBattle .mx .table:eq(1) li').last().attr('data-coor').split('x');
//             var size_x = size[0];
//             var size_y = size[1];

//             matrix = [
//                 [-1, -1], [-1, 0], [-1, 1],
//                 [0, -1], [0, 0], [0, 1],
//                 [1, -1], [1, 0], [1, 1]
//             ];

//             var game_ships = Apps.SeaBattle.game_ships;

//             var field = [];
//             for (y = 1; y <= size_y; y++) {
//                 field[y] = [];
//                 for (x = 1; x <= size_x; x++)
//                     field[y][x] = 0;
//             }
//             var iterration = 0;
//             var count = 0;
//             var ret = false;

//             loop: while (iterration != game_ships.length) {

//                 count++;
//                 if (count > 100) {
//                     break loop;
//                 }

//                 if (iterration != id) data = Apps.SeaBattle.ships[iterration];
//                 else data = newship;

//                 x = data[0][0];
//                 y = data[0][1];
//                 h = data[1];
//                 l = data[2];

//                 ship = [];

//                 while (ship.length != l) {

//                     if (x > size_x || y > size_y) {
//                         return false;
//                     }


//                     $.each(matrix, function (i, v) {
//                         if (y + v[0] > 0 && y + v[0] <= size_y && x + v[1] > 0 && x + v[1] <= size_x)
//                             if (field[y + v[0]][x + v[1]]) {
//                                 ret = true;
//                             }
//                     });

//                     if (ret)
//                         return false;
//                     ship.push([x, y]);
//                     h ? x++ : y++;
//                 }

//                 $.each(ship, function (i, cell) {
//                     field[cell[1]][cell[0]] = 1;
//                 });

//                 iterration++;

//             }
//             return true;
//         },

//         genFieldSeaBattle: function () {

//             Apps.SeaBattle.ships = [];
//             alert('genFieldSeaBattle:');

//             var size = $('.SeaBattle .table:eq(1) li').last().attr('data-coor').split('x'),
//                 size_x = size[0],
//                 size_y = size[1],
//                 matrix = [
//                     [-1, -1], [-1, 0], [-1, 1],
//                     [0, -1], [0, 0], [0, 1],
//                     [1, -1], [1, 0], [1, 1]
//                 ],
//                 game_ships = Apps.SeaBattle.game_ships,
//                 field = [],
//                 iterration = 0,
//                 count = 0;

//             for (y = 1; y <= size_y; y++) {
//                 field[y] = [];
//                 for (x = 1; x <= size_x; x++)
//                     field[y][x] = 0;
//             }

//             loop: while (Apps.SeaBattle.ships.length != game_ships.length) {

//                 count++;
//                 if (count > 100) {
//                     break loop;
//                 }

//                 x = Math.ceil(Math.random() * size_x);
//                 y = Math.ceil(Math.random() * size_y);
//                 h = Math.ceil(Math.random() * 2) - 1;
//                 l = game_ships[iterration];

//                 ship = [];

//                 while (ship.length != l) {

//                     con = false;
//                     if (l != 1 && ((h && x + 1 > size_x) || (!h && y + 1 > size_y))) {
//                         continue loop;
//                     }

//                     $.each(matrix, function (i, v) {
//                         if (y + v[0] > 0 && y + v[0] <= size_y && x + v[1] > 0 && x + v[1] <= size_x)
//                             if (field[y + v[0]][x + v[1]])
//                                 con = true;
//                     });

//                     if (con) continue loop;
//                     ship.push([x, y]);
//                     h ? x++ : y++;
//                 }

//                 $.each(ship, function (i, cell) {
//                     field[cell[1]][cell[0]] = 1;
//                 });

//                 Apps.SeaBattle.ships.push([ship[0], h, l]);
//                 iterration++;

//             }

//             var wid = parseFloat($('.SeaBattle ul:eq(1) li').last().css('width')),
//                 hei = parseFloat($('.SeaBattle ul:eq(1) li').last().css('height')),
//                 html = '';

//             $.each(Apps.SeaBattle.ships, function (index, ship) {
//                 html += '<div data-id="' + index + '" ' +
//                     'style="' +
//                     'top:' + (ship[0][1] * (hei + 1) - (hei + 1)) + 'px;' +
//                     'left:' + (ship[0][0] * (wid + 1) - (wid + 1)) + 'px;' + (ship[1]
//                         ? 'width: ' + (ship[2] * (wid + 1)) + 'px;height:' + (hei + 1) + 'px;'
//                         : 'height: ' + (ship[2] * (hei + 1)) + 'px;width:' + (wid + 1) + 'px;') + '" class="s ' + (ship[1] ? 'h' : '') + ' drag"></div>';
//             });

//             $(".SeaBattle ul.m div").remove();
//             $(".SeaBattle ul.m").append(html);

//             $(".drag").dblclick(function () {
//                 var drag = $(this)
//                 var h = drag.css('width');
//                 var w = drag.css('height');
//                 var v = drag.hasClass('h') ? 0 : 1;

//                 var ship = [].concat(Apps.SeaBattle.ships[drag.data('id')]);
//                 ship[1] = v;

//                 if (Apps.SeaBattle.checkFieldSeaBattle(ship, $(this).data('id'))) {
//                     $(this).css('width', w).css('height', h).removeClass('h').addClass(v ? 'h' : '');
//                     Apps.SeaBattle.ships[$(this).data('id')][1] = v;
//                 } else {
//                     $(this).removeClass('drag ui-draggable ui-draggable-handle');
//                     $(this).effect("shake", {distance: 5, times: 1, duration: 2});
//                     window.setTimeout(function () {
//                         drag.addClass('drag ui-draggable ui-draggable-handle');
//                     }, 1000);
//                 }

//             });

//             $(".drag").draggable({
                

//                 containment: "parent", grid: [wid + 1, hei + 1],
//                 revert: function () {
//                 alert('drag');

//                     var ship = [].concat(Apps.SeaBattle.ships[$(this).data('id')]);
//                     ship[0] = [
//                         (parseInt($(this).css('left')) + (wid + 1)) / (wid + 1),
//                         (parseInt($(this).css('top')) + (hei + 1)) / (hei + 1)
//                     ];

//                     if (Apps.SeaBattle.checkFieldSeaBattle(ship, $(this).data('id'))) {
//                         Apps.SeaBattle.ships[$(this).data('id')][0] = [
//                             (parseInt($(this).css('left')) + (wid + 1)) / (wid + 1),
//                             (parseInt($(this).css('top')) + (hei + 1)) / (hei + 1)
//                         ];
//                         return false
//                     } else return true;
//                 },

//                 start: function () {
//                 },
//                 stop: function () {


//                 }
//             });
//         }

//     }


// })();