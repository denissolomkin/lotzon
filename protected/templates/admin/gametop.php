<div class="modal fade" id="deleteModal" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Удаление игрока</h4>
            </div>
            <div class="modal-body">
                <p>Уверены, что желаете удалить игрока?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn rm btn-danger">Удалить</button>
            </div>
        </div>
    </div>
</div>


<div class="container-fluid" id="gametop">
<div class="row-fluid"">
<h2>Наши в топе</h2>
<hr />
</div>

<div class="row-fluid">
    <form method="GET">
        <div class="col-my-1">
            <input type="text" name="month" value="<?=date('F Y',$month)?>" placeholder="Месяц" class="form-control datepick" />
        </div>
    </form>
</div>

<button class="btn btn-success add-button"><span class="glyphicon glyphicon-plus"></span></button>


    <div class="row-fluid players">&nbsp;</div>


<div id="player-template" style="display:none">
    <form method="post">
        <input type="hidden" name="Id" value="">
        <input type="hidden" name="Month" value="<?=$month?>">

    <div class="form-group">
        <div class="form-inline">
            <div class="col-md-1">
                <img src="">
                <br>
                <span class="name"></span>
            </div>
            <div class="col-md-11">

                    <div class="row-fluid">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-user"></i></span>
                            <input type="text" class="form-control" name="PlayerId" placeholder="Игрок" value="">
                        </div>

                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-star"></i></span>
                            <input type="text" class="form-control" name="Rating" placeholder="Рейтинг" value="">
                        </div>

                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-gamepad"></i></span>
                            <select class="form-control" name="GameId">
                                <? foreach($onlineGames as $gid => $title):?>
                                    <option value="<?=$gid?>"><?=$title?></option>
                                <? endforeach; ?>
                            </select>
                        </div>

                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-money"></i></span>
                            <select class="form-control" name="Currency">
                                    <option value="<?=\LotterySettings::CURRENCY_MONEY?>">Деньги</option>
                                    <option value="<?=\LotterySettings::CURRENCY_POINT?>">Баллы</option>
                            </select>
                        </div>
                        <button class="btn btn-danger remove-button"><span class="glyphicon glyphicon-remove"></span> Удалить</button>

                        <br>

                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-sliders"></i></span>
                            <select class="form-control" name="Increment">
                                <option value="">Вероятность</option>
                                <? for ($i = 0; $i <=100; $i+=10) :?>
                                    <option value="<?=$i?>"><?=$i?>%</option>
                                <? endfor; ?>
                            </select>
                        </div>

                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                            <input type="text" class="form-control" name="Period" placeholder="Периодичность" value="">
                        </div>

                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-play"></i></span>
                            <input type="text" class="form-control" name="Start" placeholder="С" value="">
                        </div>

                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-stop"></i></span>
                            <input type="text" class="form-control" name="End" placeholder="До" value="">
                        </div>
                <button class="btn btn-success save-button"><span class="glyphicon glyphicon-floppy-disk"></span> Сохранить</button>
            </div>
        </div>
    </div>
    </form>
</div>


