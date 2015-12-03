<div class="modal fade" id="deleteConfirm" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Удаление поста в блоге</h4>
            </div>
            <div class="modal-body">
                <p>Уверены ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger">Удалить</button>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">    
    <div class="row-fluid">
        <h2>Список постов в блоге</h2>
        <hr />
    </div>
    <div class="row-fluid">
        <div class="btn-group">
            <? foreach (\LanguagesModel::instance()->getList() as $lang) { ?>
                <button onclick="document.location.href='/private/blogs/<?=$lang->getCode()?>'" type="button" class="btn btn-md lang btn-default<?=($pageLang == $lang->getCode() ? ' active' : '')?>" data-lang="<?=$lang->getCode()?>"><?=strtoupper($lang->getCode())?></button>
            <? } ?>
        </div>
        <button class="btn btn-md btn-success pull-right"  onclick="document.location.href='/private/blogs/<?=$pageLang?>#addForm';"><i class="glyphicon glyphicon-plus"></i> Добавить</button>
    </div>
    <div class="row-fluid">&nbsp;</div>
    <div class="row-fluid">
        <table class="table table-striped">
            <thead>
                <th>#ID</th>
                <th>Заголовок</th>
                <th>Текст</th>
                <th>Дата создания</th>
                <th>Активный</th>
                <th>Options</th>
            </thead>
            <tbody>
                <? foreach ($list as $blog) { ?>
                    <tr>
                        <td class="id"><?=$blog->getId()?></td>
                        <td class="title"><strong><?=$blog->getTitle()?></strong></td>
                        <td width="40%"><?=(mb_substr(strip_tags($blog->getText()), 0, 256) . '...')?></td>
                        <td><?=date('d.m.Y', $blog->getDateCreated())?></td>
                        <td><i class="glyphicon glyphicon-<?=($blog->getEnable()==true ? 'ok' : 'remove')?>"></i></td>
                        <td>
                            <button class="btn btn-md edit-text btn-warning"><i class="glyphicon glyphicon-edit"></i></button>&nbsp;
                            <button class="btn btn-md remove-text btn-danger" data-target="#deleteConfirm"><i class="glyphicon glyphicon-remove"></i></button>
                        </td>
                        <td class="fulltext" style="display:none"><?=$blog->getText()?></td>
                        <td class="img" style="display:none"><?=$blog->getImg()?></td>
                        <td class="enable" style="display:none"><input type="checkbox" name="<?=$blog->getId()?>_enable" value="" <?=($blog->getEnable() ? 'checked="true"' : '')?>">></td>
                    </tr>
                <? } ?>
            </tbody>
        </table>
    </div>  
    <? if ($pager['pages'] > 1) {?>
        <div class="row-fluid">
            <div class="btn-group">
                <? for ($i=1; $i <= $pager['pages']; ++$i) { ?>
                    <button onclick="document.location.href='/private/blogs/<?=$pageLang?>?page=<?=$i?>'" class="btn btn-default btn-md <?=($i == $pager['page'] ? 'active' : '')?>"><?=$i?></button>
                <? } ?>
            </div>
        </div>
    <? } ?> 
    <div class="row-fluid">
        <h2>Добавить пост</h2>
        <hr />
    </div>    
    <div class="row-fluid" id="errorForm" style="display:none">
        <div class="alert alert-danger" role="alert">
          <span class="error-container"></span>
        </div>
    </div>
    <div class="row-fluid" id="addForm">
        <form class="form">
            <div class="form-group">
                <label class="control-label">Заголовок</label>
                <input type="text" name="title" value="" placeholder="Заголовок" class="form-control" />
            </div>
            <div class="form-group">
                <label class="control-label">Img</label>
                <img src="/theme/admin/img/photo-icon-plus.png" data-image="" class="upload" id="image" alt="click to upload" style="cursor:pointer;">
            </div>
            <div class="form-group">
                <label class="control-label">Текст</label>
                <div id="text"></div>          
            </div>
            <div class="form-group">
                <label class="control-label">Включено</label>
                <input type="checkbox" name="enable" value="1">
            </div>
        </form>        
    </div>

    <div class="row-fluid">
        <button class="btn btn-md btn-success save pull-right"> Сохранить</button>
    </div>
    <div class="row-fluid">&nbsp;</div>
    <div class="row-fluid">&nbsp;</div>
    <div class="row-fluid">&nbsp;</div>
