(function() {


    Apps.WhoMore = {

        'run': function() {

            Apps.WhoMore.drawField();

        },

        'error': function() {

        },

        'action': function() {

            if (Game.field()) {
                Game.run() && Apps.WhoMore.run();
                Apps.WhoMore.drawStatuses();
                Apps.WhoMore.drawTimer();
                Apps.WhoMore.paintCell();
                Game.end() && Apps.WhoMore.end();
            }
        },

        'drawStatuses': function() {

            $('.mx .players .mt').hide();

            if (App.extra) {
                var equal = $('.ngm-bk .msg.equal');
                equal.fadeIn(200);
                window.setTimeout(function() {
                    equal.fadeOut(200);
                }, 2000);
            }

            if (App.players) {
                for (var index in App.players) {
                    if (App.players.hasOwnProperty(index)) {
                        if(App.players[index].moves || App.players[index].points)
                            $('.mx .players .player' + index + ' .wt')
                                .show()
                                .html('Ходов: ' + App.players[index].moves +
                                    '<br>Очков: ' + App.players[index].points);
                    }
                }
            } else
                alert('Empty players in drawStatuses');


        },

        'drawTimer': function() {

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
                            '<div class="cell" data-cell="' + j + "x" + i + '"></div>' +
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

            $('.btn-secondary')
                .removeClass('hidden')
                .attr('data-action','replay')
                .text(i18n('button-game-replay'));

            $('.btn-primary')
                .removeClass('hidden')
                .addClass('btn-start')
                .attr('data-action','quit')
                .text(i18n('button-game-new'));
        }

    }

})();