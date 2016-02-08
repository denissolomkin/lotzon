(function() {


    Apps.WhoMore = {

        'action': function() {

            if (Game.hasField()) {
                Game.run() && Apps.WhoMore.drawField();
                Apps.WhoMore.initStatuses();
                Apps.WhoMore.initTimers();
                Apps.WhoMore.paintCell();
                Game.end() && Apps.WhoMore.end();
            }
        },

        'initStatuses': function() {

            if (App.extra) {
                var equal = $('.ngm-bk .msg.equal');
                equal.fadeIn(200);
                window.setTimeout(function() {
                    equal.fadeOut(200);
                }, 2000);
            }

            Game.drawStatuses();

            if (App.players) {

                var messages = {};

                for (var index in App.players) {
                    if (App.players.hasOwnProperty(index)) {
                        if(App.players[index].moves || App.players[index].points)
                            messages[index] =
                                '<div><span>' + i18n('Ходов осталось ') + '</span><i>' + App.players[index].moves + '</i></div>' +
                                '<div><span>' + i18n('Очков набрано ') + '</span><i>' + App.players[index].points + '</i></div>';
                        }
                }

                Game.drawMessages(messages);

            } else
                alert('Empty players in initStatuses');


        },

        'initTimers': function() {

            if (App.players) {
                for (var index in App.players) {
                    if (App.players.hasOwnProperty(index)) {

                        Game.playerTimer
                            .remove(index);

                        if (App.current && index == App.current)
                            Game.playerTimer
                                .add();
                    }
                }
            } else
                alert('Empty players in drawTimer');

        },

        'drawField': function() {

            var field = Game.field.getElementsByClassName('mx')[0];

            field.innerHTML +=
                '<div class="moves"></div>' +
                '<div class="table"></div>' +
                '<div class="moves"></div>';

            if (App.variation && App.variation.field) {

                $('.mx .table').html('<div></div>');

                var size = parseInt(App.variation.field),
                    //                        width = Math.floor((480 - (size - 1) * 5) / size) + 'px;',
                    //                        height = Math.floor((480 - size * 5) / size) + 'px;',
                    //                        font = ((480 - size * 5) / 1.6 / size) + 'px/' + ((480 - (size * 5)) / size) + 'px Handbook-bold;',
                    html = '';

                for (i = 1; i <= size; i++) {
                    html += '<div class="vw vh vf clearfix">';
                    for (j = 1; j <= size; j++) {
                        html += '<div>' +
                            '<div class = "inner" >' +
                            '<div class="cell" data-cell="' + j + "x" + i + '"><i class="i-question"></i></div>' +
                            '</div>' +
                            '</div>';
                    }
                    html += '</div>';
                }
                $('.mx .table div').html(html);

            } else
                console.error('Empty variation field');

            if (App.field)
                $.each(App.field, function(x, cells) {
                    $.each(cells, function(y, cell) {
                        Apps.WhoMore.paintCell(cell);
                    });
                });
            else
                console.error('Empty field');

        },

        'paintCell': function(cell) {

            cell = cell || App.cell;

            if (cell) {
                var class_cell = (cell.player == Player.id ? 'm' : 'o'),
                    $cell = $('.mx div[data-cell="' + cell.coord + '"]');

                $('.mx .table div.' + class_cell + '.last')
                    .removeClass('last');

                if (!arguments.length) {
                    Apps.playAudio([App.key, 'Move-' + class_cell + '-1']);

                    $cell.html('<div style="background:' + $cell.css('background') + ';width:' + $cell.css('width') + ';height:' + $cell.css('height') + ';"></div>').find('div').toggle('explode', {
                        pieces: 4
                    }, 500).parent();

                }

                $cell
                    .html("<span>" + cell.points + "</span>")
                    .addClass((isNumeric(class_cell) ? 's' : class_cell) + ' last')
                    .fadeIn(100);

                return true;

            } else
                console.error('Empty cell');

            return false;
        },

        'end': function(){

            Game.drawButtons([
                Game.buttons.start,
                Game.buttons.replay
            ]);

        }

    }

})();