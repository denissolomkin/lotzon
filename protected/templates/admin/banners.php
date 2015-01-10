<div class="container-fluid">
    <form role="form" action="/private/banners" method="POST">
    <div class="row-fluid">
        <h2>Banners
            <button type="submit" class="btn btn-success right">Сохранить</button></h2>
        <hr />
    </div>
    <div class="row-fluid">
            <? foreach ($list as $key=>$sector) : ?>
            <div class="col-md-6">
                <h2>
                    <button type="button" data-sector="<?=$key?>" class="btn btn-success add-banner"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button>
                    <?=$key?><input type="hidden" name="banners[<?=$key?>]" value="">
                </h2>
                <div id="<?=$key?>">
                <? $cnt=0;
                if(is_array($sector))
                foreach ($sector as $banner) : ?>

                    <div class="row" data-id="<?=$cnt?>" style="margin-bottom: 10px;">
                        <div class="col-md-2" style="display: flex;">
                               <button type="button" data-sector="<?=$key?>" class="btn btn-danger del-banner"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>
                               <textarea placeholder="Title" class="form-control input-md" name=banners[<?=$key?>][<?=$cnt?>][title]><?=$banner['title'];?></textarea>
                            </div>
                        <div class="col-md-5 row">
                               <textarea placeholder="Div" class="form-control input-md" name=banners[<?=$key?>][<?=$cnt?>][div]><?=$banner['div'];?></textarea>
                            </div>
                        <div class="col-md-5">
                               <textarea placeholder="Script" class="form-control input-md" name=banners[<?=$key?>][<?=$cnt?>][script]><?=$banner['script'];?></textarea>
                            </div>
                        </div>

                <? $cnt++;
                endforeach ?>
                </div>
            </div>
            <? endforeach ?>

    </div>
    </form>
</div>

<script>

    $( ".add-banner" ).on( "click", function( event ) {
        var id=$(this).data('sector');
        var cnt = $('#'+id).children().last().data('id')+1;
        if (!cnt)
            cnt=0;
        $("#"+id).append('<div class="row" data-id="'+cnt+'" style="margin-bottom: 10px;">' +
        '<div class="col-md-2" style="display: flex;">' +
        '   <button type="button" data-sector="'+id+'" class="btn btn-danger del-banner"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>' +
        '   <textarea placeholder="Title" class="form-control input-md" name=banners['+id+']['+cnt+'][title]></textarea>' +
        '</div>' +
        '<div class="col-md-5 row">' +
        '   <textarea placeholder="Div" class="form-control input-md" name=banners['+id+']['+cnt+'][div]></textarea>' +
        '</div>' +
        '<div class="col-md-5">' +
        '   <textarea placeholder="Script" class="form-control input-md" name=banners['+id+']['+cnt+'][script]></textarea>' +
        '</div>' +
        '</div>');
    });

    $(document ).on( "click",".del-banner", function( event ) {
        $(this).parent().parent().remove();
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