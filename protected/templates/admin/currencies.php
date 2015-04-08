
<div class="container-fluid">
    <div class="row-fluid">
        <h2>Валюты
            <button type="button" class="btn btn-md btn-success add-currency"><span class="glyphicon glyphicon-plus"></span> Добавить</button></h2>
        <hr />
    </div>
    <div class="row-fluid countries">

    </div>
</div>

<div class="add-currency-template" style="display:none">
    <div class="row-fluid">
        <form class="form form-inline col-md-12">

            <input type="hidden" name="Id"  value="">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-money fa-2x"></i></span>
                <input class="form-control c" type="text" name="Code"  value="" placeholder="UAH">
            </div>

            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-money fa-2x"></i></span>
                <input class="form-control c" type="text" name="Title[iso]"  value="" placeholder="грн">
            </div>

            <div class="input-group">
                <input class="form-control c" type="text" name="Title[one]"  value="" placeholder="гривна">
            </div>

            <div class="input-group">
                <input class="form-control c" type="text" name="Title[few]"  value="" placeholder="гривни">
            </div>

            <div class="input-group">
                <input class="form-control c" type="text" name="Title[many]"  value="" placeholder="гривен">
            </div>

            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa- fa-2x"></i></span>
                <input class="form-control c" type="text" name="Coefficient"  value="" placeholder="Коэффициент">
            </div>

            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-diamond fa-2x"></i></span>
                <input class="form-control c" type="text" name="Rate"  value="" placeholder="Курс обмена">
            </div>


            <div class="input-group">
                <button type="button" class="btn btn-md btn-default save-currency"><i class="fa fa-save"></i> Сохранить изменения</button>
            </div>
        </form>
    </div>
</div>


<script>
    <?
    $array=array();
    if(is_array($currencies))
        foreach($currencies as $currency){
        $array[$currency->getCode()]=array(
            'Id'=>$currency->getId(),
            'Code'=>$currency->getCode(),
            'Title'=>$currency->getTitle(),
            'Coefficient'=>$currency->getCoefficient(),
            'Rate'=>$currency->getRate(),
        );
        }
?>

    countries = <?=json_encode($array)?>;
    $.each(countries, function(index, country) {
        var $template = $($('.add-currency-template').html()).removeClass('add-currency-template').prependTo('.countries');
        $('[name="Coefficient"]',$template).val(country['Coefficient']);
        $('[name="Rate"]',$template).val(country['Rate']);
        $('[name="Id"]',$template).val(country['Id']);
        $('[name="Code"]',$template).val(country['Code']);
        $.each(country['Title'], function(format, title) {
            $('[name="Title['+format+']"]',$template).val(title);
        });
    });


    $('.add-currency').on('click', function() {
        var template = $('.add-currency-template').html();
        $('.countries').append($(template));
    });

    $(document).on('input','input, select', function() {
        $(this).parents('.form-inline').find('.btn').addClass('btn-success');

    });
    $(document).on('click','.save-currency', function() {
        var form = $(this).parents('form');
        var button =$(this);

        button.find('.fa').hide().parent().prepend($('<i class="fa fa-spinner fa-pulse"></i>'));


        $.ajax({
            url: "/private/currencies",
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
