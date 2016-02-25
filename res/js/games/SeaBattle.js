(function() {

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

            default: function() {
                // console.error('default:');


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
                            $('.SeaBattle .mx').addClass('started');

                            // for sheme
                            if ($('.SeaBattle .mx .shipsSheme').length === 0) {
                                $('.SeaBattle .mx').append('<div class="shipsSheme o"></div><div class="shipsSheme m"></div>');
                            }

                            $('.SeaBattle .table.m .drag-wrapper').remove();
                            $('.SeaBattle .place').hide('fast');

                            Apps.SeaBattle.drawShipsSheme();
                            Apps.SeaBattle.animateFields();

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
                            // console.error("move");
                            $('.table', $(Game.field)).css('opacity', 1);
                            $('.table.player' + App.current, $(Game.field)).css('opacity', 0.7).css('border', 'none');
                            Apps.SeaBattle.paintCell();
                            Apps.SeaBattle.drawShipsSheme();
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

            error: function() {
                alert(App.error);
            },
        },

        do: {

            ready: function(e) {

                e.preventDefault();
                e.stopPropagation();

                var path = 'app/' + App.id + '/' + App.uid,
                    data = {
                        'action': 'field',
                        'field': Apps.SeaBattle.ships
                    };

                WebSocketAjaxClient(path, data);

                // Apps.SeaBattle.beforeStart(function(){
                // });
            }
        },
        animateFields: function() {
            var tableM = $('.SeaBattle .table.m'),
                tableO = $('.SeaBattle .table.o');

            tableM.css({
                "position": "absolute",
                'width': tableM.width(),
                'height': tableM.width(),
                'left': tableM.offset().left - tableM.parent().offset().left,
                'top': tableM.position().top
            });

            $(tableM).animate({
                width: 139,
                height: 139,
                top: 0,
                left: 20
            }, {
                duration: 1000,
                // specialEasing: {
                //   width: "linear",
                //   height: "linear"
                // },
                complete: function() {
                    tableO.fadeIn('slow');
                }
            });
        },
        drawShipsSheme: function() {
            $.each(App.players, function(index, value) {
                var current = (index == Player.id ? 'm' : 'o');
                el = document.querySelector('.SeaBattle .mx > .shipsSheme.' + current);
                if (!el) {
                    // alert("смотри в оба - .shipsSheme не найден");
                    return;
                }
                var rev = [];

                $.each(value.ships, function(shp, cnt) {
                    var html = '';
                    html += '<div class="s ' + (cnt ? '' : 'e ') + current + '" >';
                    for (var i = 0; i < shp; i++) {
                        html += '<div></div>';
                    }
                    html += '<div class="cnt"><i>' + cnt + '</i></div>';

                    html += '</div>';

                    rev.push(html);
                });


                var html = '';
                for (var i = rev.length - 1; i >= 0; i--) { html += rev[i]; }


                el.innerHTML = html;
            });
        },
        drawField: function() {

            var field = Game.field.getElementsByClassName('mx')[0];

            if (App.variation && App.variation.field) {

                var fieldSize = App.variation.field.split('x'),
                    // width = height = Math.min(Math.floor((220 - (fieldSize[0] - 1)) / fieldSize[0]), Math.floor((440 - fieldSize[1]) / fieldSize[0])) + 'px;',
                    // font = (parseInt(width) / 1.6) + 'px/' + (parseInt(width)) + 'px Handbook-bold;',
                    html = '';

                field.innerHTML += '<div class="place" style="inline-block">' + i18n("text-game-place-ships") + '</div>';
                for (var i = 1; i <= fieldSize[1]; i++) {

                    html += '<div class="vw vh vf clearfix">';

                    for (var j = 1; j <= fieldSize[0]; j++) {
                        html += '<div>' +
                            '<div class = "inner" >' +
                            '<div class="cell" data-coor="' + j + "x" + i + '"></div>' +
                            '</div>' +
                            '</div>';
                    }

                    html += '</div>';
                }


                for (var playerId in App.players) {

                    var playerClass = 'player' + playerId + ' ' + (Player.id == playerId ? 'm' : 'o');

                    field.innerHTML +=
                        '<div class="table ' + playerClass + '">' + html + '</div>';

                    var cell = field.querySelectorAll('.player' + playerId + ' .cell');

                    for (var index = 0; index < cell.length; index++) {
                        var cellAttr = cell[index].getAttribute('data-coor') + 'x' + playerId;
                        cell[index].setAttribute('data-cell', cellAttr);
                        playerId == Player.id && cell[index].classList.add('m');
                    }
                }
                // alert(html);
                // alert(1);
                // console.error($('.SeaBattle .table:eq(1) .cell').last().attr('data-coor').split('x'));
                // alert(1);
                // return;

            }
        },

        drawShips: function() {
            if (App.fields) {
                $.each(App.fields, function(index, field) {
                    $.each(field, function(x, cells) {
                        $.each(cells, function(y, cell) {
                            Apps.SeaBattle.paintCell({
                                coord: x + 'x' + y + 'x' + index,
                                class: cell
                            });
                        });
                    });
                });
            }
        },

        paintCell: function(cell) {
            // on game start

            if ((cell = cell || App.cell)) {
                var playerId = (cell.coord.split("x")[2]),
                    classCell = playerId == Player.id ? 'm' : 'o',
                    $gameField = $(Game.field),
                    $cell = $('.inner div[data-cell="' + cell.coord + '"]', $gameField);

                if (move = cell.class == 'e' ? 1 : cell.class == 'd' ? 2 : cell.class == 'k' ? 3 : null)
                    Apps.playAudio([App.key, 'Move-' + classCell + '-' + move]);

                $('div.player' + playerId + ' .inner > div.last', $gameField).removeClass('last');

                $cell
                    .addClass((isNumeric(cell.class) ? 's' : cell.class) + ' last')
                    .addClass(classCell)
                    .html(cell.class == 'd' ? "<img src='tpl/img/games/damage.png'>" : '');

                if (cell != App.cell) {

                    $cell.fadeIn(100);

                } else {

                    var div = '<div class="' + cell.class + '" style="background:' + $cell.css('background') + ';width:' + $cell.css('width') + ';height:' + $cell.css('height') + '"></div>';

                    $cell
                        .html(div)
                        .find('div')
                        .effect('explode', { pieces: 4 }, 500)
                        .parent()
                        .fadeIn(300);
                }
            }
        },


        'end': function() {

            $('.SeaBattle .mx ul.o li.s:not(.d,.k)').effect('pulsate', { times: 10 });
            Game.drawWinButtons([
                Game.buttons.replay,
                Game.buttons.exit
            ]);

        },

        checkFieldSeaBattle: function(newship, id) {
            var size = $('.SeaBattle .mx .table:eq(1) .cell').last().attr('data-coor').split('x');
            var size_x = size[0];
            var size_y = size[1];

            matrix = [
                [-1, -1],
                [-1, 0],
                [-1, 1],
                [0, -1],
                [0, 0],
                [0, 1],
                [1, -1],
                [1, 0],
                [1, 1]
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


                    $.each(matrix, function(i, v) {
                        // console.error(">>>>> i v >>>>", i, v);
                        // console.error(">>>>> x y >>>>", i, v);

                        if (y + v[0] > 0 && y + v[0] <= size_y && x + v[1] > 0 && x + v[1] <= size_x)
                        // console.error('field[y + v[0]][x + v[1]] >>>>field['+ y + "" + v[0] + "][" + x + "" + v[1] + "]");
                        // console.error('ship', ship);
                            if (field[y + v[0]][x + v[1]]) {
                            ret = true;
                        }
                    });

                    if (ret)
                        return false;
                    ship.push([x, y]);
                    h ? x++ : y++;
                }

                $.each(ship, function(i, cell) {
                    field[cell[1]][cell[0]] = 1;
                });

                iterration++;

            }
            return true;
        },

        genFieldSeaBattle: function() {

            Apps.SeaBattle.ships = [];

            var size = $('.SeaBattle .table:eq(1) .cell').last().attr('data-coor').split('x'),
                size_x = size[0],
                size_y = size[1],
                matrix = [
                    [-1, -1],
                    [-1, 0],
                    [-1, 1],
                    [0, -1],
                    [0, 0],
                    [0, 1],
                    [1, -1],
                    [1, 0],
                    [1, 1]
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

                    $.each(matrix, function(i, v) {
                        if (y + v[0] > 0 && y + v[0] <= size_y && x + v[1] > 0 && x + v[1] <= size_x)
                            if (field[y + v[0]][x + v[1]])
                                con = true;
                    });

                    if (con) continue loop;
                    ship.push([x, y]);
                    h ? x++ : y++;
                }

                $.each(ship, function(i, cell) {
                    field[cell[1]][cell[0]] = 1;
                });

                Apps.SeaBattle.ships.push([ship[0], h, l]);
                iterration++;

            }



            // var wid = parseFloat($('.SeaBattle .table.m .inner').last().css('width')),
            //     hei = parseFloat($('.SeaBattle .table.m .inner').last().css('height')),
            var wid = parseFloat($('.SeaBattle .table.m .inner').last().css('width')),
                hei = parseFloat($('.SeaBattle .table.m .inner').last().css('height')),
                html = '';

            $.each(Apps.SeaBattle.ships, function(index, ship) {
                html += '<div data-id="' + index + '" ' +
                    'style="' +
                    'top:' + (ship[0][1] * hei - hei) + 'px;' +
                    'left:' + (ship[0][0] * wid - wid) + 'px;' + (ship[1] ? 'width: ' + (ship[2] * wid) + 'px;height:' + hei + 'px;' : 'height: ' + (ship[2] * hei) + 'px;width:' + wid + 'px;') + '" class="s ' + (ship[1] ? 'h' : '') + ' drag"></div>';
            });

            // $(".SeaBattle ul.m div").remove();
            // $(".SeaBattle ul.m").append(html);
            $(".SeaBattle .table.m .drag-wrapper").remove();
            $(".SeaBattle .table.m").append('<div class="drag-wrapper">' + html + '</div>');

            $(".drag").dblclick(function() {
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
                    $(this).effect("shake", { distance: 5, times: 1, duration: 2 });
                    window.setTimeout(function() {
                        drag.addClass('drag ui-draggable ui-draggable-handle');
                    }, 1000);
                }

            });

            $(".drag").draggable({

                containment: "parent",
                grid: [wid, hei],
                revert: function() {

                    var ship = [].concat(Apps.SeaBattle.ships[$(this).data('id')]);
                    ship[0] = [
                        (parseInt($(this).css('left')) + (wid)) / (wid),
                        (parseInt($(this).css('top')) + (hei)) / (hei)
                    ];

                    if (Apps.SeaBattle.checkFieldSeaBattle(ship, $(this).data('id'))) {
                        Apps.SeaBattle.ships[$(this).data('id')][0] = [
                            (parseInt($(this).css('left')) + (wid)) / (wid),
                            (parseInt($(this).css('top')) + (hei)) / (hei)
                        ];
                        return false
                    } else return true;
                },

                start: function() {},
                stop: function() {


                }
            });
        }

    }


})();