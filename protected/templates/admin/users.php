
<div class="container-fluid">

    <div class="row-fluid">
        <h2>
            Пользователи (<?=$playersCount?>)
            <div class="flex"><?=($search['query']?'<button class="btn btn-md btn-success" onclick="history.back();"><i class="glyphicon glyphicon-arrow-left"></i></button>':'');?>
                <button class="btn btn-md btn-info search-users"><i class="glyphicon glyphicon-search"></i></button>
            </div>
            <button class="btn btn-md btn-warning notices-trigger right" data-id="0"><span class="glyphicon glyphicon-bell" aria-hidden="true"></span></button>
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
                <th>#ID <?=sortIcon('Id', $currentSort, $pager, $search)?></th>
                <th>ФИО</th>
                <th>Никнейм</th>
                <th>Email <?=sortIcon('Valid', $currentSort, $pager, $search)?></th>
                <th>Страна <?=sortIcon('Country', $currentSort, $pager, $search)?></th>
                <th>Дата регистрации <?=sortIcon('DateRegistered', $currentSort, $pager, $search)?></th>
                <th>IP<?=sortIcon('IP', $currentSort, $pager, $search)?></th>
                <!--th>Последний IP <?=sortIcon('LastIP', $currentSort, $pager, $search)?></th-->
                <th>Реферал <?=sortIcon('ReferalId', $currentSort, $pager, $search)?></th>
                <th>Последний логин <?=sortIcon('DateLogined', $currentSort, $pager, $search)?></th>
                <!--th>Последний пинг <?=sortIcon('OnlineTime', $currentSort, $pager, $search)?></th-->
                <th>Игр <?=sortIcon('GamesPlayed', $currentSort, $pager, $search)?></th>
                <th>Билеты <?=sortIcon('TicketsFilled', $currentSort, $pager, $search)?></th>
                <th>Ad <?=sortIcon('AdBlock', $currentSort, $pager, $search)?></th>
                <th>Денег <?=sortIcon('Money', $currentSort, $pager, $search)?></th>
                <th>Баллов <?=sortIcon('Points', $currentSort, $pager, $search)?></th>
                <th width="100">Options</th>
            </thead>
            <tbody>
                <? foreach ($list as $player) { ?>
                    <tr>
                        <td><?=$player->getId()?></td>
                        <td class="profile-trigger pointer" data-id="<?=$player->getId()?>"><?=($player->getSurname() . " " . $player->getName() . " " . $player->getSecondName())?><? if($player->getAvatar() AND 0) echo '<img src="../filestorage/'.'avatars/' . (ceil($player->getId() / 100)) . '/'.$player->getAvatar().'">'?></td>
                        <td class="profile-trigger pointer" data-id="<?=$player->getId()?>"><?=($player->getNicName())?></td>
                        <td class="<?=$player->getValid() ? "success" : "danger"?>"><?=$player->getEmail()?>
                            <div class="flex">
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
                        <td><?=$player->getDateRegistered('d.m.Y H:i')?></td>
                        <td <?=($player->getLastIP() || $player->getIP()?"onclick=\"location.href='?search[where]=Ip&search[query]=".$player->getIP().($player->getLastIP() && $player->getIP()?',':'').$player->getLastIP():'')?>'" class="pointer nobr <?=($player->getLastIP()?'warning':'')?>">
                            <?if($player->getCounters()['Ip']>1) {?>
                                <span class="label label-danger"><?=$player->getCounters()['Ip']?></span>
                           <?}?><?=$player->getIP()?></td>
                        <!--td><?if($player->getCounters()['Ip']>1) {?><a href="?search[where]=Ip&search[query]=<?=$player->getLastIP()?>"><span class="label label-danger"><?=$player->getCounters()['Ip']?><?}?></span></a> <?=$player->getLastIP()?></td-->
                        <td <?=($player->getReferalId()?'onclick="location.href=\'?search[where]=Id&search[query]='.$player->getReferalId().'\';" class="pointer"':'')?> class="<?=$player->getReferalId() ? "success" : "danger"?>">
                            <?if($player->getCounters()['Referal']>1) {?> <span class="label label-danger"><?=$player->getCounters()['Referal']?></span>
                            <?}?>
                            <?=$player->getReferalId() ? "#" . $player->getReferalId() : "&nbsp;"?></td>
                        <td class="<?=($player->getDateLastLogin()?(($player->getDateLastLogin() < strtotime('-7 day', time())) ? "warning" : ""):'warning')?>"><?=($player->getDateLastLogin()?$player->getDateLastLogin('d.m.Y H:i'):'')?></td>
                        <!--td class="<?=($player->getOnlineTime()?(($player->getOnlineTime() < strtotime('-7 day', time())) ? "warning" : ""):'warning')?>"><?=($player->getOnlineTime()?$player->getOnlineTime('d.m.Y H:i'):'')?></td-->
                        <td class="stats-trigger pointer" data-id="<?=$player->getId()?>"><?=$player->getGamesPlayed()?></td>
                        <td class="<?=$player->isTicketsFilled() || $player->getGamesPlayed()?"tickets-trigger pointer ":''?> <?=$player->isTicketsFilled() ? 'success' : 'danger'?>" data-id="<?=$player->getId()?>"><?=$player->isTicketsFilled() ?: 'нет'?></td>
                        <td>
                            <?=($player->getDateAdBlocked()? '<button class="btn btn-xs btn-danger notices-trigger" data-type="AdBlock" data-id="<?=$player->getId()?>" '.($player->getAdBlock()?'':'disabled="disabled"').'>'.
                                ($player->getCounters()['AdBlock']?:'<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>').'</button>' : '')?></td>
                        <td class="transactions-trigger pointer" data-id="<?=$player->getId()?>"><?=$player->getMoney()?> <?=$player->getCountry() == 'UA' ? 'грн' : 'руб'?></td>
                        <td class="transactions-trigger pointer" data-id="<?=$player->getId()?>"><?=$player->getPoints()?></td>
                        <td><div class="right nobr">

                            <button class="btn btn-xs btn-<?=($player->getCounters()['Note']?'danger':'warning');?> notes-trigger" data-type="Note" data-id="<?=$player->getId()?>">
                                <span class="glyphicon glyphicon-flag" aria-hidden="true"></span><?=$player->getCounters()['Note']>1?$player->getCounters()['Note']:'';?>
                            </button>
                            <button class="btn btn-xs btn-<?=($player->getCounters()['Notice']?'success':'warning');?> notices-trigger" data-type="Message" data-id="<?=$player->getId()?>">
                                <span class="glyphicon glyphicon-bell" aria-hidden="true"></span><?=$player->getCounters()['Notice']>1?$player->getCounters()['Notice']:''?>
                            </button>

                                <? if ($player->getCounters()['MyReferal']>0): ?>
                                    <button class="btn btn-xs btn-success" onclick="location.href='?search[where]=ReferalId&search[query]=<?=$player->getId();?>'">
                                        <span class="glyphicon glyphicon-bullhorn" aria-hidden="true"></span><?=($player->getCounters()['MyReferal']>1?$player->getCounters()['MyReferal']:'');?>
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
                            <!--button class="btn btn-xs btn-danger profile-trigger" data-id="<?=$player->getId()?>"><span class="glyphicon glyphicon-user" aria-hidden="true"></button>
                            <button class="btn btn-xs btn-danger ban-trigger" data-id="<?=$player->getId()?>"><span class="glyphicon glyphicon-lock" aria-hidden="true"></button-->
                            </div>
                        </td>
                    </tr>
                <? } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade users" id="social-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Social information</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default cls">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade users" id="profile-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Profile information</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default cls">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tickets-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Tickets</h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                    <th>#ID лотереи</th>
                    <th>Дата заполнения</th>
                    <th>№</th>
                    <th>Комбинация</th>
                    <th>Выигрыш</th>
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

