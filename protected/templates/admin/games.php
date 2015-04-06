<div class="container-fluid agames">
    <div class="row-fluid">
        <h2>Настройки</h2>
        <hr />
    </div>


    <div class="game-builds">

        <div class="row-fluid">
            <ul id="games">
                <?php foreach($qgames as $game): ?>
                    <li><input type="hidden" name="games[]" value="<?=$game->getId()?>"><?=$game->getTitle('RU')?><i class="fa fa-remove"></i></li>
                <?php endforeach;?>
            </ul>
        </div>

        <div class="game-build">
            <form> <? $key='QuickGame'; ?>
                <input name="key" type="hidden" value="<?=$key?>">
                <div class="t"><i class="fa fa-puzzle-piece fa-2x"></i><?=\StaticTextsModel::instance()->getText('title-random');?></div>
            <div class="o">
                <i class="fa fa-clock-o"></i><input type="text" value="<?=isset($games[$key])?$games[$key]->getOption('min'):null?>" name="options[min]">
                <span class="btn btn-success save-game"><i class="fa fa-save"></i></span>
            </div>
            <ul class="draggable">
                <?php if($games[$key] && is_array($games[$key]->getGames()))
                    foreach($games[$key]->getGames() as $game): ?>
                        <li><input type="hidden" name="games[]" value="<?=$qgames[$game]->getId()?>"><?=$qgames[$game]->getTitle('RU')?><i class="fa fa-remove"></i></li>
                    <?php endforeach;?>
            </ul>
            </form>
        </div>

        <div class="game-build">
            <form> <? $key='Moment'; ?>
                <input name="key" type="hidden" value="<?=$key?>">
                 <div class="t"><i class="fa fa-rocket"></i><?=\StaticTextsModel::instance()->getText('title-moment');?></div>
            <div class="o">
                <i class="fa fa-clock-o"></i><input type="text" value="<?=isset($games[$key])?$games[$key]->getOption('min'):null?>" name="options[min]">-<input type="text" value="<?=isset($games[$key])?$games[$key]->getOption('max'):null?>" name="options[max]">
                <i class="fa fa-history"></i><input type="text" value="<?=isset($games[$key])?$games[$key]->getOption('timeout'):null?>" name="options[timeout]">
                <span class="btn btn-success save-game"><i class="fa fa-save"></i></span>
            </div>
            <ul class="draggable">
                <?php if($games[$key] && is_array($games[$key]->getGames()))
                    foreach($games[$key]->getGames() as $game): ?>
                        <li><input type="hidden" name="games[]" value="<?=$qgames[$game]->getId()?>"><?=$qgames[$game]->getTitle('RU')?><i class="fa fa-remove"></i></li>
                    <?php endforeach;?>
            </ul>
            </form>
        </div>

        <div class="game-build">
            <form> <? $key='ChanceGame'; ?>
                <input name="key" type="hidden" value="<?=$key?>">
                <div class="t"><i class="fa fa-star fa-2x"></i><?=\StaticTextsModel::instance()->getText('title-chances');?></div>
            <div class="o">
                <span class="btn btn-success save-game"><i class="fa fa-save"></i></span>
            </div>
            <ul class="draggable">
                <?php if($games[$key] && is_array($games[$key]->getGames()))
                    foreach($games[$key]->getGames() as $game): ?>
                    <li><input type="hidden" name="games[]" value="<?=$qgames[$game]->getId()?>"><?=$qgames[$game]->getTitle('RU')?><i class="fa fa-remove"></i></li>
                <?php endforeach;?>
            </ul>
            </form>
        </div>

        <div class="game-build">
            <form> <? $key='OnlineGame'; ?>
                <input name="key" type="hidden" value="<?=$key?>">
                <div class="t"><i class="fa fa-gamepad fa-2x"></i><?=\StaticTextsModel::instance()->getText('title-online-games');?></div>
                <div class="d"></div>
                <div class="o">
                    <i class="fa fa-clock-o"></i><input type="text" value="<?=isset($games[$key])?$games[$key]->getOption('min'):null?>" style="width:16px;" name="options[min]">-<input type="text" value="<?=isset($games[$key])?$games[$key]->getOption('max'):null?>" name="options[max]">
                    <i class="fa fa-history"></i><input type="text" value="<?=isset($games[$key])?$games[$key]->getOption('timer'):null?>" name="options[timer]">
                    <i class="fa fa-sign-out"></i><input type="text" value="<?=isset($games[$key])?$games[$key]->getOption('timeout'):null?>" name="options[timeout]">
                    <span class="btn btn-success save-game"><i class="fa fa-save"></i></span>
                </div>
                <ul>
                    <?php if(!empty($ogames)) foreach($ogames as $game): ?>
                        <li><input type="hidden" name="games[]" value="<?=$game->getId()?>"><?=$game->getTitle('RU')?></li>
                    <?php endforeach;?>
                </ul>
            </form>
        </div>

    </div>

</div>

<script>

    $(".game-build ul.draggable").sortable({
        connectWith: "ul.draggable"
    });

    $(".game-build ul:not(.draggable)").sortable({
        connectWith: "ul:not(.draggable)"
    });
    $("#games li").draggable({
        helper: "clone",
        connectToSortable: ".game-build ul.draggable"
    });

    $( document ).on('click', ".game-build ul li i", function(){
        $(this).parent().remove();
    });

/*
    $( "#games li" ).draggable({
        appendTo: "body",
        helper: "clone"
    });

    $( ".game-build ul" ).droppable({
        activeClass: "ui-state-default",
        hoverClass: "ui-state-hover",
        accept: ":not(.ui-sortable-helper)",
        drop: function( event, ui ) {
            $( this ).find( ".placeholder" ).remove();
            $( "<li></li>" ).html( ui.draggable.html() ).append($('<a class="btn btn-success save" href="#"><i class="fa fa-save"></i></a>')).draggable().appendTo( this );
            //$( "<li></li>" ).text( ui.draggable.text() ).appendTo( this );
        }
    });
*/

  $('.save-game').on('click', function() {
    var button = $(this);
    var form = $(this).parents('form');
    var game = {};

    button.prepend($(' <i class="fa fa-spinner fa-pulse"></i> ').css('margin-right','5px'));

    if (button.find('.glyphicon').length) {
      button.find('.glyphicon').remove();
    }
    game.identifier = form.data('game');

    $.ajax({
        url: "/private/games",
        method: 'POST',
        data: form.serialize(),
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                button.find('.fa').first().remove();
                button.removeClass('btn-danger').addClass('btn-success').prepend($(' <i class="fa fa-check"></i> ').css('margin-right','5px'));
                button.find('.fa').first().fadeOut(500);

            } else {
                button.find('.fa').first().remove();
                button.removeClass('btn-success').addClass('btn-danger');
                alert(data.message);
            }
        }, 
        error: function() {
            button.find('.fa').first().remove();
            button.removeClass('btn-success').addClass('btn-danger');
            alert('Unexpected server error');
        }
    });
    return false;
  });

</script>

<? if($frontend) include($frontend.'_frontend.php') ?>