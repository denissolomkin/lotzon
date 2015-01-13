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
                        <label class="control-label">Тип</label>
                        <select name="type" class="form-control" /><option value="Message">Стандартное уведомление</option><option value="AdBlock">Предупреждение об AdBlock</option></select>
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
            currentEdit.type =  $('select[name="type"]').val();;

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
