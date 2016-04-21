<div class="modal fade users" id="social-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true"
     xmlns="http://www.w3.org/1999/html">
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
            <form>
            <div class="modal-body">
            </div>
            </form>
            <div class="modal-footer">
                <button class="btn btn-success add">Сохранить</button>
                <button type="button" class="btn btn-default cls">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tree" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 95%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Дерево связей</h4>
            </div>
            <div class="modal-body">
                <!--
                We will create a family tree using just CSS(3)
                The markup will be simple nested lists
                -->
                <div class="tree">
                    <ul>
                        <li>
                            <a href="#">Parent</a>
                            <ul>
                                <li>
                                    <a href="#">Child</a>
                                    <ul>
                                        <li>
                                            <a href="#">Grand Child</a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="#">Child</a>
                                    <ul>
                                        <li><a href="#">Grand Child</a></li>
                                        <li>
                                            <a href="#">Grand Child</a>
                                            <ul>
                                                <li>
                                                    <a href="#">Great Grand Child</a>
                                                </li>
                                                <li>
                                                    <a href="#">Great Grand Child</a>
                                                </li>
                                                <li>
                                                    <a href="#">Great Grand Child</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li><a href="#">Grand Child</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>


            </div>
        </div>
    </div>
</div>

<div class="modal fade users" id="logins-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Login information</h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                    <th>Дата</th>
                    <th>Agent</th>
                    <th>Ip</th>
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

<div class="modal fade users" id="mults-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Mults information</h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                    <th>Id</th>
                    <th>Player</th>
                    <th>Phone</th>
                    <th>Qiwi</th>
                    <th>WebMoney</th>
                    <th>YandexMoney</th>
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

<div class="modal fade users" id="tickets-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Tickets</h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                    <th>#ID</th>
                    <th>Дата</th>
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

<div class="modal fade users" id="stats-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Game stats</h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <th>#ID</th>
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

<div class="modal fade users" id="logs-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
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

<div class="modal fade users" id="transactions-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="btn btn-success pull-right add">Добавить транзакцию</button>
                <div style="clear:both"></div>
            </div>
            <div class="modal-body">
                <h4>Баллы
                    <button class="btn btn-info pull-right" onclick="location.href='#moneyTransactions'" style="margin-top: -10px;"><i class="glyphicon glyphicon-hand-down"></i> Деньги</button></h4>
                <hr />
                <table class="table table-striped points"  id="pointsTransactions">
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
                
                <h4>Деньги
                    <button class="btn btn-info pull-right" onclick="location.href='#pointsTransactions'" style="margin-top: -10px;"><i class="glyphicon glyphicon-hand-up"></i> Баллы</button></h4>
                <hr />
                <table class="table table-striped money" id="moneyTransactions">
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


<div class="modal fade users" id="orders-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
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
                    <th>Дата</th>
                    <th></th>
                    <th>Номер</th>
                    <th>Товар</th>
                    <th>Данные</th>
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
                    <th>Дата</th>
                    <th></th>
                    <th colspan="2">Номер</th>
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

<div class="modal fade users" id="remove-transaction" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
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


<div class="modal fade users" id="filter-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h4>Фильтр</h4>
                <hr />
                <table class="table table-striped" >
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
                <button class="btn btn-success add">Отфильтровать результаты</button>
                <button type="button" class="btn btn-default cls">Закрыть</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade users" id="notices-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="btn btn-success pull-right add">Добавить уведомление</button>
                <div style="clear:both"></div>
            </div>
            <div class="modal-body">
                <h4>Уведомления</h4>
                <hr />
                <table class="table table-striped" >
                    <thead>
                    <th>Дата</th>
                    <th>Уведомление</th>
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

