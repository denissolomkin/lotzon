<div class="container-fluid">
    <form role="form" action="/private/gamebots" method="POST">
        <div class="row-fluid">
            <h2>Bots (<span id="count_bots"><?=count($list);?></span>)
                <button type="submit" class="btn btn-success right">Сохранить</button>
                <button type="button" data-sector="<?=$key?>" class="btn btn-success add-one"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button>
            </h2>
            <hr />
        </div>
        <div class="row-fluid" id="bots">
            <?
            if(is_array($list))
                foreach ($list as $key=>$bot) : ?>


            <div class="col-md-2">
                <div class="thumbnail" data-id="<?=$key?>">
                    <img src='<?=($bot['avatar']?'../filestorage/avatars/'.(ceil($key / 100)) . '/'.$bot['avatar']:'/theme/admin/img/photo-icon-plus.png')?>' data-id="<?=$key?>" data-image="<?=$bot['avatar']?>" class="upload" alt="...">
                </div>
                <div class="flex">
                    <button type="button" data-sector="<?=$key?>" class="btn btn-danger del-one"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>
                    <input placeholder="Name" class="form-control input-md" name=bots[<?=$key?>][name] value="<?=$bot['name'];?>"</input>
                    <input type="hidden" name="bots[<?=$key?>][id]" value="<?=$key?>">
                    <input type="hidden" name="bots[<?=$key?>][bot]" value="1">
                    <input type="hidden" name="bots[<?=$key?>][avatar]" value="<?=$bot['avatar']?>">
                </div>

            </div>

            <? endforeach ?>
        </div>
    </form>
</div>

<script src="/theme/admin/lib/jquery.damnUploader.min.js"></script>
<script>

    var ids=new Array(<?=implode(",",$ids);?>);

    $( ".add-one" ).on( "click", function( event ) {
        var id=$(this).data('sector');
        if(!ids.length){
            alert('Id для ботов исчерпаны');
            return false;
        }
        var cnt = ids.shift();//$('#'+id).children().last().data('id')+1;

        $('#count_bots').text(parseInt($('#count_bots').text())+1);

        $("#bots").prepend('<div class="col-md-2">' +
        '<div class="thumbnail" data-id="'+cnt+'">' +
        '<img src="/theme/admin/img/photo-icon-plus.png" class="upload" data-id="'+cnt+'" data-image="" alt="...">' +
        '<input type="hidden" name="bots['+cnt+'][id]" value="'+cnt+'">' +
        '<input type="hidden" name="bots['+cnt+'][bot]" value="1">' +
        '<input type="hidden" name="bots['+cnt+'][avatar]" value="">' +
        '</div>' +
        '' +
        '<div class="flex">' +
        '<button type="button" class="btn btn-danger del-one"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>' +
        '<input placeholder="Name" class="form-control input-md" name=bots['+cnt+'][name] value="Участник '+cnt+'"</input>' +
        '</div>' +
        '' +
        '</div>');

        $('.upload').off('click').on('click', initUpload);
    });

    $(document ).on( "click",".del-one", function( event ) {
        $(this).parent().parent().remove();
        $('#count_bots').text(parseInt($('#count_bots').text())-1);
    });



    $('.upload').on('click', initUpload);

    function initUpload() {

        // create form
        var form = $('<form method="POST" enctype="multipart/form-data"><input type="file" name="image"/></form>');
        var image = $(this);
        var input = form.find('input[type="file"]').damnUploader({
            url: '/private/gamebots/uploadPhoto',
            fieldName: 'image',
            dataType: 'json'
        });

        input.off('du.add').on('du.add', function(e) {

            e.uploadItem.completeCallback = function(succ, data, status) {
                image.attr('src', data.imageWebPath+"?"+(new Date().getTime()));
                image.attr('data-image', data.imageName);
                $('input[name="bots['+image.attr('data-id')+'][avatar]"]').val(data.imageName);
            };

            e.uploadItem.progressCallback = function(perc) {}

            e.uploadItem.addPostData('imageName', image.attr('data-image'));
            e.uploadItem.addPostData('Id', image.attr('data-id'));
            e.uploadItem.upload();
        });

        form.find('input[type="file"]').click();
    }
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