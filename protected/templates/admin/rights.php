<div class="container-fluid">
    <form role="form" action="/private/rights" method="POST">
    <div class="row-fluid">
        <h2>Права доступа
            <button type="submit" class="btn btn-success right">Сохранить</button>
        <hr />
    </div>
    <div class="row-fluid">
            <div class="col-md-12">
            <table class="table table-striped">
<tr><th>Страница</th>
                <? foreach ($roles as $role) : ?>
                    <th><?= $role ?></th>
                <? endforeach ?>
</tr>
            <? foreach ($pages as $key=>$page) :
                if(is_array($page['pages'])) { ?>
                        <tr>
                            <td colspan="<?=count($roles)+1?>"><b><?= $key ?></b></td>
                        </tr>
                    <? foreach ($page['pages'] as $name => $pg) :?>
                        <tr>

                            <td><?= (isset($pg['icon'])?'<span class="fa fa-'.$pg['icon'].'"></span> ':'') ?><?= $pg['name'] ?></td>
                            <? foreach ($roles as $role) : ?>
                                <td><input name="rights<?='['.$role.']['.$name.']'?>" <?=($rights[$role][$name]?'checked':'')?> type="checkbox"></td>
                            <? endforeach ?>
                        </tr>
                    <? endforeach ?>
                <? } else {?>
                <tr>
                    <td><?= (isset($page['icon'])?'<span class="fa fa-'.$page['icon'].'"></span> ':'') ?><?= $page['name'] ?></td>
                    <? foreach ($roles as $role) : ?>
                        <td><input name="rights<?='['.$role.']['.$key.']'?>" <?=($rights[$role][$key]?'checked':'')?> type="checkbox"></td>
                    <? endforeach ?>
                </tr>
                <? } ?>
            <? endforeach ?>
            </table>
            </div>
        </div>
    </form>

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