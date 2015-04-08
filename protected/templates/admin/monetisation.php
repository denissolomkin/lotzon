<div class="container-fluid users">
    <div class="row-fluid" id="items">
        <h2>Запросы на вывод товаров (<span id="shopCount"><?=$shopCount?></span>) <button class="btn btn-info" onclick="location.href='#money'"><i class="glyphicon glyphicon-hand-down"></i></button>

            <button onclick="document.location.href='/private/monetisation?moneyPage=<?=$moneyPager['page']?>&moneyStatus=<?=$moneyStatus?>&moneyType=<?=$moneyType?>&shopStatus=2&amp;sortField=Id&amp;sortDirection=desc#shop'" class="btn right btn-md btn-danger <?=($shopStatus==2 ? 'active' : '' )?>"><i class="glyphicon glyphicon-ban-circle"></i></button>
            <button onclick="document.location.href='/private/monetisation?moneyPage=<?=$moneyPager['page']?>&moneyStatus=<?=$moneyStatus?>&moneyType=<?=$moneyType?>&shopStatus=1&amp;sortField=Id&amp;sortDirection=desc#shop'" class="btn right btn-md btn-success <?=($shopStatus==1 ? 'active' : '' )?>"><i class="glyphicon glyphicon-ok"></i></button>
            <button onclick="document.location.href='/private/monetisation?moneyPage=<?=$moneyPager['page']?>&moneyStatus=<?=$moneyStatus?>&moneyType=<?=$moneyType?>&shopStatus=0&amp;sortField=Id&amp;sortDirection=desc#shop'" class="btn right btn-warning btn-md <?=(!$shopStatus ? 'active' : '' )?>"><i class="glyphicon glyphicon-time"></i></button>

  </h2>
        <hr />
    </div>

    <? if ($shopPager['pages'] > 1) {?>
        <div class="row-fluid">
            <div class="btn-group">
                <? for ($i=1; $i <= $shopPager['pages']; ++$i) { ?>
                    <button onclick="document.location.href='/private/monetisation?shopPage=<?=$i?>&moneyPage=<?=$moneyPager['page']?>&moneyStatus=<?=$moneyStatus?>&moneyType=<?=$moneyType?>&shopStatus=<?=$shopStatus?>&sortField=<?=$currentSort['field']?>&sortDirection=<?=$currentSort['direction'].($search['query']?'&search[where]='.$search['where'].'&search[query]='.$search['query']:'').'#items'?>'" class="btn btn-default btn-xs <?=($i == $shopPager['page'] ? 'active' : '')?>"><?=$i?></button>
                <? } ?>
            </div>
        </div>
    <? } ?>

    <div class="row-fluid">
        <table class="table table-striped">
            <thead>
                <!--th>#ID</th-->
                <th width="50">Дата</th>
                <th width="10%">Игрок</th>
                <th width="20%">Информация</th>
                <th width="50">Баланс</th>
                <th>Данные</th>
                <th>Товар</th>
                <th width="50">Options</th>
            </thead>
            <tbody>
                <? foreach ($shopOrders as $order) { ?>
                    <tr id="shop<?=$order->getId()?>">

                        <?$player=$order->getPlayer();?>
                        <td<?=$player->getBan()?' class="danger"':''?>><?=date('d.m.Y <b\r> H:m:s', $order->getDateOrdered())?></td>
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
                        <td class="nobr pointer transactions-trigger" data-id="<?=$player->getId()?>"><?=($player->getPoints()<0?'<b class="red">'.$player->getPoints().'</b>':$player->getPoints())?> <br><?=($player->getMoney()<0?'<b class="red">':'').$player->getMoney()?> <?=\CountriesModel::instance()->getCountry($player->getCountry())->loadCurrency()->getTitle('iso');?></td>
                        <td><?=$order->getItem()->getTitle()?></br><?=($order->getChanceGameId() ? 'Выиграл в шанс' : $order->getItem()->getPrice().' баллов')?></td>


                        <?if($order->getCount()>0):?>
                        <td class="pointer orders-trigger" data-number="<?=$order->getNumber()?>">
                            <span class="label label-danger" ><?=$order->getCount()+1?></span>
                            <? else : ?> <td> <? endif ?>
                            ФИО: <?=$order->getSurname()?> <?=$order->getName()?> <?=$order->getSecondName()?> <br />
                            Телефон: <?=$order->getPhone()?> <br />
                            Адрес: <?=($order->getRegion() ? $order->getRegion() . ' обл.,' : '')?> г. <?=$order->getCity()?>, <?=$order->getAddress()?>

                        </td>
                        <td class="nobr">
                            <button class="btn btn-md btn-warning status <?=($shopStatus==0 ? ' hidden' : '' )?>" data-status='0' data-id="<?=$order->getId()?>"><i class="glyphicon glyphicon-time"></i></button>
                            <button class="btn btn-md btn-success status <?=($shopStatus==1 ? ' hidden' : '' )?>" data-status='1' data-id="<?=$order->getId()?>"><i class="glyphicon glyphicon-ok"></i></button>
                            <button class="btn btn-md btn-danger status <?=($shopStatus==2 ? ' hidden' : '' )?>" data-status='2' data-id="<?=$order->getId()?>"><i class="glyphicon glyphicon-ban-circle"></i></button>
                            <button class="btn btn-md btn-danger status <?=($shopStatus==2 ? ' hidden' : ' hidden' )?>" data-status='3' data-id="<?=$order->getId()?>"><i class="glyphicon glyphicon-remove"></i></button>
                        </td>
                    </tr>   
                <? } ?>
            </tbody>
        </table>
    </div>
    <div class="row-fluid" id="money">
        <h2>Запросы на вывод денег (<span id="moneyCount"><?=$moneyCount?></span>)
            <button class="btn btn-info" onclick="location.href='#items'" style="margin-right: 10%;"><i class="glyphicon glyphicon-hand-up"></i></button>
            <button onclick="document.location.href='/private/monetisation?shopPage=<?=$shopPager['page']?>&shopStatus=<?=$shopStatus?>&moneyStatus=<?=$moneyStatus?>&sortField=Id&sortDirection=desc#money'" class="btn btn-default btn-md <?=(!$moneyType? 'active' : '' )?>" style="padding: 6px;"><div style="width: 24px;height: 24px"></div></button>
            <? foreach(array('webmoney','yandex','private24','qiwi','phone') as $type) : ?>
            <button onclick="document.location.href='/private/monetisation?shopPage=<?=$shopPager['page']?>&shopStatus=<?=$shopStatus?>&moneyType=<?=$type?>&moneyStatus=<?=$moneyStatus?>&sortField=Id&sortDirection=desc#money'" class="btn btn-default btn-md <?=($moneyType===$type ? 'active' : '' )?>" style="padding: 6px;"><img src="../tpl/img/<?=$type?>.png"></button>
            <? endforeach ?>
            <button onclick="document.location.href='/private/monetisation?shopPage=<?=$shopPager['page']?>&shopStatus=<?=$shopStatus?>&moneyType=<?=$moneyType?>&moneyStatus=2&sortField=Id&sortDirection=desc#money'" class="btn right btn-md btn-danger <?=($moneyStatus==2 ? 'active' : '' )?>"><i class="glyphicon glyphicon-ban-circle"></i></button>
            <button onclick="document.location.href='/private/monetisation?shopPage=<?=$shopPager['page']?>&shopStatus=<?=$shopStatus?>&moneyType=<?=$moneyType?>&moneyStatus=1&sortField=Id&sortDirection=desc#money'" class="btn right btn-md btn-success <?=($moneyStatus==1 ? 'active' : '' )?>"><i class="glyphicon glyphicon-ok"></i></button>
            <button onclick="document.location.href='/private/monetisation?shopPage=<?=$shopPager['page']?>&shopStatus=<?=$shopStatus?>&moneyType=<?=$moneyType?>&moneyStatus=0&sortField=Id&sortDirection=desc#money'" class="btn right btn-warning btn-md <?=(!$moneyStatus ? 'active' : '' )?>"><i class="glyphicon glyphicon-time"></i></button>
        </h2>
        <hr />
    </div>


    <? if ($moneyPager['pages'] > 1) {?>
        <div class="row-fluid">
            <div class="btn-group">
                <? for ($i=1; $i <= $moneyPager['pages']; ++$i) { ?>
                    <button onclick="document.location.href='/private/monetisation?moneyPage=<?=$i?>&shopPage=<?=$shopPager['page']?>&shopStatus=<?=$shopStatus?>&moneyStatus=<?=$moneyStatus?>&sortField=<?=$currentSort['field']?>&sortDirection=<?=$currentSort['direction'].($search['query']?'&search[where]='.$search['where'].'&search[query]='.$search['query']:'').'#money'?>'" class="btn btn-default btn-xs <?=($i == $moneyPager['page'] ? 'active' : '')?>"><?=$i?></button>
                <? } ?>
            </div>
        </div>
    <? } ?>

    <div class="row-fluid">
        <table class="table table-striped">
            <thead>
                <!--th>#ID</th-->
                <th width="50">Дата</th>
                <th width="10%">Игрок</th>
                <th width="20%">Информация</th>
                <th width="100">Баланс</th>
                <th>Номер</th>
                <th>Данные</th>
                <th>Options</th>
            </thead>
            <tbody>
                <? foreach ($moneyOrders as $order) { ?>
                    <tr id="money<?=$order->getId()?>">
                        <?$player=$order->getPlayer();?>
                        <td<?=$player->getBan()?' class="danger"':''?>><?=date('d.m.Y <b\r> H:m:s', $order->getDateOrdered())?></td>
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
                      <!--  <span <?=($player->getLastIP() || $player->getIP()?"onclick=\"window.open('users?search[where]=Ip&search[query]=".$player->getIP().($player->getLastIP() && $player->getIP()?',':'').$player->getLastIP():'')?>');" class="pointer nobr <?=($player->getLastIP()?'warning':'')?>">
                        <?if($player->getCounters()['Ip']>1) {?>
                        <span class="label label-danger"><?=$player->getCounters()['Ip']?></span>
                        <?}?><?=$player->getIP()?></span> -->

                            <div class="left">
                                <? if($player->getCounters()['Ip']>1): ?>
                                    <button class="btn btn-xs btn-danger" <?=($player->getLastIP() || $player->getIP()?"onclick=\"window.open('users?search[where]=Ip&search[query]=".$player->getIP().($player->getLastIP() && $player->getIP()?',':'').$player->getLastIP():'')?>');">
                                    <span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span><?=$player->getCounters()['Ip']?>
                                </button>
                                <? endif ?>

                                <? if ($player->getDateAdBlocked()):
                                    ?><button class="btn btn-xs btn-<?=($player->getAdBlock()?'danger':($player->getDateAdBlocked() < strtotime('-14 day', time()) ? "success" : "warning" ))?> logs-trigger" data-action="AdBlock" data-id="<?=$player->getId()?>">
                                        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><?=($player->getCounters()['AdBlock']?:'')?>
                                    </button>
                                <? endif ?>

                                <? if(($player->getCookieId() && $player->getCookieId()!=$player->getId()) || $player->getCounters()['CookieId']>1) :?><button class="btn btn-xs btn-danger" onclick="window.open('users?search[where]=CookieId&search[query]=<?=$player->getCookieId();?>')">
                                        <span class="glyphicon glyphicon-flag" aria-hidden="true"></span><?=$player->getCounters()['CookieId']>1?$player->getCounters()['CookieId']:'';?>
                                    </button>
                                <? endif ?>

                                <? if(($count=$player->getCounters()['ShopOrder']+$player->getCounters()['MoneyOrder'])):?>
                                    <button class="btn btn-xs btn-<?=($count>1?'danger':'success')?> orders-trigger" data-id="<?=$player->getId()?>">
                                        <span class="glyphicon glyphicon-tag" aria-hidden="true"></span><?=($count>1?$count:'')?>
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

                                <? if ($player->getCounters()['Review']>0):
                                    ?><button class="btn btn-xs btn-success reviews-trigger" data-id="<?=$player->getId()?>">
                                        <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span><?=$player->getCounters()['Review']>1?$player->getCounters()['Review']:''?>
                                    </button>
                                <? endif ?>

                                <? if ($player->getCounters()['Log']>0):?>
                                <button class="btn btn-xs btn-<?=($player->getCounters()['Log']>1?'danger':(($player->getCounters()['Log']==1 AND $player->getValid())?'success':'warning'))?> logs-trigger" data-id="<?=$player->getId()?>">
                                        <span class="glyphicon glyphicon-time" aria-hidden="true"></span><?=$player->getCounters()['Log']>1?$player->getCounters()['Log']:''?>
                                    </button><? endif ?>

                            </div>

                        </td>
                        <td class="nobr pointer transactions-trigger" data-id="<?=$player->getId()?>"><?=($player->getPoints()<0?'<b class="red">'.$player->getPoints().'</b>':$player->getPoints())?> <br><?=($player->getMoney()<0?'<b class="red">':'').$player->getMoney()?> <?=\CountriesModel::instance()->getCountry($player->getCountry())->loadCurrency()->getTitle('iso');?></td>
                        <?if($order->getCount()>0):?>
                        <td class="pointer orders-trigger" data-number="<?=$order->getNumber()?>">
                        <span class="label label-danger" ><?=$order->getCount()+1?></span>
                        <? else : ?> <td> <? endif ?>
                        <img class="right" src="../tpl/img/<?=$order->getType()?>.png"><?=(($order->getType()=='webmoney')?$order->getData()['card-number']['value'][0]:(in_array($order->getType(),array('phone','qiwi'))?'+':''))?><?=($order->getNumber()?:'')?></td>
                        <td>
                            <? foreach ($order->getData() as $key => $data) { ?>
                                <?=$data['title']?>: <?=$data['value']?> <?=($data['title'] == 'Cумма' ? ($order->getPlayer()->getCountry() == 'UA' ? 'грн' : 'руб') : '')?> <br />
                            <? } ?>
                        </td>
                        <td class="nobr" width="50">

                            <button class="btn btn-md btn-warning status money <?=($moneyStatus==0 ? ' hidden' : '' )?>" data-status='0' data-id="<?=$order->getId()?>"><i class="glyphicon glyphicon-time"></i></button>
                            <button class="btn btn-md btn-success status money <?=($moneyStatus==1 ? ' hidden' : '' )?>" data-status='1' data-id="<?=$order->getId()?>"><i class="glyphicon glyphicon-ok"></i></button>
                            <button class="btn btn-md btn-danger status money<?=($moneyStatus==2 ? ' hidden' : '' )?>" data-status='2' data-id="<?=$order->getId()?>"><i class="glyphicon glyphicon-ban-circle"></i></button>
                            <button class="btn btn-md btn-danger status money<?=($moneyStatus==2 ? ' hidden' : ' hidden' )?>" data-status='3' data-id="<?=$order->getId()?>"><i class="glyphicon glyphicon-remove"></i></button>

                        </td>
                    </tr>   
                <? } ?>
            </tbody>
        </table>
    </div>
