
<div class="container-fluid users">

    <div class="row-fluid">
        <h2>
            Пользователи (<?=$playersCount?>)
            <div class="flex"><?=($search['query']?'<button class="btn btn-md btn-success" onclick="history.back();"><i class="glyphicon glyphicon-arrow-left"></i></button>':'');?>
                <button class="btn btn-md btn-info search-users"><i class="glyphicon glyphicon-search"></i></button>
            </div>
            <div class="right">
                <!--button class="btn btn-md btn-info filter-trigger" data-id="0"><span class="glyphicon glyphicon-filter" aria-hidden="true"></span></button--><button class="btn btn-md btn-warning notices-trigger" data-id="0"><span class="glyphicon glyphicon-bell" aria-hidden="true"></span></button>
            </div><small><small>
            <div class="right" id="wsStatus" style="margin: 10px ;">
                <div>
                    <span class="label label-default">
                    <span class="glyphicon glyphicon-usd" aria-hidden="true"></span>
                </span><span class="label label-info"><b><?=round($stats['Money'])?></b></span>
                </div>
                <div>
                <span class="label label-default">
                    <span class="glyphicon glyphicon-certificate" aria-hidden="true"></span>
                </span><span class="label label-info"><b><?=$stats['Points']?></b></span>
                </div>
                <div class="pointer" onclick="location.href='?search[where]=Ping&search[query]=1'">
                <span>
                    <span class="label label-md label-default"><i class="online" style="vertical-align: top;margin: 0 2px;">•</i></span></span><span class="label label-info"><b><?=$stats['Online']?></b></span>
                </div>
                <div>
                <span class="label label-default">
                    <span class="glyphicon glyphicon-tags" aria-hidden="true"></span>
                </span><span class="label label-info"><b><?=$stats['Tickets']?></b></span>
                </div>
            </div>
                    </small></small>
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
                <th>Никнейм <?=sortIcon('Nicname', $currentSort, $pager, $search)?> ФИО <?=sortIcon('Name', $currentSort, $pager, $search)?></th>
                <th class="icon"> <?=sortIcon('Country', $currentSort, $pager, $search, 'globe')?></th>
                <th>Email <?=sortIcon('Email', $currentSort, $pager, $search)?> / Регистрация <?=sortIcon('Registration', $currentSort, $pager, $search)?></th>
                <th style="min-width: 120px;"><span class="fa fa-map-marker" aria-hidden="true"></span> IP</th>
                <th class="icon"><span class="fa fa-flag" aria-hidden="true"></span></th>
                <th><?=sortIcon('ReferalId', $currentSort, $pager, $search, 'user')?> / <?=sortIcon('InviterId', $currentSort, $pager, $search, 'envelope')?></th>
                <th>Login <?=sortIcon('Login', $currentSort, $pager, $search)?> / Ping <?=sortIcon('Ping', $currentSort, $pager, $search)?></th>
                <th>Games <?=sortIcon('GamesPlayed', $currentSort, $pager, $search)?></th>
                <th class="icon"><?=sortIcon('AdBlock', $currentSort, $pager, $search ,'')?></th>
                <th class="icon"><?=sortIcon('Points', $currentSort, $pager, $search ,'diamond')?> <?=sortIcon('Money', $currentSort, $pager, $search ,'money')?></th>
                <th width="100">Options</th>
            </thead>
            <tbody>
                <? foreach ($list as $player) { ?>
                    <tr id="user<?=$player->getId()?>" class="<?=$player->isBot()?'info':($player->isBan()?'danger':'')?>">
                        <td>
                            <div data-toggle="tooltip" data-placement="right" title="<?=$player->getAgent()?>" >
                                <?=$player->getId()?>
                            </div>
                        </td>
                        <td class="profile-trigger pointer" data-id="<?=$player->getId()?>">
                            <div <? if($player->getAvatar()) : ?>data-toggle="tooltip" data-html="1" data-placement="auto" title="<img src='../filestorage/avatars/<?=(ceil($player->getId() / 100)) . '/'.$player->getAvatar()?>'>"<? endif ?>>
                            <?=($player->getNicName())?><?=($player->getDates('Ping')>time()-SettingsModel::instance()->getSettings('counters')->getValue('PLAYER_TIMEOUT')?'<i class="online right">•</i>':'');?>
                            <br>
                            <?=($player->getSurname() . " " . $player->getName() . " " . $player->getSecondName())?><? if($player->getAvatar() AND 0) echo '<img src="../filestorage/'.'avatars/' . (ceil($player->getId() / 100)) . '/'.$player->getAvatar().'">'?></td>
                            </div>
                        <td class="country"><?=$player->getCountry()?><br><?=$player->getLang()?></td>
                        <td class="email-registration <?=$player->getValid() ? "success" : "danger"?>"><? if($player->getCounters('Mult')>1) : ?>
                            <div class="mults-trigger left pointer" data-id="<?=$player->getId()?>"><div class="label label-danger label-mult"><?=$player->getCounters('Mult')?></div>
                            <? endif ?><?=$player->getEmail()?><? if($player->getCounters('Mult')>1) : ?></div><? endif ?>
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
                            <br>
                            <?if($player->getReferer()) {?><div data-toggle="tooltip" data-placement="right" title="<?=$player->getReferer()?>" class=""><span class="label label-danger">!</span><?}?>
                                <div class="date-registration"><i class="fa fa-clock-o"></i> <?=$player->getDates('Registration','d.m.Y H:i')?></div>
                                <?if($player->getReferer()) {?></div><?}?>
                        </td>


                        <td <?=($player->getCounters('Ip')>1?"onclick=\"location.href='?search[where]=Ip&search[query]=".$player->getId()."'\"":'')?> class='<?=($player->getCounters('Ip')>1?'pointer ':'')?>nobr div-ips'>
                            <? if($player->getCounters('Ip')>1) : ?>
                                <div class="label label-danger label-ips"><?=$player->getCounters('Ip')?></div>
                           <? endif ?><?=($player->getLastIP()?'<div class="ips">'.$player->getIP().'<br>'.$player->getLastIP().'</div>':$player->getIP())?></td>
                        <td <?=(($player->getCounters('CookieId')>1)
                            ?'onclick="location.href=\'?search[where]=CookieId&search[query]='.$player->getId().'\';" class="pointer danger">
                            <div data-toggle="tooltip" data-placement="right" title="'.$player->getCookieId().'" >
                            <span class="label label-danger">'.$player->getCounters('CookieId').'</span></div>':($player->getCookieId()?'class="success">':'>'))?>
                        </td>
                        <td class="<?=$player->getReferalId() || $player->getInviterId() ? "success" : "danger"?>">
                            <?if ($player->getReferalId()){?>
                            <div onclick="location.href='?search[where]=Id&search[query]=<?=$player->getReferalId()?>';" class="pointer"><nobr>
                                <span class="label label-info"><span class="glyphicon glyphicon-user" aria-hidden="true"></span><?if($player->getCounters('Referal')>1) {?><?=$player->getCounters('Referal');?> <?}?></span>&nbsp;<?=$player->getReferalId()?>
                            </nobr></div><? } ?>

                            <?=$player->getReferalId() && $player->getInviterId() ? "<br>" : ""?>

                            <?if ($player->getInviterId()){?>
                            <div onclick="location.href='?search[where]=Id&search[query]=<?=$player->getInviterId()?>';" class="pointer"><nobr>
                                <span class="label label-info"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span><?if($player->getCounters('Inviter')>1) {?><?=$player->getCounters('Inviter');?> <?}?></span>&nbsp;<?=$player->getInviterId()?>
                            </nobr></div><? } ?>
                        </td>

                        <td class="logins-trigger pointer <?=($player->getDates('Login')?(($player->getDates('Login') < strtotime('-7 day', time())) ? "warning" : "success"):'danger')?>"  data-id="<?=$player->getId()?>">
                            <?=($player->getDates('Ping')?'<div class="datestamps nobr right">'.($player->getDates('Login','d.m.Y&\nb\sp;H:i')).'<br>'.(str_replace($player->getDates('Login','d.m.Y'),'',$player->getDates('Ping','d.m.Y H:i'))).'</div>':($player->getDates('Login')?'<div class="right">'.$player->getDates('Login','d.m.Y H:i').'</div>':''))?>
                        </td>

                        <td <?=($player->getGamesPlayed()?'':'class="danger"')?>>

                            <? if($player->getGamesPlayed()){?>
                            <span class="stats-trigger pointer success" data-id="<?=$player->getId()?>">
                                <i class="fa fa-gift <?=($player->getGamesPlayed() ? '' : 'text-danger' )?>"></i><?=$player->getGamesPlayed()?>
                            </span>
                            <?}?>

                            <? if ($player->isTicketsFilled() || $player->getGamesPlayed()){?>
                                <span class="tickets-trigger pointer" data-id="<?=$player->getId()?>"><i class="glyphicon glyphicon-tags" aria-hidden="true"></i>&nbsp;<?=$player->isTicketsFilled()?:''?></span>
                            <? } ?>

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
                        </td>

                        <td>
                            <? if($player->getDateAdBlocked()) :?>
                            <button class="btn btn-xs btn-<?=($player->getAdBlock()?'danger':($player->getDateAdBlocked() < strtotime('-14 day', time()) ? "success" : "warning" ))?> logs-trigger" data-action="AdBlock" data-id="<?=$player->getId()?>">
                                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><?=($player->getCounters('AdBlock')?:'')?></button>
                            <? endif ?>
                        </td>

                        <td class="pointer transactions-trigger" data-id="<?=$player->getId()?>">
                            <?=($player->getPoints()<0?'<b class="red">'.$player->getPoints().'</b>':$player->getPoints())?>
                            <br>
                            <?=($player->getMoney()<0?'<b class="red">':'').$player->getMoney()?>&nbsp;<span><?=\CountriesModel::instance()->getCountry($player->getCountry())->loadCurrency()->getTitle('iso')?></span>
                        </td>
                        <td><div class="right nobr">

                            <button class="btn btn-xs btn-<?=($player->getCounters('Note')?'danger':'warning');?> notes-trigger" data-type="Note" data-id="<?=$player->getId()?>">
                                <span class="glyphicon glyphicon-edit" aria-hidden="true"></span><?=$player->getCounters('Note')>1?$player->getCounters('Note'):'';?>
                            </button>
                            <button class="btn btn-xs btn-<?=($player->getCounters('Notice')?'success':'warning');?> notices-trigger" data-type="Message" data-id="<?=$player->getId()?>">
                                <span class="glyphicon glyphicon-bell" aria-hidden="true"></span><?=$player->getCounters('Notice')>1?$player->getCounters('Notice'):''?>
                            </button>

                                <? if ($player->getCounters('MyReferal')>0): ?>
                                    <button class="btn btn-xs btn-success" onclick="location.href='?search[where]=ReferalId&search[query]=<?=$player->getId();?>'">
                                        <span class="glyphicon glyphicon-user" aria-hidden="true"></span><?=($player->getCounters('MyReferal')>1?$player->getCounters('MyReferal'):'');?>
                                    </button>
                                <? endif ?>

                                <? if ($player->getCounters('MyInviter')>0): ?>
                                    <button class="btn btn-xs btn-success" onclick="location.href='?search[where]=InviterId&search[query]=<?=$player->getId();?>'">
                                        <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span><?=($player->getCounters('MyInviter')>1?$player->getCounters('MyInviter'):'');?>
                                    </button>
                                <? endif ?>

                                <? if ($player->getCounters('Review')>0): ?>
                            <button class="btn btn-xs btn-success reviews-trigger" data-id="<?=$player->getId()?>">
                                <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span><?=$player->getCounters('Review')>1?$player->getCounters('Review'):''?>
                            </button>
                            <? endif ?>

                                <? if ($player->getCounters('Message')>0): ?>
                                    <button class="btn btn-xs btn-success messages-trigger" data-id="<?=$player->getId()?>">
                                        <span class="fa fa-inbox" aria-hidden="true"></span><?=$player->getCounters('Message')>1?$player->getCounters('Message'):''?>
                                    </button>
                                <? endif ?>

                                <? if (($orders=$player->getCounters('ShopOrder')+$player->getCounters('MoneyOrder'))>0): ?>
                            <button class="btn btn-xs btn-success orders-trigger" data-id="<?=$player->getId()?>">
                                <span class="glyphicon glyphicon-tag" aria-hidden="true"></span><?=($orders>1?$orders:'');?>
                            </button>
                            <? endif ?>
                            <!--button class="btn btn-xs btn-warning transactions-trigger" data-id="<?=$player->getId()?>">T</button>
                            <button class="btn btn-xs btn-warning stats-trigger" data-id="<?=$player->getId()?>">Р</button-->
                            <? if ($player->getCounters('Log')>0): ?>
                            <button class="btn btn-xs btn-<?=($player->getCounters('Log')>1?'danger':(($player->getCounters('Log')==1 AND $player->getValid())?'success':'warning'))?> logs-trigger" data-id="<?=$player->getId()?>">
                                <span class="glyphicon glyphicon-time" aria-hidden="true"></span><?=$player->getCounters('Log')>1?$player->getCounters('Log'):''?>
                            </button>
                             <? endif ?>
                                <button class="btn btn-xs btn-danger ban-trigger" data-id="<?=$player->getId()?>"><span class="glyphicon glyphicon-lock" aria-hidden="true"></button>
                                <button class="btn btn-xs btn-danger bot-trigger" data-id="<?=$player->getId()?>"><span class="fa fa-plug" aria-hidden="true"></button>
                                <button class="btn btn-xs btn-danger delete-trigger" data-id="<?=$player->getId()?>"><span class="glyphicon glyphicon-trash" aria-hidden="true"></button>
                            </div>
                        </td>
                    </tr>
                <? } ?>
            </tbody>
        </table>
    </div>
</div>






<div class="modal fade" id="ws" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Статистика</h4>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>

<script>

/* SEARCH BLOCK */
$('.search-users').on('click', function() {
    if($(this).next().hasClass('input-md')){
        $(this).parent().children('.search-form').remove();
    }else{


        var input = $('<input class="form-control input-md search-form" value="<?=(isset($search['query'])?$search['query']:'');?>" style="width:230px;display:inline-block;" placeholder="id, фио, ник или email...">' +
        '<select id="search_where" class="form-control input-md search-form" style="width: 120px;display: inline-block;">' +
        '<option value="">Везде</option>' +
        '<option value="Id">Id</option>' +
        '<option value="Ip">Ip</option>' +
        '<option value="NicName">Ник</option>' +
        '<option value="ReferalId">Реферал</option>' +
        '<option value="CookieId">Cookie</option>' +
        '<option value="Ping">Online</option>' +
        '<option value="Ban">Ban</option>' +
        '<option value="Bot">Bot</option>' +
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

<? if(isset($search['query'])){?>
$('.search-users').trigger('click');
$('#search_where').val('<?=$search['where']?>');

<? } ?>
/* END SEARCH BLOCK */

var url = 'ws<?=\Config::instance()->SSLEnabled?'s':'';?>://<?=$_SERVER['SERVER_NAME'];?>:<?=\Config::instance()->wsPort?>';
var conn;
function WebSocketAjaxClient(data) {
    if(!conn || conn.readyState !== 1)
    {
        conn = new WebSocket(url);
        conn.onopen = function (e) {
            conn.send(JSON.stringify({'path': 'chat', 'data': {'message':'stats'}}));
        };
    } else {
        conn.send(JSON.stringify({'path': 'chat', 'data': {'message': data}}));
    }

    conn.onerror = function (e) {
    };

    conn.onmessage = function (e) {
        data=$.parseJSON(e.data);
        WebSocketStatus('<b style="color:purple">receive',data)
    };

}

// try start websocket
WebSocketAjaxClient();

function WebSocketStatus(action, data) {
    if(data.res.message.players)
        $("#wsStatus").append(' <div class="pointer" onclick="WebSocketAjaxClient(\'players\');"><span class="label label-default"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></span>' +
        '<span class="label label-info"><b>'+data.res.message.players+'</b></span>  </div><div class="pointer" onclick="WebSocketAjaxClient(\'games\');"><span class="label label-default"><span class="glyphicon glyphicon-tower" aria-hidden="true"></span></span><span class="label label-info"><b>'+data.res.message.games+'<b></span></div>');
    else{
        $("#ws .modal-content .modal-body").html(data.res.message);
        $("#ws").modal();
    }

}

</script>

<?php

    function sortIcon($currentField, $currentSort, $pager, $search, $icon=null)
    {
        $icon = '<nobr><a href="/private/users?page=1&sortField=%s&sortDirection=%s'.($search['query']?'&search[where]='.$search['where'].'&search[query]='.$search['query']:'').'">'.($icon?'<span class="fa fa-'.$icon.'" aria-hidden="true"></span>':'').'<i class="glyphicon glyphicon-chevron-%s"></i></a></nobr>';
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