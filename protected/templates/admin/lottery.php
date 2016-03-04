<div class="modal fade" id="jackpotModal" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Удаление текста</h4>
            </div>
            <div class="modal-body">
                <p>Уверены, что желаете включить/отключить розыгрыш джекпота?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-success">Включить/отключить</button>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row-fluid">
        <h2>Настройки розыгрышей</h2>
        <hr />
    </div>
    <div class="row-fluid">&nbsp;</div>
    <!-- fst column -->
    <div class="col-md-6 col-md-offset-1">
        <h6>Настройка призов<span class="pull-right  glyphicon glyphicon-question-sign" data-toggle="tooltip" data-placement="top" title="Установите флажок, для того чтобы указать что приз денежный" style="color:#428BCA;cursor:help;">&nbsp;</span></h6>
        <? for ($i = 1; $i <= 6; ++$i) { ?>
        <div class="row">
            <div class="col-md-3">
                <? for ($j = 1; $j <= $i; ++$j) { ?>
                    <span style="color: #428BCA;font-size:21pt;">&bull;</span>
                <? } ?>
                <? for ($z = $j; $z <= 6; ++$z) { ?>
                    <span style="color: #CCCCCC;font-size:21pt;">&bull;</span>
                <? } ?>
            </div>
            <div class="col-md-3">
                <div class="input-group pull-right" data-balls="<?=$i?>">
                    <input type="text" class="form-control input-md" value="<?=@$settings->getPrizes('UA')[$i]['sum']?>">
                    <span class="input-group-addon">
                        <input type="checkbox" <?=(@$settings->getPrizes('UA')[$i]['currency'] == LotterySettings::CURRENCY_MONEY || $i > 3 ? 'checked' : '')?>>
                    </span>
                </div>
            </div>
            <div class="col-md-3" style="background-color: #E8CF6B;">
                <div class="input-group pull-right" data-balls-gold="<?=$i?>">
                    <input type="text" class="form-control input-md" value="<?=@$settings->getGoldPrizes('UA')[$i]['sum']?>" style="background-color: #E8CF6B;">
                    <span class="input-group-addon"  style="background-color: #E8CF6B;">
                        <input type="checkbox" <?=(@$settings->getGoldPrizes('UA')[$i]['currency'] == LotterySettings::CURRENCY_MONEY || $i > 3 ? 'checked' : '')?>>
                    </span>
                </div>
            </div>
            <div class="col-md-1">
                <div class="input-group pull-right" incr-from="<?=$i?>">
                    <input type="text" class="form-control input-md" value="<?=@$settings->getGameIncrements()[$i]['from']?>">
                </div>
            </div>
            <div class="col-md-1">
                <div class="input-group pull-right" incr-to="<?=$i?>">
                    <input type="text" class="form-control input-md" value="<?=@$settings->getGameIncrements()[$i]['to']?>">
                </div>
            </div>

        </div>
        <? } ?>

        <div class="row">
            <div class="col-md-4 pull-right">
                Цена Gold-билета
                <input type="text" class="form-control input-md" value="<?=@$goldPrice['UA']?>" name="goldPrice">
            </div>
            <div class="row">
                <div class="col-md-6 pull-left">
                    Количество сыграных игр для 4го билета
                    <input type="text" class="form-control input-md" value="<?=(int)\SettingsModel::instance()->getSettings('ticketConditions')->getValue('CONDITION_4_TICKET')?>" name="condition4">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 pull-left">
                    Количество рефералов для 5го билета
                    <input type="text" class="form-control input-md" value="<?=(int)\SettingsModel::instance()->getSettings('ticketConditions')->getValue('CONDITION_5_TICKET')?>" name="condition5">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 pull-left">
                    Количество рефералов для 6го билета
                    <input type="text" class="form-control input-md" value="<?=(int)\SettingsModel::instance()->getSettings('ticketConditions')->getValue('CONDITION_6_TICKET')?>" name="condition6">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 pull-right">
                Последний id "старых" пользователей
                <input type="text" class="form-control input-md" value="<?=(int)\SettingsModel::instance()->getSettings('ticketConditions')->getValue('LASTID_OLD_USERS')?>" name="lastIdOldUsers">
            </div>
        </div>

        <div class="row">

                <!--button type="button" class="btn btn-md btn-success pull-right add-country"><span class="glyphicon glyphicon-plus"></span></button-->

                <div class="btn-group">
                    <? foreach ($supportedCountries as $country) { ?>
                        <button type="button" class="btn btn-md country btn-default<?=($country == 'UA' ? ' active' : '') ?>" data-cc="<?=$country?>"><?=$country?></button>
                    <? } ?>
                </div>

        </div>

    </div>
    
    <!-- scnd column -->
    <div class="col-md-4" id="lotteries">
        <? $i = 1; ?>
        <? $cnt = count($settings->getLotterySettings()); ?>
        <? foreach ($settings->getLotterySettings() as $time) { ?>
            <div class="form-group">
                <? if ($i == 1) { ?>
                    <label class="control-label">Количество и время розыгрышей</label>
                <? } else { ?>
                    <label class="control-label">&nbsp;</label>
                <? } ?>
                <div class="form-inline">
                    <div class="col-md-1">
                        <h4>#<?=$i?></h4>
                    </div>
                    <div class="col-md-11 flex">
                        <div class="input-group col-md-4">
                            <span class="input-group-addon">@</span>
                            <input type="text" class="form-control col-md-3" name="StartTime" placeholder="Часы:Минуты" value="<?=date('H:i', $time['StartTime'])?>">
                        </div>
                        <div class="input-group col-md-2">
                            <input type="text" class="col-md-1 form-control " name="Tries" placeholder="Переборы" value="<?=$time['Tries'];?>">
                        </div>
                        <div class="input-group col-md-2">
                            <input type="text" class="col-md-1 form-control" name="Balls" placeholder="Шары" value="<?=$time['Balls'];?>">
                        </div>
                        <div class="input-group col-md-2">
                            <button class="btn btn-default simulate-button">Simulate</button>
                        </div>
                        <button class="btn btn-success add-button" <?=($i < $cnt ? 'style="display:none"' : '')?>><span class="glyphicon glyphicon-plus"></span></button>
                        <? if ($i > 1) { ?>
                            <button class="btn btn-danger remove-button"><span class="glyphicon glyphicon-remove"></span></button>
                        <? } ?>
                    </div>
                </div>
            </div>
        <? $i++;
        } ?>
    </div>


