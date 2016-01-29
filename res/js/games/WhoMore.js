(function () {

    WhoMore = {

        'run': function () {

            WhoMore.drawField();

        },

        'error': function () {

        },

        'action': function () {

            if (!document.getElementById('games-online-field')) {

                R.push({
                    'template': 'games-online-field',
                    'json'    : {},
                    'url'     : false,
                    'after'   : WhoMore.action
                });

            } else {

                Game.run() && WhoMore.run();
                WhoMore.drawStatuses();
                WhoMore.drawTimer();
                WhoMore.paintCell();
                Game.end() && Durak.end();

            }

        },

        'drawStatuses': function () {

            if (App.extra) {
                var equal = $('.ngm-bk .msg.equal');
                equal.fadeIn(200);
                window.setTimeout(function () {
                    equal.fadeOut(200);
                }, 2000);
            }

            if (App.players)
                $.each(App.players, function (index, value) {
                    var class_player = value.pid == Player.id ? 'l' : 'r';
                    $('.gm-pr.' + class_player + ' .pr-cl b').html(value.moves).hide().fadeIn(200);
                    $('.gm-pr.' + class_player + ' .pr-pt b').html(value.points).hide().fadeIn(200);
                });
            else
                alert('Empty players');


        },

        'drawTimer': function () {

            if (App.players)
                $.each(App.players, function (index, value) {
                    Game.playerTimer
                        .remove(index);

                    if(value.pid == App.current)
                        Game.playerTimer
                            .add();
                });
            else
                alert('Empty players');

        },

        'drawField': function () {

            $('.mx .table').html('<ul></ul>');

            if (App.variation && App.variation.field) {

                var size = parseInt(App.variation.field),
                    width = Math.floor((480 - (size - 1) * 5) / size) + 'px;',
                    height = Math.floor((480 - size * 5) / size) + 'px;',
                    font = ((480 - size * 5) / 1.6 / size ) + 'px/' + ((480 - (size * 5)) / size) + 'px Handbook-bold;',
                    html = '';

                for (i = 1; i <= size; i++)
                    for (j = 1; j <= size; j++)
                        html += "<li style='width:" + width + "height:" + height + "font:" + font + (j == size ? "margin-right: 0px;" : "") + "' data-cell='" + j + "x" + i + "'></li>";

                $('.mx .table ul').html(html);

            } else
                alert('Empty variation field');

            if(App.field)
                $.each(App.field, function (x, cells) {
                    $.each(cells, function (y, cell) {
                        WhoMore.paintCell(cell);
                    });
                });
            else
                alert('Empty field');

        },

        'paintCell': function (cell) {

            cell = cell || App.cell;

            if(cell) {
                var class_cell = (cell.player == Player.id ? 'm' : 'o'),
                    $cell = $('.mx li[data-cell="' + cell.coord + '"]');

                $('.mx .table ul li.' + class_cell + '.last')
                    .removeClass('last');

                if (!arguments.length) {
                    Apps.playAudio([App.key, 'Move-' + class_cell + '-1']);
                    $cell
                        .html('<div style="background:' + $cell.css('background') + ';width:' + $cell.css('width') + ';height:' + $cell.css('height') + ';"></div>')
                        .find('div').toggle('explode', {pieces: 4}, 500).parent();
                }

                $cell
                    .html(cell.points)
                    .addClass((isNumeric(class_cell) ? 's' : class_cell) + ' last')
                    .fadeIn(100);

                return true;

            } else
                alert('Empty cell');

            return false;
        }

    }

})();