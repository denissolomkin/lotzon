<div class="modal fade" id="deleteConfirm" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Удаление новости</h4>
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
        <h2>Список новостей</h2>
        <hr />
    </div>
    <div class="row-fluid">
        <div class="btn-group">
            <? foreach (Config::instance()->langs as $lang) { ?>
                <button onclick="document.location.href='/private/news/<?=$lang?>'" type="button" class="btn btn-md lang btn-default<?=($pageLang == $lang ? ' active' : '')?>" data-lang="<?=$lang?>"><?=strtoupper($lang)?></button>
            <? } ?>
        </div>
        <button class="btn btn-md btn-success pull-right"  onclick="document.location.href='/private/news/<?=$pageLang?>#addForm';"><i class="glyphicon glyphicon-plus"></i> Добавить</button>
    </div>
    <div class="row-fluid">&nbsp;</div>
    <div class="row-fluid">
        <table class="table table-striped">
            <thead>
                <th>#ID</th>
                <th>Заголовок</th>
                <th>Текст</th>
                <th>Дата создания</th>
                <th>Options</th>
            </thead>
            <tbody>
                <? foreach ($list as $news) { ?>
                    <tr>
                        <td class="id"><?=$news->getId()?></td>
                        <td class="title"><strong><?=$news->getTitle()?></strong></td>
                        <td width="50%"><?=(mb_substr(strip_tags($news->getText()), 0, 256) . '...')?></td>
                        <td><?=date('d.m.Y', $news->getDate())?></td>
                        <td>
                            <button class="btn btn-md edit-text btn-warning"><i class="glyphicon glyphicon-edit"></i></button>&nbsp;
                            <button class="btn btn-md remove-text btn-danger" data-target="#deleteConfirm"><i class="glyphicon glyphicon-remove"></i></button>
                        </td>
                        <td class="fulltext" style="display:none">
                            <?=$news->getText()?>
                        </td>
                    </tr>
                <? } ?>
            </tbody>
        </table>
    </div>  
    <? if ($pager['pages'] > 1) {?>
        <div class="row-fluid">
            <div class="btn-group">
                <? for ($i=1; $i <= $pager['pages']; ++$i) { ?>
                    <button onclick="document.location.href='/private/news/<?=$pageLang?>?page=<?=$i?>'" class="btn btn-default btn-md <?=($i == $pager['page'] ? 'active' : '')?>"><?=$i?></button>
                <? } ?>
            </div>
        </div>
    <? } ?> 
    <div class="row-fluid">
        <h2>Добавить новость</h2>
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
                <label class="control-label">Текст</label>
                <div id="text"></div>          
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

<script>
    var currentEdit = {
        id: '',
        title : '',
        text : '',       
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
            url: "/private/news/<?=$pageLang?>",
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

    $('.edit-text').on('click', function() {
        currentEdit.id = $(this).parents('tr').find('td.id').text();
        currentEdit.text = $(this).parents('tr').find('td.fulltext').html();
        currentEdit.title = $(this).parents('tr').find('td.title').text();

        $('#addForm').find('input[name="title"]').val(currentEdit.title);
        $('#text').code(currentEdit.text);

        document.location.href = '#addForm';
    });

    $('.remove-text').on('click', function() {
        var row = $(this).parents('tr')
        var identifier = row.find('td.id').text();
        $('#deleteConfirm').modal();
        $('#deleteConfirm').find('.btn-danger').off('click').on('click', function() {
             $.ajax({
                url: "/private/news/" + identifier,
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