<div class="modal fade" id="logs-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Log</h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                    <th>#ID</th>
                    <th>Действие</th>
                    <th>Описание</th>
                    <th>Дата</th>
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

<div class="modal fade" id="transactions-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="btn btn-success pull-right add">Добавить транзакцию</button>
                <div style="clear:both"></div>
            </div>
            <div class="modal-body">
                <h4>Баллы</h4>
                <hr />
                <table class="table table-striped points" >
                    <thead>
                        <th>#ID транзакции</th>
                        <th>Дата</th>
                        <th>Описание транзакции</th>
                        <th>Сумма</th>
                        <th>Баланс</th>
                        <th>Удалить</th>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
                
                <h4>Деньги</h4>
                <hr />
                <table class="table table-striped money" >
                    <thead>
                        <th>#ID транзакции</th>
                        <th>Дата</th>
                        <th>Описание транзакции</th>
                        <th>Сумма</th>
                        <th>Баланс</th>
                        <th>Удалить</th>                      
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success add">Добавить транзакцию</button>
                <button type="button" class="btn btn-default cls">Закрыть</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="orders-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div style="clear:both"></div>
            </div>
            <div class="modal-body">
                <div class="row-fluid" id="shopOrders">
                <h4>Запросы товаров
                <button class="btn btn-info pull-right" onclick="location.href='#moneyOrders'" style="margin-top: -10px;"><i class="glyphicon glyphicon-hand-down"></i> Запросы денег</button>
                </h4>
                <hr />
                </div>
                <table class="table table-striped shop">
                    <thead>
                    <th>#ID</th>
                    <th>Дата</th>
                    <th>Товар</th>
                    <th>Данные</th>
                    <th>Стоимость</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <div class="row-fluid" id="moneyOrders">
                <h4>Деньги
                    <button class="btn btn-info pull-right" onclick="location.href='#shopOrders'" style="margin-top: -10px;"><i class="glyphicon glyphicon-hand-up"></i> Запросы товаров</button>
                </h4>
                <hr />
                </div>
                <table class="table table-striped money">
                    <thead>
                    <th>#ID</th>
                    <th>Дата</th>
                    <th>Система</th>
                    <th>Данные</th>
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

