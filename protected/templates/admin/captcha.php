<div class="container-fluid">
    <form role="form" action="/private/captcha" method="POST">
        <input type="hidden" name='captcha[]' value="">
        <div class="row-fluid">
            <h2>
                Captcha
                <input type="checkbox" name='captcha[Enabled]' <?=$list['Enabled']?'checked ':'';?>data-toggle="toggle">
                <button type="submit" class="btn btn-default right">Сохранить</button>
            </h2>
            <hr/>
        </div>

        <div class="row-fluid">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Среднее время >, сек</th>
                    <th>Среднее время <=, сек</th>
                    <th>Вероятность</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            <button onclick="initSetting();" type="button" class="btn btn-primary">Новое условие</button>
        </div>
    </form>

    <div class="row-fluid">
        <h2>
            Статистика
        </h2>
        <hr/>
    </div>

    <div class="row-fluid">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Период</th>
                <th>Количество</th>
                <th>Среднее время на заполнение, сек</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <div class="row-fluid">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Среднее время</th>
                <th>Количество</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script>

    $(document).on('change input', 'form, form input', function(){$('form button[type="submit"]').addClass('btn-success');});

    var Settings = <?php echo json_encode(is_array($list['Settings'])?$list['Settings']:[]);?>,
        Stats = <?php echo json_encode(is_array($stats)?$stats:[]);?>,
        Times = <?php echo json_encode(is_array($times)?$times:[]);?>;;

    for (var i in Settings)
        if(Settings.hasOwnProperty(i))
            initSetting(Settings[i]);

    for (var i in Stats)
        if(Stats.hasOwnProperty(i))
            initStat(Stats[i]);

    for (var i in Times)
        if(Times.hasOwnProperty(i))
            initTime(Times[i]);

    function initStat(data) {

        $('table:eq(1) tbody').append(
            '<tr>' +
            '<td>'+data['Period']+'</td>' +
            '<td>'+data['Cnt']+'</td>' +
            '<td>'+Math.round(data['Time'])+'</td>' +
            '</tr>'
        );
    }

    function initTime(data) {

        $('table:eq(2) tbody').append(
            '<tr>' +
            '<td>'+data['Time']+'</td>' +
            '<td>'+data['Cnt']+'</td>' +
            '</tr>'
        );
    }

    function initSetting(data) {

        var i = $('table:eq(0) tbody tr').length;

        if(!data) {
            data = {
                Min: '',
                Max: '',
                Rand: ''
            };
            $('form').change();
        }


        $('table:eq(0) tbody').append(
            '<tr>' +
            '<td><input type="text" class="form-control" name="captcha[Settings]['+i+'][Min]" value="'+data['Min']+'"></td>' +
            '<td><input type="text" class="form-control" name="captcha[Settings]['+i+'][Max]" value="'+data['Max']+'"></td>' +
            '<td><input type="text" class="form-control" name="captcha[Settings]['+i+'][Rand]" value="'+data['Rand']+'"></td>' +
            '<td><button type="button" class="btn btn-danger" onclick="$(this).parent().parent().remove();$(\'form\').change();"><i class="glyphicon glyphicon-remove"></i></button></td>' +
            '</tr>'
        );
    }

</script>