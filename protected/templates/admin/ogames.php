<div class="modal fade ogames" id="editGame" role="dialog" aria-labelledby="confirmLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="confirmLabel"><span>Редактирование игры</span>
                    <span style="float: right;margin-bottom: 10px;">
                <button type="button" class="btn btn-md btn-success tab" data-tab="text">
                    <span class="glyphicon glyphicon-font"></span>
                </button>
                <button type="button" class="btn btn-md btn-success tab" data-tab="image">
                    <span class="glyphicon glyphicon-picture"></span>
                </button>
                <button type="button" class="btn btn-md btn-success tab" data-tab="audio">
                    <span class="fa fa-volume-up"></span>
                </button>
                <button type="button" class="btn btn-md btn-success tab" data-tab="field">
                    <span class="glyphicon glyphicon-cog"></span>
                </button>
                <button type="button" class="btn btn-md btn-success tab" data-tab="variations">
                    <span class="fa fa-sliders"></span>
                </button>
                <button type="button" class="btn btn-md btn-success tab" data-tab="prizes">
                    <span class="glyphicon glyphicon-gift"></span>
                </button>
                </span>
                </h3>
            </div>
            <div class="modal-body">
                <div class="row" style="text-align: center;">
                    <form class="form-inline" role="form" data-game="new" onsubmit="return false;">

                    <div class="row-fluid tab" id="text">
                        <div class="row-fluid title">
                            <div class="form-group">
                                <label class="sr-only">Название</label>
                                <? foreach ($langs as $lang) { ?>
                                <input type="text" class="form-control mui" name="game[Title][<?=$lang->getCode()?>]" placeholder="Название игры" value="">
                                <? } ?>
                                <input type="hidden" name="game[Id]" value="0">
                            </div>
                            <div class="form-group">
                                <input type="checkbox" name='game[Enabled]' data-toggle="toggle">
                            </div>
                        </div>
                        <div class="row-fluid description">
                            <? foreach ($langs as $lang) { ?>
                            <textarea class="form-control mui" rows=5 name="game[Description][<?=$lang->getCode()?>]" placeholder="Описание игры"></textarea>
                            <? } ?>
                        </div>
                        <div class="row-fluid banner">
                            <? foreach ($langs as $lang) { ?>
                            <button type="button" class="btn btn-md lang btn-default" data-lang="<?=$lang->getCode()?>"><?=strtoupper($lang->getCode())?></button>
                        <? } ?>
                        </div>
                        <!--div class="row-fluid banner">
                            <textarea class="form-control" name="game[Banner]" placeholder="Баннер"></textarea>
                        </div-->
                    </div>

                        <div class="row-fluid tab" id="key">
                            <input type="text" name='game[Key]' class="form-control k" placeholder="KeyName">
                        </div>

                        <div class="row-fluid tab" id="image">
                            <img class="i">
                        </div>

                        <div class="row-fluid tab" id="variations">

                            <div class="row-fluid">
                                <button class="btn btn-md btn-primary add-variation-trigger" data-inherit="true">Вариация игры &nbsp;<i class="fa fa-plus-circle"></i></button>
                            </div>

                            <div class="row-fluid">
                                <? foreach ($langs as $lang) { ?>
                                    <button type="button" class="btn btn-md lang btn-default" data-lang="<?=$lang->getCode()?>"><?=strtoupper($lang->getCode())?></button>
                                <? } ?>
                            </div>
                        </div>

                        <div class="row-fluid tab" id="audio">
                            <? $audio=array('start','stack','field','timeout','ready','wait','Win','Lose','Move-m-1','Move-o-1','Move-m-2','Move-o-2','Move-m-3','Move-o-3');
                            while($key= each ($audio)) {
                                $key=array_shift($key);?>
                                <div class="col-lg-6">
                                    <div class="input-group">
                                        <span class="input-group-addon"><?=$key;?></span>
                                        <input type="text" class="form-control" name="game[Audio][<?=$key;?>]" value="/tpl/audio/complete.ogg">
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-default audio-play"><i class="fa fa-play-circle"></i></button>
                                            <button type="button" class="btn btn-default audio-refresh"><i class="fa fa-refresh"></i></button>
                                            <button type="button" class="btn btn-danger audio-remove"><i class="fa fa-remove"></i></button>
                                        </div>
                                    </div><!-- /.input-group -->
                                </div>
                            <? } ?>
                        </div>


                        <div class="row-fluid tab" id="field">
                                <div class="row-fluid field">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-users"></i></span>
                                        <input class="form-control s" type="text" name="game[Field][s]" value="1" placeholder="Мин. стек" value="">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                        <input class="form-control p" type="text" name="game[Field][p]" value="1" placeholder="Макс. игроков" value="">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                                        <input class="form-control t" type="text" name="game[Field][t]" value="30" placeholder="Таймаут" value="">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-paw"></i></span>
                                        <input class="form-control m" type="text" name="game[Field][m]" value="6" placeholder="Лимит ходов" value="">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-arrows-h"></i></span>
                                        <input class="form-control x" type="text" name="game[Field][x]"  value="6" placeholder="Горизонталь" value="">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-arrows-v"></i></span>
                                        <input class="form-control y" type="text" name="game[Field][y]"  value="1" placeholder="Вертикаль" value="">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-trophy"></i></span>
                                        <input class="form-control w" type="text" name="game[Field][w]"  value="1" placeholder="Очки для досрочной победы" value="">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-star-half"></i></span>
                                        <input class="form-control h" type="text" name="game[Field][h]"  value="1" placeholder="Комиссия на выигрыш" value="">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-gift"></i></span>
                                        <input class="form-control r" type="text" name="game[Field][r]"  value="1" placeholder="Комиссия в фонд" value="">
                                    </div>
                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-eye"></i></span>
                                        <input type="checkbox" name='game[Field][v]' data-toggle="toggle">
                                    </div>
                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-flag-checkered"></i></span>
                                        <input type="checkbox" name='game[Field][f]' data-toggle="toggle">
                                    </div>
                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-plug"></i></span>
                                        <input type="checkbox" name='game[Field][b]' data-toggle="toggle">
                                    </div>
                                </div>

                                <div class="row-fluid ships">
                                    Корабли: <button class="btn btn-md btn-success add-ship" ><i class="fa fa-plus-circle"></i></button>
                                    <ul></ul>
                                </div>
                            </div>

                        <div class="row-fluid tab" id="prizes">
                                <div class="form-group holder">
                                    <div class="input-group">
                                        <label class="sr-only"></label>
                                        <button class="btn btn-md btn-primary add-trigger" data-type="MONEY"><i class="fa fa-money"></i> Деньги &nbsp;<i class="fa fa-plus-circle"></i></button>
                                    </div>
                                    <div class="row-fluid MONEY-holder">
                                    </div>
                                </div>

                                <div class="form-group holder">
                                    <div class="input-group">
                                        <label class="sr-only"></label>
                                        <button class="btn btn-md btn-primary add-trigger" data-type="POINT"><i class="fa fa-diamond"></i> Баллы &nbsp;<i class="fa fa-plus-circle"></i></button>
                                    </div>
                                    <div class="row-fluid POINT-holder">
                                    </div>
                                </div>

                            </div>
                    </div>
                    </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-md btn-success save-game"> Сохранить</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade ogames" id="price-modal" role="dialog" aria-labelledby="confirmLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Изменение ставки для игры</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="prize">
                        <input type="hidden" name="v" placeholder="Значение ставки" value="">
                        <span class="input-group-addon"><i class=""></i></span>
                        <input type="text" class="form-control v" name="v" placeholder="Значение ставки" value="">
                        <span class="input-group-addon"><i class="fa fa-laptop"></i></span>
                        <input type="text" class="form-control p" name="p" placeholder="Вероятность удачного хода бота" value="">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success add">Добавить</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade ogames" id="variation-modal" role="dialog" aria-labelledby="confirmLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Добавление вариации игры</h4>
            </div>
            <div class="modal-body">

                <div class="row-fluid">

                    <div class="row-fluid">
                        <input type="text" class="form-control" name="key" value="" placeholder="Ключ">
                    </div>
                    <div class="row-fluid">
                        <button class="btn btn-md btn-primary next-variation-trigger">Далее &nbsp;<i class="fa fa-arrow-circle-right"></i></button>
                    </div>

                </div>

                <div class="row-fluid">

                    <div class="row-fluid">
                    </div>
                    <div class="row-fluid">
                        <? foreach ($langs as $lang) { ?>
                            <button type="button" class="btn btn-md lang btn-default" data-lang="<?=$lang->getCode()?>"><?=strtoupper($lang->getCode())?></button>
                        <? } ?>
                    </div>
                    <div class="row-fluid">
                        <button type="button" class="btn btn-success save-variation-trigger">Добавить</button>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid ogames">
    <div class="row-fluid">
        <h2>Онлайн-игры
            <button class="btn btn-md btn-success add-game"> Добавить</button></h2>
        <hr />
    </div>

    <div class="game-builds">
    </div>