<div class="modal fade" id="remove-transaction" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Удаление транзакции</h4>
            </div>
            <div class="modal-body">
                <div class="row-fluid">
                    Эта транзакция будет удалена и создана новая с противоположным балансом
                    <br/>
               </div>
               <div class="row-fluid">
                    Причина удаления ?
               </div>
               <div class="row-fluid">
                    <input class="form-control input-md" name="description"></input>
               </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success rm">Удалить</button>
                <button class="btn btn-danger cls">Отмена</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="notices-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="btn btn-success pull-right add">Добавить уведомление</button>
                <div style="clear:both"></div>
            </div>
            <div class="modal-body">
                <h4>Уведомления</h4>
                <hr />
                <table class="table table-striped points" >
                    <thead>
                    <th>#ID</th>
                    <th>Дата</th>
                    <th>Заголовок</th>
                    <th>Удалить</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

            </div>
            <div class="modal-footer">
                <button class="btn btn-success add">Добавить уведомление</button>
                <button type="button" class="btn btn-default cls">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add-notice" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Добавление уведомления</h4>
            </div>
            <div class="modal-body">
                <div class="row-fluid" id="errorForm" style="display:none">
                    <div class="alert alert-danger" role="alert">
                        <span class="error-container"></span>
                    </div>
                </div>




                <form class="form">
                    <div class="form-group">
                        <label class="control-label">Заголовок</label>
                        <input type="text" name="title" value="" placeholder="Заголовок" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label class="control-label">Текст уведомления</label>
                        <div id="text"></div>
                    </div>
                </form>


                <div class="row-fluid">
                    <button class="btn btn-md btn-success save pull-right"> Сохранить</button>
                    <button class="btn btn-danger cls">Отмена</button>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="remove-notice" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Удаление уведомления</h4>
            </div>
            <div class="modal-body">
                <div class="row-fluid">
                    Это уведомление будет безвозвратно удалено, Вы уверены?
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success rm">Удалить</button>
                <button class="btn btn-danger cls">Отмена</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reviews-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h4>Комментарии</h4>
                <hr />
                <table class="table table-striped points" >
                    <thead>
                    <th>Дата</th>
                    <th>Комментарий</th>
                    <th></th>
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

