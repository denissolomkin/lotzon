<div class="container-fluid">    
    <div class="row-fluid" id="items">
        <h2>Запросы на вывод товаров (<span id="shopCount"><?=$shopCount?></span>) <button class="btn btn-info" onclick="location.href='#money'"><i class="glyphicon glyphicon-hand-down"></i> Запросы на вывод денег</button>

            <button onclick="document.location.href='/private/monetisation?moneyPage=<?=$moneyPager['page']?>&moneyStatus=<?=$moneyStatus?>&shopStatus=2&amp;sortField=Id&amp;sortDirection=desc#shop'" class="btn right btn-md btn-danger <?=($shopStatus==2 ? 'active' : '' )?>"><i class="glyphicon glyphicon-ban-circle"></i> Отклоненные</button>
            <button onclick="document.location.href='/private/monetisation?moneyPage=<?=$moneyPager['page']?>&moneyStatus=<?=$moneyStatus?>&shopStatus=1&amp;sortField=Id&amp;sortDirection=desc#shop'" class="btn right btn-md btn-success <?=($shopStatus==1 ? 'active' : '' )?>"><i class="glyphicon glyphicon-ok"></i> Одобренные</button>
            <button onclick="document.location.href='/private/monetisation?moneyPage=<?=$moneyPager['page']?>&moneyStatus=<?=$moneyStatus?>&shopStatus=0&amp;sortField=Id&amp;sortDirection=desc#shop'" class="btn right btn-warning btn-md <?=(!$shopStatus ? 'active' : '' )?>"><i class="glyphicon glyphicon-time"></i> На рассмотрении</button>

  </h2>
        <hr />
    </div>

    <? if ($shopPager['pages'] > 1) {?>
        <div class="row-fluid">
            <div class="btn-group">
                <? for ($i=1; $i <= $shopPager['pages']; ++$i) { ?>
                    <button onclick="document.location.href='/private/monetisation?shopPage=<?=$i?>&moneyPage=<?=$moneyPager['page']?>&moneyStatus=<?=$moneyStatus?>&shopStatus=<?=$shopStatus?>&sortField=<?=$currentSort['field']?>&sortDirection=<?=$currentSort['direction'].($search['query']?'&search[where]='.$search['where'].'&search[query]='.$search['query']:'')?>'" class="btn btn-default btn-xs <?=($i == $shopPager['page'] ? 'active' : '')?>"><?=$i?></button>
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
                <th>Товар</th>
                <th>Данные</th>
                <th>Стоимость</th>
                <th width="50">Options</th>
            </thead>
            <tbody>
                <? foreach ($shopOrders as $order) { ?>
                    <tr id="shop<?=$order->getId()?>">
                        <!--td><?=$order->getId()?></td-->
                        <td><?=date('d.m.Y <br> H:m:s', $order->getDateOrdered())?></td>
                        <td><?=$order->getPlayer()->getNicname()?><br><?=$order->getPlayer()->getName()?> <?=$order->getPlayer()->getSurName()?> <?=$order->getPlayer()->getSecondName()?></td>
                        <td class="<?=$order->getPlayer()->getValid() ? "success" : "danger"?>"><?=$order->getPlayer()->getEmail()?>
                            <?
                            foreach($order->getPlayer()->getAdditionalData() as $provider=>$info)
                            {
                                echo '<a href="javascript:void(0)" class="sl-bk '.$provider.($info['enabled']==1?' active':'').'"></a>
                                <div class="hidden">';
                                if(is_array($info))
                                    foreach ($info as $key=>$value) {
                                        echo $key.' : ';
                                        if(is_array($value))
                                            foreach($value as $k=>$v)
                                                echo $k.' - '.$v.' , ';
                                        else
                                            echo $value.' , ';
                                    }
                                else echo $info;
                                echo'</div>';
                            }?>

                            <br>
                            <?if($order->getPlayer()->getCounters()['Ip']>1) {?><a target=_blank href="users?search[where]=Ip&search[query]=<?=$order->getPlayer()->getIP()?>"><span class="label label-danger"><?=$order->getPlayer()->getCounters()['Ip']?><?}?></span></a> <?=$order->getPlayer()->getIP()?>
                            <div class="right">
                                <button class="btn btn-xs btn-warning notices-trigger" data-id="<?=$order->getPlayer()->getId()?>"><span class="glyphicon glyphicon-bell" aria-hidden="true"></span></button>
                                <button class="btn btn-xs btn-warning transactions-trigger" data-id="<?=$order->getPlayer()->getId()?>">T</button>
                                <button class="btn btn-xs btn-warning stats-trigger" data-id="<?=$order->getPlayer()->getId()?>">Р</button>
                            </div>
                        </td>
                        <td><?=$order->getItem()->getTitle()?></td>


                        <td>
                            ФИО: <?=$order->getSurname()?> <?=$order->getName()?> <?=$order->getSecondName()?> <br />
                            Телефон: <?=$order->getPhone()?> <br />
                            Адрес: <?=($order->getRegion() ? $order->getRegion() . ' обл.,' : '')?> г. <?=$order->getCity()?>, <?=$order->getAddress()?>

                        </td>
                        <td><?=($order->getChanceGameId() ? 'Выиграл в шанс' : $order->getItem()->getPrice())?></td>
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
            <button class="btn btn-info" onclick="location.href='#items'"><i class="glyphicon glyphicon-hand-up"></i> Запросы на вывод товаров</button>

            <button onclick="document.location.href='/private/monetisation?shopPage=<?=$shopPager['page']?>&shopStatus=<?=$shopStatus?>&moneyStatus=2&amp;sortField=Id&amp;sortDirection=desc#money'" class="btn right btn-md btn-danger <?=($moneyStatus==2 ? 'active' : '' )?>"><i class="glyphicon glyphicon-ban-circle"></i> Отклоненные</button>
            <button onclick="document.location.href='/private/monetisation?shopPage=<?=$shopPager['page']?>&shopStatus=<?=$shopStatus?>&moneyStatus=1&amp;sortField=Id&amp;sortDirection=desc#money'" class="btn right btn-md btn-success <?=($moneyStatus==1 ? 'active' : '' )?>"><i class="glyphicon glyphicon-ok"></i> Одобренные</button>
            <button onclick="document.location.href='/private/monetisation?shopPage=<?=$shopPager['page']?>&shopStatus=<?=$shopStatus?>&moneyStatus=0&amp;sortField=Id&amp;sortDirection=desc#money'" class="btn right btn-warning btn-md <?=(!$moneyStatus ? 'active' : '' )?>"><i class="glyphicon glyphicon-time"></i> На рассмотрении</button>
        </h2>
        <hr />
    </div>


    <? if ($moneyPager['pages'] > 1) {?>
        <div class="row-fluid">
            <div class="btn-group">
                <? for ($i=1; $i <= $moneyPager['pages']; ++$i) { ?>
                    <button onclick="document.location.href='/private/monetisation?moneyPage=<?=$i?>&shopPage=<?=$shopPager['page']?>&shopStatus=<?=$shopStatus?>&moneyStatus=<?=$moneyStatus?>&sortField=<?=$currentSort['field']?>&sortDirection=<?=$currentSort['direction'].($search['query']?'&search[where]='.$search['where'].'&search[query]='.$search['query']:'')?>'" class="btn btn-default btn-xs <?=($i == $moneyPager['page'] ? 'active' : '')?>"><?=$i?></button>
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
                <th>Платежная сис-ма</th>
                <th>Данные</th>
                <th>Options</th>
            </thead>
            <tbody>
                <? foreach ($moneyOrders as $order) { ?>
                    <tr id="money<?=$order->getId()?>">
                        <td><?=date('d.m.Y <br> H:m:s', $order->getDateOrdered())?></td>
                        <td><?=$order->getPlayer()->getNicname()?><br><?=$order->getPlayer()->getName()?> <?=$order->getPlayer()->getSurName()?> <?=$order->getPlayer()->getSecondName()?></td>
                        <td class="<?=$order->getPlayer()->getValid() ? "success" : "danger"?>"><?=$order->getPlayer()->getEmail()?>
                            <?foreach($order->getPlayer()->getAdditionalData() as $provider=>$info)
                            {
                                echo '<a href="javascript:void(0)" class="sl-bk '.$provider.($info['enabled']==1?' active':'').'"></a>
                                <div class="hidden">';
                                if(is_array($info))
                                    foreach ($info as $key=>$value) {
                                        echo $key.' : ';
                                        if(is_array($value))
                                            foreach($value as $k=>$v)
                                                echo $k.' - '.$v.' , ';
                                        else
                                            echo $value.' , ';
                                    }
                                else echo $info;
                                echo'</div>';
                            }?>
                            <br>
                            <?if($order->getPlayer()->getCounters()['Ip']>1) {?><a target=_blank href="users?search[where]=Ip&search[query]=<?=$order->getPlayer()->getIP()?>"><span class="label label-danger"><?=$order->getPlayer()->getCounters()['Ip']?><?}?></span></a> <?=$order->getPlayer()->getIP()?>
                            <div class="right"><button class="btn btn-xs btn-warning notices-trigger" data-id="<?=$order->getPlayer()->getId()?>"><span class="glyphicon glyphicon-bell" aria-hidden="true"></span></button>
                            <button class="btn btn-xs btn-warning transactions-trigger" data-id="<?=$order->getPlayer()->getId()?>">T</button>
                            <button class="btn btn-xs btn-warning stats-trigger" data-id="<?=$order->getPlayer()->getId()?>">Р</button>
                            <!--button data-id="<?=$order->getId()?>" class="btn btn-xs approve money btn-success"><i class="glyphicon glyphicon-ok"></i></button>
                            <button data-id="<?=$order->getId()?>" class="btn btn-xs decline money btn-danger" data-target="#deleteConfirm"><i class="glyphicon glyphicon-remove"></i></button-->
                            </div>
                        </td>
                        <td><?=$order->getType()?></td>
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

    $('.notices-trigger').on('click', function() {
        var plid = $(this).data('id');
        $.ajax({
            url: "/private/users/notices/" + $(this).data('id'),
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
                    temp_bal = '';
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



    $('.sl-bk').on('click', function() {

        $("#social-holder").modal();

        $("#social-holder").find('.cls').off('click').on('click', function() {
            $("#social-holder").modal('hide');
        });

        array=($(this).first().next().html().toString()).split(' , ');
        tbl=photo=icon=name=page='';
        $.each(array, function(key,value) {
            if(value) {
                param = value.split(' : ');
                if (param[0] == 'photoURL')
                    photo = '<img style="float:left;padding:0 10px 10px 0;width:30%;" src="' + param[1] + '">';
                else if (param[0] == 'profileURL')
                    page = '<a target="_blank" href="' + param[1] + '">';
                else if (param[0] == 'displayName')
                    name = param[1];
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
        html=photo+'<table class="table table-striped" style="width:70%;"><thead><th colspan=2>'+page+name+icon+'</a></th></thead><tbody>'+tbl+'</tbody></table>';

        $("#social-holder").modal().find('.modal-body').html(html);
    });



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
                        if(($("table tr#money"+id).parent().children().length)==1)
                            location.reload()
                        $("table tr#money"+id).remove();
                        $("#moneyCount").text(parseInt($("#moneyCount").text())-1);

                    } else {
                        if(($("table tr#shop"+id).parent().children().length)==1)
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