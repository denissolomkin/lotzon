
<div class="container-fluid">

    <div class="row-fluid">
        <h2>Пользователи (<?=$playersCount?>) <button class="btn btn-md btn-success search-users"><i class="glyphicon glyphicon-search"></i></button> <button class="btn btn-md btn-warning notices-trigger" data-id="0"><span class="glyphicon glyphicon-bell" aria-hidden="true"></span></button></h2>
        <hr/>
    </div>
    <? if ($pager['pages'] > 1) {?>
        <div class="row-fluid">
            <div class="btn-group">
                <? for ($i=1; $i <= $pager['pages']; ++$i) { ?>
                    <button onclick="document.location.href='/private/users?page=<?=$i?>&sortField=<?=$currentSort['field']?>&sortDirection=<?=$currentSort['direction'].($search['query']?'&search[where]='.$search['where'].'&search[query]='.$search['query']:'')?>'" class="btn btn-default btn-md <?=($i == $pager['page'] ? 'active' : '')?>"><?=$i?></button>
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
                <th>IP <?=sortIcon('IP', $currentSort, $pager, $search)?></th>
                <th>Реферал <?=sortIcon('ReferalId', $currentSort, $pager, $search)?></th>
                <th>Последний логин <?=sortIcon('DateLogined', $currentSort, $pager, $search)?></th>
                <th>Последний пинг <?=sortIcon('OnlineTime', $currentSort, $pager, $search)?></th>
                <th>Игр сыграно <?=sortIcon('GamesPlayed', $currentSort, $pager, $search)?></th>
                <th>Билеты <?=sortIcon('TicketsFilled', $currentSort, $pager, $search)?></th>
                <th>Ad <?=sortIcon('AdBlock', $currentSort, $pager, $search)?></th>
                <th>Денег <?=sortIcon('Money', $currentSort, $pager, $search)?></th>
                <th>Баллов <?=sortIcon('Points', $currentSort, $pager, $search)?></th>
                <th>Options</th>
            </thead>
            <tbody>
                <? foreach ($list as $player) { ?>
                    <tr>
                        <td><?=$player->getId()?></td>
                        <td><?=($player->getSurname() . " " . $player->getName() . " " . $player->getSecondName())?><? if($player->getAvatar()) echo '<img src="../filestorage/'.'avatars/' . (ceil($player->getId() / 100)) . '/'.$player->getAvatar().'">'?></td>
                        <td><?=($player->getNicName())?></td>
                        <td class="<?=$player->getValid() ? "success" : "danger"?>"><?=$player->getEmail()?>
                            <?foreach($player->getAdditionalData() as $provider=>$info)
                            {
                                echo '<a href="javascript:void(0)" class="sl-bk '.$provider.'"></a>
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

                        </td>
                        <td><?=$player->getCountry()?></td>
                        <td><?=$player->getDateRegistered('d.m.Y H:i')?></td>
                        <td><?if($player->getCountIp()>1) {?><a href="?search[where]=Ip&search[query]=<?=$player->getIP()?>"><span class="label label-danger"><?=$player->getCountIp()?><?}?></span></a> <?=$player->getIP()?></td>
                        <td <?=($player->getReferalId()?'onclick="location.href=\'?search[where]=Id&search[query]='.$player->getReferalId().'\';" style="cursor:pointer;"':'')?> class="<?=$player->getReferalId() ? "success" : "danger"?>"><?=$player->getReferalId() ? "#" . $player->getReferalId() : "&nbsp;"?></td>
                        <td class="<?=($player->getDateLastLogin()?(($player->getDateLastLogin() < strtotime('-7 day', time())) ? "warning" : ""):'warning')?>"><?=($player->getDateLastLogin()?$player->getDateLastLogin('d.m.Y H:i'):'')?></td>
                        <td class="<?=($player->getOnlineTime()?(($player->getOnlineTime() < strtotime('-7 day', time())) ? "warning" : ""):'warning')?>"><?=($player->getOnlineTime()?$player->getOnlineTime('d.m.Y H:i'):'')?></td>
                        <td><?=$player->getGamesPlayed()?></td>
                        <td class="<?=$player->isTicketsFilled() ? 'success' : 'danger'?>"><?=$player->isTicketsFilled() ? 'да' : 'нет'?></td>
                        <td><?=$player->getAdBlock()? '<button class="btn btn-xs btn-danger notices-trigger" style="opacity: 1;" disabled="disabled"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span></button>' : ''?></td>
                        <td><?=$player->getMoney()?> <?=$player->getCountry() == 'UA' ? 'грн' : 'руб'?></td>
                        <td><?=$player->getPoints()?></td>
                        <td>
                            <button class="btn btn-xs btn-warning notices-trigger" data-id="<?=$player->getId()?>"><span class="glyphicon glyphicon-bell" aria-hidden="true"></span></button>
                            <button class="btn btn-xs btn-warning transactions-trigger" data-id="<?=$player->getId()?>">T</button>
                            <button class="btn btn-xs btn-warning stats-trigger" data-id="<?=$player->getId()?>">Р</button>
                            <button class="btn btn-xs btn-danger profile-trigger" data-id="<?=$player->getId()?>"><span class="glyphicon glyphicon-user" aria-hidden="true"></button>
                            <button class="btn btn-xs btn-danger ban-trigger" data-id="<?=$player->getId()?>"><span class="glyphicon glyphicon-lock" aria-hidden="true"></button>
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
    $('.search-users').on('click', function() {
        var input = $('<input class="form-control input-md" value="<?=(isset($search['query'])?$search['query']:'');?>" style="width:200px;display:inline-block;" placeholder="id, фио, ник или email...">' +
        '<select id="search_where" class="form-control input-md" style="width: 100px;display: inline-block;">' +
        '<option value="">Везде</option>' +
        '<option value="Id">Id</option>' +
        '<option value="Ip">Ip</option>' +
        '<option value="NicName">Ник</option>' +
        '<option value="CONCAT(`Surname`,`Name`)">Фио</option>' +
        '<option value="Email">Email</option></select>');
        var sccButton = $('<button class="btn btn-md btn-success"><i class="glyphicon glyphicon-ok"></i></button>')
        var cnlButton = $('<button class="btn btn-md btn-danger"><i class="glyphicon glyphicon-remove"></i></button>');
        var button = $(this);

        input.insertBefore(button);
        sccButton.insertBefore(button);
        cnlButton.insertBefore(button);
        button.hide();

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
            url="/private/users?search[query]="+input.val()+
            ($("#search_where").val() ? "&search[where]="+$("#search_where").val(): '');
            document.location.href=url;
        });
    });
    <? if(isset($search['query']) and $search['query']){?>
    $('.search-users').trigger('click');
    $('#search_where').val('<?=$search['where']?>');

    <? } ?>

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
                    enabled = param[1]?'success':'danger';
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