<div class="modal fade" id="notes-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="btn btn-success pull-right add">Добавить заметку</button>
                <div style="clear:both"></div>
            </div>
            <div class="modal-body">
                <h4>Заметки</h4>
                <hr />
                <table class="table table-striped points" >
                    <thead>
                    <th>Дата</th>
                    <th>Автор</th>
                    <th>Заметка</th>
                    <th>Удалить</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

            </div>
            <div class="modal-footer">
                <button class="btn btn-success add">Добавить заметку</button>
                <button type="button" class="btn btn-default cls">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add-note" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Добавление заметки</h4>
            </div>
            <div class="modal-body">
                <div class="row-fluid" id="errorForm" style="display:none">
                    <div class="alert alert-danger" role="alert">
                        <span class="error-container"></span>
                    </div>
                </div>

                <form class="form">
                    <div class="form-group">
                        <textarea class="form-control input-md" id="note-text"></textarea>
                    </div>
                </form>


                <div class="row-fluid">
                    <button class="btn btn-md btn-success save pull-right"> Сохранить</button>
                    <button class="btn btn-danger cls">Отмена</button>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="remove-note" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Удаление заметки</h4>
            </div>
            <div class="modal-body">
                <div class="row-fluid">
                    Эта заметка будет безвозвратно удалена, Вы уверены?
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success rm">Удалить</button>
                <button class="btn btn-danger cls">Отмена</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="add-transaction" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Добавление транзакции</h4>
            </div>
            <div class="modal-body">
                <div class="row-fluid">
                    <select class="input-md form-control" name="currency">
                        <option value="<?=GameSettings::CURRENCY_POINT?>">Баллы</option>
                        <option value="<?=GameSettings::CURRENCY_MONEY?>">Деньги</option>
                    </select>
               </div>
               <div class="row-fluid">
                    <small>Отрицательное число, для снятия баланса</small>
                    <input class="form-control input-md" name="sum" placeholder="Сумма транзакции"></input>
               </div>
               <br />
               <div class="row-fluid">
                    <input class="form-control input-md" name="description" placeholder="Описание"></input>
               </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success add">Создать</button>
                <button class="btn btn-danger cls">Отмена</button>
            </div>
        </div>
    </div>
</div>


<script>

