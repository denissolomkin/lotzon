<div class="container-fluid">
    <div class="row-fluid">
        <h2>Email рассылка > Задания</h2>
        <hr />
    </div>
    <div class="row-fluid">
        <button onclick="document.location.href='/private/maillist/tasks?status=archived'" class="btn right btn-warning btn-md <?=($status!='archived' ? 'active' : '')?>"><i class="glyphicon glyphicon-time"></i>Архив</button>
        <button onclick="document.location.href='/private/maillist/tasks'" class="btn right  btn-md btn-success <?=($status!='archived' ? 'active' : '')?>"><i class="glyphicon glyphicon-ok"></i>Текущие</button>
        <div class="btn-group">
            <button onclick="document.location.href='/private/maillist/tasks'" type="button" class="btn btn-md lang btn-default active" data-lang="">Задания</button>
            <button onclick="document.location.href='/private/maillist/messages'" type="button" class="btn btn-md lang btn-default" data-lang="">Шаблоны</button>
        </div>
        <button class="btn btn-md btn-success text-trigger" data-key="0"><i class="glyphicon glyphicon-plus"></i> Добавить</button>
    </div>
    <div class="row-fluid">&nbsp;</div>
    <div class="row-fluid">
        <table class="table table-striped">
            <thead>
            <th>#ID</th>
            <th>Описание</th>
            <th>Запланирована</th>
            <th>Статус</th>
            <th>Включена</th>
            <th>Options</th>
            </thead>
            <tbody>
            <? foreach ($tasks as $key=>$task) { ?>
                <tr>
                    <td class="id"><?=$task->getId()?></td>
                    <td width="60%" class="title"><strong><?=$task->getDescription()?></strong></td>
                    <td><i class="glyphicon glyphicon-<?=($task->getSchedule()==true ? 'ok' : 'remove')?>"></i></td>
                    <td class="<?=($task->getStatus()=='waiting' ? 'warning' : 'success')?>"><?=$task->getStatus()?></td>
                    <td><i class="glyphicon glyphicon-<?=($task->getEnable()==true ? 'ok' : 'remove')?>"></i></td>
                    <td>
                        <button class="btn btn-md edit-text btn-warning text-trigger" data-key="<?=$key?>"><i class="glyphicon glyphicon-edit"></i></button>
                        <button class="btn btn-md statistic-text btn-success" data-key="<?=$key?>"><i class="fa fa-bar-chart"></i></button>
                        <?php if ($status=='archived') { ?>
                            <button class="btn btn-md remove-text btn-danger" data-target="#deleteConfirm"><i class="glyphicon glyphicon-remove"></i></button>
                        <?php } else { ?>
                            <button class="btn btn-md remove-text btn-danger" data-target="#archiveConfirm"><i class="glyphicon glyphicon-time"></i></button>
                        <?php } ?>
                    </td>
                </tr>
            <? } ?>
            </tbody>
        </table>
    </div>
</div>
<!-- ===========================DELETE=========================== -->
<div class="modal fade" id="deleteConfirm" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Удаление задания</h4>
            </div>
            <div class="modal-body">
                <p>Уверены ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger">Удалить</button>
            </div>
        </div>
    </div>
</div>
<script>
    $('.remove-text').on('click', function() {
        var row = $(this).parents('tr');
        var identifier = row.find('td.id').text();
        $('#deleteConfirm').modal();
        $('#deleteConfirm').find('.btn-danger').off('click').on('click', function() {
            $.ajax({
                url: "/private/maillist/tasks/" + identifier,
                method: 'DELETE',
                data: {},
                async: true,
                dataType: 'json',
                success: function(data) {
                    if (data.status == 1) {
                        $('#deleteConfirm').modal('hide')
                        row.remove();
                    } else {
                        showError(data.message);
                    }
                },
                error: function() {
                    showError('Unexpected server error');
                }
            });
        });
    });
</script>
<!-- ==========================/DELETE=========================== -->

