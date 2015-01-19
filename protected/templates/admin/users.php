
<div class="container-fluid">

    <div class="row-fluid">
        <h2>
            Пользователи (<?=$playersCount?>)
            <div class="flex"><?=($search['query']?'<button class="btn btn-md btn-success" onclick="history.back();"><i class="glyphicon glyphicon-arrow-left"></i></button>':'');?>
                <button class="btn btn-md btn-info search-users"><i class="glyphicon glyphicon-search"></i></button>
            </div>
            <div class="right">
                <!--button class="btn btn-md btn-info filter-trigger" data-id="0"><span class="glyphicon glyphicon-filter" aria-hidden="true"></span></button--><button class="btn btn-md btn-warning notices-trigger" data-id="0"><span class="glyphicon glyphicon-bell" aria-hidden="true"></span></button>
            </div>
        </h2>
    </div>  <hr/>
    <? if ($pager['pages'] > 1) {?>
        <div class="row-fluid">
            <div class="btn-group">
                <? for ($i=1; $i <= $pager['pages']; ++$i) { ?>
                    <button onclick="document.location.href='/private/users?page=<?=$i?>&sortField=<?=$currentSort['field']?>&sortDirection=<?=$currentSort['direction'].($search['query']?'&search[where]='.$search['where'].'&search[query]='.$search['query']:'')?>'" class="btn btn-default btn-xs <?=($i == $pager['page'] ? 'active' : '')?>"><?=$i?></button>
                <? } ?>
            </div>
        </div>
    <? } ?>
    <div class="row-fluid">
        <table class="table table-striped users">
            <thead>
                <th>ID <?=sortIcon('Id', $currentSort, $pager, $search)?></th>
                <th>ФИО</th>
                <th>Никнейм</th>
                <th>Email <?=sortIcon('Valid', $currentSort, $pager, $search)?></th>
                <th class="icon"><?=sortIcon('Country', $currentSort, $pager, $search, 'globe')?></th>
                <th>Регистрация <?=sortIcon('DateRegistered', $currentSort, $pager, $search)?></th>
                <th style="min-width: 120px;"><?=sortIcon('CountIP', $currentSort, $pager, $search, 'map-marker')?></th>
                <th class="icon"><?=sortIcon('CountCookieId', $currentSort, $pager, $search, 'flag')?></th>
                <th class="icon"><?=sortIcon('CountReferal', $currentSort, $pager, $search, 'user')?></th>
                <th>Login / Ping <?=sortIcon('DateLogined', $currentSort, $pager, $search)?></th>
                <th class="icon"><?=sortIcon('GamesPlayed', $currentSort, $pager, $search, 'gift')?></th>
                <th class="icon"><?=sortIcon('TicketsFilled', $currentSort, $pager, $search, 'tags')?></th>
                <th class="icon"><?=sortIcon('AdBlock', $currentSort, $pager, $search ,'exclamation-sign')?></th>
                <th>Денег <?=sortIcon('Money', $currentSort, $pager, $search)?></th>
                <th>Баллы <?=sortIcon('Points', $currentSort, $pager, $search)?></th>
                <th width="100">Options</th>
            </thead>
            <tbody>
                <? foreach ($list as $player) { ?>
                    <tr id="user<?=$player->getId()?>" class="<?=$player->getBan()?'danger':''?>">
                        <td><?=$player->getId()?></td>
                        <td class="profile-trigger pointer" data-id="<?=$player->getId()?>">
                            <div <? if($player->getAvatar()) : ?>data-toggle="tooltip" data-html="1" data-placement="auto" title="<img src='../filestorage/avatars/<?=(ceil($player->getId() / 100)) . '/'.$player->getAvatar()?>'>"<? endif ?>>
                            <?=($player->getSurname() . " " . $player->getName() . " " . $player->getSecondName())?><? if($player->getAvatar() AND 0) echo '<img src="../filestorage/'.'avatars/' . (ceil($player->getId() / 100)) . '/'.$player->getAvatar().'">'?></td>
                            </div>
                        <td class="profile-trigger pointer" data-id="<?=$player->getId()?>">
                            <div <? if($player->getAvatar()) : ?>data-toggle="tooltip" data-html="1" data-placement="auto" title="<img src='../filestorage/avatars/<?=(ceil($player->getId() / 100)) . '/'.$player->getAvatar()?>'>"<? endif ?>>
                            <?=($player->getNicName())?><?=($player->isOnline()?'<i class="online right">•</i>':'');?>
                            </div>
                        </td>
                        <td class="<?=$player->getValid() ? "success" : "danger"?>"><?=$player->getEmail()?>
                            <div class="right">
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
                        </td>


                        <td class="country"><?=$player->getCountry()?></td>
                        <td class="nobr"><?=$player->getDateRegistered('d.m.Y H:i')?></td>
                        <td <?=($player->getLastIP() || $player->getIP()?"onclick=\"location.href='?search[where]=Ip&search[query]=".$player->getIP().($player->getLastIP() && $player->getIP()?',':'').$player->getLastIP():'')?>'" class="pointer nobr div-ips">
                            <? if($player->getCounters()['Ip']>1) : ?>
                                <div class="label label-danger label-ips"><?=$player->getCounters()['Ip']?></div>
                           <? endif ?><?=($player->getLastIP()?'<div class="ips">'.$player->getIP().'<br>'.$player->getLastIP().'</div>':$player->getIP())?></td>
                        <td <?=(($player->getCounters()['CookieId']>1)
                            ?'onclick="location.href=\'?search[where]=CookieId&search[query]='.$player->getCookieId().'\';" class="pointer danger"><span class="label label-danger">'.$player->getCounters()['CookieId'].'</span> #' . $player->getCookieId():($player->getCookieId()?'class="success">':'>'))?>
                        </td>
                        <td <?=($player->getReferalId()?'onclick="location.href=\'?search[where]=Id&search[query]='.$player->getReferalId().'\';" class="pointer ':' class="')?><?=$player->getReferalId() ? "success" : "danger"?>">
                            <?if($player->getCounters()['Referal']>1) {?> <span class="label label-info"><?=$player->getCounters()['Referal']?></span>
                            <?}?>
                            <?=$player->getReferalId() ? "#" . $player->getReferalId() : "&nbsp;"?></td>
                        <td class="<?=($player->getDateLastLogin()?(($player->getDateLastLogin() < strtotime('-7 day', time())) ? "warning" : "success"):'danger')?>">
                            <?=($player->getOnlineTime()?'<div class="datestamps nobr right">'.($player->getDateLastLogin('d.m.Y&\nb\sp;H:i')).'<br>'.(str_replace($player->getDateLastLogin('d.m.Y'),'',$player->getOnlineTime('d.m.Y H:i'))).'</div>':($player->getDateLastLogin()?'<div class="right">'.$player->getDateLastLogin('d.m.Y H:i').'</div>':''))?>

                        </td>
                        <td <?=($player->getGamesPlayed()?'class="stats-trigger pointer success" data-id='.$player->getId().'"':'class="danger"')?>><?=($player->getGamesPlayed()?:'нет')?></td>
                        <td class="<?=$player->isTicketsFilled() || $player->getGamesPlayed()?"tickets-trigger pointer ":''?> <?=$player->isTicketsFilled() ? 'success' : 'danger'?>" data-id="<?=$player->getId()?>"><?=$player->isTicketsFilled() ?: 'нет'?></td>
                        <td>
                            <? if($player->getDateAdBlocked()) :?>
                            <button class="btn btn-xs btn-<?=($player->getAdBlock()?'danger':($player->getDateAdBlocked() < strtotime('-7 day', time()) ? "success" : "warning" ))?> logs-trigger" data-action="AdBlock" data-id="<?=$player->getId()?>">
                                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><?=($player->getCounters()['AdBlock']?:'')?></button>
                            <? endif ?>
                        </td>
                        <td class="transactions-trigger pointer" data-id="<?=$player->getId()?>"><?=$player->getMoney()?> <?=$player->getCountry() == 'UA' ? 'грн' : 'руб'?></td>
                        <td class="transactions-trigger pointer" data-id="<?=$player->getId()?>"><?=$player->getPoints()?></td>
                        <td><div class="right nobr">

                            <button class="btn btn-xs btn-<?=($player->getCounters()['Note']?'danger':'warning');?> notes-trigger" data-type="Note" data-id="<?=$player->getId()?>">
                                <span class="glyphicon glyphicon-edit" aria-hidden="true"></span><?=$player->getCounters()['Note']>1?$player->getCounters()['Note']:'';?>
                            </button>
                            <button class="btn btn-xs btn-<?=($player->getCounters()['Notice']?'success':'warning');?> notices-trigger" data-type="Message" data-id="<?=$player->getId()?>">
                                <span class="glyphicon glyphicon-bell" aria-hidden="true"></span><?=$player->getCounters()['Notice']>1?$player->getCounters()['Notice']:''?>
                            </button>

                                <? if ($player->getCounters()['MyReferal']>0): ?>
                                    <button class="btn btn-xs btn-success" onclick="location.href='?search[where]=ReferalId&search[query]=<?=$player->getId();?>'">
                                        <span class="glyphicon glyphicon-user" aria-hidden="true"></span><?=($player->getCounters()['MyReferal']>1?$player->getCounters()['MyReferal']:'');?>
                                    </button>
                                <? endif ?>

                                <? if ($player->getCounters()['Review']>0): ?>
                            <button class="btn btn-xs btn-success reviews-trigger" data-id="<?=$player->getId()?>">
                                <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span><?=$player->getCounters()['Review']>1?$player->getCounters()['Review']:''?>
                            </button>
                            <? endif ?>

                                <? if ($player->getCounters()['Order']>0): ?>
                            <button class="btn btn-xs btn-success orders-trigger" data-id="<?=$player->getId()?>">
                                <span class="glyphicon glyphicon-tag" aria-hidden="true"></span><?=($player->getCounters()['Order']>1?$player->getCounters()['Order']:'');?>
                            </button>
                            <? endif ?>
                            <!--button class="btn btn-xs btn-warning transactions-trigger" data-id="<?=$player->getId()?>">T</button>
                            <button class="btn btn-xs btn-warning stats-trigger" data-id="<?=$player->getId()?>">Р</button-->
                            <? if ($player->getCounters()['Log']>0): ?>
                            <button class="btn btn-xs btn-<?=($player->getCounters()['Log']>1?'danger':(($player->getCounters()['Log']==1 AND $player->getValid())?'success':'warning'))?> logs-trigger" data-id="<?=$player->getId()?>">
                                <span class="glyphicon glyphicon-time" aria-hidden="true"></span><?=$player->getCounters()['Log']>1?$player->getCounters()['Log']:''?>
                            </button>
                             <? endif ?>
                                <button class="btn btn-xs btn-danger ban-trigger" data-id="<?=$player->getId()?>"><span class="glyphicon glyphicon-lock" aria-hidden="true"></button>
                                <button class="btn btn-xs btn-danger delete-trigger" data-id="<?=$player->getId()?>"><span class="glyphicon glyphicon-trash" aria-hidden="true"></button>
                            </div>
                        </td>
                    </tr>
                <? } ?>
            </tbody>
        </table>
    </div>
