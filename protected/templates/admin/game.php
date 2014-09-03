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
                <input type="text" name="sum" value="0" placeholder="Сумма розыгрыша" class="form-control" />
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
                        <input type="checkbox" <?=($i > 3 ? 'checked' : '')?>>
                    </span>
                </div>
            </div>
        </div>  
        <? } ?>
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
<div class="row-fluid">
    <div class="col-md-6">
        <button type="button" class="btn btn-sm btn-success pull-right add-country"><span class="glyphicon glyphicon-plus"></span></button>
        <span class="pull-right">&nbsp;</span>
        <div class="btn-group pull-right">
            <? foreach ($supportedCountries as $country) { ?>
                <button type="button" class="btn btn-sm btn-default<?=($country->getCountryCode() == 'UA' ? ' active' : '') ?>" data-cc="<?=$country->getCountryCode()?>"><?=$country->getTitle()?></button>
            <? } ?>
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

<div class="add-country-template" style="display:none">
    <form class="form form-inline pull-right">
        <div class="form-group">
            <label class="sr-only"></label>
            <input class="input input-sm form-control col-md-1" name="cc" placeholder="Код страны" style="width:90px;">
        </div>
        <div class="form-group">
            <label class="sr-only"></label>
            <input class="input input-sm form-control col-md-1" name="title" placeholder="Название" style="width:90px;">
        </div>
        <div class="form-group">
            <label class="sr-only"></label>
            <select class="input input-sm form-control col-md-1" name="lang" placeholder="Язык">
                <? foreach (Config::instance()->langs as $lang) { ?>
                    <option value="<?=$lang?>"><?=$lang?></option>
                <? } ?>
            </select>
        </div>
        <div class="form-group">
            <button type="button" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-ok"></span></button>
            <button type="button" class="btn btn-sm btn-danger" style="margin-right:5px;"><span class="glyphicon glyphicon-remove"></span></button>
        </div>
    </form>
</div>
<script>
    var gameSettings = {
        lotteryTotal : {},
        isJackpot    : {},
        prizes       : {},
        lotteries    : [],
    };

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
                url: "/private/game/addcountry",
                method: 'POST',
                data: countryData,
                async: true,
                dataType: 'json',
                success: function(data) {
                    if (data.status == 1) {
                        form.remove();
                        button.parent().find('.btn-group').append($('<button type="button" class="btn btn-sm btn-default" data-cc="' + countryData.cc + '">' + countryData.title + '</button>'));

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
        gameSettings.lotteryTotal[prevCountry] = $('input[name="sum"]').val();
        gameSettings.isJackpot[prevCountry] = $('input[name="jackpot"]:checked').length;

        gameSettings.prizes[prevCountry] = {};
        $([1,2,3,4,5,6]).each(function(id, ballsCount) {
            var won = $('[data-balls="' + ballsCount + '"]').find('input[type="text"]').val();
            var currency = $('[data-balls="' + ballsCount + '"]').find('input[type="checkbox"]:checked').length ? 'money' : 'points';

            gameSettings.prizes[prevCountry][ballsCount] = {
                'summ' : won,
                'currency' : currency
            }
        });

        // rebuild form values to current country data
        $('input[name="sum"]').val(gameSettings.lotteryTotal[currentCountry] || "0");
        $('input[name="jackpot"]').prop('checked', gameSettings.isJackpot[currentCountry] ? true : false);
        
        $([1,2,3,4,5,6]).each(function(id, ballsCount) {
            if (gameSettings.prizes[currentCountry] != undefined) {
                $('[data-balls="' + ballsCount + '"]').find('input[type="text"]').val(gameSettings.prizes[currentCountry][ballsCount].summ);
                $('[data-balls="' + ballsCount + '"]').find('input[type="checkbox"]').prop('checked', gameSettings.prizes[currentCountry][ballsCount].currency == 'money');
            } else {
                $('[data-balls="' + ballsCount + '"]').find('input[type="text"]').val("0");
                $('[data-balls="' + ballsCount + '"]').find('input[type="checkbox"]').prop('checked', false);
            }
        });  

        console.log(gameSettings);      
    });
</script>
