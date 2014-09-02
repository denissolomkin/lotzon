<div class="container-fluid">
    <div class="row-fluid">
        <h2>Настройки розыгрыша</h2>
        <hr />
    </div>
    <div class="row-fluid">&nbsp;</div>
    <!-- fst column -->
    <div class="col-md-4 col-md-offset-2">
        <div class="form-group">
            <label class="control-label" for="sum">Cумма розыгрыша</label>
            <div class="input-group">
                <input type="text" name="sum" value="10000" placeholder="Сумма розыгрыша" class="form-control" />
                <span class="input-group-addon">
                    <input type="checkbox" name="jackpot" value="1" class="form-control" data-toggle="tooltip" data-placement="auto" title="JackPot!" />
                </span>
            </div>
        </div>
        <h5><strong>Настройка призов</strong><span class="pull-right  glyphicon glyphicon-question-sign" data-toggle="tooltip" data-placement="top" title="Установите флажок, для того чтобы указать что приз денежный" style="color:#428BCA;cursor:help;">&nbsp;</span></h5>
        <? for ($i = 1; $i <= 6; ++$i) { ?>
        <div class="row">
            <div class="col-md-4">
                <? for ($j = 1; $j <= $i; ++$j) { ?>
                    <span style="color: #428BCA;font-size:20pt;">&bull;</span>
                <? } ?>
                <? for ($z = $j; $z <= 6; ++$z) { ?>
                    <span style="color: #CCCCCC;font-size:20pt;">&bull;</span>
                <? } ?>
            </div>
            <div class="col-md-8">
                <div class="input-group pull-right" data-balls="<?=$i?>">
                    <input type="text" class="form-control input-sm" value="0"> 
                    <span class="input-group-addon">
                        <input type="checkbox">
                    </span>
                </div>
            </div>
        </div>  
        <? } ?>
        <div class="row-fluid">&nbsp;</div>
    </div>

    <!-- scnd column -->
    <div class="col-md-4" id="lotteries">
        <div class="form-group">
            <label class="control-label">Количество и время розыгрышей</label>
            <div class="form-inline">
                <div class="col-md-1">
                    <h4>#1</h4>
                </div>
                <div class="col-md-11">
                    <div class="input-group">
                        <span class="input-group-addon">@</span>
                        <input type="text" class="form-control col-md-3" placeholder="Часы:Минуты">
                    </div>
                    <button class="btn btn-success add-button"><span class="glyphicon glyphicon-plus"></span></button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row-fluid">&nbsp;</div>
<div class="col-md-4 col-md-offset-8">
    <button class="btn btn-success save-button"> Cохранить</button>
</div>

<div id="lottery-time-template" style="display:none">
    <div class="form-group">
        <label class="control-label">&nbsp;</label>
        <div class="form-inline">
            <div class="col-md-1">
                <h4>#{num}</h4>
            </div>
            <div class="col-md-11">
                <div class="input-group">
                    <span class="input-group-addon">@</span>
                    <input type="text" class="form-control col-md-3" placeholder="Часы:Минуты">
                </div>
                <button class="btn btn-success add-button"><span class="glyphicon glyphicon-plus"></span></button>
                <button class="btn btn-danger remove-button"><span class="glyphicon glyphicon-remove"></span></button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });

    $('.add-button').on('click', addLotteryInput);

    function addLotteryInput() {
        var template = $('#lottery-time-template').html();

        var currentNum = $(this).parents('.form-group').parent().find('.form-group').length + 1;
        template = template.replace("{num}", currentNum);

        $(this).parents('.form-group').parent().append($(template));

        $(this).hide();
        $('.add-button').off('click').on('click', addLotteryInput);        
        $('.remove-button').off('click').on('click', removeLotteryInput);        
    }

    function removeLotteryInput() {
        var container = $(this).parents('.form-group').parent();
        var button = $(this);
        var currentPosition = parseInt($(this).parents('.form-group').find('h4').text().replace("#",""));

        var position = 0;
        container.find('.form-group').each(function() {
            position = parseInt($(this).find('h4').text().replace("#",""));
            if (position > currentPosition) {
                $(this).find('h4').text("#" + (position - 1));
            }
        })
        $(this).parents('.form-group').remove();

        container.find('.form-group').last().find('.add-button').show();        
    }

    $(".save-button").on('click', function() {
        var submitData = {}

        submitData.lotteryTotal = $('input[name="sum"]').val();
        submitData.isJackpot = $('input[name="jackpot"]:checked').length;

        submitData.prizes = [];
        $([1,2,3,4,5,6]).each(function(id, ballsCount) {
            var won = $('[data-balls="' + ballsCount + '"]').find('input[type="text"]').val();
            var currency = $('[data-balls="' + ballsCount + '"]').find('input[type="checkbox"]:checked').length ? 'money' : 'points';

            submitData.prizes[ballsCount] = {
                'summ' : won,
                'currency' : currency
            }
        });

        submitData.lotteries = [];
        $('#lotteries').find("input").each(function(id, input) {
            if ($(input).val()) {
                submitData.lotteries.push($(input).val());
            }
        });

        var button = $(this);
        button.remove('span');
        button.parent().find('alert').remove();

        $.ajax({
            url: "/private/game/",
            method: 'POST',

            async: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    button.parents('tr').fadeOut('300', function() {
                        $(this).remove();
                    });
                    button.prepend($('<span class="glyphicon glyphicon-ok"></span>')).removeClass('btn-danger').addClass('btn-success');
                } else {
                    button.find('span').remove();
                    button.prepend($('<span class="glyphicon glyphicon-remove"></span>'));
                    button.removeClass('btn-success').addClass('btn-danger');

                    button.parent().prepend($('<alert class="alert alert-danger">' + data.message + '</alert>'))
                }
                
            }, 
            error: function() {

                button.find('span').remove();
                button.prepend($('<span class="glyphicon glyphicon-remove"></span>'));
                button.removeClass('btn-success').addClass('btn-danger');

                button.parent().prepend($('<alert class="alert alert-danger">Unexpected server error</alert>'))
           }
        });
    });
</script>