<div class="modal fade users" id="add-notice" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
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
                        <label class="control-label">Фильтры</label>
                        <div class="row-fluid">
                            <div class="col-my-5">

                        <select name="type" id="notice-type" class="form-control" />
                            <option value="Message">Стандартное уведомление</option>
                            <option value="AdBlock">Предупреждение об AdBlock</option>
                            <option value="Mult">Отказ - Мультиаккаунт</option>
                            <option value="AdBlock2">Отказ - AdBlock</option>
                            <option value="Success">Вывод денежных средств</option>
                        </select>
                            </div>

                            <div class="col-my-1">
                                <input type="text" name="minLotteries" value="" placeholder="Игр" class="form-control" />
                            </div>
                            <div class="col-my-2">
                                <input type="text" name="registeredFrom" value="" placeholder="От" class="form-control datepick" />
                            </div>
                            <div class="col-my-2">
                                <input type="text" name="registeredUntil" id="" value="" placeholder="До" class="form-control datepick" />
                            </div>



                            <div class="col-my-2">
                                <select name="country" class="form-control" placeholder="Страна" />
                                    <option value="">Страна:</option>
                                <? foreach(\CountriesModel::instance()->getCountries() as $lang):?>
                                    <option value="<?=$lang;?>"><?=$lang;?></option>
                                <? endforeach;?>
                                </select>
                            </div>
                        </div>

                        <div class="row-fluid">

                        <label class="control-label">Или перечень ID через запятую</label>
                        <textarea oninput="this.value=this.value.replace(/[a-z -]|[\r\n]|[\.\/\\\+\=\[\]\`\;\:]|[а-я]+/g, '').replace(/\,\,/g,',');" type="text" name="ids" value="" class="form-control"></textarea>

                        </div>

                        <script type="text/javascript">
                            $(".datepick").datepicker({format: 'yyyy-mm-dd',
                                showTimePicker: false,
                                autoclose: true,
                                pickTime: false});
                        </script>
                    </div>

                    <div style="clear: both;"></div>

                    <div class="form-group">
                        <label class="control-label">Текст уведомления</label>
                        <div id="text"></div>
                    </div>
                </form>

                <div class="row-fluid">
                    <button class="btn btn-md btn-success save pull-right">Отправить</button>
                    <button class="btn btn-danger cls">Отмена</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade users" id="delete-user" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Удаление пользователя</h4>
            </div>
            <div class="modal-body">
                <div class="row-fluid">
                    Пользователь <b><span id="username"></span></b> будет подчистую стерт из базы данных без возможности восстановления.<br>
                    <div style="text-align: center;"><h4> <b> Вы уверены?</b></h4></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success rm">Удалить</button>
                <button class="btn btn-danger cls">Отмена</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade users" id="remove-notice" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
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

<div class="modal fade users" id="reviews-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h4>Комментарии</h4>
                <hr />
                <table class="table table-striped" >
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

<div class="modal fade users" id="messages-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h4>Сообщения</h4>
                <hr />
                <table class="table table-striped" >
                    <thead>
                    <th>Дата</th>
                    <th>Сообщение</th>
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

<div class="modal fade users" id="notes-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="btn btn-success pull-right add">Добавить заметку</button>
                <div style="clear:both"></div>
            </div>
            <div class="modal-body">
                <h4>Заметки</h4>
                <hr />
                <table class="table table-striped" >
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

<div class="modal fade users" id="add-note" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
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


<div class="modal fade users" id="remove-note" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
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

<div class="modal fade users" id="add-transaction" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Добавление транзакции</h4>
            </div>
            <div class="modal-body">
                <div class="row-fluid">
                    <select class="input-md form-control" name="currency">
                        <option value="<?=LotterySettings::CURRENCY_POINT?>">Баллы</option>
                        <option value="<?=LotterySettings::CURRENCY_MONEY?>">Деньги</option>
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

<script src="/theme/admin/lib/jquery.damnUploader.min.js"></script>
<script>

<? $langs=array();
foreach(\LanguagesModel::instance()->getList() as $lang)
    $langs[$lang->getCode()]=$lang->getCode();?>
langs=<?=json_encode($langs);?>;

