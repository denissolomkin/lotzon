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

                var size = parseInt(App.variation.field),
                    width = Math.floor((480 - (size - 1)) / size) + 'px;',
                    height = Math.floor((480 - size) / size) + 'px;',
                    font = ((480 - size) / 1.6 / size ) + 'px/' + ((480 - (size)) / size) + 'px Handbook-bold;',
                    html = '';

                for (i = 1; i <= size; i++)
                    for (j = 1; j <= size; j++)
                        html += "<li style='width:" + width + "height:" + height + "font:" + font + (j == size ? "margin-right: 0px;" : "") + "' data-cell='" + j + "x" + i + "'></li>";

                $('.mx .table').html('<ul>' + html + '</ul>');

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
                    .html('<div style="display:none;"></div>')
                    .find('div').fadeIn(200);

                if (!arguments.length) {
                    Apps.playAudio([App.key, 'Move-' + class_cell + '-1']);
                    $cell.addClass(class_cell+' last').fadeIn(300);
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