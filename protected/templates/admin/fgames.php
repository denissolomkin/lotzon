<div class="container-fluid fgames">
    <form role="form" action="/private/fgames" method="POST">
        <div class="row-fluid">
            <h2>Flash Games
                <button type="submit" class="btn btn-success right">Сохранить</button>
                <button type="button" data-sector="fgames" class="btn btn-success add-one"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button>
            </h2>
            <hr />
        </div>
        <div class="row-fluid" id="fgames">

            <? if(is_array($list) && !empty($list)) :

                foreach ($list as $game) :
                    if(empty($game) || !is_array($game))
                        continue;?>

            <div class="col-md-1">
                <div class="holder">
                <div class="thumbnail" data-id="<?=$game['id']?>">
                    <img src='<?='../filestorage/games/Flash'.$game['id']. '.png';?>' data-id="<?=$game['id'];?>" class="upload" alt="...">
                </div>
                <div class="flex">
                    <input placeholder="Title" class="form-control input-md" name=fgames[<?=$game['id'];?>][title] value="<?=$game['title'];?>"</input>
                    <button type="button" class="btn btn-danger del-one"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
                    <input type="hidden" name="fgames[<?=$game['id']?>][id]" value="<?=$game['id'];?>">
                </div>
                    <textarea placeholder="Code" class="form-control input-md" name="fgames[<?=$game['id'];?>][code]"><?=$game['code'];?></textarea>
                </div>
            </div>

            <?  endforeach;endif; ?>
        </div>
    </form>
</div>

<script src="/theme/admin/lib/jquery.damnUploader.min.js"></script>
<script>


    $( ".add-one" ).on( "click", function( event ) {
        
        var id = $(this).data('sector');
        var cnt = $('#'+id).children().first().find('.thumbnail').data('id') + 1 || 1;

        $("#fgames").prepend(
            '<div class="col-md-1"><div class="holder">' +
            '<div class="thumbnail" data-id="'+cnt+'">' +
            '<img src="../filestorage/games/Flash'+cnt+'.png?'+(new Date().getTime())+'" class="upload" data-id="'+cnt+'" data-image="" alt="...">' +
            '<input type="hidden" name="fgames['+cnt+'][id]" value="'+cnt+'">' +
            '</div>' +
            '' +
            '<div class="flex">' +
            '<button type="button" class="btn btn-danger del-one"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>' +
            '<input placeholder="Title" class="form-control input-md" name="fgames['+cnt+'][title]" value=""></input>' +
            '</div>' +
            '<textarea placeholder="Code" class="form-control input-md" name="fgames['+cnt+'][code]"></textarea>' +
            '</div>' +
            '</div>'
        );

    });

    $(document ).on( "click",".del-one", function( event ) {
        $(this).parent().parent().parent().remove();
    });

    $(document).on('click', '.upload', initUpload);

    function initUpload() {

        // create form
        var form = $('<form method="POST" enctype="multipart/form-data"><input type="file" name="image"/></form>'),
            image = $(this),
            path = 'filestorage/',
            input = form.find('input[type="file"]').damnUploader({
            url: '/private/images?folder=games',
            fieldName: 'image',
            dataType: 'json'
        });

        input.off('du.add').on('du.add', function(e) {
            e.uploadItem.completeCallback = function(succ, data, status) {
                image.attr('src', '../filestorage/games/'+data.imageName+"?"+(new Date().getTime()));
            };
            e.uploadItem.progressCallback = function(perc) {}
            e.uploadItem.addPostData('Id', image.attr('data-id'));
            e.uploadItem.addPostData('name', 'Flash'+image.attr('data-id')+'.png');
            e.uploadItem.addPostData('path', path);
            e.uploadItem.upload();
        });
        form.find('input[type="file"]').click();
    }
</script>