</div>
<div class="row-fluid">&nbsp;</div>

<div class="row-fluid">
    <div class="col-md-3 col-md-offset-1">
        <button class="btn btn-danger force-modal-button"> Принудительный розыгрыш</button>
    </div>
    <div class="col-md-3 col-md-offset-5">
        <button class="btn btn-success save-button"> Cохранить</button>
    </div>
</div>


<div id="lottery-time-template" style="display:none">
    <div class="form-group">
        <label class="control-label">&nbsp;</label>
        <div class="form-inline">
            <div class="col-md-1">
                <h4>#{num}</h4>
            </div>
            <div class="col-md-11 flex">
                <div class="input-group">
                    <span class="input-group-addon">@</span>
                    <input type="text" class="form-control col-md-3" name="StartTime" placeholder="Часы:Минуты">
                </div>
                <input type="text" class="col-md-1 form-control" name="Tries" placeholder="Переборы" value="">
                <input type="text" class="col-md-1 form-control" name="Balls" placeholder="Шары" value="">
                <button class="btn btn-default simulate-button">Simulate</button>
                <button class="btn btn-success add-button"><span class="glyphicon glyphicon-plus"></span></button>
                <button class="btn btn-danger remove-button"><span class="glyphicon glyphicon-remove"></span></button>
            </div>
        </div>
    </div>
</div>

<!--div class="add-country-template" style="display:none">
    <div class="row-fluid">
        <form class="form form-inline pull-right">
            <div class="form-group">
                <label class="sr-only"></label>
                <input class="input input-md form-control col-md-1" name="cc" placeholder="Код страны" style="width:90px;">
            </div>
            <div class="form-group">
                <label class="sr-only"></label>
                <input class="input input-md form-control col-md-1" name="title" placeholder="Название" style="width:90px;">
            </div>
            <div class="form-group">
                <label class="sr-only"></label>
                <select class="input input-md form-control col-md-1" name="lang" placeholder="Язык">
                    <? foreach ($supportedCountries as $lang) { ?>
                        <option value="<?=$lang?>"><?=$lang?></option>
                    <? } ?>
                </select>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-md btn-success"><span class="glyphicon glyphicon-ok"></span></button>
                <button type="button" class="btn btn-md btn-danger" style="margin-right:5px;"><span class="glyphicon glyphicon-remove"></span></button>
            </div>
        </form>
    </div>