<? $countries=array();
foreach(\CountriesModel::instance()->getCountries() as $lang)
    $countries[$lang]=$lang;?>
countries=<?=json_encode($countries);?>;

/* OEDERS BLOCK */
$('.orders-trigger').on('click', function() {
    $.ajax({
        url: "/private/users/orders/" + ($(this).attr('data-id')?$(this).data('id'):'0?number='+$(this).data('number')),
        method: 'GET',
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {

                var tdata = '';
                $(data.data.MoneyOrders).each(function(id, tr) {
                    tdata += '<tr data-toggle="tooltip" data-placement="auto" title="'+(tr.username)+'" class="'+(tr.status==0?'warning':tr.status==1?'success':'danger')+'"><td>'+tr.date+'</td><td'+(tr.playername?'>'+tr.playername+'</td><td>':' colspan=2>')+(tr.number>0?tr.number:'')+'</td><td><img src="../tpl/img/'+tr.type+'.png"></td><td>'+tr.data+'</td>'
                    tdata += '</td></tr>'

                });
                $("#orders-holder").find('.money tbody').html(tdata);

                tdata = '';
                $(data.data.ShopOrders).each(function(id, tr) {
                    tdata += '<tr data-toggle="tooltip" data-placement="auto" title="'+(tr.username)+'"  class="'+(tr.status==0?'warning':tr.status==1?'success':'danger')+'"><td>'+tr.date+'</td><td>'+tr.playername+'</td><td>'+(tr.number>0?tr.number:'')+'</td><td>'+tr.item+'<br>'+tr.price+'</td><td>ФИО: '+tr.name+'<br>Телефон: '+tr.phone+'<br>Адрес: '+tr.address+'</td>'
                    tdata += '</tr>'

                });
                $("#orders-holder").find('.shop tbody').html(tdata);
                $("#orders-holder").modal();
                $("#orders-holder").find('.cls').on('click', function() {
                    $("#orders-holder").modal('hide');
                })

                $('[data-toggle="tooltip"]').tooltip()
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

/* FILTER BLOCK */
$('.filter-trigger').on('click', function() {

    $("#filter-holder").modal();
    $("#filter-holder").find('.cls').on('click', function() {
        $("#filter-holder").modal('hide');
    })
});
/* END TICKETS BLOCK */

/* TICKETS BLOCK */
$('.tickets-trigger').on('click', function() {
    var currency=($(this).parents('tr').find('td.transactions-trigger span').text());
    $.ajax({
        url: "/private/users/tickets/" + $(this).data('id'),
        method: 'GET',
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                var tdata = ''
                $(data.data.tickets).each(function(id, ticket) {
                    var tickets=[];
                    var WinCombination=ticket.WinCombination;
                    $(ticket.Combination).each(function(i, num) {
                        tickets.push('<li'+($.inArray( parseInt(num), WinCombination )>=0 ? ' class="win"':'')+'>'+num+'</li>');
                    });

                    tdata += '<tr>' +
                    '<td>'+(ticket.LotteryId>0?ticket.LotteryId:'')+'</td>' +
                    '<td>'+(ticket.LotteryId>0?ticket.Date:'')+'</td>' +
                    '<td>'+ticket.DateCreated+'</td>' +
                    '<td>'+ticket.TicketNum+'</td>' +
                    '<td><ul class="ticket-numbers">' +
                    tickets.join('')+
                    '</ul></td>' +
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

/* MULTS BLOCK */
$('.mults-trigger').on('click', function() {

    $.ajax({
        url: "/private/users/mults/" + $(this).data('id'),
        method: 'GET',
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                var tdata = ''
                $(data.data.mults).each(function(id, login) {

                    tdata += '<tr>' +
                    '<td>'+login.Id+'</td>' +
                    '<td>'+login.Nicname+'</td>' +
                    '<td>'+(login.Phone?login.Phone:'')+'</td>' +
                    '<td>'+(login.Qiwi?login.Qiwi:'')+'</td>' +
                    '<td>'+(login.WebMoney?login.WebMoney:'')+'</td>' +
                    '<td>'+(login.YandexMoney?login.YandexMoney:'')+'</td>' +
                    '</tr>'
                });
                $("#mults-holder").find('tbody').html(tdata);
                $("#mults-holder").modal();
                $("#mults-holder").find('.cls').on('click', function() {
                    $("#mults-holder").modal('hide');
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
/* END MULTS BLOCK */

/* LOGINS BLOCK */
$('.logins-trigger').on('click', function() {

    $.ajax({
        url: "/private/users/logins/" + $(this).data('id'),
        method: 'GET',
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                var tdata = ''
                $(data.data.logins).each(function(id, login) {

                    tdata += '<tr>' +
                    '<td>'+login.Date+'</td>' +
                    '<td>'+login.Agent+'</td>' +
                    '<td>'+login.Ip+'</td>' +
                    '</tr>'
                });
                $("#logins-holder").find('tbody').html(tdata);
                $("#logins-holder").modal();
                $("#logins-holder").find('.cls').on('click', function() {
                    $("#logins-holder").modal('hide');
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
/* END LOGINS BLOCK */

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
                    tdata += '<td><button class="btn btn-md btn-danger" onclick="removeNote('+tr.id+');"><i class="glyphicon glyphicon-remove"></i></td></tr>';
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
    $("#remove-note").modal();
    $("#remove-note").find('.cls').off('click').on('click', function() {
        $("#remove-note").modal('hide');
    });
    $("#remove-note").find('.rm').off('click').on('click', function() {
        $.ajax({
            url: "/private/users/rmNote/" + trid,
            method: 'POST',
            async: true,
            data: {},
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    $("#remove-note").modal('hide');
                    $("#notes-holder").modal('hide');

                    alert('Заметка удалена');
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


/* notice BLOCK */
$("#add-notice select#notice-type").on('change', function() {


    texts=  {
        'Mult': {
            'text': " Согласно п.п. 4.1.1. п. 4 Участник вправе участвовать в Игре путем регистрации и создания лишь одной учетной записи с соблюдением условий Соглашения.<br><br>По нашим данным Вы играете с нескольких аккаунтов.<br><br>Напишите нам на info@lotzon.com подтверждение или опровержение этой информации.<br><br>В выплате отказано. Денежные средства возвращены на Ваш счет.",
            'title': "Вывод денежных средств"
        },

        'AdBlock': {
            'text': "Согласно п.п. 9.1.7. п. 9 Участник обязан отключить все системы блокировки показа рекламных сообщений (AdBlock и подобные).<br><br>Устраните это нарушение в ближайшее время. Если это нарушение не будет устранено до следующей проверки, мы будем вынуждены заблокировать ваш аккаунт.",
            'title': "Нарушение правил участия"
        },

        'AdBlock2': {
            'text': "Согласно п.п. 9.1.7. п. 9 Участник обязан отключить все системы блокировки показа рекламных сообщений (AdBlock и подобные).<br><br>Поэтому, заявки на получение выигрыша от участников, у которых была включена блокировка показа рекламных сообщений будут рассматриваться через две недели после отключения блокировки.<br><br>Статус: блокировка включена.<br>В выплате отказано. Денежные средства возвращены на Ваш счет.",
            'title': "Получение выигрыша"
        },

        'Success': {
            'text': 'Поздравляем!<br><br>Ваш счет успешно пополнен.<br> Напишите пожалуйста о своем выигрыше в нашей группе <a href="http://vk.com/topic-78075693_33621597" target="_blank">ВКонтакте</a><br> Скриншот приветствуется.<br><br> Будем очень благодарны!',
            'title': "Вывод денежных средств"
        },

    };

    if(texts[$(this).val()]){
        $('#text').code(texts[$(this).val()]['text']);
        $('input[name="title"]').val(texts[$(this).val()]['title']);
    } else {
        $('#text').code('');
        $('input[name="title"]').val('');
    }

});

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
                        tdata += '<tr data-toggle="tooltip" data-placement="auto" title="'+(tr.username)+'"><td>'+tr.date+
                            (tr.registeredFrom ?'<br><span class="label label-primary">от '+tr.registeredFrom+'</span>':'')+
                            (tr.registeredUntil ?'<br><span class="label label-primary">до '+tr.registeredUntil+'</span>':'')+
                            (tr.country ?'<br><span class="label label-primary">'+tr.country+'</span>':'')+
                            (tr.minLotteries ?'<br><span class="label label-primary"><i class="fa fa-gift "></i> '+tr.minLotteries+'</span>':'')+
                            '</td><td><b>'+tr.title+'</b>'+(tr.text?'<br>'+tr.text:'')+'</td>';
                        tdata += '<td><button class="btn btn-md btn-danger" onclick="removeNotice('+tr.id+');"><i class="glyphicon glyphicon-remove"></i></td></tr>';

                    });
                    $("#notices-holder").find('tbody').html(tdata);

                    $("#notices-holder").modal();
                    $("#notices-holder").find('.cls').on('click', function() {
                        $("#notices-holder").modal('hide');
                    })
                    $("#notices-holder").find('.add').off('click').on('click', function() {
                        addNotice(plid);
                    });

                    $('[data-toggle="tooltip"]').tooltip();
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
        $('textarea[name="ids"]').val('');
        $('input[name="title"]').val('');
        $("#add-notice select").val('Message');


        $("#add-notice").find('.save').off('click').on('click', function() {
            var text = $('#text').code();

            if (!$('input[name="title"]').val()) {
                showError('Title can\'t be empty');

                return false;
            }
            currentEdit.title = $('input[name="title"]').val();
            currentEdit.playerId = plid;
            currentEdit.ids = $('textarea[name="ids"]').val();
            currentEdit.text = text;
            currentEdit.country = $('select[name="country"]').val();
            currentEdit.minLotteries = $('input[name="minLotteries"]').val();
            currentEdit.registeredFrom = $('input[name="registeredFrom"]').val();
            currentEdit.registeredUntil = $('input[name="registeredUntil"]').val();
            currentEdit.type =  $('select[name="type"]').val();

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
$('.profile-trigger').on('click', function() {
    plid=$(this).data('id');
    $.ajax({
        url: "/private/users/profile/" + plid,
        method: 'GET',
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                user=data.data;

                html='<img class="avatar-trigger" data-id="'+user.Id+'" src="'+(user.Avatar?'../filestorage/avatars/'+Math.ceil(user.Id / 100) + '/'+user.Avatar:'../tpl/img/but-upload-review.png')+'">' +
                '<div>'+
                '<div class="input-group"><span class="input-group-addon">Ник</span><input type="text" class="form-control" name="Nicname" placeholder="Ник" value="'+user.Nicname+'"></div>' +
                '<div class="input-group"><span class="input-group-addon">Имя</span><input type="text" class="form-control" name="Name" placeholder="Имя" value="'+user.Name+'"></div>' +
                '<div class="input-group"><span class="input-group-addon">Фамилия</span><input type="text" class="form-control" name="Surname" placeholder="Surname" value="'+user.Surname+'"></div>' +
                '<div class="input-group"><span class="input-group-addon">День рождения</span><input type="text" class="form-control" name="bd" placeholder="День рождения" value="'+user.Birthday+'"></div>' +
                '<div class="input-group"><span class="input-group-addon">Телефон</span><input type="text" class="form-control" name="phone" placeholder="Телефон" value="'+user.Phone+'"></div>' +
                '<div class="input-group"><span class="input-group-addon">Qiwi</span><input type="text" class="form-control" name="qiwi" placeholder="Qiwi" value="'+user.Qiwi+'"></div>' +
                '<div class="input-group"><span class="input-group-addon">WebMoney</span><input type="text" class="form-control" name="webmoney" placeholder="WebMoney" value="'+user.WebMoney+'"></div>' +
                '<div class="input-group"><span class="input-group-addon">YandexMoney</span><input type="text" class="form-control" name="yandexmoney" placeholder="YandexMoney" value="'+user.YandexMoney+'"></div>' +
                '<div class="input-group"><span class="input-group-addon">UTC</span><input type="text" class="form-control" name="Utc" placeholder="UTC" value="'+(user.Utc || '')+'"></div>' +
                '<div class="input-group"><span class="input-group-addon">Страна</span>' +
                '<select class="form-control" name="Country">';

                if(!(user.Country in countries))
                    html+='<option value="'+user.Country+'" selected>'+user.Country+'</option>';
                $.each(countries, function(code,country){
                    html+='<option value="'+code+'" '+(code==user.Country?' selected':'')+'>'+country+'</option>';
                });

                html+='</select></div>' +
                '<div class="input-group"><span class="input-group-addon">Язык</span>' +
                '<select class="form-control" name="Lang">';

                $.each(langs, function(code,country){
                    html+='<option value="'+code+'" '+(code==user.Lang?' selected':'')+'>'+country+'</option>';
                });

                html+='</select></div>' +
                '<div class="input-group"><span class="input-group-addon">Пароль</span><input type="text" class="form-control col-md-12" name="Password" placeholder="Пароль" value=""></div>' +
                '</div>';


                $("#profile-holder").find('.modal-body').html(html);
                $("#profile-holder").modal();
                $("#profile-holder").find('.cls').on('click', function() {
                    $("#profile-holder").modal('hide');
                });
                $("#profile-holder").find('.add').off('click').on('click', function() {
                    updateProfile(plid);
                });
                $('.avatar-trigger').off('click').on('click', uploadAvatar);

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

function updateProfile(plid){
    $.ajax({
        url: "/private/users/profile/" + plid,
        method: 'POST',
        async: true,
        data: $('#profile-holder form').serialize(),
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                $("#profile-holder").modal('hide');
                alert('Профиль изменен');
            } else {
                alert(data.message);
            }
        },
        error: function() {
            alert('Unexpected server error');
        }
    });
}
/* MESSAGE BLOCK */
$('.messages-trigger').on('click', function() {
    $.ajax({
        url: "/private/users/messages/" + $(this).data('id'),
        method: 'GET',
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                var tdata = '';
                $(data.data.messages).each(function(id, tr) {
                    tdata += '<tr><td>'
                        + tr.Date
                        + '</td><td>'
                        + (tr.Image?'<img src="/filestorage/messages/'+tr.Image+'">':'')
                        + tr.Text
                        + '</td><td style="text-align: right;">'
                        + '<span style="display: block;" class="label label-default">' + tr.PlayerName + '</span>' + '<span style="display: block;" class="label label-primary">' + tr.ToPlayerName + '</span>'
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
/* END MESSAGE BLOCK */

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
                    tdata += '<tr class="'+(
                            tr.Status == 0
                                ? 'warning'
                                : tr.Status == 1
                                ? 'success'
                                : tr.Status == 2
                                ? 'default'
                                : 'danger'
                        )
                        + '"><td>'
                        + tr.Date
                        + '</td><td>'
                        + tr.Text
                        + '</td><td>'
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
/* END REVIEW BLOCK */

/* LOG BLOCK */
$('.logs-trigger').on('click', function() {

    $.ajax({
        url: "/private/users/logs/" + $(this).data('id')+'?action='+($(this).data('action')?$(this).data('action'):''),
        method: 'GET',
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                var tdata = '';
                $(data.data.logs).each(function(id, tr) {
                    tdata += '<tr class="'+tr.Status+'"><td>'+tr.Id+'</td><td>'+tr.Action+'</td><td>'+tr.Desc+'</td><td>'+tr.Date+'</td>'
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

/* BOT USER BLOCK */
$('.bot-trigger').on('click', function() {

    var plid = $(this).data('id');
    var status = ($("tr#user"+plid).hasClass("info") ? 0 : 1);
    $.ajax({
        url: "/private/users/" + plid + "/bot/" + status,
        method: 'POST',
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                if($("tr#user"+plid).hasClass("info") && !data.data.bot)
                    $("tr#user"+plid).addClass('danger').removeClass('info');
                else if($("tr#user"+plid).hasClass("danger") && data.data.bot)
                    $("tr#user"+plid).removeClass('danger').addClass('info');
            } else {
                alert(data.message);
            }
        },
        error: function() {
            alert('Unexpected server error');
        }
    });

});

/* BAN USER BLOCK */
$('.ban-trigger').on('click', function() {

    var plid = $(this).data('id');
    var status = ($("tr#user"+plid).hasClass("danger") ? 0 : 1);
        $.ajax({
            url: "/private/users/" + plid + "/ban/" + status,
            method: 'POST',
            async: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    if($("tr#user"+plid).hasClass("danger") && !data.data.ban)
                        $("tr#user"+plid).removeClass('danger');
                    else if (data.data.ban)
                        $("tr#user"+plid).addClass('danger');
                } else {
                    alert(data.message);
                }
            },
            error: function() {
                alert('Unexpected server error');
            }
        });

});

$('.options').hover(
    function(){
        $('button', this).not('.cog').css('display', 'inline-block');
        $('button.cog', this).css('display', 'none');
        $(this).parent().children('button').css('display', 'none');
    },
    function(){
        $('button', this).not('.cog').css('display', 'none');
        $('button.cog', this).css('display', 'inline-block');
        $(this).parent().children('button').css('display', 'inline-block');
    }
);
/* LOGOUT USER BLOCK */
$('.logout-trigger').on('click', function() {

    var that = $(this),
        plid = that.data('id'),
        status = that.hasClass("btn-success") ? 0 : 1;

    $.ajax({
        url: "/private/users/" + plid + "/logout/" + status,
        method: 'POST',
        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                if (data.data.status == 1) {
                    that.removeClass('btn-danger').addClass('btn-success');
                } else {
                    that.removeClass('btn-success').addClass('btn-danger');
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

/* UPLOAD AVATAR */
uploadAvatar = function() {

    // create form
    var form = $('<form method="POST" enctype="multipart/form-data"><input type="file" name="image"/></form>'),
        image = $(this),
        plid = image.data('id'),
        input = form.find('input[type="file"]').damnUploader({
            url      : '/private/users/' + plid + '/avatar',
            fieldName: 'image',
            dataType : 'json'
        });

    input.off('du.add').on('du.add', function (e) {

        e.uploadItem.completeCallback = function (succ, data, status) {
            console.log(succ, data, status);
            if(succ && data.status)
                image.attr('src', '../filestorage/avatars/' + Math.ceil(plid / 100) + '/' + data.data.image);
            else
                alert('Error ' + (data.message ? data.message : ''));
        };

        e.uploadItem.progressCallback = function (perc) {}
        e.uploadItem.upload();
    });

    form.find('input[type="file"]').click();
}
/* TREE */
$('.tree-trigger').on('click', function() {

    $("#tree").modal();
    $("#tree").find('.cls').off('click').on('click', function() {
        $("#tree").modal('hide');
    });

});

/* END TREE BLOCK */

/* DELETE USER BLOCK */
$('.delete-trigger').on('click', function() {

    var plid = $(this).data('id');
    $("#delete-user #username").text($("tr#user"+plid+" td:nth-child(3)").text()+' ('+$("tr#user"+plid+" td:nth-child(4)").contents().first().text()+')');
    $("#delete-user").modal();
    $("#delete-user").find('.cls').off('click').on('click', function() {
        $("#delete-user").modal('hide');
    });

    $("#delete-user").find('.rm').off('click').on('click', function() {
        $.ajax({
            url: "/private/users/delete/" + plid,
            method: 'POST',
            async: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    $("#delete-user").modal('hide');
                    $("tr#user"+plid).remove();

                    alert('Пользователь удален');
                } else {
                    alert(data.message);
                }
            },
            error: function() {
                alert('Unexpected server error');
            }
        });

    });

});

/* END DELETE BLOCK */

/* TRANSACTIONS BLOCK */
    $(document).on('click', '.transactions-trigger', function() {
        var plid = $(this).data('id');
        var offset = parseInt($(this).attr('data-offset')) || 0;
        var currency = $(this).attr('data-currency') || null;


        $.ajax({
            url: "/private/users/transactions/" + $(this).data('id') +'?offset='+offset+(currency ?'&currency='+currency :''),
            method: 'GET',
            async: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {

                    var tdata = '';
                    temp_bal = '';


                    if(data.data.points && (data.data.points).length) {

                        $(data.data.points).each(function (id, tr) {
                            tdata += '<tr><td>' + tr.id + '</td><td>' + tr.date + '</td><td>' + tr.desc + '</td><td>' + (tr.sum < 0 ? '<span class=red>' : '') + tr.sum + '</td><td>' + ((temp_bal) != parseFloat(tr.bal) && id != 0 ? '<span class=red>' : '') + tr.bal + '</td>'
                            tdata += '<td><button class="btn btn-md btn-danger" onclick="removeTransaction(' + tr.id + ');"><i class="glyphicon glyphicon-remove"></i></td></td></tr>'
                            temp_bal = parseFloat(tr.bal) - parseFloat(tr.sum);
                        });

                        if(offset) {
                            $("#transactions-holder").find('.points tbody button').parents('tr').first().before(tdata);
                            $("#transactions-holder").find('.points tbody button').attr('data-offset',(offset + (data.data.points).length));
                        } else {
                            tdata += '<tr><td></td><td></td><td><button class="transactions-trigger" data-currency="points" data-id="'+plid+'" data-offset="'+(offset + (data.data.points).length)+ '">показать еще</button></td><td></td><td></td></tr>';
                            $("#transactions-holder").find('.points tbody').html(tdata);
                        }

                        console.log(data.data.limit , (data.data.points).length)
                        if(data.data.limit > (data.data.points).length)
                            $("#transactions-holder").find('.points tbody button').parents('tr').first().remove();
                    }


                    tdata = '';
                    temp_bal = 0;

                    if(data.data.money && (data.data.money).length) {
                        console.log((data.data.money).length);

                        $(data.data.money).each(function (id, tr) {
                            tdata += '<tr><td>' + tr.id + '</td><td>' + tr.date + '</td><td>' + tr.desc + '</td><td>' + (tr.sum < 0 ? '<span class=red>' : '') + tr.sum + '</td><td>' + ((temp_bal) != parseFloat(tr.bal) && id != 0 ? '<span class=red>' : '') + tr.bal + '</td>'
                            tdata += '<td><button class="btn btn-md btn-danger" onclick="removeTransaction(' + tr.id + ');"><i class="glyphicon glyphicon-remove"></i></td></td></tr>'
                            temp_bal = parseFloat(tr.bal) - parseFloat(tr.sum);
                        });

                        if(offset) {
                            $("#transactions-holder").find('.money tbody button').parents('tr').first().before(tdata);
                            $("#transactions-holder").find('.money tbody button').attr('data-offset',(offset + (data.data.money).length));
                        } else {
                            tdata += '<tr><td></td><td></td><td><button class="transactions-trigger" data-currency="money" data-id="'+plid+'" data-offset="'+(offset + (data.data.money).length)+ '">показать еще</button></td><td></td><td></td></tr>';
                            $("#transactions-holder").find('.money tbody').html(tdata);
                        }

                        if(data.data.limit > (data.data.money).length)
                            $("#transactions-holder").find('.money tbody button').parents('tr').first().remove();
                    }


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

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})


</script>
