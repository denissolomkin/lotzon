<div class="container-fluid">
    <div class="row-fluid">
        <h2>Спам</h2>
        <hr/>
    </div>
    <? if ($pager['pages'] > 1) {?>
        <div class="row-fluid">
            <div class="btn-group">
                <? for ($i=1; $i <= $pager['pages']; ++$i) { ?>
                    <button onclick="<?=href(array('page'=>$i))?>" class="btn btn-default btn-xs <?=($i == $pager['page'] ? 'active' : '')?>"><?=$i?></button>
                <? } ?>
            </div>
        </div>
    <? } ?> 
    <div class="row-fluid">
        <table class="table table-striped users messages">
            <thead>
                <th>Дата</th>
                <th style="min-width: 170px;">Игрок </th>
                <th style="min-width: 200px;">Информация </th>
                <th>Сообщение</th>
                <th style="width: 102px;">Options</th>
            </thead>
            <tbody>
                <? foreach ($list as $message) {
                    $player = new Player;
                    $player->setId($message->getPlayerId())->fetch()->initDates()->initCounters()->initStats();?>
                    <tr data-id="<?=$message->getId()?>" data-playerid="<?=$message->getPlayerId()?>">
                        <td><?=date('d.m.Y <b\r> H:i:s', $message->getDate())?></td>

                        <?php include('user_template.php');?>

                        <td class="text">
                                <?='<i data-toplayerid="'.$message->getToPlayerId().'" data-playerid="'.$message->getPlayerId().'" class="fa fa-reply pointer replies-trigger"></i> '?><?=$message->getText()?><?=$message->getImage()?"<br><img src='/filestorage/messages/".$message->getImage()."'>":''?>
                        </td>
                        <td>
                            <button class="btn btn-md btn-success status-trigger" data-status='1' data-id="<?=$message->getId()?>"><i class="glyphicon glyphicon-ok"></i></button>
                            <button class="btn btn-md btn-danger remove-trigger" data-id="<?=$message->getId()?>"><i class="glyphicon glyphicon-trash"></i></button>
                        </td>
                    </tr>
                <? } ?>
            </tbody>
        </table>
    </div>

    <? if ($pager['pages'] > 1) {?>
        <div class="row-fluid">
            <div class="btn-group">
                <? for ($i=1; $i <= $pager['pages']; ++$i) { ?>
                    <button onclick="<?=href(array('page'=>$i),$defaults)?>" class="btn btn-default btn-xs <?=($i == $pager['page'] ? 'active' : '')?>"><?=$i?></button>
                <? } ?>
            </div>
        </div>
    <? } ?> 
</div>

<script>


    $('.replies-trigger').on('click', function() {

        $.ajax({
            url: "/private/messages/list/" + $(this).data('playerid') + '/' +  $(this).data('toplayerid'),
            method: 'GET',
            async: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    var tdata = '';
                    $(data.data.messages).each(function(id, tr) {
                        console.log(tr);
                        tdata += '<tr>' +
                        '<td>'+tr.Date+'</td>' +
                        '<td> <span style="display: flex;">' +
                            '<div onclick="window.open(\'/private/users?search[where]=Id&search[query]='+tr.PlayerId+'\')" class="pointer"><i class="fa fa-user"></i> '+tr.PlayerName+'</div>' +
                            ' -> ' +
                            '<div onclick="window.open(\'/private/users?search[where]=Id&search[query]='+tr.ToPlayerId+'\')" class="pointer"><i class="fa fa-user"></i> '+tr.ToPlayerId+'</div>' +
                            '</span>' +
                            tr.Text+'</td>' +
                        '<td>'
                            + (tr.Image?'<img src="/filestorage/messages/'+tr.Image+'">':'')
                            + '</td>'
                    });
                    $("#messages-holder").find('tbody').html(tdata);


                    $("#messages-holder").modal();
                    $("#messages-holder").find('.cls').on('click', function() {
                        $("#messages-holder").modal('hide');
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

    $('.status-trigger').on('click', function() {

        var that = $(this);
        $.ajax({
            url: '/private/messages/approve/' +that.data('id'),
            method: 'GET',
            async: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    that.closest('tr').remove();
                    $('#count-spam').text(data.data.count);
                } else {
                    alert(data.message);
                }
            },
            error: function() {
                alert('Unexpected server error');
            }
        });
    });

    $('.remove-trigger').on('click', function() {

        var that = $(this);
        $.ajax({
            url: '/private/messages/delete/' +that.data('id'),
            method: 'GET',
            async: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    that.closest('tr').remove();
                    $('#count-spam').text(data.data.count);
                } else {
                    alert(data.message);
                }
            },
            error: function() {
                alert('Unexpected server error');
            }
        });

    });

</script>

<?php

function href($args = array())
{
    $where = array();

    foreach ($args as $key => $value)
        if (isset($value))
            $where[] = $key . '=' . $value;

    return "document.location.href='/private/messages" . (!empty($where)?'?':'') . implode('&', $where) . "'";
}
?>

<? if($frontend) require_once($frontend.'_frontend.php'); ?>