<!-- =========================STATISTIC========================== -->
<div class="modal fade" id="Statistic" role="dialog" aria-labelledby="StatisticLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="width:700px;">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Статистика по заданию</h4>
            </div>
            <div class="modal-body" id="chart"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<script>
    $('.statistic-text').on('click', function() {
        var row = $(this).parents('tr');
        var identifier = row.find('td.id').text();
        $('#Statistic').modal();
        $.ajax({
            url: "/private/maillist/tasks/statistic/player_games/" + identifier,
            method: 'GET',
            data: {},
            async: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {

                    if (data.data.count<=0)
                        return false;

                    bars_data = [];
                    cats = [];
                    for(var i = 0; i < data.data.bars_count; i++) {
                        bars_data[i] = [""+i, parseInt(data.data.bars[i])*100/data.data.count];
                        cats[i] = ""+i;
                    }
                    bars_data[i] = [i+'+', parseInt(data.data.bars.over)*100/data.data.count];
                    cats[i] = i+"+";
                    console.log(bars_data);

                    $('#chart').highcharts({
                        credits: {
                            enabled: false
                        },
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'Сыграно лотерей игроками, которым пришёл email после рассылки'
                        },
                        subtitle: {
                            text: 'Всего отослано писем:'+data.data.count
                        },
                        xAxis: {
                            type: 'игр',
                            labels: {
                                style: {
                                    fontSize: '12px',
                                    fontFamily: 'Verdana, sans-serif'
                                }
                            },
                            categories: cats
                        },
                        yAxis: {
                            min: 0,
                            max:100,
                            title: {
                                text: '%'
                            }
                        },
                        legend: {
                            enabled: false
                        },
                        tooltip: {
                            pointFormat: '{point.y:.1f}'
                        },
                        series: [{
                            name: '%',
                            data: bars_data
                        }]
                    });

                } else {
                    showError(data.message);
                }
            },
            error: function() {
                showError('Unexpected server error');
            }
        });
    });
</script>
<!-- ========================/STATISTIC========================== -->

