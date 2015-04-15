<div class="modal fade qgames" id="editGame" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="confirmLabel"><span>Редактирование игры</span>
                    <span style="right: 10px;position: absolute;">
                <button type="button" class="btn btn-md btn-success tab" data-tab="text">
                    <span class="glyphicon glyphicon-font" aria-hidden="true"></span>
                </button>
                <button type="button" class="btn btn-md btn-success tab" data-tab="image">
                    <span class="glyphicon glyphicon-picture" aria-hidden="true"></span>
                </button>
                <button type="button" class="btn btn-md btn-success tab" data-tab="audio">
                    <span class="fa fa-volume-up" aria-hidden="true"></span>
                </button>
                <button type="button" class="btn btn-md btn-success tab" data-tab="field">
                    <span class="glyphicon glyphicon-th" aria-hidden="true"></span>
                </button>
                <button type="button" class="btn btn-md btn-success tab" data-tab="prizes">
                    <span class="glyphicon glyphicon-gift" aria-hidden="true"></span>
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
                                <input type="text" class="form-control" name="game[Title][<?=$lang->getCode()?>]" placeholder="Название игры" value="">
                                <? } ?>
                                <input type="hidden" class="form-control" name="game[Id]" value="0">
                            </div>
                            <div class="form-group">
                                <input type="checkbox" name='game[Enabled]' data-toggle="toggle">
                           </div>
                        </div>
                        <div class="row-fluid description">
                            <? foreach ($langs as $lang) { ?>
                            <textarea class="form-control" rows=5 name="game[Description][<?=$lang->getCode()?>]" placeholder="Описание игры"></textarea>
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
                        <input class="k" value="">
                    </div>

                        <div class="row-fluid tab" id="image">
                            <img class="i">
                        </div>

                    <div class="row-fluid tab" id="audio">
                        <? $audio=array('start','click','hit','miss','win','lose');
                        while($key= each ($audio)) {
                            $key=array_shift($key);?>
                            <div class="col-lg-6">
                                <div class="input-group">
                                    <span class="input-group-addon"><?=ucfirst($key);?></span>
                                    <input type="text" class="form-control" name="game[Audio][<?=$key;?>]" value="">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default audio-play"><i class="fa fa-play-circle"></i></button>
                                        <button type="button" class="btn btn-default audio-refresh"><i class="fa fa-refresh"></i></button>
                                        <button type="button" class="btn btn-danger audio-remove"><i class="fa fa-remove"></i></button>
                                    </div>
                                </div>
                            </div>
                        <? } ?>
                    </div>

                        <div class="row-fluid tab" id="field">
                                <div class="row-fluid field">
                                    <div id="comb">
                                        <div>
                                            <button class="btn btn-default" data-combination="line">
                                                <ul>
                                                    <li class="fill"></li><li></li><li></li>
                                                    <li></li><li class="fill"></li><li></li>
                                                    <li></li><li></li><li class="fill"></li>
                                                </ul>
                                            </button>

                                            <button class="btn btn-default" data-combination="snake">
                                                <ul>
                                                    <li></li><li></li><li></li>
                                                    <li></li><li class="fill"></li><li class="fill"></li>
                                                    <li></li><li></li><li class="fill"></li>
                                                </ul>
                                            </button>

                                            <button class="btn btn-default" data-combination="random">
                                                <ul>
                                                    <li class="fill"></li><li></li><li></li>
                                                    <li></li><li></li><li class="fill"></li>
                                                    <li></li><li class="fill"></li><li></li>
                                                </ul>
                                            </button>


                                            <button class="btn btn-default" disabled data-combination="square">
                                                <ul>
                                                    <li class="fill"></li><li class="fill"></li><li></li>
                                                    <li class="fill"></li><li class="fill"></li><li></li>
                                                    <li></li><li></li><li></li>
                                                </ul>
                                            </button>


                                            <button class="btn btn-default" disabled data-combination="star">
                                                <ul>
                                                    <li class="fill"></li><li></li><li class="fill"></li>
                                                    <li></li><li class="fill"></li><li></li>
                                                    <li class="fill"></li><li></li><li class="fill"></li>
                                                </ul>
                                            </button>

                                        </div>
                                    </div>

                                    <div>

                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-diamond fa-2x"></i></span>
                                            <input class="form-control p" type="text" name="game[Field][p]" value="" placeholder="Цена" value="">
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-circle-o fa-2x"></i></span>
                                            <input class="form-control c" type="text" name="game[Field][c]"  value="1" placeholder="Ходов" value="">
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-times-circle-o fa-2x"></i></span>
                                            <input class="form-control m" type="text" name="game[Field][m]"  value="1" placeholder="Промахов" value="">
                                        </div>
                                        <div class="input-group" style="margin-left: 11px;">
                                            <span class="input-group-addon"><i class="fa fa-toggle-on fa-2x"></i></span>
                                            <select name="game[Field][e]" class="form-control e" style="width: 127px;" id="effectTypes">
                                                <option value=""></option>
                                                <option value="clip">Clip</option>
                                                <option value="drop">Drop</option>
                                                <option value="puff">Puff</option>
                                                <option value="bounce">Bounce</option>
                                                <option value="pulsate">Pulsate</option>
                                                <!--option value="explode">Explode</option-->
                                                <option value="blind">Blind</option>
                                                <option value="scale">Scale</option>
                                                <option value="highlight">Highlight</option>
                                                <option value="shake">Shake</option>
                                                <option value="size">Size</option>
                                                <option value="slide">Slide</option>
                                                <option value="fold">Fold</option>
                                            </select>
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-clock-o fa-2x"></i></span>
                                            <input class="form-control s" type="text" name="game[Field][s]"  value="1" placeholder="Скорость" value="">
                                        </div>
                                        <br>

                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-long-arrow-right fa-2x"></i></span>
                                            <input class="form-control r" type="text" name="game[Field][r]" value="1" placeholder="Справа" value="">
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-long-arrow-down fa-2x"></i></span>
                                            <input class="form-control b" type="text" name="game[Field][b]" value="1" placeholder="Снизу" value="">
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-arrows-h fa-2x"></i></span>
                                            <input class="form-control w" type="text" name="game[Field][w]" value="95" placeholder="Ширина" value="">
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-arrows-v fa-2x"></i></span>
                                            <input class="form-control h" type="text" name="game[Field][h]" value="95" placeholder="Высота" value="">
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-align-justify fa-rotate-90 fa-2x"></i></span>
                                            <input class="form-control x" type="text" name="game[Field][x]"  value="6" placeholder="По горизонтали" value="">
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-align-justify fa-2x"></i></span>
                                            <input class="form-control y" type="text" name="game[Field][y]"  value="1" placeholder="По вертикали" value="">
                                        </div>
                                            <ul id="field-ul"></ul>
                                        <span id="field-size">1213</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row-fluid tab" id="prizes">


                                <div id="format">
                                    <div>
                                        <button class="btn btn-default" data-format="cell">
                                            <ul>
                                                <li class="points"><div>10</div><div>грн</div></li>
                                                <li class="points"><div>5</div><div>баллов</div></li>
                                                <li class="item"><img alt="Видеорегистратор Highscreen" src="/filestorage/shop/54465c39ba029.jpg" width="100%" height="100%"></li>
                                            </ul>
                                        </button>

                                        <button class="btn btn-default" data-format="hit">
                                            <ul>
                                                <li class="fill"></li><li class="fill"></li><li class="fill"></li>
                                            </ul>
                                        </button>

                                        <button class="btn btn-default" data-format="miss">
                                            <ul>
                                                <li class="fill"></li><li class="fill"></li><li class="los"></li>
                                            </ul>
                                        </button>

                                    </div>
                                    <input value="" name="game[Field][f]" type="hidden">
                                </div>

                                <div id="chance-prizes">
                                    <div class="row-fluid">
                                        <div class="col-md-3"><span class="label label-primary">Условие</span></div>
                                        <div class="col-md-3"><span class="label label-primary">Товары</span></div>
                                        <div class="col-md-3"><span class="label label-primary">Деньги</span></div>
                                        <div class="col-md-3"><span class="label label-primary">Баллы</span></div>
                                    </div>
                                </div>

                                <div id="quick-prizes">
                                    <div class="row-fluid">

                                    <div class="form-group holder col-md-3">
                                        <div class="label-primary label">Функции</div>
                                        <div class="row-fluid math-holder" data-type="math">
                                            <div class="empty-prize add-trigger"></div>
                                        </div>
                                    </div>

                                    <div class="form-group holder col-md-3">
                                        <span class="label label-primary">Товары</span>
                                        <div class="row-fluid item-holder" data-type="item">
                                            <div class="empty-prize add-trigger"></div>
                                        </div>
                                    </div>

                                    <div class="form-group holder col-md-3">
                                        <div class="label-primary label">Деньги</div>
                                        <div class="row-fluid money-holder" data-type="money">
                                            <div class="empty-prize add-trigger"></div>
                                        </div>
                                    </div>

                                    <div class="form-group holder col-md-3">
                                        <div class="label-primary label">Баллы</div>
                                        <div class="row-fluid points-holder" data-type="points">
                                            <div class="empty-prize add-trigger"></div>
                                        </div>
                                    </div>

                                    </div>
                                </div>

                            </div>
                    </form>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-md btn-success save-game"> Сохранить</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade qgames" id="itemsModal" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Товар для игры</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <? foreach ($shopItems as $item) { ?>
                            <div class="thumbnail" style="cursor:pointer" data-id="<?=$item->getId()?>">
                                <img alt="<?=$item->getTitle()?>" src="/filestorage/shop/<?=$item->getImage()?>" width="100%" height="100%" />
                                <div class="caption clearfix">
                                    <span><?=$item->getTitle()?></span>
                                </div>
                            </div>
                    <? } ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid qgames">
    <div class="row-fluid">
        <h2>Конструктор игр
            <button class="btn btn-md btn-success add-game"> Добавить</button></h2>
        <hr />
    </div>

    <div class="game-builds">
    </div>

</div>

<script>

    $(function() {

        function runEffect() {

            var selectedEffect='';
            if(!(selectedEffect = $( "#effectTypes" ).val()))
                return;

            cell=$("li", $('#field ul').last());
            cell.first().removeClass('points').next().removeClass('los');

            speed = parseInt($('#editGame form input.s').val()) || 400;

            var options = {};
            if ( selectedEffect === "scale" ) {
                options = { percent: 0 };
            } else if ( selectedEffect === "size" ) {
                options = { to: { width: 0, height: 0 } };
            }
            cell.first().html($('<div></div>').css('height','100%').css('background',cell.first().css('background'))).addClass('points').find('div').effect( selectedEffect, options, speed,function(){this.remove();} );
            cell.first().next().html($('<div></div>').css('height','100%').css('background',cell.first().next().css('background'))).addClass('los').find('div').effect( selectedEffect, options, speed,function(){this.remove();} );
        };


        $( "#effectTypes" ).on('input', function() { runEffect();});


        $('button[data-combination]').on('click', function() {

            $(this).toggleClass('active');
            holder=$("#editGame").find('form');

            if($('#field input[value="'+$(this).data('combination')+'"]', holder).length)
                $('#field input[value="'+$(this).data('combination')+'"]', holder).remove();
            else
                $('#field',holder).append($('<input type=hidden name="game[Field][combination][]" value="'+$(this).data('combination')+'">'));
        });


        $('button[data-format]').on('click', function() {

            $('button[data-format]').removeClass('active');
            $(this).toggleClass('active');

            holder=$("#editGame").find('form');
            format=$(this).data('format');
            $('#format input[name="game[Field][f]"]',holder).val(format);

            genChancePrizes();

        });


        $('.add-game').on('click', function() {
            var game = {Id:0,Title:'',Key:'',Description:'',Field:{x:6,y:1,b:1,r:1,w:95,h:95,c:1}};
            editGame(game);
        });


        $(document).on('click','.game-build', function() {
            editGame(games[$(this).data('id')]);
        });


        games=<?=json_encode($games, JSON_PRETTY_PRINT)?>;
        $.each(games, function(index, game) {
            buildGame(game);
        });


        function editGame(game){
            console.log(game);
            $('#editGame').modal().find('button.tab').removeClass('active').first().addClass('active');
            $('#editGame').find('div.tab').hide().first().show();

            holder=$("#editGame").find('form');
            holder.find('.lang').first().click();
            holder.find('.k').val('Chance'+game.Id);
            holder.find('.p').val(game.Field.p);
            holder.find('.m').val(game.Field.m);
            holder.find('.x').val(game.Field.x);
            holder.find('.y').val(game.Field.y);
            holder.find('.h').val(game.Field.h);
            holder.find('.w').val(game.Field.w);
            holder.find('.s').val(game.Field.s);
            holder.find('.c').val(game.Field.c);
            holder.find('.e').val(game.Field.e).change();
            holder.find('.r').val(typeof game.Field.r !== "undefined" && game.Field.r?game.Field.r:0);
            holder.find('.b').val(typeof game.Field.b !== "undefined" && game.Field.b?game.Field.b:0).trigger('input');
            holder.find('[name="game[Enabled]"]').bootstrapToggle((game.Enabled==1 || game.Enabled=='on'?'on':'off'));
            holder.find('[name="game[Id]"]').val(game.Id);
            holder.find('[name^="game[Title]"], [name^="game[Description]"]').val('');

            $('#editGame').find('h3 span').first().text($.isPlainObject(game.Title)?game.Title[Object.keys(game.Title)[0]]:'Новая игра');

            $.isPlainObject(game.Title) && $.each(game.Title, function (lang, text) {
                holder.find('[name="game[Title]['+lang+']"]').val(text);
            });
            $.isPlainObject(game.Description) && $.each(game.Description, function (lang, text) {
                holder.find('[name="game[Description]['+lang+']"]').val(text);
            });


            holder.find('#audio input').val('');
            if (game.Audio) {
                $.each(game.Audio, function (i, f) {
                    holder.find('#audio input[name="game[Audio][' + i + ']"]').val(f);
                });
            }

            $('#field input[name="game[Field][combination][]"]', holder).remove();
            $('[data-combination]', holder).removeClass('active');
            $('#chance-prizes .row-prize').remove();

            if(game.Field.combination){
                $.each(game.Field.combination, function (index, value) {
                    $('[data-combination="'+value+'"]', holder).click();
                });
            }


            //holder.find('[name="game[Title]"]').val(game.Title);
            //holder.find('[name="game[Description]"]').text(game.Description);

            $('.i').attr('src', 'http://<?=$_SERVER['SERVER_NAME']?>/tpl/img/games/Chance' + game.Id + ".png?" + (new Date().getTime()));

            if(game.Field.f){
                $('[data-format="'+game.Field.f+'"]', holder).click();
            } else {
                $('[data-format]', holder).first().click();
            }

            holder.find('.prize').remove();
            if(game.Prizes) {
                $.each(game.Prizes, function (index, prize) {
                    var html = $('<div class="prize" data-id="'+index+'">'+
                    '<span class="glyphicon glyphicon-remove remove" aria-hidden="true"></span>'+
                    '<input type="hidden" name="game[Prizes]['+index+'][t]" value="'+prize.t+'" >' +
                    '<input type="text" class="form-control value" name="game[Prizes]['+index+'][v]" placeholder="Значение" value="'+prize.v+'">' +
                    '<input type="text" class="form-control" name="game[Prizes]['+index+'][p]" placeholder="Вероятность" value="'+prize.p+'">' +
                    '</div>');

                    if (prize.t == 'item'){
                        html.find('input:eq(1)').attr('type','hidden');
                        html.append($('.thumbnail[data-id="' + prize.v + '"] img').clone().attr('width','100').attr('height','100'));
                    }
                    if(game.Field.f && game.Field.f!='cell')
                        holder.find('.row-prize[data-click="'+index+'"] [data-type="'+prize.t+'"]').html(html);
                    else
                        holder.find('.'+prize.t+'-holder').prepend(html);
                });
            }

        }

        function genChancePrizes() {

            var holder=$("#editGame #prizes");
            var format=$('input[name="game[Field][f]"]',holder).val();
            var hit = parseInt($("#editGame").find('form').find('.c').val());
            var miss = parseInt($("#editGame").find('form').find('.m').val());

                if(format!='cell'){

                    var clicks = (format=='hit') ? hit : miss;

                    while ((c=(parseInt($('.row-prize').last().attr('data-click')) ? parseInt($('.row-prize').last().attr('data-click')) : 0) +1) <= clicks) {
                        html = genChancePrize(c);
                        if(p=$('.prize[data-id="' + c + '"]', holder).detach())
                            html.find('div.'+ p.find('input[type="hidden"]').val()+'-holder').html(p);
                        $('#chance-prizes').append(html);
                    }

                    while ($('.row-prize').last().attr('data-click') > clicks)
                        $('.row-prize').last().remove();


                    $('#chance-prizes .row-prize').each(function (i, html) {
                        id=i+1;
                        ul = new Array(format=='hit' ? id+1 : hit+1 ).join('<li class=fill></li>') + new Array(format=='hit'? 0:miss-id+1).join('<li class=los></li>');
                        $('ul', html).html(ul);
                    });


                    $('#chance-prizes').show();
                    $('#quick-prizes').hide().find('.prize').remove();

                } else {

                    $('#chance-prizes .prize').each(function (i, html) {
                        holder.find('#quick-prizes .'+$(html).find('input[type="hidden"]').val()+'-holder').prepend(html);
                    });

                    $('#chance-prizes').hide().find('.row-prize').remove();
                    $('#quick-prizes').show();

                }


        }

        function genChancePrize(id) {


            return $('<div class="row-fluid row-prize" data-click="' + id + '">' +
            '<div class="col-md-3"><ul>' + '</ul></div>' +
            '<div class="col-md-3 item-holder" data-type="item"><div class="empty-prize add-trigger"></div></div>' +
            '<div class="col-md-3 money-holder" data-type="money"><div class="empty-prize add-trigger"></div></div>' +
            '<div class="col-md-3 points-holder" data-type="points"><div class="empty-prize add-trigger"></div></div>' +
            '</div>');
        }

        function buildGame(game){
            if(!$('.game-build[data-id="'+game.Id+'"]').length){
                $('.game-builds').prepend($('<div class="game-build" data-id="'+game.Id+'">' +
                    '<div class="t"></div>' +
                    '<div class="d"></div>' +
                    '<div class="c"></div>' +
                    '<ul></ul>' +
                '</div>'));
            }
            holder=$('.game-build[data-id="'+game.Id+'"]');

        if(game.Enabled==1 || game.Enabled=='on' || 1)
            holder.removeClass('disabled');
        else
            holder.addClass('disabled');

            var html='';
            for(y1=1;y1<=game.Field.y;y1++)
                for(x1=1;x1<=game.Field.x;x1++)
                    html+="<li style='width: "+game.Field.w+"px;height: "+game.Field.h+"px;margin: 0 "+(x1!=game.Field.x?game.Field.r:0)+"px "+(y1!=game.Field.y?game.Field.b:0)+"px 0;'></li>"
            holder.find('.t').text(game.Title.<?=$defaultLang;?>).next().text(game.Description.<?=$defaultLang;?>).next().text(game.Field.c).next().css('width',((parseInt(game.Field.w)+parseInt(game.Field.r))*parseInt(game.Field.x)-parseInt(game.Field.r))).html(html);


            var i=0;
            if(game.Prizes)
            $.each(game.Prizes, function(index, prize) {
                do
                    i = Math.ceil(Math.random() * holder.find('ul li').length) - 1;
                while (holder.find('ul li:eq(' + i + ')').html());

                if (prize.t != 'item')
                    holder.find('ul li:eq(' + i + ')').addClass(prize.t).html(
                        (prize.p && prize.p > 0? '<span>'+ Math.ceil((100/parseInt(prize.p))*10000)/10000+'%</span>':'')+
                        '<div style="margin: 0 0 -' + parseInt(game.Field.h) / 15 + 'px 0;font-size:' + parseInt(game.Field.h) / (prize.t == 'math' ? 1.7 : 2) + 'px;">' + (prize.v ? prize.v.replaceArray(["\*", "\/"], ["x", "÷"]) : 0) + '</div>' +
                        '<div style="margin-top:-' + parseInt(game.Field.h) / 10 + 'px;font-size:' + parseInt(game.Field.h) / 5 + 'px;">' + (prize.t == 'points' ? 'баллов' : prize.t == 'money' ? 'грн' : '') + '</div>');
                else {
                    holder.find('ul li:eq(' + i + ')').addClass(prize.t).html(
                        (prize.p && prize.p > 0? '<span>'+ Math.ceil((100/parseInt(prize.p))*10000)/10000+'%</span>':'')+'<div></div>').find('div').append($('.thumbnail[data-id="' + prize.v + '"] img').clone().attr('width','100%').attr('height','100%'));
                }
                i++;
            });
        };


        $(document).on('input','.m,.c',genChancePrizes);
        $(document).on('input','.w,.h,.x,.y,.r,.b,.c',function(){

            holder=$(this).parents('form');
            x=(parseInt(holder.find('.x').val())?parseInt(holder.find('.x').val()):0);
            y=(parseInt(holder.find('.y').val())?parseInt(holder.find('.y').val()):0);
            h=(parseInt(holder.find('.h').val())?parseInt(holder.find('.h').val()):0);
            w=(parseInt(holder.find('.w').val())?parseInt(holder.find('.w').val()):0);
            r=(parseInt(holder.find('.r').val())?parseInt(holder.find('.r').val()):0);
            b=(parseInt(holder.find('.b').val())?parseInt(holder.find('.b').val()):0);
            var html='';
            for(y1=1;y1<=y;y1++)
                for(x1=1;x1<=x;x1++)
                    html+="<li style='width: "+w+"px;height: "+h+"px;margin: 0 "+(x1!=x?r:0)+"px "+(y1!=y?b:0)+"px 0;'></li>"

            holder.find('#field ul').last().css('width',((w+r)*x-r)).html(html);
            $("#field-size").text(((w+r)*x-r)+'x'+((h+b)*y-b)+'px');
            holder.find('.save-game').addClass('btn-success');
        });



    $(document).on('click','.add-trigger', function() {

        var form = $("#editGame").find('form');
        if (form.find('.prize').length >= form.find('#field-ul li').length) {
            alert('Призов не может быть больше, чем ячеек!');
            return false;
        }

        var type = ($(this).attr('data-type')?$(this).attr('data-type'):$(this).parent().attr('data-type'));
        var holder= $(this);

        if (!(id = $(this).parent().parent().attr('data-click'))) {
            id = 0;
            $(this).parent().parent().parent().find('div.prize').each(function (i, n) {
                var check = $(n).attr('data-id');
                if (check > id) id = check;
            });
            id++;
        } else {
                $('.row-prize[data-click="'+id+'"] .prize',form).parent().html($('<div class="empty-prize add-trigger"></div>'));
        }
        var img = '';
        if(type=='item'){

            $('#itemsModal').modal();
            $('#itemsModal').find('.thumbnail').off('click').on('click', function() {
                 img = $('<div class="prize" data-id="'+id+'">'+
                '<span class="glyphicon glyphicon-remove remove" aria-hidden="true"></span>'+
                '<input type="hidden" name="game[Prizes]['+id+'][t]" value="item" >' +
                '<input type="hidden" class="form-control value" name="game[Prizes]['+id+'][v]" placeholder="Значение" value="'+$(this).data('id')+'">' +
                '<img src="' + $(this).find('img').attr('src') + '" width="100" height="100"/>'+
                '<input type="text" class="form-control" name="game[Prizes]['+id+'][p]" placeholder="Вероятность" value="">' +
                '</div>');

                if ($('#format input[name="game[Field][f]"]').val()!='cell')
                    holder.parent().html(img);
                else
                    img.insertBefore(holder);


                $('#itemsModal').modal('hide');
            });

        } else {

            img = $('<div class="prize" data-id="' + id + '">' +
            '<span class="glyphicon glyphicon-remove remove" aria-hidden="true"></span>' +
            '<input type="hidden" name="game[Prizes][' + id + '][t]" value="' + type + '" >' +
            '<input type="text" class="form-control value" name="game[Prizes][' + id + '][v]" placeholder="Значение" value="">' +
            '<input type="text" class="form-control" name="game[Prizes][' + id + '][p]" placeholder="Вероятность" value="">' +
            '</div>');
            if ($('#format input[name="game[Field][f]"]').val()!='cell')
                holder.parent().html(img);
            else
                img.insertBefore(holder);

        }

        });


  $('.save-game').on('click', function() {

    var button = $(this);
    var form = $(this).parent().parent().find('form');

      if(form.find('.prize').length>form.find('#field-ul li').length){
          alert('Призов не может быть больше, чем ячеек!');
          return false;
      }

    $.ajax({
        url: "/private/qgames",
        method: 'POST',
        data: form.serialize(),
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                $("#editGame").modal('hide');
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