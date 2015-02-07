<div class="container-fluid images">

    <div class="row-fluid">
        <h2>Изображения <?=$curDir?' / '.$curDir:''?></h2>
        <hr/>
    </div>

    <div class="row-fluid">

        <div class="btn-group">
        <? if(is_array($folders))
        foreach ($folders as $folder) : ?>
            <button onclick="document.location.href='./images?folder=<?=$folder['name']?>'" type="button" class="btn btn-md lang btn-default"><span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span> &nbsp;<?=$folder['name']?></button>
        <? endforeach;
        if($curDir) {?>
            <button onclick="document.location.href='./images'" type="button" class="btn btn-md lang btn-default"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> назад</button>
        <? } ?>
        </div>
    </div>

    <div class="row-fluid">&nbsp;</div>

    <div class="row-fluid">


        <div class="pointer " data-image='' style='display:inline-table;position: relative;margin:5px;'>
            <img src="/theme/admin/img/photo-icon-plus.png" class="upload" alt="click to upload" style="cursor:pointer;">
        </div>


        <? $new=true;
        foreach ($images as $image) : ?>
        <?=$image['size'][1]>500 && $new?'<br>'.($new=false):''?>
        <div class="pointer image-trigger">
            <div class="name"><?=$image['name']?></div>
            <?=($image['size']?"<div class='size'>{$image['size'][0]}x{$image['size'][1]}</div>":"")?>
            <img src="http://<?=$_SERVER['SERVER_NAME'].$webDir?><?=$image['name']?>" style='<?=$image['size'][1]>400?'max-height: 300px':''?>'/>
        </div>
        <? endforeach;?>
    </div>
</div>

<div class="modal fade" id="image" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="btn btn-danger delete-trigger right" style="margin: -5px;"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Удалить</button>
                <h4 class="modal-title" id="confirmLabel">Image Information</h4>
            </div>
            <div class="modal-body" style="overflow: overlay">
            </div>
            <div class="modal-footer">
                <div class="row-fluid">
                    <input placeholder="Link" class="form-control input-md" style="width: 80%;float: left;"/>
                    <input placeholder="Width" id="width" class="form-control input-md" style="width: 10%;float: left;"/>
                    <input placeholder="Height" id="height" class="form-control input-md" style="width: 10%"/>
                </div>
                <textarea class="form-control input-md" rows="4"></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default cls">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade delete" id="deleteConfirm" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Удаление изображения</h4>
            </div>
            <div class="modal-body">
                <p>Изображение <span id="name"></span> будет безвозвратно удалено. Вы уверены?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger rm">Да</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>

<script src="/theme/admin/lib/jquery.damnUploader.min.js"></script>
<script>
/* INFO */
$(document).on('click','.image-trigger', function() {

    $("#image").modal();
    var size=$(this).children(':eq(1)').html().split('x');
    console.log(size);

    tdata=$(this).find('img').clone().removeAttr('style').css('width',size[0]).css('height',size[1]);

    $("#image .modal-title").text($(this).children(':eq(0)').html());
    $("#image input#width").val(size[0]);
    $("#image input#height").val(size[1]);
    $("#image input").trigger('input');

    $("#image").find('.modal-body').html(tdata);
    updateCode();

    $("#image").find('.cls').off('click').on('click', function () {
        $("#image").modal('hide');
    });

    $('#image img').off().on('click', initUpload);
});

$("#image input#width").on('input', function(){ $("#image .modal-body img").css('width',$(this).val()); });
$("#image input#height").on('input', function(){ $("#image .modal-body img").css('height',$(this).val()); });
$("#image input").on('input', updateCode);
$('.upload, #image img').on('click', initUpload);

function updateCode() { $("#image textarea").text(($("#image input").val()?'<a target="_blank" href="'+$("#image input").val()+'">':'')+$("#image .modal-body").html()+($("#image input").val()?'</a>':'')); }
function initUpload() {

    // create form
    var form = $('<form method="POST" enctype="multipart/form-data"><input type="file" name="image"/></form>');
    var image = $(this);
    var input = form.find('input[type="file"]').damnUploader({
        url: '/private/images?folder=<?=$curDir?>',
        fieldName: 'image',
        dataType: 'json'
    });

    input.off('du.add').on('du.add', function(e) {
        e.uploadItem.completeCallback = function(succ, data, status) {
            if(image.parent().prev().find('h4').text()) {
                image.attr('src', 'http://<?=$_SERVER['SERVER_NAME']?>/' + data.imageWebPath + "?" + (new Date().getTime()));
                $("#image .modal-body img").css('height', data.imageHeight).css('width', data.imageWidth);
                $("#image input#width").val(data.imageWidth);
                $("#image input#height").val(data.imageHeight);
                updateCode();
            } else{
                insert=$('<div class="pointer image-trigger">'+
                '<div class="name">'+data.imageName+'</div>'+
                '<div class="size">'+data.imageWidth+'x'+data.imageHeight+'</div>'+
                '<img src="http://<?=$_SERVER['SERVER_NAME']?>/'+data.imageWebPath+"?"+(new Date().getTime())+'" style="'+(data.imageHeight>400?'max-height: 300px;':'')+'"/>'+
                '</div>');
                insert.insertAfter($('.upload').parent());
            }

            //if(!image.data('image'))
            //    currentItem.image = data.imageName;
        };

        e.uploadItem.progressCallback = function(perc) {}
        e.uploadItem.addPostData('name', image.parent().prev().find('h4').text());
        e.uploadItem.upload();
    });

    form.find('input[type="file"]').click();
}


/* DELETE BLOCK */
$('.delete-trigger').on('click', function() {
    var image = $(this).parent().parent().children(':eq(0)').find('h4').text().trim();
    $(".delete #name").text(image);
    $(".delete").modal();
    $(".delete").find('.cls').off('click').on('click', function() {
        $(".delete").modal('hide');
    });
    $(".delete").find('.rm').off('click').on('click', function() {
        $.ajax({
            url: "/private/images/?folder=<?=$curDir?>&image="+image,
            method: 'DELETE',
            async: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    $(".delete, #image").modal('hide');
                    $("img[src=\"http://<?=$_SERVER['SERVER_NAME']?>/"+data.delete+"\"]").parent().remove();
                    alert('Изображение удалено');
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
</script>