</div>

<script src="/theme/admin/lib/jquery.damnUploader.min.js"></script>

<script>
    var currentEdit = {
        id: '',
        title : '',
        text : '',
        img : '',
        enable : '',
    };

    $(document).ready(function() {
        $('#text').summernote({
            height: 200,
        });
        $('#text').code('');
    });

    $('.save').on('click', function() {
        var text = $('#text').code();

        currentEdit.text = text;
        currentEdit.enable = $('input[name="enable"]').prop("checked");

        $("#errorForm").hide();
        $(this).find('.glyphicon').remove();

        if (!$('input[name="title"]').val()) {
            showError('Title can\'t be empty');

            return false;
        }
        currentEdit.title = $('input[name="title"]').val();
        if (!currentEdit.text) {
            showError('Text can\'t be empty');

            return false;
        }

        $.ajax({
            url: "/private/blogs/<?=$pageLang?>",
            method: 'POST',
            data: currentEdit,
            async: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    document.location.reload();
                } else {
                    showError(data.message);
                }
            }, 
            error: function() {
                showError('Unexpected server error');
           }
        });
    });

    $('.upload').on('click', initUpload);

    function initUpload() {

        var form = $('<form method="POST" enctype="multipart/form-data"><input type="file" name="img"/></form>');

        var image = $(this);
        var input = form.find('input[type="file"]').damnUploader({
            url: '/private/blogs/uploadPhoto/',
            fieldName: 'img',
            data: currentEdit,
            dataType: 'json'
        });

        input.off('du.add').on('du.add', function(e) {

            e.uploadItem.completeCallback = function(succ, data, status) {
                image.attr('src', data.imageWebPath+"?"+(new Date().getTime()));
                image.attr('data-image', data.imageName);
                currentEdit.img = data.imageName;
            };

            e.uploadItem.progressCallback = function(perc) {}

            e.uploadItem.addPostData('imageName', image.attr('data-image'));
            e.uploadItem.upload();
        });

        form.find('input[type="file"]').click();
    }

    $('.edit-text').on('click', function () {
        currentEdit.id     = $(this).parents('tr').find('td.id').text();
        currentEdit.text   = $(this).parents('tr').find('td.fulltext').html();
        currentEdit.title  = $(this).parents('tr').find('td.title').text();
        currentEdit.img    = $(this).parents('tr').find('td.img').text();
        currentEdit.enable = $('input[name="'+currentEdit.id+'_enable"]').prop("checked");

        $('#addForm').find('input[name="title"]').val(currentEdit.title);
        $('#text').code(currentEdit.text);

        $('img#image').attr('data-image',"/filestorage/blog/320/"+currentEdit.img);
        $('img#image').attr('src',"/filestorage/blog/320/"+currentEdit.img);

        if (currentEdit.enable==true) {
            $('input[name="enable"]').prop("checked", true);
        } else {
            $('input[name="enable"]').prop("checked", false);
        }
        document.location.href = '#addForm';
    });

    $('.remove-text').on('click', function() {
        var row = $(this).parents('tr');
        var identifier = row.find('td.id').text();
        $('#deleteConfirm').modal();
        $('#deleteConfirm').find('.btn-danger').off('click').on('click', function() {
             $.ajax({
                url: "/private/blogs/" + identifier,
                method: 'DELETE',
                data: {},
                async: true,
                dataType: 'json',
                success: function(data) {
                    if (data.status == 1) {
                        $('#deleteConfirm').modal('hide')
                        row.remove();
                    } else {
                        showError(data.message);
                    }
                }, 
                error: function() {
                    showError('Unexpected server error');
               }
            });
        });
    });

    function showError(message) {
        $(".error-container").text(message);
        $("#errorForm").show();

        $('.save').removeClass('btn-success').addClass('btn-danger');
        $('.save').prepend($('<i class="glyphicon glyphicon-remove"></i>'));
    }
</script>