<!-- =========================== EDIT =========================== -->
<div class="modal fade texts" id="text-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Редактирование задания</h4>
                <div class="row-fluid" id="errorForm" style="display:none">
                    <div class="alert alert-danger" role="alert">
                        <span class="error-container"></span>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="row-fluid" id="addForm">
                    <form class="form">
                        <input name="id" type="hidden">
                        <div class="form-group">
                            <label class="control-label">Описание</label>
                            <input type="text" name="description" value="" placeholder="Описание" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label class="control-label">Включено</label>
                            <input type="checkbox" name="enable" value="">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Шаблон</label>
                            <select class="form-control" name="messageId">
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Фильтры</label>
                            <div id="filters">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Дата начала действия</label>
                            <input type="text" name="dateFrom" value="" placeholder="дата" class="form-control" />
                        </div>
                        <script>
                            $('input[name="dateFrom"]').datepicker({
                                format: "yyyy-mm-dd",
                                viewformat: "yyyy-mm-dd",
                                startView: 1,
                                todayBtn: "linked"
                            });
                        </script>
                        <div class="form-group">
                            <label class="control-label">Время отправки</label>
                            <input type="text inline" name="timeFrom" value="" placeholder="от" class="form-control" />
                            <input type="text inline" name="timeTo" value="" placeholder="до" class="form-control" />
                        </div>
                        <script>
                            $('input[name="timeFrom"]').timepicker({
                                maxHours: 24,
                                showMeridian: false,
                                snapToStep: false,
                                minuteStep: 1
                            });
                            $('input[name="timeTo"]').timepicker({
                                maxHours: 24,
                                showMeridian: false,
                                snapToStep: false,
                                minuteStep: 1
                            });
                        </script>
                        <div class="form-group">
                            <label class="control-label">Тип</label>
                            <select class="form-control" name="type">
                            </select>
                        </div>
                        <div class="form-group">
                            <div id="settings"></div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-success save">Сохранить</button>
                <button type="button" class="btn btn-default cls">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<script>
    var currentEdit = {
        id          : 0,
        description : '',
        messageId   : '',
        schedule    : false,
        settings    : {},
        enable      : false,
        status      : '',
        messages    : {}
    };
    var equals = [
        {value: "=",   text: "="},
        {value: "<>",  text: "<>"},
        {value: "<",   text: "<"},
        {value: "<=",  text: "<="},
        {value: ">",   text: ">"},
        {value: ">=",  text: ">="},
        {value: "IN",  text: "IN"},
        {value: "NOT IN", text: "NOT IN"},
        {value: "LIKE", text: "LIKE"},
    ];
    var filters = [];
    var events  = [];

    function drawFilterCount() {
        messageId   = $('select[name="messageId"]').val();
        settings    = {
            filters: currentEdit.settings.filters
        };
        $('#filterEmailsCount', modal).html('<i class="glyphicon glyphicon-refresh"></i>');
        $.ajax({
            url     : "/private/maillist/tasks/filter",
            method  : 'POST',
            data    : {
                messageId  : messageId,
                settings   : settings
            },
            async   : true,
            dataType: 'json',
            success: function(data) {
                $('#filterEmailsCount', modal).html(data.data.count);
            },
            error: function() {
                $('#filterEmailsCount', modal).html('error');
            }
        });
    }

    function drawFilter() {
        retHTML = '<table class="table table-striped">';
        retJS = '';
        for(key in currentEdit.settings.filters) {
            filter = currentEdit.settings.filters[key];

            if (Object.keys(filter).length===0) {
                html_filter = '<span href="#" id="filter_' + key + '" data-unsavedclass="test" style="cursor:pointer;" data-send="never" data-type="select" data-pk="1" data-url="/post" data-emptytext="фильтр" data-title="фильтр"></span>';
                js_filter   = '<scr' + 'ipt> $(document).ready(function() { $("#filter_' + key + '").editable( { source: filters,success: function(response, newValue) { if (newValue) { currentEdit.settings.filters[' + key + '].filter = newValue; currentEdit.settings.filters[' + key + '].equal = "="; currentEdit.settings.filters[' + key + '].value = currentEdit.filters[newValue].default; } drawFilter(); } }  ); });</scr' + 'ipt>';
                retHTML = retHTML + '<tr><td>'+html_filter+'</td></tr>';
                retJS   = retJS + js_filter;
            } else {
                if (currentEdit.filters[filter.filter].type=='date') {
                    date = new Date(filter.value);
                    filter.value = date.getFullYear() + "-" + ("0" + (date.getMonth() + 1)).slice(-2) + "-" + ("0" + date.getDate()).slice(-2);
                }
                if (filter.equal == "IN" || filter.equal == "NOT IN") {
                    type = "select2";
                } else {
                    type = currentEdit.filters[filter.filter].type;
                }
                html_value = '<span href="#" id="value_' + key + '" data-unsavedclass="test" style="cursor:pointer;" data-send="never" data-type="' + type + '" data-pk="1" data-url="/post" data-emptytext="значение" data-title="значение">' + filter.value + '</span>';
                js_value   = '<scr' + 'ipt> $(document).ready(function() { $("#value_' + key + '").editable( { select2: {tags:[]}, datepicker: {weekStart:1}, success: function(response, newValue) { if (newValue) currentEdit.settings.filters[' + key + '].value = newValue; drawFilter(); } }  ); });</scr' + 'ipt>';
                html_equal = '<span href="#" id="equal_' + key + '" data-unsavedclass="test" style="cursor:pointer;" data-send="never" data-type="select" data-pk="1" data-url="/post" data-emptytext="значение" data-title="значение">' + filter.equal + '</span>';
                js_equal   = '<scr' + 'ipt> $(document).ready(function() { $("#equal_' + key + '").editable( { source: equals, prepend: "' + filter.equal + '",success: function(response, newValue) { if (newValue) currentEdit.settings.filters[' + key + '].equal = newValue; drawFilter(); } }  ); });</scr' + 'ipt>';
                retHTML = retHTML + '<tr><td>' + currentEdit.filters[filter.filter].description + '</td><td style="width:10px;"></td><td>' + html_equal + '</td><td style="width:10px;"></td><td>' + html_value + '</td><td><a href="#" id="del_filter" data-key="' + key + '"><i class="glyphicon glyphicon-minus"></i></a></td></tr>';
                retJS   = retJS + js_value + js_equal;
            }
        }
        retHTML = retHTML + '</table>';
        retHTML = retHTML + '<div class="row"><div class="col-sm-8"><a href="#" id="add_filter"><i class="glyphicon glyphicon-plus"></i></a></div>';
        retHTML = retHTML + '<div class="col-sm-4" style="align:right;"><div style="float:right;" id="filterEmailsCount"></div><div style="float:right;"><i class="glyphicon glyphicon-envelope"></i>: </div></div></div>';
        $('#filters', modal).html(retHTML+retJS);
        drawFilterCount();
    }

    $(document).on('click', '#del_filter', function() {
        key = $(this).attr('data-key');
        delete currentEdit.settings.filters[key];
        if (Object.keys(currentEdit.settings.filters).length === 0) {
            delete currentEdit.settings.filters;
        }
        drawFilter();
    });

    $(document).on('click', '#add_filter', function() {
        if (currentEdit.settings.filters) {
            key = Math.max.apply(Math, Object.keys(currentEdit.settings.filters)) + 1;
            currentEdit.settings.filters[key] = {};
        } else {
            currentEdit.settings.filters = { 1: {}, };
        }
        drawFilter();
    });

    $(document).on('change', 'select[name="type"]', function() {
        drawSettings();
    });

    function drawSettings() {
        retHTML = '';
        $type = $('select[name="type"]').val();
        if ($type=="schedule") {
            retHTML = retHTML + '<div class="form-group"><label class="control-label">Периодичность</label><select class="form-control" name="period"><option value="day">Ежедневно</option><option value="week">Еженедельно</option><option value="month">Ежемесячно</option></select></div><div id="parameter"></div>';
            $('#settings', modal).html(retHTML);
            if (currentEdit.settings.period) {
                $('select[name="period"] [value="'+currentEdit.settings.period+'"]').attr("selected", "selected");
            }
            $('select[name="period"]').change();
        }
        if ($type=="events") {
            retHTML = retHTML + '<div class="form-group"><label class="control-label">Событие</label><select class="form-control" name="event"></select></div><div id="parameter"></div>';
            $('#settings', modal).html(retHTML);
            for(key in currentEdit.events) {
                event = currentEdit.events[key];
                $('select[name="event"]', modal).append($('<option value="' + key + '">' + event.description + '</option>'));
            }
            if (currentEdit.settings.event) {
                $('select[name="event"] [value="'+currentEdit.settings.event.type+'"]').attr("selected", "selected");
            }
            $('select[name="event"]').change();
        }
        if ($type=="once") {
            $('#settings', modal).html(retHTML);
        }
    }

    $(document).on('change', 'select[name="period"]', function() {
        select_period = $('select[name="period"] option:selected').val();
        if (select_period == 'week') {
            $('#parameter', modal).html('<div class="form-group"><label class="control-label">дни недели:</label><select multiple class="form-control" name="parameter"><option value="monday">Понедельник</option><option value="tuesday">Вторник</option><option value="wednesday">Среда</option><option value="thursday">Четверг</option><option value="friday">Пятница</option><option value="Saturday">Суббота</option><option value="sunday">Воскресенье</option></select></div>');
            if (currentEdit.settings.parameter) {
                for (key in currentEdit.settings.parameter) {
                    param = currentEdit.settings.parameter[key];
                    $('select[name="parameter"] [value="'+param+'"]').attr("selected", "selected");
                }
            }
        }
        if (select_period == 'month') {
            preHTML = '';
            for(var i = 1; i < 32; i++) {
                preHTML = preHTML + '<option value="'+i+'">'+i+'</option>';
            }
            preHTML = '<div class="form-group"><label class="control-label">дни месяца:</label><select multiple class="form-control" name="parameter">'+preHTML+'<option value="last">последний день</option></select></div>';
            $('#parameter', modal).html(preHTML);
            if (currentEdit.settings.parameter) {
                for (key in currentEdit.settings.parameter) {
                    param = currentEdit.settings.parameter[key];
                    $('select[name="parameter"] [value="'+param+'"]').attr("selected", "selected");
                }
            }
        }
        if (select_period == 'day') {
            $('#parameter', modal).html('');
        }
    });

    $(document).on('change', 'select[name="messageId"]', function() {
        drawFilterCount();
    });

    $(document).on('change', 'select[name="event"]', function() {
        select_event = $('select[name="event"] option:selected').val();
        if (currentEdit.events[select_event].parameter) {
            $('#parameter', modal).html('<div class="form-group"><label class="control-label">n</label><input type="text" name="parameter" value="" placeholder="значение" class="form-control" /></div>');
            if (currentEdit.settings.event.value && currentEdit.settings.event.type == select_event) {
                $('input[name="parameter"]', modal).val(currentEdit.settings.event.value);
            }
        } else {
            $('#parameter', modal).html('');
        }
    });

    $(document).on('click', '.text-trigger', function() {
        currentEdit.id          = 0;
        currentEdit.description = '';
        currentEdit.messageId   = '';
        currentEdit.schedule    = false;
        currentEdit.settings    = {};
        currentEdit.enable      = false;
        currentEdit.status      = '';
        currentEdit.messages    = {};
        currentEdit.filters     = {};
        currentEdit.events      = {};

        filters = [];

        modal = $("#text-holder");
        modal.modal();
        $('.cls', modal).off('click').on('click', function() {modal.modal('hide');});

        if($(this).attr('data-key')) {
            $.ajax({
                url: "/private/maillist/tasks/" + $(this).attr('data-key'),
                method: 'GET',
                async: true,
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        currentEdit.id          = data.data.Id;
                        currentEdit.description = data.data.Description;
                        currentEdit.messageId   = data.data.MessageId;
                        currentEdit.schedule    = data.data.Schedule;
                        currentEdit.settings    = data.data.Settings ? data.data.Settings : {};
                        currentEdit.enable      = data.data.Enable;
                        currentEdit.status      = data.data.Status;
                        currentEdit.messages    = data.messages ? data.messages : {};
                        currentEdit.filters     = data.filters ? data.filters : {};
                        currentEdit.events      = data.events ? data.events : {};

                        $('input[name="description"]', modal).val(currentEdit.description);

                        /* forming select of messages  */
                        $('select[name="messageId"]', modal).empty();
                        for(key in currentEdit.messages) {
                            message = currentEdit.messages[key];
                            $('select[name="messageId"]', modal).append($('<option value="'+message['Id']+'">'+message['Description']+'</option>'));
                        }
                        $('select[name="messageId"] [value="'+currentEdit.messageId+'"]').attr("selected", "selected");
                        $('select[name="messageId"]').change();

                        /* enable checkbox */
                        if (currentEdit.enable==true) {
                            $('input[name="enable"]', modal).prop("checked", true);
                        } else {
                            $('input[name="enable"]', modal).prop("checked", false);
                        }

                        /* forming select of type */
                        $('select[name="type"]', modal).empty();
                        $('select[name="type"]', modal).append($('<option value="once">Однократно</option>'));
                        $('select[name="type"]', modal).append($('<option value="schedule">По расписанию</option>'));
                        $('select[name="type"]', modal).append($('<option value="events">По событию</option>'));
                        if (currentEdit.schedule!=true) {
                            $('select[name="type"] [value="once"]', modal).attr("selected", "selected");
                        } else if (currentEdit.settings.event) {
                            $('select[name="type"] [value="events"]', modal).attr("selected", "selected");
                        } else {
                            $('select[name="type"] [value="schedule"]', modal).attr("selected", "selected");
                        }

                        /* setup date, time */
                        if (currentEdit.settings.dateFrom) {
                            $('input[name="dateFrom').datepicker('setDate', currentEdit.settings.dateFrom);
                        } else {
                            $('input[name="dateFrom').datepicker('setDate', '2015-01-01');
                        }
                        if (currentEdit.settings.timeFrom) {
                            $('input[name="timeFrom').timepicker('setTime', currentEdit.settings.timeFrom);
                        } else {
                            $('input[name="timeFrom').timepicker('setTime', '0:00');
                        }
                        if (currentEdit.settings.timeTo) {
                            $('input[name="timeTo').timepicker('setTime', currentEdit.settings.timeTo);
                        } else {
                            $('input[name="timeTo').timepicker('setTime', '23:59');
                        }

                        /* forming select of filter types */
                        for(key in currentEdit.filters) {
                            filter = currentEdit.filters[key];
                            filters.push({value: key, text:filter.description})
                        }

                        drawFilter();
                        drawSettings();

                    } else {
                        alert(data.message);
                    }
                },
                error: function () {
                    alert('Unexpected server error');
                }
            });
        }
        return false;
    });

    $('.save').on('click', function() {
        if (!$('input[name="description"]').val()) {
            showError('Описание не может быть пустым');
            return false;
        }

        description = $('input[name="description"]', modal).val();
        messageId   = $('select[name="messageId"]').val();
        type        = $('select[name="type"]').val();
        enable      = $('input[name="enable"]', modal).prop("checked");
        dateFrom    = $('input[name="dateFrom"]', modal).val();
        timeFrom    = $('input[name="timeFrom"]', modal).val();
        timeTo      = $('input[name="timeTo"]', modal).val();

        switch (type) {
            case "once":
                settings = {
                    filters : currentEdit.settings.filters,
                    dateFrom: dateFrom,
                    timeFrom: timeFrom,
                    timeTo  : timeTo
                };
                schedule = false;
                break;
            case "schedule":
                settings = {
                    filters : currentEdit.settings.filters,
                    period  : $('select[name="period"]', modal).val(),
                    dateFrom: dateFrom,
                    timeFrom: timeFrom,
                    timeTo  : timeTo
                };
                if ($('select[name="period"]', modal).val() != "day") {
                    settings.parameter = $('select[name="parameter"]', modal).val();
                }
                schedule = true;
                break;
            case "events":
                event = {
                    type: $('select[name="event"] option:selected').val(),
                };
                if (currentEdit.events[select_event].parameter) {
                    event.parameter = $('input[name="parameter"]', modal).val();
                };
                settings = {
                    filters : currentEdit.settings.filters,
                    event   : event,
                    dateFrom: dateFrom,
                    timeFrom: timeFrom,
                    timeTo  : timeTo
                };
                schedule = true;
                break;
            default:
                showError('Wrong input data');
                return false;
        }

        $.ajax({
            url     : "/private/maillist/tasks",
            method  : 'POST',
            data    : {
                id         : currentEdit.id,
                description: description,
                messageId  : messageId,
                schedule   : schedule,
                settings   : settings,
                enable     : enable
            },
            async   : true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    document.location.reload();
                } else {
                    showError(data.message);
                }
            },
            error: function() {
                showError('Unexpected server error');
            }
        });
    });

    function showError(message) {
        $(".error-container").text(message);
        $("#errorForm").show();

        $('.save').removeClass('btn-success').addClass('btn-danger');
        $('.save').prepend($('<i class="glyphicon glyphicon-remove"></i>'));
    }
</script>
