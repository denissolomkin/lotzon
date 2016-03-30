(function () {


    Apps.FiveLine = {

        'action': {

            default: function () {

                if (Game.hasField()) {
                    Game.run() && Apps.FiveLine.drawField();
                    Game.drawStatuses();
                    Game.drawButtons();
                    Game.initTimers();
                    Apps.FiveLine.paintCell();
                    Game.end() && Apps.FiveLine.end();
                }
            }
        },

        'drawField': function () {

            var field = Game.field.getElementsByClassName('mx')[0];

            field.innerHTML +=
                '<div class="wrapper">' +
                    '<div class="table"></div>' +
                '</div>';

            if (App.variation && App.variation.field) {

                $('.mx .table').html('<div></div>');

                var size = App.variation.field.split('x'),
                    html = '';

                for (i = 1; i <= parseInt(size[1]); i++) {
                    html += '<div class="vw vh vf clearfix">';
                    for (j = 1; j <= parseInt(size[0]); j++) {
                        html += '<div>' +
                            '<div class = "inner" >' +
                            '<div class="cell" data-cell="' + j + "x" + i + '"></div>' +
                            '</div>' +
                            '</div>';
                    }
                    html += '</div>';
                }
                $('.mx .table').html(html);

            } else
                console.error('Empty variation field');

            if (App.field)
                $.each(App.field, function (x, cells) {
                    $.each(cells, function (y, cell) {
                        Apps.FiveLine.paintCell(cell);
                    });
                });
            else
                console.error('Empty field');

        },

        'paintCell': function (cell) {

            cell = cell || App.cell;

            if (cell) {

                var class_cell = (cell.player == Player.id ? 'm' : 'o'),
                    $cell = $('.mx [data-cell="' + cell.coord + '"]');

                $('.mx .' + class_cell + '.last')
                    .removeClass('last');

                $cell
                    .addClass(class_cell+' last')
                    .html('<div style="display:none;"></div>')
                    .find('div').fadeIn(200);

                if (!arguments.length) {
                    Apps.playAudio([App.key, 'Move-' + class_cell + '-1']);
                }

                return true;

            } else
                console.error('Empty cell');

            return false;
        },

        'end': function () {

            if(App.line) {
                $.each(App.line, function (x, cells) {
                    $.each(cells, function (y, cell) {
                        $('.mx [data-cell="' + x + 'x' + y + '"]')
                            .addClass('w')
                            .addClass('last');
                    });
                });
            }

            Game.drawWinButtons([
                Game.buttons.replay,
                Game.buttons.exit
            ]);

        }

    }

})();