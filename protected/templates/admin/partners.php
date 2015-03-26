<div class="container-fluid images">
<?print_r($list);?>
    <form role="form" action="/private/partners" method="POST">
    <div class="row-fluid">
        <h2>Партнеры
            <button type="submit" class="btn btn-success right">Сохранить</button></h2>
        <hr/>
    </div>


    <div class="row-fluid">&nbsp;</div>

    <div class="row-fluid" style="
background: #ccc;
margin-left: -15px;
position: absolute;
padding: 5px;">


        <div class="pointer " data-image='' style='display:inline-table;position: relative;margin:5px;'>
            <img src="/theme/admin/img/photo-icon-plus.png" class="upload" alt="click to upload" style="cursor:pointer;">
        </div><? $new=true;
        foreach ($images as $image) : ?><?=$image['size'][1]>500 && $new?'<br>'.($new=false):''
            ?><div class="pointer ">
            <div class="name"><?=$image['name']?></div>
            <?=($image['size']?"<div class='size'>{$image['size'][0]}x{$image['size'][1]}</div>":"")?>
            <img class="image-trigger" src="http://<?=$_SERVER['SERVER_NAME'].$webDir?><?=$image['name']?>" style='<?=$image['size'][1]>400?'max-height: 300px':''?>'/>
            <input class="form-control" placeholder="ссылка" name="partners[<?=$image['name']?>]" value="<?=$list[$image['name']]?>">
            </div><?
        unset($list[$image['name']]);
        endforeach;?>
        <? $new=true;
        if(!empty($list))
        foreach ($list as $name=>$href) : ?><div class="pointer ">
            <div class="name"><?=$name?></div>
            <img class="image-trigger" src="http://<?=$_SERVER['SERVER_NAME'].$webDir?><?=$name?>"/>
            <input class="form-control" name="partners[<?=$name?>]" value="<?=$href?>">
        </div><? endforeach;?>
    </div>
        </form>
</div>

<div class="modal fade" id="image" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="btn btn-danger delete-trigger right" style="margin: -5px;"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Удалить</button>
                <h4 class="modal-title" id="confirmLabel">Image Information</h4>
            </div>
            <div class="modal-body" style="overflow: overlay">
                <div class="image">
                </div>
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
        var size=$(this).parent().children(':eq(1)').html().split('x');

        tdata=$(this).clone().removeAttr('style').removeAttr('class').css('width',size[0]).css('height',size[1]);

        $("#image .modal-title").text($(this).parent().children(':eq(0)').html());
        $("#image input#width").val(size[0]);
        $("#image input#height").val(size[1]);
        $("#image input").trigger('input');

        $("#image").find('.modal-body div').html(tdata);
        updateCode();

        $("#image").find('.cls').off('click').on('click', function () {
            $("#image").modal('hide');
        });

        $('#image .modal-body div').off().on('click', initUpload);
    });

    $("#image input#width").on('input', function(){ $("#image .modal-body div img").css('width',$(this).val()); });
    $("#image input#height").on('input', function(){ $("#image .modal-body div img").css('height',$(this).val()); });
    $("#image input").on('input', updateCode);
    $('.upload, #image img').on('click', initUpload);

    function updateCode() { $("#image textarea").text(($("#image input").val()?'<a target="_blank" href="'+$("#image input").val()+'">':'')+$("#image .modal-body div").html()+($("#image input").val()?'</a>':'')); }
    function initUpload() {

        // create form
        var form = $('<form method="POST" enctype="multipart/form-data"><input type="file" name="image"/></form>');
        var image = $(this).find('img');
        var input = form.find('input[type="file"]').damnUploader({
            url: '/private/images?folder=<?=$curDir?>',
            fieldName: 'image',
            dataType: 'json'
        });

        input.off('du.add').on('du.add', function(e) {
            e.uploadItem.completeCallback = function(succ, data, status) {
                if(image.parent().parent().prev().find('h4').text()) {
                    image.attr('src', 'http://<?=$_SERVER['SERVER_NAME']?>/' + data.imageWebPath + "?" + (new Date().getTime()));
                    $("img[src^=\"http://<?=$_SERVER['SERVER_NAME']?>/"+data.imageWebPath+"\"]").
                        attr('src', 'http://<?=$_SERVER['SERVER_NAME']?>/' + data.imageWebPath + "?" + (new Date().getTime())).
                        parent().find('.size').
                        text(data.imageWidth+'x'+data.imageHeight);

                    $("#image .modal-body img").css('height', data.imageHeight).css('width', data.imageWidth);
                    $("#image input#width").val(data.imageWidth);
                    $("#image input#height").val(data.imageHeight);
                    updateCode();
                } else{
                    insert=$('<div class="pointer">'+
                    '<div class="name">'+data.imageName+'</div>'+
                    '<div class="size">'+data.imageWidth+'x'+data.imageHeight+'</div>'+
                    '<img class="image-trigger" src="http://<?=$_SERVER['SERVER_NAME']?>/'+data.imageWebPath+"?"+(new Date().getTime())+'" style="'+(data.imageHeight>400?'max-height: 300px;':'')+'"/>'+
                    '<input class="form-control" placeholder="ссылка" name="partners['+data.imageName+']" value="">'+
                    '</div>');
                    insert.insertAfter($('.upload').parent());
                }

                //if(!image.data('image'))
                //    currentItem.image = data.imageName;
            };

            e.uploadItem.progressCallback = function(perc) {}
            e.uploadItem.addPostData('name', image.parent().parent().prev().find('h4').text());
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
                        $(".images img[src^=\"http://<?=$_SERVER['SERVER_NAME']?>/"+data.delete+"\"]").parent().remove();
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