</div-->


<div class="modal fade" id="simulation" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Симуляция розыгрыша</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <pre></pre>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="force" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Принудительное проведение розыгрыша</h4>
            </div>
            <div class="modal-body">
                <div class="row-fluid">
                    <p class="bg-warning">Внимание! Запустив розыгрыш принудительно Вы разыграете все заполненные <span class="label label-danger"></span> билетов. <br>Принудительное проведение розыгрыша сейчас не отменит проведение розыгрыша согласно расписанию.</p>
                    <p class="bg-danger file-locked">ВАЖНО! Обнаружен файл блокировки, это означает, что прямо сейчас происходит проведение розыгрыша, Вы не можете запустить еще один.</p>
                    <button type="button" class="btn btn-danger force-lottery-button" >Я понимаю всю ответственность, запустить принудительно всё равно</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть окно</button>
            </div>
        </div>
    </div>
</div>

<? 
$ajaxedSettings = array(
    'lotteryTotal' => array(),
    'isJackpot'    => array(),
    'prizes'       => array(),
    'goldPrizes'   => array(),
    'lotteries'    => array(),
    'goldPrice'    => array(),
); 

$ajaxedSettings['countryCoefficients'] = (object)$ajaxedSettings['countryCoefficients'];
$ajaxedSettings['countryRates'] = (object)$ajaxedSettings['countryRates'];

foreach ($goldPrice as $country => $price) {
    $ajaxedSettings['goldPrice'][$country] = $price;
}
foreach ($settings->getPrizes() as $country => $prize) {
    $ajaxedSettings['prizes'][$country] = $prize;
}
foreach ($settings->getGoldPrizes() as $country => $prize) {
    $ajaxedSettings['goldPrizes'][$country] = $prize;
}
$ajaxedSettings['goldPrice']  = (object)$ajaxedSettings['goldPrice'];
$ajaxedSettings['prizes']     = (object)$ajaxedSettings['prizes'];
$ajaxedSettings['goldPrizes'] = (object)$ajaxedSettings['goldPrizes'];

