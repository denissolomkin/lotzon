<div class="modal fade banners" id="banner-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Banner preview</h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid banners">
    <form role="form" action="/private/banners" method="POST">
    <div class="row-fluid">
        <h2>Banners
            <button type="submit" class="btn btn-success right">Сохранить</button>
            <input type="checkbox" name='banners[settings][enabled]' <?=$list['settings']['enabled']?'checked ':'';?>data-toggle="toggle">
        </h2>
    </div>
        <? foreach($games as $id=>$game)
            if(!isset($list['game'.$id]))
                $list['game'.$id]='';?>
        <script>
        var safeColors = ['00','33','66','99','cc','ff'];
        var rand = function() {
        return Math.floor(Math.random()*6);
        };
        var randomColor = function() {
        var r = safeColors[rand()];
        var g = safeColors[rand()];
        var b = safeColors[rand()];
        return "#"+r+g+b;
        };
        $(document).ready(function() {
            $('div.sector1').each(function() {
                    $(this).css('background',randomColor());
                $(this).css('background',randomColor());
                });
        });
        </script>
    <div class="row-fluid" style="background: #ccc;margin-left: -15px;position: absolute;padding: 0 0 5px 5px;">
            <?
            unset($list['settings']);
            if(is_array($list))
                foreach ($list as $sid=>$sector) : ?>
                    <div class="col-md-3  row-banner">
            <div class="sector" /*style="background-color: rgba(<?=rand(0,255).','.rand(0,255).','.rand(0,255)?>,0.2);"*/>
                <div>
                    <button type="button" data-sector="<?=$sid?>" class="btn btn-success btn-xs add-group"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button>
                    <span class="glyphicon glyphicon-filter" aria-hidden="true"></span> <small><?=(strstr($sid,'game')?$games[(str_replace('game','',$sid))]['Title']['RU']:$sid);?></small><input type="hidden" name="banners[<?=$sid?>]" value="">
                </div>
                <div id="<?=$sid?>">
                <? $gid=0;
                if(is_array($sector))
                foreach ($sector as $group) : ?>

                    <div id="group<?=$gid?>" class='group' data-gid="<?=$gid?>" style="clear: both;">
                        <div class="row-fluid" style=""><input type="hidden" name="banners[<?=$sid?>][<?=$gid?>]" value="">
                            <button type="button" data-group="<?=$gid?>" data-sector="<?=$sid?>" class="btn btn-success  add-banner btn-xs"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button><button type="button" class="btn btn-danger del-group btn-xs"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>
                            <small>Группа баннеров №<?=($gid+1)?></small>
                        </div>

                    <? $bid=0;
                    if(is_array($group))
                        foreach ($group as $banner) : ?>

                    <div class="row-banner banner" data-bid="<?=$bid?>" >

                        <div class="col-md-3" style="display: flex;">
                            <button type="button" style="margin-top: 0px;" data-sector="<?=$key?>" class="btn btn-danger del-banner btn-xs"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>
                            <textarea placeholder="Title" rows=1 class="form-control-banner input-md" name="banners[<?=$sid?>][<?=$gid?>][<?=$bid?>][title]"><?=$banner['title'];?></textarea>
                            </div>
                        <div class="col-md-3">
                               <textarea placeholder="Div" rows=1  class="form-control-banner input-md div" name="banners[<?=$sid?>][<?=$gid?>][<?=$bid?>][div]"><?=$banner['div'];?></textarea>
                            </div>
                        <div class="col-md-3">
                               <textarea placeholder="Script" rows=1 class="form-control-banner input-md script" name="banners[<?=$sid?>][<?=$gid?>][<?=$bid?>][script]"><?=$banner['script'];?></textarea>
                            </div>
                        <div class="col-md-1">
                            <input placeholder="Chance"  class="form-control-banner input-md" name="banners[<?=$sid?>][<?=$gid?>][<?=$bid?>][chance]" value="<?=$banner['chance'];?>">
                        </div>



                        <div class="col-md-2" style="display: flex;">
                        <select name="banners[<?=$sid?>][<?=$gid?>][<?=$bid?>][countries][]" size="1" multiple="multiple" class="form-control-banner input-sm" value="" placeholder="Страны" />
                        <? foreach ($supportedCountries as $country) {?>
                            <option <?=(is_array($banner['countries']) && array_search($country->getCountryCode(),$banner['countries'])!==false?' selected ':'');?> value="<?=$country->getCountryCode()?>"><?=$country->getCountryCode()?></option>
                        <? } ?>
                        </select>
                            <button type="button" class="btn btn-info btn-xs view-banner right"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
                        </div>
                        </div>


                            <?  $bid++;?>
                        <? endforeach ?>
                    </div>
                <? $gid++;
                endforeach ?>
                </div>
            </div>
            </div>
            <? endforeach ?>

    </div>
    </form>
