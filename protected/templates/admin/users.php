<div class="container-fluid">
    <div class="row-fluid">
        <h2>Пользователи (<?=$playersCount?>)</h2>
        <hr/>
    </div>

    <div class="row-fluid">
        <table class="table table-striped">
            <thead>
                <th>#ID</th>
                <th>ФИО</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Страна</th>
                <th>Дата регистрации</th>
                <th>Игр сыграно</th>
                <th>Денег</th>
                <th>Баллов</th>
            </thead>
            <tbody>
                <? foreach ($list as $player) { ?>
                    <tr data-id="<?=$player->getId()?>" class="stats-trigger" style="cursor:pointer">
                        <td><?=$player->getId()?></td>
                        <td><?=($player->getSurname() . " " . $player->getName() . " " . $player->getSecondName())?></td>
                        <td><?=$player->getEmail()?></td>
                        <td><?=$player->getPhone()?></td>
                        <td><?=$player->getCountry()?></td>
                        <td><?=$player->getDateRegistered('d.m.Y')?></td>
                        <td><?=$player->getGamesPlayed()?></td>
                        <td><?=$player->getMoney()?></td>
                        <td><?=$player->getPoints()?></td>
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
    })

</script>