<script>
    var gameTop = eval(<?=json_encode($gameTop)?>),
        template = $('#player-template'),
        timezones = <?= $timezones?>,
        xhr;


    $('[name="month"]').datepicker({
        format: 'MM yyyy',
        startView: "months",
        minViewMode: "months",
        changeMonth: true,
        changeYear: true,
        autoclose: true,
        showTimePicker: false,
        pickTime: false
    }).on('changeDate', function(e){
        $(this).parents('form').submit();
    });

    $(gameTop).each(function(id, player) {

        form = template.clone();
        $('img',                form).attr('src','../filestorage/avatars/'+Math.ceil(player.PlayerId/100)+'/'+player.Avatar);
        $('.name',              form).text(player.Nicname);
        $('[name="Id"]',        form).val(player.Id);
        $('[name="PlayerId"]',  form).val(player.PlayerId);
        $('[name="Period"]',    form).val(player.Period);
        $('[name="GameId"]',    form).val(player.GameId).change();
        $('[name="Currency"]',  form).val(player.Currency).change();
        $('[name="Rating"]',    form).val(player.Rating);
        $('[name="Increment"]', form).val(player.Increment).change();
        $('[name="Start"]',     form).val(player.Start);
        $('[name="End"]',       form).val(player.End);

        $('.players').append($(form).show());

    });

    $('.add-button').on('click', function() {
        form = template.clone();
        $('.players').append($(form).find('.save-button').addClass('btn-warning').parents('form').show());

    });

    $(document).on('input', 'input, select', function() {

        $(this).parents('form').find('.save-button').addClass('btn-warning');

    });

    $(document).on('input', '[name="PlayerId"]', function() {

        form = $(this).parents('.form-group');

        $('img',                form).attr('src','../tpl/img/preloader.gif');
        $('.name',              form).text('...');

        if(xhr && xhr.readystate != 4)
            xhr.abort();

        xhr = $.ajax({
            url: "/private/gametop/getPlayer/"+$(this).val(),
            method: 'GET',
            async: true,
            dataType: 'json',
            success: function(data) {
                player = data.data;
                $('img', form).attr(
                    'src',
                    player.Nicname
                        ? (player.Avatar?'../filestorage/users/50/'+player.Avatar:'../tpl/img/default.jpg')
                        : ''
                );

                $('.name', form).text(player.Nicname?player.Nicname:data.message);

                if(timezones) {
                    if((player.Utc = parseInt(player.Utc))) {
                        $('input[name="Start"]', form).val((24 / timezones * (player.Utc) - 24 / timezones) + ':00');
                        $('input[name="End"]', form).val((24 / timezones * player.Utc - 1) + ':59');
                    } else {
                        $('input[name="End"], input[name="Start"]', form).val('');
                    }
                }
            },
            error: function() {

            }
        });

        xhr;

    });

    $(document).on('click', ".save-button", function() {

        var form = $(this).parents('form');
        var button = $(this);
        button.append($(' <i class="fa fa-spinner fa-pulse"></i> ').css('margin-left','5px'));

        $.ajax({
            url: "/private/gametop/",
            method: 'POST',
            data: form.serialize(),
            async: true,
            dataType: 'json',
            success: function(data) {
                button.find('.fa').last().remove();
                if (data.status == 1) {
                    $('[name="Id"]',form).val(data.data.Id);
                    button.append($(' <i class="glyphicon glyphicon-ok"></i>').css('margin-left','5px')).removeClass('btn-warning');
                    window.setTimeout(function () {button.removeClass('btn-warning').find('i').last().fadeOut(200);},1000);
                } else {
                    button.append($(' <i class="glyphicon glyphicon-exclamation-sign"></i>')).addClass('btn-danger');
                    window.setTimeout(function () {button.removeClass('btn-danger').find('i').last().fadeOut(200);},1000);
                    alert(data.message);
                }
            },
            error: function() {
                button.find('.fa').last().remove();
                button.append($(' <i class="glyphicon glyphicon-exclamation-sign"></i>')).addClass('btn-danger');
                window.setTimeout(function () {button.removeClass('btn-danger').find('i').last().fadeOut(200);},1000);
                alert('Unexpected server error');
            }
        });
        return false;

    });

    $(document).on('click', ".remove-button", function() {

        var form = $(this).parents('form');
        var modal = $("#deleteModal");
        var button = $(this);

        modal.modal();
        modal.find('.cls').off('click').on('click', function() {
            modal.modal('hide');
        });

        modal.find('.rm').off('click').on('click', function() {

            if(id = $('[name="Id"]', form).val()){

                $.ajax({
                    url: "/private/gametop/delete/"+id,
                    method: 'GET',
                    async: true,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == 1) {

                            form.remove();

                        } else {
                            button.prepend($('<i class="glyphicon glyphicon-exclamation-sign"></i>')).addClass('btn-danger');
                            window.setTimeout(function () {button.removeClass('btn-danger').find('i').fadeOut(200);},1000);
                            alert(data.message);
                        }
                    },
                    error: function() {
                        button.prepend($('<i class="glyphicon glyphicon-exclamation-sign"></i>')).addClass('btn-danger');
                        window.setTimeout(function () {button.removeClass('btn-danger').find('i').fadeOut(200);},1000);
                        alert('Unexpected server error');
                    }
                });

            } else {

                form.remove();
            }


            modal.modal('hide');

        });

        return false;

    });


</script>