</div>

<script>
    $( document ).on( "mouseover", "select", function( event ) {
        $(this).attr('size',3).css('z-index',10);
    });
    $( document ).on( "mouseleave", "select", function( event ) {
        $(this).attr('size',1).css('z-index',1);
    });


    $( document ).on( "click", ".add-banner", function( event ) {
        var sid=$(this).data('sector');
        var gid=$(this).data('group');
        console.log($('#group'+gid).children('.banner').last());
        var bid = $('#'+sid+' #group'+gid).children('.banner').last().data('bid')+1;
        if (!bid)
            bid=0;

        $("#"+sid+" #group"+gid).append('<div class="row-banner banner" data-bid="'+bid+'" class="group'+gid+'">' +
        '<div class="col-md-3" style="display: flex;">' +
        '   <button type="button" class="btn btn-xs btn-danger del-banner" style="margin-top: 0px;"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>' +
        '   <textarea placeholder="Title" rows=1 class="form-control-banner input-md" name=banners['+sid+']['+gid+']['+bid+'][title]></textarea>' +
        '</div>' +
        '<div class="col-md-3">' +
        '   <textarea placeholder="Div" rows=1 class="form-control-banner input-md div" name="banners['+sid+']['+gid+']['+bid+'][div]"></textarea>' +
        '</div>' +
        '<div class="col-md-3">' +
        '   <textarea placeholder="Script" rows=1 class="form-control-banner input-md script" name="banners['+sid+']['+gid+']['+bid+'][script]"></textarea>' +
        '</div>' +
        '<div class="col-md-1">' +
        '<input placeholder="Chance" class="form-control-banner input-md" name="banners['+sid+']['+gid+']['+bid+'][chance]">' +
        '</div>' +

        '<div class="col-md-2">' +
        '<select size=1 name="banners['+sid+']['+gid+']['+bid+'][countries][]"  multiple="multiple" class="form-control-banner input-sm" value="" placeholder="Страны">'+
        <? foreach ($supportedCountries as $country) { ?>'<option value="<?=$country->getCountryCode()?>"><?=$country->getCountryCode()?></option>'+
        <? } ?>
        '</select>' +
        '</div>' +
        '</div>');
    });


    $( ".add-group" ).on( "click", function( event ) {
        var sid=$(this).data('sector');
        var gid = $('#'+sid).children('.group').last().data('gid')+1;
        if (!gid)
            gid=0;


        $("#"+sid).append('<div id="group'+gid+'" class="group" style="clear: both;" data-gid="'+gid+'">' +
        '<div class="row-fluid" style="">'+
        '<input type="hidden" name="banners['+sid+']['+gid+']" value="">'+
        '   <button type="button" data-group="'+gid+'" data-sector="'+sid+'" class="btn btn-success add-banner btn-xs"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button>' +
        '   <button type="button" data-sector="'+sid+'" class="btn btn-danger del-group btn-xs"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>' +
        '<small> Группа баннеров №'+(gid+1)+'</small>'+
        '</div>' +
        '</div>');
    });
    $(document ).on( "click",".del-group", function( event ) {
        //$(".group"+$(this).data('group')).remove();
        $(this).parent().parent().remove();
    });

    $(document ).on( "click",".del-banner", function( event ) {
        $(this).parent().parent().remove();
    });

    $(document ).on( "click",".view-banner", function( event ) {

        $("#banner-holder").find('.modal-body').html($(this).parent().parent().find('.div').text());
        el=document.getElementsByClassName("modal-body");

        //console.log($($(this).parent().parent().find('.script').text()).attr('src'));
        //console.log($($(this).parent().parent().find('.script').text()));
        $.each($($(this).parent().parent().find('.script').text()), function(id,val){
            if($(val).prop("tagName")=='SCRIPT'){
                if(url=$(val).attr('src')) {
                    var script = document.createElement("script");
                    script.type = "text/javascript";
                    script.src = url;
                    el[0].appendChild(script);
                } else {
                    var script   = document.createElement("script");
                    script.type  = "text/javascript";
                    script.text  = $(val).text();
                    el[0].appendChild(script);
                }
            }
        })

        $("#banner-holder").modal();
        $("#banner-holder").find('.cls').on('click', function() {
            $("#banner-holder").modal('hide');
        })
    });
/*
    $( "form" ).on( "submit", function( event ) {

        event.preventDefault();
        $.ajax({
            url: "/private/banners/",
            method: 'POST',
            data: $( this ).serialize(),
            async: true,
            dataType: 'json',
            success: function(data) {
                console.log(data)
            },
            error: function() {

            }
        });

    });
    */
</script>