</div>

<script>

/* SEARCH BLOCK */
$('.search-users').on('click', function() {
    if($(this).next().hasClass('input-md')){
        $(this).parent().children('.search-form').remove();
    }else{


        var input = $('<input class="form-control input-md search-form" value="<?=(isset($search['query'])?$search['query']:'');?>" style="width:260px;display:inline-block;" placeholder="id, фио, ник или email...">' +
        '<select id="search_where" class="form-control input-md search-form" style="width: 120px;display: inline-block;">' +
        '<option value="">Везде</option>' +
        '<option value="Id">Id</option>' +
        '<option value="Ip">Ip</option>' +
        '<option value="NicName">Ник</option>' +
        '<option value="ReferalId">Реферал</option>' +
        '<option value="CookieId">Cookie</option>' +
        '<option value="CONCAT(`Surname`,`Name`)">Фио</option>' +
        '<option value="Email">Email</option></select>');
        var sccButton = $('<button class="btn btn-md btn-success search-form"><i class="glyphicon glyphicon-ok"></i></button>')
        var cnlButton = $('<button class="btn btn-md btn-danger search-form"><i class="glyphicon glyphicon-remove"></i></button>');
        var button = $(this);

        cnlButton.insertAfter(button);
        sccButton.insertAfter(button);
        input.insertAfter(button);
        /*button.hide();*/

        cnlButton.on('click', function() {
            url="/private/users";
            document.location.href=url;
        });

        cnlButton.on('click', function() {
            input.remove();
            sccButton.remove();
            cnlButton.remove();
            button.show();
        });

        sccButton.on('click', function() {
            if(input.val()){
                url="/private/users?search[query]="+input.val()+
                ($("#search_where").val() ? "&search[where]="+$("#search_where").val(): '');
                document.location.href=url;
            }
        });
    }

});

<? if(isset($search['query']) and $search['query']){?>
$('.search-users').trigger('click');
$('#search_where').val('<?=$search['where']?>');

<? } ?>
/* END SEARCH BLOCK */

</script>

<?php

    function sortIcon($currentField, $currentSort, $pager, $search, $icon=null)
    {
        $icon = '<a href="/private/users?page=1&sortField=%s&sortDirection=%s'.($search['query']?'&search[where]='.$search['where'].'&search[query]='.$search['query']:'').'">'.($icon?'<span class="glyphicon glyphicon-'.$icon.'" aria-hidden="true"></span>':'').'<i class="glyphicon glyphicon-chevron-%s"></i>';
        if ($currentField == $currentSort['field']) {
            $icon = vsprintf($icon, array(
                $currentField,
                $currentSort['direction'] == 'desc' ? 'asc' : 'desc',
                $currentSort['direction'] == 'desc' ? 'down' : 'up',
            ));
        } else {
            $icon = vsprintf($icon, array(
                $currentField,
                'desc',
                'up',
            ));
        }

        return $icon;
    }
?>

<? if($frontend)
    require_once(PATH_TEMPLATES.$frontend);?>