</div>

<script>

    $(function() {

        $('#field .ships .add-ship').on('click',function() {
            $('#field .ships ul').first().append('<li class="ship"><i class="fa fa-minus"></i><ul></ul><i class="fa fa-plus"></i><input name="game[Field][ships][]" type=hidden value=1></li>').find('input').trigger('change');
        });

        $(document).on('click','#field .ships .fa-minus',function() {
            ship=$(this).parent().find('input');
            parseInt(ship.val())==1 && $(this).parent().remove() || ship.val(parseInt(ship.val())-1).trigger('change');

        });

        $(document).on('click','#field .ships .fa-plus',function() {
            ship=$(this).parent().find('input');
            ship.val(parseInt(ship.val())+1).trigger('change');
        });

        $(document).on('change','#field .ships input',function() {
            $(this).parent().find('ul li').remove();
            html=new Array(parseInt($(this).val())+1).join('<li></li>');
            $(this).parent().find('ul').html(html);

            $('#field .ships li').sortElements(function(a, b){
                return parseFloat($('input', a).val()) > parseFloat($('input', b).val()) ? 1 : -1;
            });
        });

        games=<?
        foreach ($games as $game)
            $list[$game->getId()]=array(
            'Id'            => $game->getId(),
            'Key'           => $game->getKey(),
            'Title'         => $game->getTitle(),
            'Description'   => $game->getDescription(),
            'Field'         => $game->getOptions(),
            'Prizes'        => $game->getModes(),
            'Audio'         => $game->getAudio(),
            'Enabled'       => $game->isEnabled()
            );
        echo json_encode($list, JSON_PRETTY_PRINT)?>;

        $('.add-game').on('click', function() {
            var game = {Id:0,Title:'',Key:'',Description:'',Field:{x:6,y:1,b:1,r:1,w:95,h:95,c:1}};
            editGame(game);
        });

        $(document).on('click','.game-build', function() {
            editGame(games[$(this).data('id')]);
        });


        $.each(games, function(index, game) {
            buildGame(game);
        });

        function editGame(game) {
            $('#editGame').modal().find('button.tab').removeClass('active').first().addClass('active');
            $('#editGame').find('div.tab').hide().first().show();
            $('#editGame').find('h3 span').first().text($.isPlainObject(game.Title) ? game.Title[Object.keys(game.Title)[0]] : 'Новая игра');


            holder = $("#editGame").find('form');
            holder.find('.lang').first().click();
            holder.find('.x').val(game.Field.x);
            holder.find('.y').val(game.Field.y);
            holder.find('.t').val(game.Field.t);
            holder.find('.w').val(game.Field.w);
            holder.find('.s').val(game.Field.s);
            holder.find('.p').val(game.Field.p);
            holder.find('.m').val(game.Field.m);
            holder.find('.h').val(game.Field.h);
            holder.find('.r').val(game.Field.r);
            holder.find('[name="game[Field][b]"]').bootstrapToggle({
                on: 'Enabled',
                off: 'Disabled'
            }).bootstrapToggle((game.Field.b == 1 || game.Field.b == 'on' ? 'on' : 'off'));
            holder.find('[name="game[Field][f]"]').bootstrapToggle({
                on: 'Enabled',
                off: 'Disabled'
            }).bootstrapToggle((game.Field.f == 1 || game.Field.f == 'on' ? 'on' : 'off'));
            holder.find('[name="game[Field][v]"]').bootstrapToggle({
                on: 'Enabled',
                off: 'Disabled'
            }).bootstrapToggle((game.Field.v == 1 || game.Field.v == 'on' ? 'on' : 'off'));

            if (game.Key) {
                $('#editGame button[data-tab="text"]').next().attr('data-tab', 'image');
                holder.find('.k').val(game.Key);
                holder.find('.i').attr('src', 'http://<?=$_SERVER['SERVER_NAME']?>/tpl/img/games/' + game.Key + ".png?" + (new Date().getTime()));
            } else {
                $('#editGame button[data-tab="text"]').next().attr('data-tab', 'key');
                holder.find('.k').val('');
            }

            holder.find('[name="game[Enabled]"]').bootstrapToggle((game.Enabled == 1 || game.Enabled == 'on' ? 'on' : 'off'));
            holder.find('[name="game[Id]"]').val(game.Id);
            holder.find('[name^="game[Title]"], [name^="game[Description]"]').val('');
            $.isPlainObject(game.Title) && $.each(game.Title, function (lang, text) {
                holder.find('[name="game[Title][' + lang + ']"]').val(text);
            });
            $.isPlainObject(game.Description) && $.each(game.Description, function (lang, text) {
                holder.find('[name="game[Description][' + lang + ']"]').val(text);
            });


            holder.find('#audio input').val('');

            if (game.Audio) {
                $.each(game.Audio, function (i, f) {
                    holder.find('#audio input[name="game[Audio][' + i + ']"]').val(f);
                });
            }

            holder.find('#field .ships').hide();
            holder.find('#field .ships ul li').remove();


            if (game.Key == 'SeaBattle') {

                holder.find('#field .ships').show();
                if (game.Field.ships)
                    $.each(game.Field.ships, function (i, ship) {
                        holder.find('#ships ul').first.append('<li data-ship="' + ship + '"><i class="fa fa-minus"><ul><ul><i class="fa fa-plus"> <input name="game[Field][ships][]" value=' + ship + '></li>').val(ship);
                    });
            }

            holder.find('.prize').remove();
            if (game.Prizes) {
                $.each(game.Prizes, function (index, t) {
                    var button = holder.find('.' + index + '-holder').prev();
                    $.each(t, function (v, p) {
                        console.log(v);

                        var html = $('<div class="prize row-fluid" id="' + index + '-' + v + '">' +
                        '<input type="hidden" name="game[Prizes][' + index + '][' + v + ']" value="' + p + '" >' +
                        '<div class="input-group">' +
                        '<span class="input-group-addon"><i class="' + $('i', button).first().attr('class') + '"></i></span>' +
                        '<input type="text" class="form-control v" value="' + v + '" readonly>' +
                        '</div>' +
                        '<div class="input-group">' +
                        '<span class="input-group-addon"><i class="fa fa-laptop"></i></span>' +
                        '<input type="text" class="form-control p" value="' + p + '" readonly>' +
                        '</div>' +
                        '<span class="btn btn-danger remove"><i class="fa fa-remove"></i></span>' +
                        '</div>');

                        holder.find('.' + index + '-holder').append(html);
                    });

                    holder.find('.' + index + '-holder').find('div.prize').sort(function (a, b) {
                        return parseFloat($('.v', a).val()) > parseFloat($('.v', b).val()) ? 1 : -1;
                    }).appendTo(holder.find('.' + index + '-holder').find('div.prize').parent());

                });

            }


            holder.find('.variation').remove();
            if (game.Field.Variations) {
                html = '';
                $.each(game.Field.Variations, function (index, variation) {

                    html += '<div class="variation"><div class="row-fluid"><button class="btn btn-md btn-danger delete-variation-trigger"><i class="fa fa-times-circle"></i></button>';

                    $('#variations .lang', '').each(function (i, lang) {
                        html +='<input type="text" class="form-control mui" name="game[Field][Variations][' + index + '][t]['+$(lang).data('lang')+']" placeholder="Название" value="'+variation.t[$(lang).data('lang')]+'">'
                    });

                    html +='<button class="btn btn-md btn-primary add-variation-trigger" data-key="'+index+'"> <i class="fa fa-plus-circle"></i></button>';

                    if(variation.v) {
                        $.each(variation.v, function (key, value) {
                            html += '<div class="variation"><div class="row-fluid"><button class="btn btn-md btn-danger delete-variation-trigger"><i class="fa fa-times-circle"></i></button>';

                            $('#variations .lang', '').each(function (i, lang) {
                                html += '<input type="text" class="form-control mui" name="game[Field][Variations][' + index + '][v][' + key + '][t][' + $(lang).data('lang') + ']" placeholder="Название" value="' + value.t[$(lang).data('lang')] + '">';
                            });

                            html += '<button class="btn btn-md btn-' + (variation.d && variation.d == key ? 'success' : 'default') + ' default-variation-trigger" data-key="' + key + '" data-variation="' + index + '"><i class="fa fa-check-circle"></i></button>';

                            if (variation.d && variation.d == key)
                                html += '<input type="hidden" name="game[Field][Variations][' + index + '][d]" value="' + key + '">';

                            html += '</div></div>';
                        });

                    }

                    html+='</div></div>';

                });

                $(html).insertAfter($('.add-variation-trigger').first());

                $('#variations .lang').first().click();

            }
        }

        function buildGame(game) {
            if (!$('.game-build[data-id="' + game.Id + '"]').length) {
                $('.game-builds').prepend($('<div class="game-build" data-id="' + game.Id + '">' +
                '<div class="t"></div>' +
                '<div class="d"></div>' +
                '<div class="o"></div>' +
                '<img>' +
                '</div>'));
            }
            holder = $('.game-build[data-id="' + game.Id + '"]');
            if (game.Enabled == 1 || game.Enabled == 'on')
                holder.removeClass('disabled');
            else
                holder.addClass('disabled');

            holder.find('.t').text(game.Title.<?=$defaultLang;?>)
                .next()//.html(nl2br(game.Description.<?=$defaultLang;?>))
                .next().html('<i class="fa fa-users"></i>' + game.Field.s +
                ' <i class="fa fa-user"></i>' + game.Field.p +
                ' <i class="fa fa-clock-o"></i>' + game.Field.t +
                ' <i class="fa fa-paw"></i>' + game.Field.m +
                ' <i class="fa fa-arrows-h"></i>' + game.Field.x +
                ' <i class="fa fa-arrows-v"></i>' + game.Field.y +
                ' <i class="fa fa-trophy"></i>' + game.Field.w +
                ' <i class="fa fa-star-half"></i>' + (game.Field.h?game.Field.h:0) + '%' +
                ' <i class="fa fa-gift"></i>' + (game.Field.r?game.Field.r:0) + '%' +
                '<br>' +
                (game.Field.b ? '<i class="fa fa-plug"></i>' : '<span class="fa-stack fa-lg"><i class="fa fa-plug fa-stack-1x"></i><i class="fa fa-ban fa-stack-2x text-danger"></i></span>')+
                (game.Field.f ? '<i class="fa fa-flag-checkered"></i>' : '<span class="fa-stack fa-lg"><i class="fa fa-flag-checkered fa-stack-1x"></i><i class="fa fa-ban fa-stack-2x text-danger"></i></span>')+
                (game.Field.v ? '<i class="fa fa-eye"></i>' : '<span class="fa-stack fa-lg"><i class="fa fa-eye fa-stack-1x"></i><i class="fa fa-ban fa-stack-2x text-danger"></i></span>')
            );
            holder.find('img').attr('src', 'http://<?=$_SERVER['SERVER_NAME']?>/tpl/img/games/' + game.Key + ".png?" + (new Date().getTime()));

        };


        $('.add-trigger').on('click', function() {

            var holder = $("#price-modal");
            var button = $(this);

            holder.modal();

            $('input', holder).val('');
            $('i', holder).first().removeAttr('class').addClass($('i', this).first().attr('class'));

            holder.find('.cls').off('click').on('click', function () {
                holder.modal('hide');
            });

            holder.find('.add').off('click').on('click', function () {
                if(!$('.v', holder).val() || $('.v', holder).val()<0){
                    alert('Выберите ставку!');
                    return false;
                }

                id=button.data('type') + '-'+ holder.find('.v').val().replace('.','\\.');
                price=button.parent().next().find('#'+id);
                if(!price.length) {
                    var img = $('<div class="prize row-fluid" id="' + button.data('type') + '-' + holder.find('.v').val() + '">' +
                    '<input type="hidden" name="game[Prizes][' + button.data('type') + '][' + parseFloat(holder.find('.v').val()) + ']" value="' + holder.find('.p').val() + '" >' +
                    '<div class="input-group">' +
                    '<span class="input-group-addon"><i class="' + $('i', button).first().attr('class') + '"></i></span>' +
                    '<input type="text" class="form-control v" value="' + parseFloat(holder.find('.v').val()) + '" readonly>' +
                    '</div>' +
                    '<div class="input-group">' +
                    '<span class="input-group-addon"><i class="fa fa-plug"></i></span>' +
                    '<input type="text" class="form-control p" value="' + holder.find('.p').val() + '" readonly>' +
                    '</div>' +
                    '<span class="btn btn-danger remove"><i class="fa fa-remove"></i></span>' +
                    '</div>');
                    button.parent().next().append(img);
                } else {
                    price.find('input').first().val(holder.find('.p').val());
                    price.find('input.p').first().val(holder.find('.p').val());
                }

                button.parent().next().find('div.prize').sort(function(a, b){
                    return parseFloat($('.v', a).val()) > parseFloat($('.v', b).val()) ? 1 : -1;
                }).appendTo( button.parent().next().find('div.prize').parent());

                holder.modal('hide');

            });
            return false;
        });

        $(document).on('click','.delete-variation-trigger',function() {
            $(this).closest('.variation').remove();
        });

        $(document).on('click','.default-variation-trigger',function() {

            var button = $(this);
            var holder = button.parents('.variation').find('.add-variation-trigger').parent(); //button.parent('.variation .variation');
            $('[name$="[d]"]', holder).remove();
            $('.default-variation-trigger',holder).removeClass('btn-success').addClass('btn-default');
            button.addClass('btn-success');
            $('<input type=hidden name="game[Field][Variations]['+button.attr('data-variation')+'][d]" value="'+button.attr('data-key')+'">').insertAfter(button);
        });

        $(document).on('click', '.add-variation-trigger', function() {

            var holder = $("#variation-modal");
            var button = $(this);
            var variation = button.attr('data-key') ? button.attr('data-key') : null;

            holder.modal();

            $('.modal-body > .row-fluid',holder).hide().first().show().find('input').val('');

            holder.find('.next-variation-trigger').off('click').on('click', function () {

                if( key = $('.modal-body > .row-fluid input',holder).first().val()) {
                    $('.modal-body > .row-fluid:eq(1) > .row-fluid', holder).first().html('');
                    $('.lang', holder).each(function (index, value) {
                        $('.modal-body > .row-fluid:eq(1) > .row-fluid', holder).first().append($(
                            '<input type="text" class="form-control mui" name="game[Field][Variations]' + (variation ? '['+variation+'][v]' : '') + '[' + key + '][t]['+$(value).data('lang')+']" placeholder="Название" value="">'
                        ));
                        $('.lang', holder).first().click();

                    });


                    $('.modal-body > .row-fluid', holder).toggle();
                }
            });

            holder.find('.save-variation-trigger').off('click').on('click', function () {

                html = $('.modal-body > .row-fluid:eq(1) > .row-fluid', holder).first().clone();
                html.prepend('<button class="btn btn-md btn-danger delete-variation-trigger"><i class="fa fa-times-circle"></i></button>');
                html.append( variation
                    ? '<button class="btn btn-md btn-default default-variation-trigger" data-key="'+key+'" data-variation="'+variation+'"><i class="fa fa-check-circle"></i></button>'
                    : '<button class="btn btn-md btn-primary add-variation-trigger" data-key="'+(button.data('inherit')?key:variation)+'"> <i class="fa fa-plus-circle"></i></button>');

                html.insertAfter(button).wrap("<div class='variation'></div>");

                holder.modal('hide');

            });

            holder.find('.cls').off('click').on('click', function () {
                holder.modal('hide');
            });

            return false;
        });

  $('.save-game').on('click', function() {

    var button = $(this);
    var form = $(this).parent().parent().find('form');

    $.ajax({
        url: "/private/ogames",
        method: 'POST',
        data: form.serialize(),
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                $("#editGame").modal('hide');
                console.log(form.serializeObject());
                post=form.serializeObject();
                game=post.game;
                game.Id=data.data.Id;
                games[data.data.Id]=game;
                buildGame(games[data.data.Id]);
            } else {
                button.prepend($('<i class="glyphicon glyphicon-exclamation-sign"></i>')).addClass('btn-danger');
                window.setTimeout(function () {button.removeClass('btn-danger').find('i').fadeOut(200);},1000);
                alert(data.message);
            }
        }, 
        error: function() {
            button.prepend($('<i class="glyphicon glyphicon-exclamation-sign"></i>')).addClass('btn-danger');
            window.setTimeout(function () {button.removeClass('btn-danger').find('i').fadeOut(200);},1000);
            alert('Unexpected server error');
        }
    });
    return false;
  });


    });
</script>

<? if($frontend)
    require_once(PATH_TEMPLATES.$frontend);?>