</div>

<script>

    $('.status').on('click', function() {
        id= $(this).data('id');
        status= $(this).data('status');
        money=($(this).hasClass('money') ? 1 : 0);
        $.ajax({
            url: "/private/monetisation/status/"+id+"?status="+status+"&money=" + money,
            method: 'GET',
            async: true,
            data: null,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    if(money){
                        if(($("table tr#money"+id).parent().children().length)==1 && parseInt($("#shopCount").text())>1)
                            location.reload()
                        $("table tr#money"+id).remove();
                        $("#moneyCount").text(parseInt($("#moneyCount").text())-1);

                    } else {
                        if(($("table tr#shop"+id).parent().children().length)==1 && parseInt($("#shopCount").text())>1)
                            location.reload()
                        $("table tr#shop"+id).remove();
                        $("#shopCount").text(parseInt($("#shopCount").text())-1);
                    }
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
        location.href = '/private/monetisation/approve/' + $(this).data('id') + '?moneyStatus=<?=$moneyStatus?>&shopStatus=<?=$shopStatus?>&money=' + ($(this).hasClass('money') ? 1 : 0);
    });
    $('.decline').on('click', function() {
        location.href = '/private/monetisation/decline/' + $(this).data('id') + '?moneyStatus=<?=$moneyStatus?>&shopStatus=<?=$shopStatus?>&money=' + ($(this).hasClass('money') ? 1 : 0);
    });
    $('.process').on('click', function() {
        location.href = '/private/monetisation/process/' + $(this).data('id') + '?moneyStatus=<?=$moneyStatus?>&shopStatus=<?=$shopStatus?>&money=' + ($(this).hasClass('money') ? 1 : 0);
    });
    $('.delete').on('click', function() {
        location.href = '/private/monetisation/delete/' + $(this).data('id') + '?moneyStatus=<?=$moneyStatus?>&shopStatus=<?=$shopStatus?>&money=' + ($(this).hasClass('money') ? 1 : 0);
    });
</script>

<? if($frontend) require_once($frontend.'_frontend.php') ?>