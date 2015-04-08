<link href="/theme/admin/lib/country.css" rel="stylesheet">
<div class="container-fluid">
    <div class="row-fluid">
        <h2>Страны
            <button type="button" class="btn btn-md btn-success add-country"><span class="glyphicon glyphicon-plus"></span> Добавить</button></h2>
        <hr />
    </div>
    <div class="row-fluid countries">

    </div>
</div>

<div class="add-country-template" style="display:none">
    <div class="row-fluid">
        <form class="form form-inline">

            <div class="input-group"><div class="flag"></div>
            </div>
            <input type="hidden" name="Id"  value="">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-globe fa-2x"></i></span>
                <select class="form-control c" name="Code">
                    <option></option>
                    <? if(is_array($availabledCountries))
                        foreach($availabledCountries as $country):?><option value="<?=$country['Country'];?>"><?=$country['Country'].' ('.$country['Count'].')';?></option><? endforeach; ?>
                </select>
            </div>

            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-language fa-2x"></i></span>
                <select class="form-control c" name="Lang">
                    <option></option>
                    <? if(is_array($langs))
                        foreach($langs as $lang):?><option value="<?=$lang->getCode();?>"><?=$lang->getTitle();?></option><? endforeach; ?>
                </select>
            </div>

            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-money fa-2x"></i></span>
                <select class="form-control c" name="Currency">
                    <option></option>
                    <? if(is_array($currencies))
                        foreach($currencies as $currency):?><option value="<?=$currency->getId();?>"><?=$currency->getCode();?></option><? endforeach; ?>
                </select>
            </div>

            <div class="input-group">
                <button type="button" class="btn btn-md btn-default save-country"><i class="fa fa-save"></i> Сохранить изменения</button>
            </div>
        </form>
    </div>
</div>


<script>
    <?
    $array=array();
    if(is_array($countries))
        foreach($countries as $country){
        $array[$country->getCode()]=array(
            'Id'=>$country->getId(),
            'Code'=>$country->getCode(),
            'Lang'=>$country->getLang(),
            'Currency'=>$country->getCurrency(),
        );
        }
?>

    countries = <?=json_encode($array)?>;
    $.each(countries, function(index, country) {
        var $template = $($('.add-country-template').html()).removeClass('add-country-template').appendTo('.countries');
        $('[name="Coefficient"]',$template).val(country['Coefficient']);
        $('[name="Rate"]',$template).val(country['Rate']);
        $('.flag',$template).addClass('flag-'+country['Code'].toLowerCase());
        $('[name="Id"]',$template).val(country['Id']);
        $('[name="Code"]',$template).val(country['Code']);
        $('[name="Currency"]',$template).val(country['Currency']);
        $('[name="Lang"]',$template).val(country['Lang']).change();

    });



    $('.add-country').on('click', function() {
        var template = $('.add-country-template').html();
        $('.countries').append($(template));
    });

    $(document).on('input','input, select', function() {
        $(this).parents('.form-inline').find('.btn').addClass('btn-success');

    });

    $(document).on('input','select[name="Code"]', function() {
        $(this).parents('.form-inline').find('.flag').removeClass().addClass('flag flag-'+$(this).val().toLowerCase());

    });

    $(document).on('click','.save-country', function() {
        var form = $(this).parents('form');
        var button =$(this);

        button.find('.fa').hide().parent().prepend($('<i class="fa fa-spinner fa-pulse"></i>'));


        $.ajax({
            url: "/private/countries",
            method: 'POST',
            data: form.serialize(),
            async: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    button.find('.fa').first().remove();
                    button.removeClass('btn-danger btn-success').addClass('btn-default').prepend($('<i class="fa fa-check"></i>'));
                    button.find('.fa').fadeOut(500);
                    window.setTimeout(function(){
                        button.find('.fa').show().filter(':not(.fa-save)').remove();
                    },500);

                    $('[name="Id"]',form).val(data.data.Id);
                    form.removeClass('label-danger');

                } else {
                    button.find('.fa').first().remove();
                    button.removeClass('btn-success').addClass('btn-danger');
                    button.find('.fa').show().filter(':not(.fa-save)').remove();
                    alert(data.message);
                }
            },
            error: function(data) {
                button.find('.fa').first().remove();
                button.removeClass('btn-success').addClass('btn-danger');
                button.find('.fa').show().filter(':not(.fa-save)').remove();
                alert('Unexpected server error');
                console.log(data.responseText);
            }
        });

    });


</script>