/* OEDERS BLOCK */
$('.orders-trigger').on('click', function() {
    $.ajax({
        url: "/private/users/orders/" + $(this).data('id'),
        method: 'GET',
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {

                var tdata = '';
                $(data.data.MoneyOrders).each(function(id, tr) {
                    tdata += '<tr class="'+(tr.status==0?'warning':tr.status==1?'success':'danger')+'"><td>'+tr.id+'</td><td>'+tr.date+'</td><td>'+tr.type+'</td><td>'+tr.data+'</td>'
                    tdata += '</td></tr>'

                });
                $("#orders-holder").find('.money tbody').html(tdata);

                tdata = '';
                $(data.data.ShopOrders).each(function(id, tr) {
                    tdata += '<tr class="'+(tr.status==0?'warning':tr.status==1?'success':'danger')+'"><td>'+tr.id+'</td><td>'+tr.date+'</td><td>'+tr.item+'</td><td>ФИО: '+tr.name+'<br>Телефон: '+tr.phone+'<br>Адрес: '+tr.address+'</td><td>'+tr.price+'</td>'
                    tdata += '</tr>'

                });
                $("#orders-holder").find('.shop tbody').html(tdata);
                $("#orders-holder").modal();
                $("#orders-holder").find('.cls').on('click', function() {
                    $("#orders-holder").modal('hide');
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
/* END ORDERS BLOCK */

/* TICKETS BLOCK */
$('.tickets-trigger').on('click', function() {
    currency=($(this).parent().find('td.country').text()=='UA'?'грн':'руб');
    $.ajax({
        url: "/private/users/tickets/" + $(this).data('id'),
        method: 'GET',
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                var tdata = ''
                $(data.data.tickets).each(function(id, ticket) {
                    tdata += '<tr>' +
                    '<td>'+(ticket.LotteryId>0?ticket.LotteryId:'')+'</td>' +
                    '<td>'+ticket.DateCreated+'</td>' +
                    '<td>'+ticket.TicketNum+'</td>' +
                    '<td><ul class="ticket-numbers"><li>'+(ticket.Combination).join('</li><li>')+'</li></ul></td>' +
                    '<td>'+(ticket.LotteryId>0?ticket.TicketWin+(ticket.TicketWin>0?' '+(ticket.TicketWinCurrency=='MONEY'?currency:'баллов'):''):'')+'</td>' +
                    '</tr>'
                });
                $("#tickets-holder").find('tbody').html(tdata);
                $("#tickets-holder").modal();
                $("#tickets-holder").find('.cls').on('click', function() {
                    $("#tickets-holder").modal('hide');
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
/* END TICKETS BLOCK */

/* STATS BLOCK */
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
/* END STATS BLOCK */

/* NOTE BLOCK */
$('.notes-trigger').on('click', function() {
    var plid = $(this).data('id');
    $.ajax({
        url: "/private/users/notes/" + $(this).data('id'),
        method: 'GET',
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                var tdata = ''
                $(data.data.notes).each(function(id, tr) {
                    tdata += '<tr><td>'+tr.date+'</td><td>'+tr.user+'</td><td>'+tr.text+'</td>'
                    tdata += '<td><button class="btn btn-md btn-danger" onclick="removeNotice('+tr.id+');"><i class="glyphicon glyphicon-remove"></i></td></tr>';
                });
                $("#notes-holder").find('tbody').html(tdata);

                $("#notes-holder").modal();
                $("#notes-holder").find('.cls').on('click', function() {
                    $("#notes-holder").modal('hide');
                })
                $("#notes-holder").find('.add').off('click').on('click', function() {
                    addNote(plid);
                });
            } else {
                alert(data.message);
            }
        },
        error: function() {
            alert('Unexpected server error');
        }
    });
});


function removeNote(trid) {
    $("#remove-notice").modal();
    $("#remove-notice").find('.cls').off('click').on('click', function() {
        $("#remove-notice").modal('hide');
    });
    $("#remove-notice").find('.rm').off('click').on('click', function() {
        $.ajax({
            url: "/private/users/rmNotice/" + trid,
            method: 'POST',
            async: true,
            data: {},
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    $("#remove-notice").modal('hide');
                    $("#notices-holder").modal('hide');

                    alert('Уведомление удалено');
                } else {
                    alert(data.message);
                }
            },
            error: function() {
                alert('Unexpected server error');
            }
        });

    });
}

function addNote(plid) {
    $("#add-note").modal();
    $("#add-note").find('.cls').off('click').on('click', function() {
        $("#add-note").modal('hide');
    });
    $('#note-text').val('');

    $("#add-note").find('.save').off('click').on('click', function() {
        var text = $('#note-text').val();

        if (!text) {
            alert('Text can\'t be empty');
            return false;
        }
        currentEdit.playerId = plid;
        currentEdit.text = text;

        $("#errorForm").hide();
        $(this).find('.glyphicon').remove();



        $.ajax({
            url: "/private/users/addNote/" + plid,
            method: 'POST',
            async: true,
            data: currentEdit,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    $("#add-note").modal('hide');
                    $("#notes-holder").modal('hide');

                    alert('Заметка добавлена');
                } else {
                    alert(data.message);
                }
            },
            error: function() {
                alert('Unexpected server error');
            }
        });


    });

}
/* END NOTE BLOCK */

/* NOTICE BLOCK */
var currentEdit = {
    id: '',
    text : '',
};

$(document).ready(function() {
    $('#text').summernote({
        height: 200,
    });
    $('#text').code('');
});

function showError(message) {
    $(".error-container").text(message);
    $("#errorForm").show();

    $('.save').removeClass('btn-success').addClass('btn-danger');
    $('.save').prepend($('<i class="glyphicon glyphicon-remove"></i>'));
}

    $('.notices-trigger').on('click', function() {
        var plid = $(this).data('id');
        var type = $(this).data('type');
        $.ajax({
            url: "/private/users/notices/" + $(this).data('id')+'?type='+type,
            method: 'GET',
            async: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    var tdata = ''
                    $(data.data.notices).each(function(id, tr) {
                        tdata += '<tr><td>'+tr.id+'</td><td>'+tr.date+'</td><td>'+tr.title+'</td>'
                        tdata += '<td><button class="btn btn-md btn-danger" onclick="removeNotice('+tr.id+');"><i class="glyphicon glyphicon-remove"></i></td></tr>';
                        if(tr.text)
                            tdata += '<tr><td colspan=4>'+tr.text+'</td></tr>';
                    });
                    $("#notices-holder").find('tbody').html(tdata);

                    $("#notices-holder").modal();
                    $("#notices-holder").find('.cls').on('click', function() {
                        $("#notices-holder").modal('hide');
                    })
                    $("#notices-holder").find('.add').off('click').on('click', function() {
                        addNotice(plid);
                    });
                } else {
                    alert(data.message);
                }
            },
            error: function() {
                alert('Unexpected server error');
            }
        });
    });


    function removeNotice(trid) {
        $("#remove-notice").modal();
        $("#remove-notice").find('.cls').off('click').on('click', function() {
            $("#remove-notice").modal('hide');
        });
        $("#remove-notice").find('.rm').off('click').on('click', function() {
            $.ajax({
                url: "/private/users/rmNotice/" + trid,
                method: 'POST',
                async: true,
                data: {},
                dataType: 'json',
                success: function(data) {
                    if (data.status == 1) {
                        $("#remove-notice").modal('hide');
                        $("#notices-holder").modal('hide');

                        alert('Уведомление удалено');
                    } else {
                        alert(data.message);
                    }
                },
                error: function() {
                    alert('Unexpected server error');
                }
            });

        });
    }

    function addNotice(plid) {
        $("#add-notice").modal();
        $("#add-notice").find('.cls').off('click').on('click', function() {
            $("#add-notice").modal('hide');
        });
        $('#text').code('');
        $('input[name="title"]').val('')


        $("#add-notice").find('.save').off('click').on('click', function() {
            var text = $('#text').code();

            if (!$('input[name="title"]').val()) {
                showError('Title can\'t be empty');

                return false;
            }
            currentEdit.title = $('input[name="title"]').val();
            currentEdit.playerId = plid;
            currentEdit.text = text;

            $("#errorForm").hide();
            $(this).find('.glyphicon').remove();



            $.ajax({
                url: "/private/users/addNotice/" + plid,
                method: 'POST',
                async: true,
                data: currentEdit,
                dataType: 'json',
                success: function(data) {
                    if (data.status == 1) {
                        $("#add-notice").modal('hide');
                        $("#notices-holder").modal('hide');

                        alert('Уведомление добавлено');
                    } else {
                        alert(data.message);
                    }
                },
                error: function() {
                    alert('Unexpected server error');
                }
            });


        });

    }
/* END NOTICE BLOCK */

/* REVIEW BLOCK */
$('.reviews-trigger').on('click', function() {
    $.ajax({
        url: "/private/users/reviews/" + $(this).data('id'),
        method: 'GET',
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                var tdata = '';
                $(data.data.reviews).each(function(id, tr) {
                    tdata += '<tr class="'+(tr.Status==0?'warning':tr.Status==1?'success':'danger')+'"><td>'+tr.Date+'</td><td>'+tr.Text+'</td><td>'+(tr.Image?'<img src="/filestorage/reviews/'+tr.Image+'">':'')+'</td>'
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
/* END REVIEW BLOCK */

/* LOG BLOCK */
$('.logs-trigger').on('click', function() {
    $.ajax({
        url: "/private/users/logs/" + $(this).data('id'),
        method: 'GET',
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                var tdata = '';
                $(data.data.logs).each(function(id, tr) {
                    tdata += '<tr><td>'+tr.Id+'</td><td>'+tr.Action+'</td><td>'+tr.Desc+'</td><td>'+tr.Date+'</td>'
                });
                $("#logs-holder").find('tbody').html(tdata);


                $("#logs-holder").modal();
                $("#logs-holder").find('.cls').on('click', function() {
                    $("#logs-holder").modal('hide');
                })
                $("#logs-holder").find('.add').off('click').on('click', function() {
                    addTransaction(plid);
                });
            } else {
                alert(data.message);
            }
        },
        error: function() {
            alert('Unexpected server error');
        }
    });
});
/* END LOG BLOCK */

/* TRANSACTIONS BLOCK */
    $('.transactions-trigger').on('click', function() {
        var plid = $(this).data('id');
        $.ajax({
            url: "/private/users/transactions/" + $(this).data('id'),
            method: 'GET',
            async: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    var tdata = '';
                    temp_bal = '';
                    $(data.data.points).each(function(id, tr) {
                        tdata += '<tr><td>'+tr.id+'</td><td>'+tr.date+'</td><td>'+tr.desc+'</td><td>'+(tr.sum<0?'<span class=red>':'')+tr.sum+'</td><td>'+((temp_bal)!=parseFloat(tr.bal) && id!=0?'<span class=red>':'')+tr.bal+'</td>'
                        tdata += '<td><button class="btn btn-md btn-danger" onclick="removeTransaction('+tr.id+');"><i class="glyphicon glyphicon-remove"></i></td></td></tr>'
                        temp_bal=parseFloat(tr.bal)-parseFloat(tr.sum);
                    });
                    $("#transactions-holder").find('.points tbody').html(tdata);
                    tdata = '';
                    temp_bal = 0;
                    $(data.data.money).each(function(id, tr) {
                        tdata += '<tr><td>'+tr.id+'</td><td>'+tr.date+'</td><td>'+tr.desc+'</td><td>'+(tr.sum<0?'<span class=red>':'')+tr.sum+'</td><td>'+((temp_bal)!=parseFloat(tr.bal) && id!=0?'<span class=red>':'')+tr.bal+'</td>'
                        tdata += '<td><button class="btn btn-md btn-danger" onclick="removeTransaction('+tr.id+');"><i class="glyphicon glyphicon-remove"></i></td></td></tr>'
                        temp_bal=parseFloat(tr.bal)-parseFloat(tr.sum);
                    });
                    $("#transactions-holder").find('.money tbody').html(tdata);
                    $("#transactions-holder").modal();
                    $("#transactions-holder").find('.cls').on('click', function() {
                        $("#transactions-holder").modal('hide');
                    })
                    $("#transactions-holder").find('.add').off('click').on('click', function() {
                        addTransaction(plid);
                    });
                } else {
                    alert(data.message);
                }
            },
            error: function() {
                alert('Unexpected server error');        
            }
        });   
    });

function removeTransaction(trid) {
    $("#remove-transaction").modal();
    $("#remove-transaction").find('.cls').off('click').on('click', function() {
        $("#remove-transaction").modal('hide');
    });    
    $("#remove-transaction").find('.rm').off('click').on('click', function() {
        $.ajax({
            url: "/private/users/rmTransaction/" + trid,
            method: 'POST',
            async: true,
            data: {
                'description' : $('#remove-transaction').find('input[name="description"]').val()
            },
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    $("#remove-transaction").modal('hide');            
                    $("#transactions-holder").modal('hide');

                    alert('Транзакция удалена');
                } else {
                    alert(data.message);
                }
            },
            error: function() {
                alert('Unexpected server error');        
            }
        });   
        
    });    
}

function addTransaction(plid) {
    $("#add-transaction").modal();

    $("#add-transaction").find('.cls').off('click').on('click', function() {
        $("#add-transaction").modal('hide');
    });   
    $("#add-transaction").find('.add').off('click').on('click', function() {
        $.ajax({
            url: "/private/users/addTransaction/" + plid,
            method: 'POST',
            async: true,
            data: {
                'currency' : $('#add-transaction').find('select[name="currency"]').val(),
                'sum' : $('#add-transaction').find('input[name="sum"]').val(),
                'description' : $('#add-transaction').find('input[name="description"]').val()
            },
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    $("#add-transaction").modal('hide');            
                    $("#transactions-holder").modal('hide');

                    alert('Транзакция добавлена');
                } else {
                    alert(data.message);
                }
            },
            error: function() {
                alert('Unexpected server error');        
            }
        });   
    });
}
/* END TRANSACTIONS BLOCK */

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

/* SOCIAL INFORMATION */
    $('.sl-bk').on('click', function() {

        $("#social-holder").modal();

        $("#social-holder").find('.cls').off('click').on('click', function() {
            $("#social-holder").modal('hide');
        });

        array=($(this).first().next().html().toString()).split(' ; ');
        tbl=photo=icon=name=method=page=enabled='';
        $.each(array, function(key,value) {
            if(value) {
                param = value.split(' : ');
                if (param[0] == 'photoURL')
                    photo = '<img style="float:left;padding:0 10px 10px 0;width:30%;" src="' + param[1] + '">';
                else if (param[0] == 'profileURL')
                    page = '<a target="_blank" href="' + param[1] + '">';
                else if (param[0] == 'displayName')
                    name = param[1];
                else if (param[0] == 'method')
                    method = '<span class="glyphicon glyphicon-'+param[1]+'" aria-hidden="true"></span> ';
                else if (param[0] == 'enabled')
                    enabled = param[1]==1?'success':'danger';
                else {
                    tbl += '<tr><td>' + param[0] + '</td><td>';
                    if($.isArray(param[1]))
                        $.each(param[1], function(k,v){
                            tbl +=k+": "+v;   });
                    else
                        tbl += param[1];

                    tbl += '</td></tr>';
                }
            }
        });
        icon='<span class="'+$(this).attr("class")+'"></span>';
        html=photo+'<table class="table table-striped" style="width:70%;"><thead><th colspan=2 class='+enabled+'>'+page+method+name+icon+'</a></th></thead><tbody>'+tbl+'</tbody></table>';

        $("#social-holder").modal().find('.modal-body').html(html);

    });
/* END SOCIAL INFORMATION */

</script>

<?php

    function sortIcon($currentField, $currentSort, $pager, $search)
    {
        $icon = '<a href="/private/users?page=1&sortField=%s&sortDirection=%s'.($search['query']?'&search[where]='.$search['where'].'&search[query]='.$search['query']:'').'"><i class="glyphicon glyphicon-chevron-%s"></i></a>';
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