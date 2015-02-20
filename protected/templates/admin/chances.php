<div class="modal fade" id="itemsModal" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Товар для розыграша</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                  <? foreach ($shopItems as $item) { ?>
                    <div class="col-md-3">
                      <div class="thumbnail" style="cursor:pointer" data-id="<?=$item->getId()?>">
                        <img alt="<?=$item->getTitle()?>" src="/filestorage/shop/<?=$item->getImage()?>" width="50%" height="50%" />
                        <div class="caption clearfix">
                          <span><?=$item->getTitle()?></span>
                        </div>
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
<div class="container-fluid">
    <div class="row-fluid">
        <h2>Моментальные шансы</h2>
        <hr />
    </div>
    <div class="row-fluid">
        <h4>Моментальный шанс</h4>
    </div>  
    <div class="row-fluid">
      <form class="form-inline" role="form" data-game="moment">
        <div class="form-group">
          <label class="sr-only" >От</label>
          <input type="text" name="minFrom" class="form-control" placeholder="От" value="<?=@$games['moment']->getMinFrom()?>">
        </div>
        -
        <div class="form-group">
          <div class="input-group">
              <label class="sr-only" >До</label>
            <input class="form-control" name="minTo" type="text" placeholder="До" value="<?=@$games['moment']->getMinTo()?>">
          </div>
        </div>
        мин.
        <div class="form-group">
          <div class="input-group">
              <label class="sr-only" for="win">Выиграш</label>
            <input class="form-control" id="win" name="pointsWin" type="text" placeholder="Баллы" value="<?=@$games['moment']->getPointsWin()?>">
          </div>
        </div>
        баллов.
        <div class="form-group">
          <div class="input-group">
            <label class="sr-only" for="win"></label>
            <button class="btn btn-md btn-success save-game"> Сохранить</button>
          </div>
        </div>
      </form> 
    </div>
    <hr />
    <div class="row-fluid">
        <h4>Игра 3х3</h4>
    </div>
    <div class="row-fluid">
        <form class="form-inline" role="form" data-game="33">
          <div class="form-group">
            <label class="sr-only">Название</label>
            <input type="text" class="form-control" name="title" placeholder="Название игры" value="<?=@$games['33']->getGameTitle()?>">
          </div>
          <div class="form-group">
            <div class="input-group">
                <label class="sr-only"></label>
                <input class="form-control" type="text" name="price" placeholder="Стоимость" value="<?=@$games['33']->getGamePrice()?>">
            </div>
          </div>
          баллов.
          <div class="form-group">
            <div class="input-group">
                <label class="sr-only"></label>
                <button class="btn btn-md btn-warning items-modal">Добавить товар</button>
            </div>
          </div>
          <div class="form-group">
            <div class="input-group">
                <label class="sr-only"></label>
                <button class="btn btn-md btn-success save-game"> Сохранить</button>
            </div>
          </div>
        </form> 
        <div class="row-fluid">&nbsp;</div>
        <div class="row-fluid prize-holder">
          <? if (@$games['33']->getPrizes()) { ?>
            <? foreach ($games['33']->loadPrizes() as $prize) { ?>
              <img src="/filestorage/shop/<?=$prize->getImage()?>" data-item-id="<?=$prize->getId()?>" width="105" height="105"/>
            <? } ?>
          <? } ?>
        </div>
    </div>
    <hr />
    <div class="row-fluid">
        <h4>Игра 4х4</h4>
    </div>
    <div class="row-fluid">
        <form class="form-inline" role="form" data-game="44">
          <div class="form-group">
            <label class="sr-only">Название</label>
            <input type="text" class="form-control" name="title" placeholder="Название игры" value="<?=@$games['44']->getGameTitle()?>">
          </div>
          <div class="form-group">
            <div class="input-group">
                <label class="sr-only"></label>
                <input class="form-control" type="text" name="price" placeholder="Стоимость" value="<?=@$games['44']->getGamePrice()?>">
            </div>
          </div>
          баллов.
          <div class="form-group">
            <div class="input-group">
                <label class="sr-only"></label>
                <button class="btn btn-md btn-warning items-modal">Добавить товар</button>
            </div>
          </div>
          <div class="form-group">
            <div class="input-group">
                <label class="sr-only"></label>
                <button class="btn btn-md btn-success save-game"> Сохранить</button>
            </div>
          </div>
        </form> 
        <div class="row-fluid">&nbsp;</div>
        <div class="row-fluid prize-holder">
          <? if (@$games['44']->getPrizes()) { ?>
            <? foreach ($games['44']->loadPrizes() as $prize) { ?>
              <img src="/filestorage/shop/<?=$prize->getImage()?>" data-item-id="<?=$prize->getId()?>" width="105" height="105"/>
            <? } ?>
          <? } ?>
        </div>
    </div>
    <hr />
    <div class="row-fluid">
        <h4>Игра 5 из 7</h4>
    </div>
    <div class="row-fluid">
        <form class="form-inline" role="form" data-game="55">
          <div class="form-group">
            <label class="sr-only">Название</label>
            <input type="text" class="form-control" name="title" placeholder="Название игры" value="<?=@$games['55']->getGameTitle()?>">
          </div>
          <div class="form-group">
            <div class="input-group">
                <label class="sr-only"></label>
                <input class="form-control" type="text" name="price" placeholder="Стоимость" value="<?=@$games['55']->getGamePrice()?>">
            </div>
          </div>
          баллов.
          <div class="form-group">
            <div class="input-group">
                <label class="sr-only">Вариантов</label>
                <input class="form-control" type="text" name="tries" placeholder="Кол-во кликов" value="<?=@$games['55']->getTriesCount()?>">
            </div>
          </div>
          <div class="form-group">
            <div class="input-group">
                <label class="sr-only"></label>
                <button class="btn btn-md btn-warning items-modal">Добавить товары</button>
            </div>
          </div>
          <div class="form-group">
            <div class="input-group">
                <label class="sr-only"></label>
                <button class="btn btn-md btn-success save-game"> Сохранить</button>
            </div>
          </div>
          <div class="row-fluid">&nbsp;</div>
          <div class="row-fluid prize-holder">
            <? if (@$games['55']->getPrizes()) { ?>
              <? foreach ($games['55']->loadPrizes() as $prize) { ?>
                <img src="/filestorage/shop/<?=$prize->getImage()?>" data-item-id="<?=$prize->getId()?>" width="105" height="105"/>
              <? } ?>
            <? } ?>
          </div>
        </form> 
    </div>
    <div class="row-fluid">
        <h4>Случайная игра</h4>
    </div>
    <div class="row-fluid">
        <form class="form-inline" role="form" data-game="quickgame">
            <div class="form-group">
                <label class="sr-only">Название</label>
                <input type="text" class="form-control" name="title" placeholder="Название игры" value="<?=@$games['quickgame']->getGameTitle()?>">
            </div>
            <div class="form-group">
                <div class="input-group">
                    <label class="sr-only"></label>
                    <input type="text" name="minFrom" class="form-control" placeholder="Периодичность" value="<?=@$games['quickgame']->getMinFrom()?>">
                </div>
            </div>
            мин.
            <div class="form-group">
                <div class="input-group">
                    <label class="sr-only"></label>
                    <button class="btn btn-md btn-success save-game"> Сохранить</button>
                </div>
            </div>
        </form>

    </div>
    <hr />
    </div>
</div>

<script>
  $('.items-modal').on('click', function() {
    var container = $(this).parents('form').parent();
    var gameId = container.find('form').data('game');
    $('#itemsModal').modal();
    $('#itemsModal').find('.thumbnail').off('click').on('click', function() {
        var img = $('<img src="' + $(this).find('img').attr('src') + '" data-item-id="' + $(this).data('id') + '" width="105" height="105"/>');
        if (gameId != '55') {
          container.find('.prize-holder').html(img);  
        } else {
          if (container.find('.prize-holder').find('img').length == 3) {
            container.find('.prize-holder').find('img').remove();
          }
          container.find('.prize-holder').append(img);
        }
        
        $('#itemsModal').modal('hide');
    });

    return false;
  });

  $('.save-game').on('click', function() {
    var button = $(this);
    var form = $(this).parents('form');
    var game = {};

    if (button.find('.glyphicon').length) {
      button.find('.glyphicon').remove();
    }
    game.identifier = form.data('game');
    switch(game.identifier) {
      case 'moment' :
        game.minFrom = form.find('input[name="minFrom"]').val();
        game.minTo = form.find('input[name="minTo"]').val();
        game.pointsWin = form.find('input[name="pointsWin"]').val();
          break;
      case 'quickgame' :
          game.title = form.find('input[name="title"]').val();
          game.minFrom = form.find('input[name="minFrom"]').val();
      break;
      case 33 : 
      case 44 :
      case 55 :
        game.title = form.find('input[name="title"]').val();
        game.price = form.find('input[name="price"]').val();
        game.prizes = [];

        form.parent().find('.prize-holder').find('img').each(function(id, item) {
          game.prizes.push($(item).data('itemId'));
        });
      case 55 :

      break;
      break;
    }

    if (game.identifier == 55) {
      game.tries = form.find('input[name="tries"]').val();      
    }

    $.ajax({
        url: "/private/chances",
        method: 'POST',
        data: game,
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                button.prepend($('<i class="glyphicon glyphicon-ok"></i>'));
            } else {
                alert(data.message);
            }
        }, 
        error: function() {
            alert('Unexpected server error');
        }
    });
    return false;
  });

</script>