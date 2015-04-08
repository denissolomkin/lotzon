
<div class="container-fluid">

    <div class="row-fluid">
        <button onclick="document.location.href='/private/reviews?status=2&sortField=<?=$currentSort['field']?>&sortDirection=<?=$currentSort['direction']?>'" class="btn right btn-md btn-danger <?=($status==2 ? 'active' : '' )?>"><i class="glyphicon glyphicon-ban-circle"></i> Отклоненные</button>
        <button onclick="document.location.href='/private/reviews?status=1&sortField=<?=$currentSort['field']?>&sortDirection=<?=$currentSort['direction']?>'" class="btn right  btn-md btn-success <?=($status==1 ? 'active' : '')?>"><i class="glyphicon glyphicon-ok"></i> Одобренные</button>
        <button onclick="document.location.href='/private/reviews?status=0&sortField=<?=$currentSort['field']?>&sortDirection=<?=$currentSort['direction']?>'" class="btn right btn-warning btn-md <?=(!$status ? 'active' : '')?>"><i class="glyphicon glyphicon-time"></i> На рассмотрении</button>
        <h2>Отзывы</h2>
        <hr/>
    </div>
    <? if ($pager['pages'] > 1) {?>
        <div class="row-fluid">
            <div class="btn-group">
                <? for ($i=1; $i <= $pager['pages']; ++$i) { ?>
                    <button onclick="document.location.href='/private/reviews?status=<?=$status?>&page=<?=$i?>&sortField=<?=$currentSort['field']?>&sortDirection=<?=$currentSort['direction']?>'" class="btn btn-default btn-md <?=($i == $pager['page'] ? 'active' : '')?>"><?=$i?></button>
                <? } ?>
            </div>
        </div>
    <? } ?> 
    <div class="row-fluid">
        <table class="table table-striped users">
            <thead>
                <th>Дата</th>
                <th style="min-width: 170px;">Игрок </th>
                <th>Информация </th>
                <th>Отзыв</th>
                <th style="width: 146px;">Options</th>
            </thead>
            <tbody>
                <? foreach ($list as $review) {
                    $player = new Player;
                    $player->setId($review->getPlayerId())->fetch()->initDates();?>
                    <tr data-id="<?=$review->getId()?>">
                        <td><?=$review->getDate('d.m.Y <b\r> H:m:s')?></td>
                        <td class="" style="position: relative;" >
                            <div onclick="window.open('users?search[where]=Id&search[query]=<?=$player->getId();?>')" data-id="<?=$player->getId()?>" class="left pointer<?=$player->getBan()?' danger':''?>" style="width: 80%;" <? if($player->getAvatar()) : ?>data-toggle="tooltip" data-html="1" data-placement="auto" title="<img style='width:32px;' src='../filestorage/avatars/<?=(ceil($player->getId() / 100)) . '/'.$player->getAvatar()?>'>"<? endif ?>>
                                <?=$player->getNicname()?>
                                <br>
                                <?=$player->getName()?> <?=$player->getSurName()?> <?=$player->getSecondName()?>

                            </div>
                            <div class="right" style="position: absolute;right: 5px;">
                                <div style="position: relative;" class="pointer profile-trigger<?=$player->getBan()?' danger':''?>" data-id="<?=$player->getId()?>">
                                    <?=($player->getOnlineTime()>time()-SettingsModel::instance()->getSettings('counters')->getValue('PLAYER_TIMEOUT')?'<i class="online" style="margin-top: 5px;   line-height: 0px;">•</i>':'');?>
                                    <?=$player->getCountry()?>
                                </div>
                                <? if($player->getGamesPlayed()){?> <i data-id="<?=$player->getId()?>" class="fa fa-gift pointer stats-trigger <?=($player->getGamesPlayed() ? '' : 'text-danger' )?>"><?=$player->getGamesPlayed()?></i><?}?>

                                <? if($player->getDateLastQuickGame()){?>
                                    <i class="fa fa-puzzle-piece <?=
                                    ($player->getDateLastQuickGame() > strtotime('-2 day', time()) ? 'text-success' :
                                        ($player->getDateLastQuickGame() > strtotime('-7 day', time()) ? 'text-warning' : 'text-danger')
                                    )?>"></i>
                                <?}?>

                                <? if($player->getDateLastMoment()){?>
                                    <i class="fa fa-rocket <?=
                                    ($player->getDateLastMoment() > strtotime('-2 day', time()) ? 'text-success' :
                                        ($player->getDateLastMoment() > strtotime('-7 day', time()) ? 'text-warning' : 'text-danger')
                                    )?>"></i>
                                <?}?>

                                <? if($player->getDateLastChance()){?>
                                    <i class="fa fa-star <?=
                                    ($player->getDateLastChance() > strtotime('-2 day', time()) ? 'text-success' :
                                        ($player->getDateLastChance() > strtotime('-7 day', time()) ? 'text-warning' : 'text-danger')
                                    )?>"></i>
                                <?}?>
                            </div>
                        </td>
                        <td class="<?=$player->getValid() ? "success" : "danger"?>"><?=$player->getEmail()?>
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
                            <br>

                            <div class="left">
                                <? if($player->getCounters()['Ip']>1):?>
                                    <button class="btn btn-xs btn-danger" <?=($player->getLastIP() || $player->getIP()?"onclick=\"window.open('users?search[where]=Ip&search[query]=".$player->getIP().($player->getLastIP() && $player->getIP()?',':'').$player->getLastIP():'')?>');">
                                    <span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span><?=$player->getCounters()['Ip']?>
                                </button>
                            <? endif ?>

                                <? if ($player->getDateAdBlocked()):?>
                                    <button class="btn btn-xs btn-<?=($player->getAdBlock()?'danger':($player->getDateAdBlocked() < strtotime('-14 day', time()) ? "success" : "warning" ))?> logs-trigger" data-action="AdBlock" data-id="<?=$player->getId()?>">
                                        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><?=($player->getCounters()['AdBlock']?:'')?>
                                    </button>
                                <? endif ?>

                                <? if(($player->getCookieId() && $player->getCookieId()!=$player->getId()) || $player->getCounters()['CookieId']>1) :?>
                                    <button class="btn btn-xs btn-danger" onclick="window.open('users?search[where]=CookieId&search[query]=<?=$player->getCookieId();?>')">
                                        <span class="glyphicon glyphicon-flag" aria-hidden="true"></span><?=$player->getCounters()['CookieId']>1?$player->getCounters()['CookieId']:'';?>
                                    </button>
                                <? endif ?>

                                <? if(($player->getCounters()['ShopOrder']+$player->getCounters()['MoneyOrder'])>1):?>
                                    <button class="btn btn-xs btn-danger orders-trigger" data-id="<?=$player->getId()?>">
                                        <span class="glyphicon glyphicon-tag" aria-hidden="true"></span><?=(($player->getCounters()['ShopOrder']+$player->getCounters()['MoneyOrder'])?:'')?>
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

                                <!--button class="btn btn-xs btn-warning transactions-trigger" data-id="<?=$player->getId()?>">T</button>
                            <button class="btn btn-xs btn-warning stats-trigger" data-id="<?=$player->getId()?>">Р</button-->
                                <? if ($player->getCounters()['Log']>0): ?>
                                    <button class="btn btn-xs btn-<?=($player->getCounters()['Log']>1?'danger':(($player->getCounters()['Log']==1 AND $player->getValid())?'success':'warning'))?> logs-trigger" data-id="<?=$player->getId()?>">
                                        <span class="glyphicon glyphicon-time" aria-hidden="true"></span><?=$player->getCounters()['Log']>1?$player->getCounters()['Log']:''?>
                                    </button>
                                <? endif ?>

                            </div>

                        </td>
                        <td><?=$review->getText()?><?=$review->getImage()?"<br><img src='/filestorage/reviews/".$review->getImage()."'>":''?></td>
                        <td>
                            <button class="btn btn-md btn-warning answer-trigger" data-id="<?=$review->getId()?>"><i class="glyphicon glyphicon-edit"></i></button>
                            <button class="btn btn-md btn-warning status-trigger<?=($status==0 ? ' hidden' : '' )?>" data-status='0' data-id="<?=$review->getId()?>"><i class="glyphicon glyphicon-time"></i></button>
                            <button class="btn btn-md btn-success status-trigger<?=($status==1 ? ' hidden' : '' )?>" data-status='1' data-id="<?=$review->getId()?>"><i class="glyphicon glyphicon-ok"></i></button>
                            <button class="btn btn-md btn-danger status-trigger<?=($status==2 ? ' hidden' : '' )?>" data-status='2' data-id="<?=$review->getId()?>"><i class="glyphicon glyphicon-ban-circle"></i></button>
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
                    <button onclick="document.location.href='/private/reviews?status=<?=$status?>&page=<?=$i?>&sortField=<?=$currentSort['field']?>&sortDirection=<?=$currentSort['direction']?>'" class="btn btn-default btn-md <?=($i == $pager['page'] ? 'active' : '')?>"><?=$i?></button>
                <? } ?>
            </div>
        </div>
    <? } ?> 
</div>


<script>

    $('.status-trigger').on('click', function() {
        location.href = '/private/reviews/status/' + $(this).data('id') + '?status=<?=$status?>&setstatus=' + $(this).data('status');
    });

</script>
<?php

    function sortIcon($currentField, $currentSort, $pager)
    {
        $icon = '<a href="/private/users?page=1&sortField=%s&sortDirection=%s"><i class="glyphicon glyphicon-chevron-%s"></i></a>';
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
<? if($frontend) require_once($frontend.'_frontend.php') ?>