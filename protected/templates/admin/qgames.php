<div class="modal fade qgames" id="editGame" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="confirmLabel">Редактирование игры
                    <span style="float: right;margin-bottom: 10px;">
                <button type="button" class="btn btn-md btn-success tab" data-tab="text">
                    <span class="glyphicon glyphicon-font" aria-hidden="true"></span>
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
                    <form class="form-inline" role="form" data-game="new">

                    <div class="row-fluid tab" id="text">
                        <div class="row-fluid title">
                            <div class="form-group">
                                <label class="sr-only">Название</label>
                                <? foreach ($langs as $lang) { ?>
                                <input type="text" class="form-control" name="game[Title][<?=$lang?>]" placeholder="Название игры" value="">
                                <? } ?>
                                <input type="hidden" class="form-control" name="game[Id]" value="0">
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

                        <div class="row-fluid tab" id="field">
                                <div class="row-fluid field">
                                    <div class="input-group">
                                        <span class="input-group-addon">R</span>
                                        <input class="form-control r" type="text" name="game[Field][r]" value="1" placeholder="Справа" value="">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon">B</span>
                                        <input class="form-control b" type="text" name="game[Field][b]" value="1" placeholder="Снизу" value="">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon">W</span>
                                        <input class="form-control w" type="text" name="game[Field][w]" value="95" placeholder="Ширина" value="">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon">H</span>
                                        <input class="form-control h" type="text" name="game[Field][h]" value="95" placeholder="Высота" value="">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon">X</span>
                                        <input class="form-control x" type="text" name="game[Field][x]"  value="6" placeholder="По горизонтали" value="">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon">Y</span>
                                        <input class="form-control y" type="text" name="game[Field][y]"  value="1" placeholder="По вертикали" value="">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon">C</span>
                                        <input class="form-control c" type="text" name="game[Field][c]"  value="1" placeholder="Количество ходов" value="">
                                    </div>
                                        <ul></ul>
                                    <span id="field-size">1213</span>
                                </div>
                            </div>

                            <div class="row-fluid tab" id="prizes">
                                <div class="form-group holder">
                                    <div class="input-group">
                                        <label class="sr-only"></label>
                                        <button class="btn btn-md btn-warning items-modal" data-type="items">Товары</button>
                                    </div>
                                    <div class="row-fluid item-holder">
                                    </div>
                                </div>

                                <div class="form-group holder">
                                    <div class="input-group">
                                        <label class="sr-only"></label>
                                        <button class="btn btn-md btn-warning add-trigger" data-type="money">Деньги</button>
                                    </div>
                                    <div class="row-fluid money-holder">
                                    </div>
                                </div>

                                <div class="form-group holder">
                                    <div class="input-group">
                                        <label class="sr-only"></label>
                                        <button class="btn btn-md btn-warning add-trigger" data-type="points">Баллы</button>
                                    </div>
                                    <div class="row-fluid points-holder">
                                    </div>
                                </div>

                                <div class="form-group holder">
                                    <div class="input-group">
                                        <label class="sr-only"></label>
                                        <button class="btn btn-md btn-warning add-trigger" data-type="math">Функции</button>
                                    </div>
                                    <div class="row-fluid math-holder">
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

        games=<?=json_encode($games, JSON_PRETTY_PRINT)?>;
        $.each(games, function(index, game) {
            buildGame(game);
        });

        function genGame(game){
            holder=$("#editGame").find('form');
            holder.find('.lang').first().click();
            holder.find('.x').val(game.Field.x);
            holder.find('.y').val(game.Field.y);
            holder.find('.h').val(game.Field.h);
            holder.find('.w').val(game.Field.w);
            holder.find('.c').val(game.Field.c);
            holder.find('.r').val(typeof game.Field.r !== "undefined" && game.Field.r?game.Field.r:0);
            holder.find('.b').val(typeof game.Field.b !== "undefined" && game.Field.b?game.Field.b:0).trigger('input');
            holder.find('[name="game[Enabled]"]').bootstrapToggle((game.Enabled==1 || game.Enabled=='on'?'on':'off'));
            holder.find('[name="game[Id]"]').val(game.Id);
            holder.find('[name^="game[Title]"], [name^="game[Description]"]').val('');
            $.isPlainObject(game.Title) && $.each(game.Title, function (lang, text) {
                holder.find('[name="game[Title]['+lang+']"]').val(text);
            });
            $.isPlainObject(game.Description) && $.each(game.Description, function (lang, text) {
                holder.find('[name="game[Description]['+lang+']"]').val(text);
            });

            //holder.find('[name="game[Title]"]').val(game.Title);
            //holder.find('[name="game[Description]"]').text(game.Description);


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
                        html.prepend($('.thumbnail[data-id="' + prize.v + '"] img').clone().attr('width','100').attr('height','100'));
                    }

                    holder.find('.'+prize.t+'-holder').append(html);
                });
            }
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
        if(game.Enabled==1 || game.Enabled=='on')
            holder.removeClass('disabled');
        else
            holder.addClass('disabled');

            var html='';
            for(y1=1;y1<=game.Field.y;y1++)
                for(x1=1;x1<=game.Field.x;x1++)
                    html+="<li style='width: "+game.Field.w+"px;height: "+game.Field.h+"px;margin: 0 "+(x1!=game.Field.x?game.Field.r:0)+"px "+(y1!=game.Field.y?game.Field.b:0)+"px 0;'></li>"
            holder.find('.t').text(game.Title.<?=\Config::instance()->defaultLang;?>).next().text(game.Description.<?=\Config::instance()->defaultLang;?>).next().text(game.Field.c).next().css('width',((parseInt(game.Field.w)+parseInt(game.Field.r))*parseInt(game.Field.x)-parseInt(game.Field.r))).html(html);


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


        $(document).on('input','.w,.h,.x,.y,.r,.b',function(){
            holder=$(this).parent().parent().parent().parent();
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

            holder.find('ul').css('width',((w+r)*x-r)).html(html);
            $("#field-size").text(((w+r)*x-r)+'x'+((h+b)*y-b)+'px')
            holder.find('.save-game').addClass('btn-success');

        });

    $(document).on('click','.remove', function() {
        $(this).parent().remove();
    });


    $('.add-trigger').on('click', function() {

        var form = $("#editGame").find('form');
        if(form.find('.prize').length>=form.find('li').length){
            alert('Призов не может быть больше, чем ячеек!');
            return false;
        }

        var id = 0;
        $(this).parent().parent().parent().find('div.prize').each(function(i,n){
            var check = $(n).attr('data-id');
            if(check>id) id = check;
        });
        id++;

        var img = $('<div class="prize" data-id="'+id+'">'+
        '<span class="glyphicon glyphicon-remove remove" aria-hidden="true"></span>'+
        '<input type="hidden" name="game[Prizes]['+id+'][t]" value="'+$(this).data('type')+'" >' +
        '<input type="text" class="form-control value" name="game[Prizes]['+id+'][v]" placeholder="Значение" value="">' +
        '<input type="text" class="form-control" name="game[Prizes]['+id+'][p]" placeholder="Вероятность" value="">' +
        '</div>');
            $(this).parent().next().append(img);
        return false;
        });

    $('.add-game').on('click', function() {
        var game = {Id:0,Title:'',Description:'',Banner:'',Field:{x:6,y:1,b:1,r:1,w:95,h:95,c:1}};
        genGame(game);
        $('#editGame').modal().find('button.tab').removeClass('active').first().addClass('active');
        $('#editGame').find('div.tab').hide().first().show();
    });

    $(document).on('click','.game-build', function() {
        $('.add-game').trigger('click');
        genGame(games[$(this).data('id')]);
    });

  $('.items-modal').on('click', function() {

      var form = $("#editGame").find('form');
      if(form.find('.prize').length>=form.find('li').length){
          alert('Призов не может быть больше, чем ячеек!');
          return false;
      }

      holder= $(this).parent();
      var id = 0;
      $(this).parent().parent().parent().find('div.prize').each(function(i,n){
          var check = $(n).attr('data-id');
          if(check>id) id = check;
      });
      id++;


      $('#itemsModal').modal();
        $('#itemsModal').find('.thumbnail').off('click').on('click', function() {
        var img = $('<div class="prize" data-id="'+id+'">'+
        '<span class="glyphicon glyphicon-remove remove" aria-hidden="true"></span>'+
        '<input type="hidden" name="game[Prizes]['+id+'][t]" value="item" >' +
        '<input type="hidden" class="form-control value" name="game[Prizes]['+id+'][v]" placeholder="Значение" value="'+$(this).data('id')+'">' +
        '<img src="' + $(this).find('img').attr('src') + '" width="100" height="100"/>'+
        '<input type="text" class="form-control" name="game[Prizes]['+id+'][p]" placeholder="Вероятность" value="">' +
        '</div>');
         holder.next().append(img);
        $('#itemsModal').modal('hide');
    });


    return false;
  });

    $('#editGame button.tab').on('click', function() {
        $("#editGame button.tab").removeClass("active");
        $("#editGame div.tab:visible").hide();
        $("#editGame div.tab#"+$(this).data("tab")).fadeIn(200);

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

      if(form.find('.prize').length>form.find('li').length){
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