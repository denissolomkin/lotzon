<div class="modal fade ogames" id="editGame" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="confirmLabel">Редактирование игры
                    <span style="float: right;margin-bottom: 10px;">
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
                    <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
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
                                <input type="text" class="form-control" name="game[Title][<?=$lang?>]" placeholder="Название игры" value="">
                                <? } ?>
                                <input type="hidden" name="game[Id]" value="0">
                            </div>
                            <div class="form-group">
                                <input type="checkbox" name='game[Enabled]' data-toggle="toggle">
                            </div>
                        </div>
                        <div class="row-fluid description">
                            <? foreach ($langs as $lang) { ?>
                            <textarea class="form-control" rows=5 name="game[Description][<?=$lang?>]" placeholder="Описание игры"></textarea>
                            <? } ?>
                        </div>
                        <div class="row-fluid banner">
                            <? foreach ($langs as $lang) { ?>
                            <button type="button" class="btn btn-md lang btn-default" data-lang="<?=$lang?>"><?=strtoupper($lang)?></button>
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

                        <div class="row-fluid tab" id="audio">
                            <? $audio=array('start','stack','field','start','ready','Timer','Win','Lose','Move-m-1','Move-o-1','Move-m-2','Move-o-2','Move-m-3','Move-o-3');
                            while($key= each ($audio)) {
                                $key=array_shift($key);?>
                                <div class="col-lg-6">
                                    <div class="input-group">
                                        <span class="input-group-addon"><?=$key;?></span>
                                        <input type="text" class="form-control" name="game[Audio][<?=$key;?>]" value="http://192.168.1.253/tpl/audio/complete.ogg">
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
                                        <input class="form-control s" type="text" name="game[Field][s]" value="1" placeholder="Стек" value="">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                        <input class="form-control p" type="text" name="game[Field][p]" value="1" placeholder="Игроков" value="">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                                        <input class="form-control t" type="text" name="game[Field][t]" value="30" placeholder="Таймаут" value="">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-paw"></i></span>
                                        <input class="form-control m" type="text" name="game[Field][m]" value="6" placeholder="Ходов" value="">
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
                                        <input class="form-control w" type="text" name="game[Field][w]"  value="1" placeholder="Очки" value="">
                                    </div>
                                    <div class="input-group">

                                        <span class="input-group-addon"><i class="fa fa-laptop"></i></span>
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

<div class="modal fade ogames" id="audio-modal" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Выбор аудио
                    <button type="button" class="btn btn-success add-audio"><i class="fa fa-plus"></i> Добавить</button></h4>
            </div>
            <div class="modal-body">
               <ul><?$openDir=opendir(dirname(__FILE__).'/../../../tpl/audio/');
                   while(($file=readdir($openDir)) !== false)
                   if($file != "." && $file != "..") {
                       echo '<li data-file="'.$file.'"><i class="fa fa-file-audio-o"> '.$file.' </i><i class="fa fa-play-circle audio-play"></i></li>';
                   }?></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade ogames" id="price-modal" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
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

<div class="container-fluid ogames">
    <div class="row-fluid">
        <h2>Онлайн-игры
            <button class="btn btn-md btn-success add-game"> Добавить</button></h2>
        <hr />
    </div>

    <div class="game-builds">
    </div>

</div>
<script src="/theme/admin/lib/jquery.damnUploader.min.js"></script>
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

        $(document).on('click','.audio-play',function() {
            if($(this).prop("tagName")=='I')
                $('<audio src=""></audio>').attr('src', '../../../tpl/audio/' + $.trim($(this).parent().text())).trigger("play");
            else if($(this).parent().prev().val()) {
                $('<audio src=""></audio>').attr('src', '../../../tpl/audio/' + $(this).parent().prev().val()).trigger("play");
            }
        });

        $('.audio-remove').on('click',function() {
            $(this).parent().prev().val('');
        });

        $('.audio-refresh').on('click',function() {
            var holder = $("#audio-modal");
            var input = $(this).parent().prev();
            holder.modal();
            $('li .fa-file-audio-o', holder).off().on('click', function(){
                input.val($.trim($(this).text()));
                holder.modal('hide');
            });
        });

        games=<?
        foreach ($games as $game)
            $list[$game->getId()]=array(
            'Id'    =>  $game->getId(),
            'Key'   =>  $game->getKey(),
            'Title' =>  $game->getTitle(),
            'Description'   =>  $game->getDescription(),
            'Field'     => $game->getOptions(),
            'Prizes'    =>  $game->getModes(),
            'Audio'    =>  $game->getAudio(),
            'Enabled'   =>  $game->isEnabled()
            );
        echo json_encode($list, JSON_PRETTY_PRINT)?>;

        $.each(games, function(index, game) {
            buildGame(game);
        });

        function genGame(game){
            $('#editGame').modal().find('button.tab').removeClass('active').first().addClass('active');
            $('#editGame').find('div.tab').hide().first().show();
            $('#editGame').find('h3').text($.isPlainObject(game.Title)?game.Title[0]:'*New game');


            holder=$("#editGame").find('form');
            holder.find('.lang').first().click();
            holder.find('.x').val(game.Field.x);
            holder.find('.y').val(game.Field.y);
            holder.find('.t').val(game.Field.t);
            holder.find('.w').val(game.Field.w);
            holder.find('.s').val(game.Field.s);
            holder.find('.p').val(game.Field.p);
            holder.find('.m').val(game.Field.m);
            holder.find('[name="game[Field][b]"]').bootstrapToggle({on: 'Enabled',
                off: 'Disabled'}).bootstrapToggle((game.Field.b==1 || game.Field.b=='on'?'on':'off'));

            console.log(game.Key);
            if(game.Key){
                $('#editGame button[data-tab="text"]').next().attr('data-tab','image');
                holder.find('.k').val(game.Key);
                holder.find('.i').attr('src', 'http://<?=$_SERVER['SERVER_NAME']?>/tpl/img/games/' + game.Key + ".png?" + (new Date().getTime()));
/*
                $.ajax({
                    url: 'http://<?=$_SERVER['SERVER_NAME']?>/tpl/img/games/' + game.Key + ".png?" + (new Date().getTime()),
                    type: 'HEAD',
                    error: function () {
                        holder.find('.i').attr('src', 'http://<?=$_SERVER['SERVER_NAME']?>/tpl/img/but-upload-review.png');
                    },
                    success: function () {
                        holder.find('.i').attr('src', 'http://<?=$_SERVER['SERVER_NAME']?>/tpl/img/games/' + game.Key + ".png?" + (new Date().getTime()));
                    }
                });
                */
            } else {
                $('#editGame button[data-tab="text"]').next().attr('data-tab','key');
                holder.find('.k').val('');
            }

            holder.find('[name="game[Enabled]"]').bootstrapToggle((game.Enabled==1 || game.Enabled=='on'?'on':'off'));
            holder.find('[name="game[Id]"]').val(game.Id);
            holder.find('[name^="game[Title]"], [name^="game[Description]"]').val('');
            $.isPlainObject(game.Title) && $.each(game.Title, function (lang, text) {
                holder.find('[name="game[Title]['+lang+']"]').val(text);
            });
            $.isPlainObject(game.Description) && $.each(game.Description, function (lang, text) {
                holder.find('[name="game[Description]['+lang+']"]').val(text);
            });


            holder.find('#audio input').val('');

            if(game.Audio) {
                $.each(game.Audio, function (i, f) {
                    holder.find('#audio input[name="game[Audio]['+i+']"]').val(f);
                });
            }

            holder.find('#field .ships').hide();
            holder.find('#field .ships ul li').remove();


            if(game.Key=='SeaBattle') {
                holder.find('#field .ships').show();
                if(game.Field.ships)
                    $.each(game.Field.ships, function (i, ship) {
                        holder.find('#ships ul').first.append('<li data-ship="'+ship+'"><i class="fa fa-minus"><ul><ul><i class="fa fa-plus"> <input name="game[Field][ships][]" value='+ship+'></li>').val(ship);
                    });
            }

            holder.find('.prize').remove();
            if(game.Prizes) {
                $.each(game.Prizes, function (index, t) {
                    var button=holder.find('.' + index + '-holder').prev();
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
                });
            }
        }

        function buildGame(game){
            if(!$('.game-build[data-id="'+game.Id+'"]').length){
                $('.game-builds').prepend($('<div class="game-build" data-id="'+game.Id+'">' +
                    '<div class="t"></div>' +
                    '<div class="d"></div>' +
                    '<div class="o"></div>' +
                    '<img>' +
                '</div>'));
            }
            holder=$('.game-build[data-id="'+game.Id+'"]');
        if(game.Enabled==1 || game.Enabled=='on')
            holder.removeClass('disabled');
        else
            holder.addClass('disabled');

            holder.find('.t').text(game.Title.<?=\Config::instance()->defaultLang;?>)
                .next().html(nl2br(game.Description.<?=\Config::instance()->defaultLang;?>))
                .next().html(
                '<i class="fa fa-users"></i>'+game.Field.s+
                ' <i class="fa fa-user"></i>'+game.Field.p+
                ' <i class="fa fa-clock-o"></i>'+game.Field.t+
                ' <i class="fa fa-paw"></i>'+game.Field.m+
                ' <i class="fa fa-arrows-h"></i>'+game.Field.x+
                ' <i class="fa fa-arrows-v"></i>'+game.Field.y+
                ' <i class="fa fa-trophy"></i>'+game.Field.w+
                (game.Field.b ? '<i class="fa fa-laptop"></i>':'<span class="fa-stack fa-lg"><i class="fa fa-laptop fa-stack-1x"></i><i class="fa fa-ban fa-stack-2x text-danger"></i></span>')
                );//.next().css('width',((parseInt(game.Field.w)+parseInt(game.Field.r))*parseInt(game.Field.x)-parseInt(game.Field.r))).html(html);

                holder.find('img').attr('src', 'http://<?=$_SERVER['SERVER_NAME']?>/tpl/img/games/' + game.Key + ".png?" + (new Date().getTime()));

/*
                $.ajax({
                    url: 'http://<?=$_SERVER['SERVER_NAME']?>/tpl/img/games/' + game.Key + ".png?" + (new Date().getTime()),
                    type: 'HEAD',
                    error: function () {
                        holder.find('img').attr('src', 'http://<?=$_SERVER['SERVER_NAME']?>/tpl/img/but-upload-review.png');
                    },
                    success: function () {
                        holder.find('img').attr('src', 'http://<?=$_SERVER['SERVER_NAME']?>/tpl/img/games/' + game.Key + ".png?" + (new Date().getTime()));
                    }
                });
                */
        };




    $(document).on('click','.remove', function() {
        $(this).parent().remove();
    });

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
                    '<span class="input-group-addon"><i class="fa fa-laptop"></i></span>' +
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
                    console.log(parseFloat($('.v', a).val()) > parseFloat($('.v', b).val()) ? 1 : -1);
                    return parseFloat($('.v', a).val()) > parseFloat($('.v', b).val()) ? 1 : -1;
                }).appendTo( button.parent().next().find('div.prize').parent());

                holder.modal('hide');

            });
            return false;
        });


    $('.add-game').on('click', function() {
        var game = {Id:0,Title:'',Key:'',Description:'',Field:{x:6,y:1,b:1,r:1,w:95,h:95,c:1}};
        genGame(game);
    });

    $(document).on('click','.game-build', function() {
        genGame(games[$(this).data('id')]);
    });


    $('#editGame button.tab').on('click', function() {
        $("#editGame button.tab").removeClass("active");
        $("#editGame div.tab:visible").hide();
        $("#editGame div.tab#"+$(this).attr("data-tab")).fadeIn(200);

        $(this).addClass("active");
    });


        $('.lang').on('click', function() {
            lang=$(this).data('lang');
            $('.lang').removeClass('active');
            $(this).addClass('active');
            $('input[name^="game[Title]"], textarea[name^="game[Description]"]',$('#editGame')).hide();
            $('input[name="game[Title]['+lang+']"], textarea[name="game[Description]['+lang+']"]',$('#editGame')).fadeIn(200);
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

        $('#image').on('click', initUpload);
        function initUpload() {

            // create form
            var form = $('<form method="POST" enctype="multipart/form-data"><input type="file" name="image"/></form>');
            var image = $(this).find('img');
            var input = form.find('input[type="file"]').damnUploader({
                url: '/private/images?folder=games',
                fieldName: 'image',
                dataType: 'json'
            });

            input.off('du.add').on('du.add', function(e) {
                e.uploadItem.completeCallback = function(succ, data, status) {

                    image.attr('src', 'http://<?=$_SERVER['SERVER_NAME']?>/' + data.imageWebPath + "?" + (new Date().getTime()));


                    $('.game-build[data-id="'+image.parent().parent().find('input[name="game[Id]"]').val()+'"]').find('img').//$("img[src^=\"http://<?=$_SERVER['SERVER_NAME']?>/"+data.imageWebPath+"\"]").
                        attr('src', 'http://<?=$_SERVER['SERVER_NAME']?>/' + data.imageWebPath + "?" + (new Date().getTime()));
                };

                e.uploadItem.progressCallback = function(perc) {}
                e.uploadItem.addPostData('name', image.parent().prev().find('input').val()+'.png');
                e.uploadItem.upload();
            });

            form.find('input[type="file"]').click();
        }

        $('#audio-modal .add-audio').on('click', function () {

            // create form
            var form = $('<form method="POST" enctype="multipart/form-data"><input type="file" name="audio"/></form>');
            var input = form.find('input[type="file"]').damnUploader({
                url: '/private/audio',
                fieldName: 'audio',
                dataType: 'json'
            });

            input.off('du.add').on('du.add', function(e) {
                e.uploadItem.completeCallback = function(succ, data, status) {
                    $('#audio-modal ul li[data-file="'+ data.audioName+'"]').remove();
                    $('#audio-modal ul').append('<li data-file="'+ data.audioName+'"><i class="fa fa-file-audio-o"> '+ data.audioName +' </i><i class="fa fa-play-circle audio-play"></i></li>');
                };
                e.uploadItem.progressCallback = function(perc) {}
                e.uploadItem.upload();
            });

            form.find('input[type="file"]').click();
        });

    });
</script>