<div class="container-fluid">
    <div class="row-fluid">
        <h2>Blacklist
        <hr />
    </div>
    <div class="row-fluid">
            <? foreach ($list as $key=>$blocked) : ?>
        <form role="form" action="/private/blacklist" method="POST">
            <div class="col-md-12">
                <h2>

                    <?=$key?><input type="hidden" name="block[<?=$key?>]" value="">
                    <button type="submit" class="btn btn-success ">Сохранить</button></h2>
                </h2>
                <div id="<?=$key?>" class="row row-fluid">
                <?
                if(is_array($blocked))
                foreach ($blocked as $block) : ?>
                        <div class="col-md-2" style="display: flex;">
                               <button type="button" data-sector="<?=$key?>" class="btn btn-danger del-block"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>
                               <input placeholder="Value" class="form-control input-md" value="<?=$block?>" name=<?=$key?>[]>
                         </div>
                <?
                endforeach ?>
                    <div class="col-md-2" style="display: flex;">
                        <button type="button" data-sector="<?=$key?>" class="btn btn-success add-block"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button>
                    </div>
                </div>
            </div>
        </form>
            <? endforeach ?>

    </div>
</div>

<script>

    $( ".add-block" ).on( "click", function( event ) {
        var id=$(this).data('sector');
        $("#"+id).append('<div class="col-md-2" style="display: flex;">' +
        '   <button type="button" data-sector="'+id+'" class="btn btn-danger del-block"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>' +
        '   <input placeholder="Value" class="form-control input-md" value="" name='+id+'[]>' +
        '</div>').append($(this).parent());
    });

    $(document ).on( "click",".del-block", function( event ) {
        $(this).parent().remove();
    });
</script>