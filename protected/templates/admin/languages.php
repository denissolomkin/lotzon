<link href="/theme/admin/lib/country.css" rel="stylesheet">
<div class="container-fluid">
    <div class="row-fluid">
        <h2>Языки
            <button type="button" class="btn btn-md btn-success add-language"><span class="glyphicon glyphicon-plus"></span> Добавить</button></h2>
        <hr />
    </div>
    <div class="row-fluid countries">

    </div>
</div>

<div class="add-language-template" style="display:none">
    <div class="row-fluid">
        <form class="form form-inline">

            <div class="input-group">
                <div class="flag"></div>
            </div>
            <input type="hidden" name="Id"  value="">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-globe fa-2x"></i></span>
                <select class="form-control c" name="Code">
                    <option></option>
                    <?
                    $langs = array('AF','AX','AL','DZ','AS','AD','AO','AI','AQ','AG','AR','AM','AW','AU','AT','AZ','BS','BH','BD','BB','BY','BE','BZ','BJ','BM','BT','BO','BQ','BA','BW','BV','BR','IO','BN','BG','BF','BI','KH','CM','CA','CV','KY','CF','TD','CL','CN','CX','CC','CO','KM','CG','CD','CK','CR','CI','HR','CU','CW','CY','CZ','DK','DJ','DM','DO','EC','EG','SV','GQ','ER','EE','ET','FK','FO','FJ','FI','FR','GF','PF','TF','GA','GM','GE','DE','GH','GI','GR','GL','GD','GP','GU','GT','GG','GN','GW','GY','HT','HM','VA','HN','HK','HU','IS','IN','ID','IR','IQ','IE','IM','IL','IT','JM','JP','JE','JO','KZ','KE','KI','KP','KR','KW','KG','LA','LV','LB','LS','LR','LY','LI','LT','LU','MO','MK','MG','MW','MY','MV','ML','MT','MH','MQ','MR','MU','YT','MX','FM','MD','MC','MN','ME','MS','MA','MZ','MM','NA','NR','NP','NL','NC','NZ','NI','NE','NG','NU','NF','MP','NO','OM','PK','PW','PS','PA','PG','PY','PE','PH','PN','PL','PT','PR','QA','RE','RO','RU','RW','BL','SH','KN','LC','MF','PM','VC','WS','SM','ST','SA','SN','RS','SC','SL','SG','SX','SK','SI','SB','SO','ZA','GS','SS','ES','LK','SD','SR','SJ','SZ','SE','CH','SY','TW','TJ','TZ','TH','TL','TG','TK','TO','TT','TN','TR','TM','TC','TV','UG','UA','AE','GB','US','UM','UY','UZ','VU','VE','VN','VG','VI','WF','EH','YE','ZM','ZW');
                    sort($langs);
                    if(is_array($availabledCountries))
                        foreach($availabledCountries as $language):
                            unset ($langs[array_search($language['Country'], $languages)])?><option value="<?=$language['Country'];?>"><?=$language['Country'].' ('.$language['Count'].')';?></option><? endforeach;
                    if(is_array($langs))
                        foreach($langs as $language):?><option value="<?=$language;?>"><?=$language;?></option><? endforeach;?>
                </select>
            </div>

            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-language fa-2x"></i></span>
                <input name="Title" value="" class="form-control t">
            </div>

            <div class="input-group">
                <button type="button" class="btn btn-md btn-default save-language"><i class="fa fa-save"></i> Сохранить изменения</button>
            </div>
        </form>
    </div>
</div>


<script>
    <? $array=array();
    if(is_array($languages)){
        foreach($languages as $lang){
        $array[$lang->getCode()]=array(
            'Id'=>$lang->getId(),
            'Code'=>$lang->getCode(),
            'Title'=>$lang->getTitle(),
        );
        }}
?>

    langs = <?=json_encode($array)?>;
    $.each(langs, function(index, lang) {
        var $template = $($('.add-language-template').html()).removeClass('add-language-template').appendTo('.countries');
        $('.flag',$template).addClass('flag-'+lang['Code'].toLowerCase());
        $('[name="Id"]',$template).val(lang['Id']);
        $('[name="Code"]',$template).val(lang['Code']);
        $('[name="Title"]',$template).val(lang['Title']).change();

    });



    $('.add-language').on('click', function() {
        var template = $('.add-language-template').html();
        $('.countries').append($(template));
    });

    $(document).on('input','input, select', function() {
        $(this).parents('.form-inline').find('.btn').addClass('btn-success');

    });

    $(document).on('input','select[name="Code"]', function() {
        console.log($(this).val());
        $(this).parents('.form-inline').find('.flag').removeClass().addClass('flag flag-'+$(this).val().toLowerCase());

    });

    $(document).on('click','.save-language', function() {
        var form = $(this).parents('form');
        var button =$(this);

        button.find('.fa').hide().parent().prepend($('<i class="fa fa-spinner fa-pulse"></i>'));


        $.ajax({
            url: "/private/languages",
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
