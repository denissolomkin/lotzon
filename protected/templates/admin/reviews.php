<?php
$defaults = array(
    'status' => $status,
    'module' => $module,
    'auto'   => $auto
);
?>
<div class="container-fluid">

    <div class="row-fluid">
        <button onclick="<?=href(array('status'=>3, 'auto'=>null),$defaults)?>" class="btn right btn-md btn-danger <?=($status==3 ? 'active' : '')?>"><i class="glyphicon glyphicon-exclamation-sign"></i> Бан</button>
        <button onclick="<?=href(array('status'=>2, 'auto'=>null),$defaults)?>" class="btn right btn-md btn-default <?=($status==2 ? 'active' : '' )?>"><i class="glyphicon glyphicon-trash"></i> Удалены</button>
        <button onclick="<?=href(array('status'=>1, 'auto'=>'notzero'),$defaults)?>" class="btn right btn-md btn-success <?=($status==1 && $auto  ? 'active' : '')?>"><i class="glyphicon glyphicon-ok"></i> Одобрены</button>
        <button onclick="<?=href(array('status'=>1, 'auto'=>0),$defaults)?>" class="btn right btn-md btn-primary <?=($status==1 && !$auto ? 'active' : '')?>"><i class="glyphicon glyphicon-pushpin"></i> Авто</button>
        <button onclick="<?=href(array('status'=>0, 'auto'=>null),$defaults)?>" class="btn right btn-warning btn-md <?=(!$status ? 'active' : '')?>"><i class="glyphicon glyphicon-time"></i> Ожидают</button>
        <h2>
            <button type="button" onclick="<?=href(array('module'=>'comments'))?>" class="btn btn-default <?=($module=='comments' ? 'active' : '')?>"">Комменты</button>
            <button type="button" onclick="<?=href(array('module'=>'blog'))?>" class="btn btn-default <?=($module=='blog' ? 'active' : '')?>"">Блог</button>
            <button type="button" data-sector="<?=$key?>" class="btn btn-success edit-trigger"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button>
        </h2>
        <hr/>
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
    <div class="row-fluid">
        <table class="table table-striped users reviews">
            <thead>
                <th>Дата</th>
                <th style="min-width: 170px;">Игрок </th>
                <th style="min-width: 200px;">Информация </th>
                <th>Отзыв</th>
                <th style="width: 234px;">Options</th>
            </thead>
            <tbody>
                <? while ($reviews = array_pop($list))
                    foreach ($reviews as $review) {
                    $player = new Player;
                    $player->setId($review->getPlayerId())->fetch()->initDates()->initCounters();?>
                    <tr data-id="<?=$review->getId()?>" data-playerid="<?=$review->getPlayerId()?>" data-ispromo="<?=$review->isPromo()?>" data-parentid="<?=$review->getReviewId()?>">
                        <td><?=$review->getDate('d.m.Y <b\r> H:i:s')?></td>
                        <td class="" style="position: relative;" >
                            <div onclick="window.open('/private/users?search[where]=Id&search[query]=<?=$player->getId();?>')" data-id="<?=$player->getId()?>" class="left pointer<?=$player->getBan()?' danger':''?>" style="width: 80%;" <? if($player->getAvatar()) : ?>data-toggle="tooltip" data-html="1" data-placement="auto" title="<img style='width:32px;' src='../filestorage/avatars/<?=(ceil($player->getId() / 100)) . '/'.$player->getAvatar()?>'>"<? endif ?>>
                                <?=$player->getNicname()?>
                                <br>
                                <?=$player->getName()?> <?=$player->getSurName()?> <?=$player->getSecondName()?>

                            </div>
                            <div style="position: relative;text-align: right;" class="pointer profile-trigger<?=$player->getBan()?' danger':''?>" data-id="<?=$player->getId()?>">
                                <?=($player->getDates('Ping')>time()-SettingsModel::instance()->getSettings('counters')->getValue('PLAYER_TIMEOUT')?'<i class="online" style="margin-top: 5px;   line-height: 0px;">•</i>':'');?>
                                <?=$player->getCountry()?>
                            </div>
                            <div class="right games-holder">

                                <? if($player->getGamesPlayed()){?>
                                    <span class="stats-trigger pointer success" data-id="<?=$player->getId()?>">
                                        <i class="fa fa-gift <?=($player->getGamesPlayed() ? '' : 'text-danger' )?>"></i><?=$player->getGamesPlayed()?>
                                    </span>
                                <?}?>

                                <? if($player->getDates('QuickGame')){?>
                                    <i class="fa fa-puzzle-piece <?=
                                    ($player->getDates('QuickGame') > strtotime('-2 day', time()) ? 'text-success' :
                                        ($player->getDates('QuickGame') > strtotime('-7 day', time()) ? 'text-warning' : 'text-danger')
                                    )?>"></i>
                                <?}?>

                                <? if($player->getDates('Moment')){?>
                                    <i class="fa fa-rocket <?=
                                    ($player->getDates('Moment') > strtotime('-2 day', time()) ? 'text-success' :
                                        ($player->getDates('Moment') > strtotime('-7 day', time()) ? 'text-warning' : 'text-danger')
                                    )?>"></i>
                                <?}?>

                                <? if($player->getDates('ChanceGame')){?>
                                    <i class="fa fa-star <?=
                                    ($player->getDates('ChanceGame') > strtotime('-2 day', time()) ? 'text-success' :
                                        ($player->getDates('ChanceGame') > strtotime('-7 day', time()) ? 'text-warning' : 'text-danger')
                                    )?>"></i>
                                <?}?>

                                <? if($player->getCounters('WhoMore')){?>
                                    <span <?=($player->getCounters('WhoMore')*100 > SettingsModel::instance()->getSettings('counters')->getValue('DANGER_MAX_WIN') || $player->getCounters('WhoMore')*100 < SettingsModel::instance()->getSettings('counters')->getValue('DANGER_MIN_WIN')? 'class="text-danger"' : '' )?>>
                                    <nobr><i class="fa fa-sort-numeric-asc"></i><?=ceil($player->getCounters('WhoMore')*100).'%'?></nobr>
                                </span>
                                <?}?>

                                <? if($player->getCounters('SeaBattle')){?>
                                    <span <?=($player->getCounters('SeaBattle')*100 > SettingsModel::instance()->getSettings('counters')->getValue('DANGER_MAX_WIN') || $player->getCounters('SeaBattle')*100 < SettingsModel::instance()->getSettings('counters')->getValue('DANGER_MIN_WIN')? 'class="text-danger"' : '' )?>>
                                    <nobr><i class="fa fa-ship"></i><?=ceil($player->getCounters('SeaBattle')*100).'%'?></nobr>
                                </span>
                                <?}?>

                            </div>
                        </td>
                        <td class="contact-information <?=$player->getValid() ? "success" : "danger"?>">
                            <? if($player->getCounters('Mult')>1) : ?>
                                <div class="mults-trigger left pointer" data-id="<?=$player->getId()?>"><div class="label label-danger label-mult"><?=$player->getCounters('Mult')?></div>
                            <? endif ?><?=$player->getEmail()?><? if($player->getCounters('Mult')>1) : ?></div><? endif ?>
                            <div class="social-holder">
                            <?foreach($player->getAdditionalData() as $provider=>$info)
                            {
                                echo '<a href="javascript:void(0)" class="sl-bk '.$provider.($info['enabled']==1?' active':'').'"></a>
                                <div class="hidden">';
                                if(is_array($info))
                                    foreach ($info as $key=>$value) {
                                        echo $key.' : ';
                                        if(is_array($value))
                                        {
                                            $array=array();
                                            foreach($value as $k=>$v)
                                                $array[] = $k.' - '.$v;
                                            echo implode('<br>',$array).' ; ';
                                        }
                                        else
                                            echo $value.' ; ';
                                    }
                                else echo $info;
                                echo'</div>';
                            }?>
                            </div>
                            <br>

                            <div class="left">
                                <? if($player->getCounters()['Ip']>1):?>
                                    <button class="btn btn-xs btn-danger" <?=($player->getLastIP() || $player->getIP()?"onclick=\"window.open('users?search[where]=Ip&search[query]=".$player->getId():'')?>');">
                                    <span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span><?=$player->getCounters()['Ip']?>
                                </button>
                            <? endif ?>

                                <? if ($player->getDateAdBlocked()):?>
                                    <button class="btn btn-xs btn-<?=($player->getAdBlock()?'danger':($player->getDateAdBlocked() < strtotime('-14 day', time()) ? "success" : "warning" ))?> logs-trigger" data-action="AdBlock" data-id="<?=$player->getId()?>">
                                        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><?=($player->getCounters()['AdBlock']?:'')?>
                                    </button>
                                <? endif ?>

                                <? if(($player->getCookieId() && $player->getCookieId()!=$player->getId()) || $player->getCounters()['CookieId']>1) :?>
                                    <button class="btn btn-xs btn-danger" onclick="window.open('users?search[where]=CookieId&search[query]=<?=$player->getId();?>')">
                                        <span class="glyphicon glyphicon-flag" aria-hidden="true"></span><?=$player->getCounters()['CookieId']>1?$player->getCounters()['CookieId']:'';?>
                                    </button>
                                <? endif ?>

                                <? if (($orders=$player->getCounters('ShopOrder')+$player->getCounters('MoneyOrder'))>0): ?>
                                    <button class="btn btn-xs btn-success orders-trigger" data-id="<?=$player->getId()?>">
                                        <span class="glyphicon glyphicon-tag" aria-hidden="true"></span><?=($orders>1?$orders:'');?>
                                    </button>
                                <? endif ?>

                            </div >



                            <div class="right">

                                <button class="btn btn-xs btn-<?=($player->getCounters()['Note']?'danger':'warning');?> notes-trigger" data-type="Note" data-id="<?=$player->getId()?>">
                                    <span class="glyphicon glyphicon-edit" aria-hidden="true"></span><?=$player->getCounters()['Note']>1?$player->getCounters()['Note']:'';?>
                                </button>
                                <button class="btn btn-xs btn-<?=($player->getCounters()['Notice']?'success':'warning');?> notices-trigger" data-type="Message" data-id="<?=$player->getId()?>">
                                    <span class="glyphicon glyphicon-bell" aria-hidden="true"></span><?=$player->getCounters()['Notice']>1?$player->getCounters()['Notice']:''?>
                                </button>

                                <? if ($player->getCounters()['MyReferal']>0): ?>
                                    <button class="btn btn-xs btn-success" onclick="window.open('users?search[where]=ReferalId&search[query]=<?=$player->getId();?>')">
                                        <span class="glyphicon glyphicon-user" aria-hidden="true"></span><?=($player->getCounters()['MyReferal']>1?$player->getCounters()['MyReferal']:'');?>
                                    </button>
                                <? endif ?>

                                <? if ($player->getCounters()['MyInviter']>0): ?>
                                    <button class="btn btn-xs btn-success" onclick="window.open('users?search[where]=InviterId&search[query]=<?=$player->getId();?>')">
                                        <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span><?=($player->getCounters()['MyInviter']>1?$player->getCounters()['MyInviter']:'');?>
                                    </button>
                                <? endif ?>

                                <? if ($player->getCounters()['Review']>0): ?>
                                    <button class="btn btn-xs btn-success reviews-trigger" data-id="<?=$player->getId()?>">
                                        <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span><?=$player->getCounters()['Review']>1?$player->getCounters()['Review']:''?>
                                    </button>
                                <? endif ?>

                                <? if ($player->getCounters('Message')>0): ?>
                                    <button class="btn btn-xs btn-success messages-trigger" data-id="<?=$player->getId()?>">
                                        <span class="fa fa-inbox" aria-hidden="true"></span><?=$player->getCounters('Message')>1?$player->getCounters('Message'):''?>
                                    </button>
                                <? endif ?>

                                <? if ($player->getCounters()['Log']>0): ?>
                                    <button class="btn btn-xs btn-<?=($player->getCounters()['Log']>1?'danger':(($player->getCounters()['Log']==1 AND $player->getValid())?'success':'warning'))?> logs-trigger" data-id="<?=$player->getId()?>">
                                        <span class="glyphicon glyphicon-time" aria-hidden="true"></span><?=$player->getCounters()['Log']>1?$player->getCounters()['Log']:''?>
                                    </button>
                                <? endif ?>

                            </div>

                        </td>
                        <td class="text">
                            <div data-toggle="tooltip" title="<?=$review->getUserName()?>">
                                <?=$review->getReviewId()?'<i data-id="'.$review->getReviewId().'" class="fa fa-reply pointer replies-trigger"></i> ':''?><?=$review->getText()?><?=$review->getImage()?"<br><img src='/filestorage/reviews/".$review->getImage()."'>":''?>
                                <? if ($review->getModeratorName()) :?>
                                    <span class="right label label-danger"><?=$review->getModeratorName()?><? if($review->getComplain()) echo ': '.$review->getComplain(); ?></span>
                                <? endif; ?>
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-md btn-primary edit-trigger"><i class="glyphicon glyphicon-edit"></i></button>
                            <button class="btn btn-md btn-warning status-trigger<?=($status==0 ? ' hidden' : '' )?>" data-status='0' data-id="<?=$review->getId()?>"><i class="glyphicon glyphicon-time"></i></button>
                            <button class="btn btn-md btn-success status-trigger<?=($status==1 && $auto ? ' hidden' : '' )?>" data-status='1' data-id="<?=$review->getId()?>"><i class="glyphicon glyphicon-ok"></i></button>
                            <button class="btn btn-md btn-default status-trigger<?=($status==2 ? ' hidden' : '' )?>" data-status='2' data-id="<?=$review->getId()?>"><i class="glyphicon glyphicon-trash"></i></button>
                            <button class="btn btn-md btn-danger status-trigger<?=($status==3 ? ' hidden' : '' )?>" data-status='3' data-id="<?=$review->getId()?>"><i class="glyphicon glyphicon-exclamation-sign"></i></button>
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



<div class="modal fade" id="edit-review" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Редактирование отзыва</h4>
            </div>
            <div class="modal-body">
                <div class="row-fluid" id="errorForm" style="display:none">
                    <div class="alert alert-danger" role="alert">
                        <span class="error-container"></span>
                    </div>
                </div>

                <form class="form">
                    <input type="hidden" name="edit[Id]" id="edit-id" value="" />
                    <input type="hidden" name="edit[ParentId]" id="edit-parentid" value="" />
                    <input type="hidden" name="add[ParentId]" id="add-parentid" value="" />
                    <input type="hidden" name="add[Status]" value="1" />
                    <input type="hidden" name="add[Module]" value="<?=$module;?>" />

                    <div class="form-group">

                        <div id="edit-sector">
                        <div class="row-fluid">

                            <div class="col-my-4">
                                <label>Id пользователя</label>
                                <input type="input" class="form-control" name="edit[PlayerId]" id="edit-playerid" value="" />
                            </div>

                            <div class="col-my-4">
                                <label>Опубликовать в промо</label>
                                <select name="edit[IsPromo]" id="edit-ispromo" class="form-control"/>
                                <option value="0">Нет</option>
                                <option value="1">Да</option>
                                </select>
                            </div>

                            <div class="col-my-4">
                                <label>Статус</label>
                                <select name="edit[Status]" id="edit-status" class="form-control"/>
                                    <option value="1">Одобрен</option>
                                    <option value="0">Ожидает</option>
                                    <option value="2">Удален</option>
                                    <option value="3">Забанен</option>
                                </select>
                            </div>

                        </div>

                    <div class="row-fluid">
                    <div class="form-group">
                        <label class="control-label">Текст отзыва</label>
                        <textarea name="edit[Text]" class="form-control" id="edit-text"></textarea>
                    </div>
                    </div>
                    <div style="clear: both;"></div>

                        </div>
                    <hr/>
                        <button class="btn btn-md" type="button" onclick="$(this).hide().next().show().next().show().next().val(1);">Добавить ответ</button>
                        <button class="btn btn-md" type="button" onclick="$(this).hide().prev().show().next().next().hide().next().val(0);" style="display:none;">Убрать ответ</button>
                        <div style="display:none;">

                    <div class="row-fluid">
                        <div class="col-my-4">
                            <label>Id пользователя</label>
                            <input type="input" class="form-control" name="add[PlayerId]" id="add-playerid" value="<?=\SettingsModel::instance()->getSettings('counters')->getValue('USER_REVIEW_DEFAULT');?>"/>
                        </div>
                        <div class="col-my-4">
                            <label>Опубликовать в промо</label>
                            <select name="add[IsPromo]" class="form-control"/>
                            <option value="0">Нет</option>
                            <option value="1">Да</option>
                            </select>
                        </div>
                    </div>

                        <div style="clear: both;"></div>

                    <div class="row-fluid">
                        <div class="form-group">
                            <label class="control-label">Текст ответа</label>
                            <textarea name="add[Text]" class="form-control" id="add-text"></textarea>
                        </div>
                    </div>

                    </div>
                        <input type="hidden" name="answer" value="0">
                    </div>
                </form>

                <div class="row-fluid">
                    <button class="btn btn-md btn-success save pull-right">Сохранить</button>
                    <button class="btn btn-danger cls">Отмена</button>
                </div>
            </div>
        </div>
    </div>
</div>


<script>


    $('.replies-trigger').on('click', function() {

        $.ajax({
            url: "/private/reviews/list/" + $(this).data('id'),
            method: 'GET',
            async: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    var tdata = '';
                    $(data.data.reviews).each(function(id, tr) {
                        tdata += '<tr class="' + (
                                tr.Status == 0
                                    ? 'warning'
                                    : tr.Status == 1
                                        ? 'success'
                                        : tr.Status == 2
                                            ? 'default'
                                            : 'danger'
                            ) + '">' +
                        '<td>'+tr.Date+'</td>' +
                        '<td> <div onclick="window.open(\'/private/users?search[where]=Id&search[query]='+tr.PlayerId+'\')" class="pointer"><i class="fa fa-user"></i> '+tr.PlayerName+'</div>'+tr.Text+'</td>' +
                        '<td>'
                            + (tr.Image?'<img src="/filestorage/reviews/'+tr.Image+'">':'')
                            + (tr.ModeratorName ? '<span class="right label label-danger">' + tr.ModeratorName + (tr.Complain?': '+tr.Complain:'')+ '</span>' : '')
                            + '</td>'
                    });
                    $("#reviews-holder").find('tbody').html(tdata);


                    $("#reviews-holder").modal();
                    $("#reviews-holder").find('.cls').on('click', function() {
                        $("#reviews-holder").modal('hide');
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
            url: '/private/reviews/status/' +that.data('id') + '?status=' + that.data('status'),
            method: 'GET',
            async: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    that.closest('tr').remove();
                    $('#count-'+data.data.module).text(data.data.count);
                } else {
                    alert(data.message);
                }
            },
            error: function() {
                alert('Unexpected server error');
            }
        });
    });

    $('.edit-trigger').on('click', function() {

        var button = $(this);
        var tr = button.parents('tr').first();

        $("#edit-review").modal();
        $("#edit-review").find('.cls').off('click').on('click', function () {
            $("#edit-review").modal('hide');
        });

        $('#add-parentid').val(tr.data('parentid') ? tr.data('parentid') : (tr.data('id') ? tr.data('id') : null));
        $('#add-text').val('');

        if (!tr.length) {
            $('#edit-sector').hide();
            $('#edit-text').val('');
        }else{
            var name = ($('td div:eq(0)',tr).html()).split("<br>");
            $('#add-text').val(name[0].trim()+', ');
            $('#edit-sector').show();
            $('#edit-id').val(tr.data('id'));
            $('#edit-text').val(tr.find('.text').text().trim());
            $('#edit-playerid').val(tr.data('playerid'));
            $('#edit-ispromo').val(tr.data('ispromo') ? tr.data('ispromo') : 0).change();
        }

        $("#edit-review").find('.save').off('click').on('click', function() {
            var button =$(this);
            button.find('.fa').hide().parent().prepend($('<i class="fa fa-spinner fa-pulse"></i>'));
            var form =  $("#edit-review").find('form');

            $.ajax({
                url: "/private/reviews",
                method: 'POST',
                data: form.serialize(),
                async: true,
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        button.find('.fa').first().remove();
                        button.removeClass('btn-danger btn-success').addClass('btn-default').prepend($('<i class="fa fa-check"></i>'));
                        button.find('.fa').fadeOut(500);
                        window.setTimeout(function () {
                            button.find('.fa').show().filter(':not(.fa-save)').remove();
                        }, 500);

                        $('[name="Id"]', form).val(data.data.Id);
                        form.removeClass('label-danger');
                        location.href = '/private/reviews/?status=<?=$status?>';

                    } else {
                        button.find('.fa').first().remove();
                        button.removeClass('btn-success').addClass('btn-danger');
                        button.find('.fa').show().filter(':not(.fa-save)').remove();
                        alert(data.message);
                    }
                },
                error: function (data) {
                    button.find('.fa').first().remove();
                    button.removeClass('btn-success').addClass('btn-danger');
                    button.find('.fa').show().filter(':not(.fa-save)').remove();
                    alert('Unexpected server error');
                    console.log(data.responseText);
                }
            });
        });

    });

    $('.delete-trigger').on('click', function() {
        location.href = '/private/reviews/delete/' + $(this).data('id');
    });

</script>

<?php

function href($args = array(), $defaults = array())
{
    $args += $defaults;
    $where = array();

    foreach ($args as $key => $value)
        if (isset($value))
            $where[] = $key . '=' . $value;

    return "document.location.href='/private/reviews" . (!empty($where)?'?':'') . implode('&', $where) . "'";
}
?>

<? if($frontend) require_once($frontend.'_frontend.php'); ?>