?>
<script>
    var gameSettings = eval(<?=json_encode($ajaxedSettings)?>);
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
        $('.add-button').on('click', addLotteryInput);
        $('.remove-button').on('click', removeLotteryInput);
    });


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

    $(document).on('click', '.simulate-button', function() {

        button=$(this);
        var simulation={Balls: button.prev().val(), Tries: button.prev().prev().val()};


        $.ajax({
            url: "/private/lottery/simulation",
            method: 'POST',
            data: simulation,
            async: true,
            success: function(data) {
                console.log(data);
                $('#simulation').modal().find('.modal-body .row pre').html(data);

            },
            error: function() {
                button.find('span').remove();
                button.prepend($('<span class="glyphicon glyphicon-remove"></span>'));
                button.removeClass('btn-success').addClass('btn-danger');
                button.parent().prepend($('<alert class="alert alert-danger">Unexpected server error</alert>'))
            }
        });
    });

    $(document).on('click', '.force-lottery-button', function() {

        button=$(this);
        $('#force').find('.modal-body .row-fluid').hide().parent().append('<pre>розыгрыш запущен, результат будет выведен здесь, ожидайте...</pre>');

        $.ajax({
            url: "/private/lottery/force",
            method: 'POST',
            async: true,
            dataType: 'json',
            success: function(data) {
                console.log(data);
                $('#force').find('.modal-body pre:visible').html(data.data);

            },
            error: function(xhr, status, error) {
                $('#force').find('.modal-body pre:visible').html(xhr.responseText);
            }
        });
    });

    $(document).on('click', '.force-modal-button', function() {

        button=$(this);

        $.ajax({
            url: "/private/lottery/checkLock",
            method: 'POST',
            async: true,
            dataType: 'json',
            success: function(data) {
                console.log(data);
                $('#force').find('.modal-body>pre').remove();
                $('#force').modal().find('.modal-body .row-fluid').show()
                    .find('.label').text(data.data.tickets)
                    .parents('.row-fluid').children().hide()
                    .parents('.row-fluid').find(!data.data.lock?':not(.file-locked)':'.file-locked').show();

            },
            error: function() {
                button.find('span').remove();
                button.prepend($('<span class="glyphicon glyphicon-remove"></span>'));
                button.removeClass('btn-success').addClass('btn-danger');
                button.parent().prepend($('<alert class="alert alert-danger">Unexpected server error</alert>'))
            }
        });
    });

    $(".save-button").on('click', function() {
        var country = $('.country.active').data('cc');

        gameSettings.lotteryTotal = $('input[name="sum"]').val();
        gameSettings.isJackpot = $('.jackpot').hasClass('btn-success') ? 1 : 0;
        gameSettings.countryCoefficients[country] = $('input[name="coof"]').val();
        gameSettings.countryRates[country] = $('input[name="rate"]').val();
        gameSettings.goldPrice[country] = $('input[name="goldPrice"]').val();

        gameSettings.condition4 = $('input[name="condition4"]').val();
        gameSettings.condition5 = $('input[name="condition5"]').val();
        gameSettings.condition6 = $('input[name="condition6"]').val();
        gameSettings.lastIdOldUsers = $('input[name="lastIdOldUsers"]').val();

        gameSettings.prizes[country] = {};
        $([1,2,3,4,5,6]).each(function(id, ballsCount) {
            var won = $('[data-balls="' + ballsCount + '"]').find('input[type="text"]').val();
            var currency = $('[data-balls="' + ballsCount + '"]').find('input[type="checkbox"]:checked').length ? 'money' : 'point';

            gameSettings.prizes[country][ballsCount] = {
                'sum' : won,
                'currency' : currency
            }
        });

        gameSettings.goldPrizes[country] = {};
        $([1,2,3,4,5,6]).each(function(id, ballsCount) {
            var won = $('[data-balls-gold="' + ballsCount + '"]').find('input[type="text"]').val();
            var currency = $('[data-balls-gold="' + ballsCount + '"]').find('input[type="checkbox"]:checked').length ? 'money' : 'point';

            gameSettings.goldPrizes[country][ballsCount] = {
                'sum' : won,
                'currency' : currency
            }
        });

        gameSettings.increments = {};
        $([1,2,3,4,5,6]).each(function(id, ballsCount) {
            var from = $('[incr-from="' + ballsCount + '"]').find('input[type="text"]').val();
            var to   = $('[incr-to="' + ballsCount + '"]').find('input[type="text"]').val();

            gameSettings.increments[ballsCount] = {
                'from' : from,
                'to'   : to
            }
        });

        gameSettings.lotteries = [];
        //$('#lotteries').find("input").each(function(id, input) {
        $('#lotteries').find(".form-inline").each(function(id, input) {
            //if ($(input).val()) {
            //    gameSettings.lotteries.push($(input).val());
            //}
            var game={
                StartTime:$(input).find('input[name="StartTime"]').val(),
                Tries:$(input).find('input[name="Tries"]').val(),
                Balls:$(input).find('input[name="Balls"]').val()
            };
            gameSettings.lotteries.push(game);
        });


        var button = $(this);
        button.find('span').remove();
        button.parent().find('alert').remove();

        $.ajax({
            url: "/private/lottery/",
            method: 'POST',
            data: gameSettings,
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

    $('.add-country').on('click', function() {
        var button = $(this);
        button.hide();

        var template = $('.add-country-template').html();
        button.parent().append($(template));

        button.parent().find('form').find('.btn-danger').on('click', function() {            
            $(this).parents('form').remove();

            button.show();
        });

        button.parent().find('form').find('.btn-success').on('click', function() {            
            var countryData = {}
            var form = $(this).parents('form');

            countryData.cc = form.find('input[name="cc"]').val();
            countryData.title = form.find('input[name="title"]').val();
            countryData.lang = form.find('select[name="lang"]').val();

            $.ajax({
                url: "/private/lottery/addcountry",
                method: 'POST',
                data: countryData,
                async: true,
                dataType: 'json',
                success: function(data) {
                    if (data.status == 1) {
                        form.remove();
                        button.parent().find('.btn-group').append($('<button type="button" class="btn btn-md btn-default" data-cc="' + countryData.cc + '">' + countryData.title + '</button>'));

                        button.show();        
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

    $('[data-cc]').on('click', function() {
        var prevCountry = $('[data-cc].active').data('cc');
        var currentCountry = $(this).data('cc');

        $('[data-cc]').removeClass('active');
        $(this).addClass('active');

        // save pervious country data
        gameSettings.countryCoefficients[prevCountry] = $('input[name="coof"]').val();
        gameSettings.countryRates[prevCountry] = $('input[name="rate"]').val();
        gameSettings.goldPrice[prevCountry] = $('input[name="goldPrice"]').val();

        gameSettings.prizes[prevCountry] = {};
        $([1,2,3,4,5,6]).each(function(id, ballsCount) {
            var won = $('[data-balls="' + ballsCount + '"]').find('input[type="text"]').val();
            var currency = $('[data-balls="' + ballsCount + '"]').find('input[type="checkbox"]:checked').length ? '<?=LotterySettings::CURRENCY_MONEY?>' : '<?=LotterySettings::CURRENCY_POINT?>';

            gameSettings.prizes[prevCountry][ballsCount] = {
                'sum' : won,
                'currency' : currency
            }
        });
        gameSettings.goldPrizes[prevCountry] = {};
        $([1,2,3,4,5,6]).each(function(id, ballsCount) {
            var won = $('[data-balls-gold="' + ballsCount + '"]').find('input[type="text"]').val();
            var currency = $('[data-balls-gold="' + ballsCount + '"]').find('input[type="checkbox"]:checked').length ? '<?=LotterySettings::CURRENCY_MONEY?>' : '<?=LotterySettings::CURRENCY_POINT?>';

            gameSettings.goldPrizes[prevCountry][ballsCount] = {
                'sum' : won,
                'currency' : currency
            }
        });

            // rebuild form values to current country data
        $('input[name="coof"]').val(gameSettings.countryCoefficients[currentCountry] || "0");
        $('input[name="rate"]').val(gameSettings.countryRates[currentCountry] || "0");
        $('input[name="goldPrice"]').val(gameSettings.goldPrice[currentCountry] || "0");

        $([1,2,3,4,5,6]).each(function(id, ballsCount) {
            if (gameSettings.prizes[currentCountry] != undefined) {
                $('[data-balls="' + ballsCount + '"]').find('input[type="text"]').val(gameSettings.prizes[currentCountry][ballsCount].sum);
                $('[data-balls="' + ballsCount + '"]').find('input[type="checkbox"]').prop('checked', gameSettings.prizes[currentCountry][ballsCount].currency == '<?=LotterySettings::CURRENCY_MONEY?>');
            } else {
                $('[data-balls="' + ballsCount + '"]').find('input[type="text"]').val("0");
                $('[data-balls="' + ballsCount + '"]').find('input[type="checkbox"]').prop('checked', ballsCount > 3 ? true : false);
            }
            if (gameSettings.goldPrizes[currentCountry] != undefined) {
                $('[data-balls-gold="' + ballsCount + '"]').find('input[type="text"]').val(gameSettings.goldPrizes[currentCountry][ballsCount].sum);
                $('[data-balls-gold="' + ballsCount + '"]').find('input[type="checkbox"]').prop('checked', gameSettings.goldPrizes[currentCountry][ballsCount].currency == '<?=LotterySettings::CURRENCY_MONEY?>');
            } else {
                $('[data-balls-gold="' + ballsCount + '"]').find('input[type="text"]').val("0");
                $('[data-balls-gold="' + ballsCount + '"]').find('input[type="checkbox"]').prop('checked', ballsCount > 3 ? true : false);
            }
        });  
    });

    $('.jackpot').on('click', function() {
        $('#jackpotModal').modal();
        $('#jackpotModal').find('.btn-success').off('click').on('click', function(){
            if ($('.jackpot').hasClass('btn-success')) {
                $('.jackpot').removeClass('btn-success');
                $('.jackpot').addClass('btn-default');
            } else {
                $('.jackpot').removeClass('btn-default');
                $('.jackpot').addClass('btn-success');
            }
            $('#jackpotModal').modal('hide');
        });
    })
</script>
