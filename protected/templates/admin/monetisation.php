<div class="container-fluid">    
    <div class="row-fluid" id="items">
        <h2>Запросы на вывод товаров <button class="btn btn-success pull-right" onclick="location.href='#money'">Запросы на вывод денег</button></h2>        
        <hr />
    </div>
    <div class="row-fluid">
        <table class="table table-striped">
            <thead>
                <th>#ID</th>
                <th>Дата заказа</th>
                <th>Игрок</th>
                <th>Товар</th>
                <th>Данные</th>
                <th>Стоимость</th>
                <th>Options</th>
            </thead>
            <tbody>
                <? foreach ($list as $order) { ?>
                    <tr>
                        <td><?=$order->getId()?></td>
                        <td><?=date('d.m.Y', $order->getDateOrdered())?></td>
                        <td><?=$order->getPlayer()->getEmail()?></td>
                        <td><?=$order->getItem()->getTitle()?></td>
                        <td>
                            ФИО: <?=$order->getSurname()?> <?=$order->getName()?> <?=$order->getSecondName()?> <br />
                            Телефон: <?=$order->getPhone()?> <br />
                            Адрес: <?=($order->getRegion() ? $order->getRegion() . ' обл.,' : '')?> г. <?=$order->getCity()?>, <?=$order->getAddress()?>

                        </td>
                        <td><?=($order->getChanceGameId() ? 'Выиграл в шанс' : $order->getItem()->getPrice())?></td>
                        <td width="15%">
                            <button data-id="<?=$order->getPlayer()->getId()?>" class="btn btn-md stats-trigger btn-warning"><i class="glyphicon glyphicon-search"></i></button>&nbsp;
                            <button data-id="<?=$order->getId()?>" class="btn btn-md approve btn-success"><i class="glyphicon glyphicon-ok"></i></button>&nbsp;
                            <button data-id="<?=$order->getId()?>" class="btn btn-md decline btn-danger" data-target="#deleteConfirm"><i class="glyphicon glyphicon-remove"></i></button>
                        </td>
                    </tr>   
                <? } ?>
            </tbody>
        </table>
    </div>
    <div class="row-fluid" id="money">
        <h2>Запросы на вывод денег  <button class="btn btn-success pull-right" onclick="location.href='#items'">Запросы на вывод товаров</button></h2>
        <hr />
    </div>
    <div class="row-fluid">
        <table class="table table-striped">
            <thead>
                <th>#ID</th>
                <th>Дата заказа</th>
                <th>Игрок</th>
                <th>Платежная сис-ма</th>
                <th>Данные</th>
                <th>Options</th>
            </thead>
            <tbody>
                <? foreach ($moneyOrders as $order) { ?>
                    <tr>
                        <td><?=$order->getId()?></td>
                        <td><?=date('d.m.Y', $order->getDateOrdered())?></td>
                        <td><?=$order->getPlayer()->getEmail()?></td>
                        <td><?=$order->getType()?></td>
                        <td>
                            <? foreach ($order->getData() as $key => $data) { ?>
                                <?=$data['title']?>: <?=$data['value']?> <?=($data['title'] == 'Cумма' ? ($order->getPlayer()->getCountry() == 'UA' ? 'грн' : 'руб') : '')?> <br />
                            <? } ?>
                        </td>
                        <td width="15%">
                            <button data-id="<?=$order->getPlayer()->getId()?>" class="btn btn-md stats-trigger btn-warning"><i class="glyphicon glyphicon-search"></i></button>&nbsp;
                            <button data-id="<?=$order->getId()?>" class="btn btn-md approve money btn-success"><i class="glyphicon glyphicon-ok"></i></button>&nbsp;
                            <button data-id="<?=$order->getId()?>" class="btn btn-md decline money btn-danger" data-target="#deleteConfirm"><i class="glyphicon glyphicon-remove"></i></button>
                        </td>
                    </tr>   
                <? } ?>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="stats-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Game stats</h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <th>#ID лотереи</th>
                        <th>Дата</th>
                        <th>Баллов выиграно</th>
                        <th>Денег выиграно</th>                        
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default cls">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<script>
    $('.stats-trigger').on('click', function() {
        $.ajax({
            url: "/private/users/stats/" + $(this).data('id'),
            method: 'GET',
            async: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    var tdata = ''
                    $(data.data.lotteries).each(function(id, lottery) {
                        tdata += '<tr><td>'+lottery.LotteryId+'</td><td>'+lottery.Date+'</td><td>'+lottery.PointsWin+'</td><td>'+lottery.MoneyWin+'</td></tr>'
                    });
                    $("#stats-holder").find('tbody').html(tdata);
                    $("#stats-holder").modal();
                    $("#stats-holder").find('.cls').on('click', function() {
                        $("#stats-holder").modal('hide');
                    })      
                } else {
                    alert(data.message);
                }
            },
            error: function() {
                alert('Unexpected server error');        
            }
        });   
    });

    $('.approve').on('click', function() {
        location.href = '/private/monetisation/approve/' + $(this).data('id') + '?money=' + ($(this).hasClass('money') ? 1 : 0);
    });
    $('.descline').on('click', function() {
        location.href = '/private/monetisation/decline/' + $(this).data('id') + '?money=' + ($(this).hasClass('money') ? 1 : 0);
    });
</script>