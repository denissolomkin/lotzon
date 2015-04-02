<div class="modal fade" id="deleteConfirm" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Удаление текста</h4>
            </div>
            <div class="modal-body">
                <p>Удаление текста может привести к ошибкам на паблике сайта</p>
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
        <h2>Список текстов на сайте</h2>
        <hr />
    </div>
    <div class="row-fluid">
        <button class="btn btn-md btn-success pull-right"  onclick="document.location.href='#addForm';"><i class="glyphicon glyphicon-plus"></i> Добавить</button>
    </div>
    <div class="row-fluid">&nbsp;</div>
    <div class="row-fluid">&nbsp;</div>
    <div class="row-fluid">
        <table class="table table-striped">
            <thead>
                <th>Идентификатор</th>
                <th>Текст</th>
                <th>Options</th>
            </thead>
            <tbody>
                <? foreach ($list as $id => $text) { ?>
                    <tr>
                        <td><strong class="identifier"><?=$id?></strong></td>
                        <td><?=($text['UA'] ? mb_substr(strip_tags(@$text['UA']->getText()), 0, 255) . '...' : '')?></td>
                        <td>
                            <button class="btn btn-md edit-text btn-warning"><i class="glyphicon glyphicon-edit"></i></button>&nbsp;
                            <button class="btn btn-md remove-text btn-danger" data-target="#deleteConfirm"><i class="glyphicon glyphicon-remove"></i></button>
                        </td>
                        <td style="display:none">
                        <? foreach ($text as $lang => $text) { ?>
                            <div data-lang="<?=$text->getLang()?>"><?=$text->getText()?></div>
                        <? } ?>
                        </td>
                    </tr>
                <? } ?>
            </tbody>
        </table>
    </div>  
    <div class="row-fluid">
        <h2>Добавить текст</h2>
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
                <label class="control-label">Идентификатор</label>
                <input type="text" name="id" value="" placeholder="Идентификатор" class="form-control" />
            </div>
            <div class="form-group">
                <label class="control-label">Текст</label>
                <div id="text"></div>          
            </div>
        </form>        
    </div>

    <div class="row-fluid">
        <div class="btn-group">
            <? $fst = true; ?>
            <? foreach ($langs as $lang) { ?>
                <button type="button" class="btn btn-md lang btn-default<?=($fst ? ' active' : '')?>" data-lang="<?=$lang?>"><?=strtoupper($lang)?></button>
            <? $fst = false;} ?>
        </div>
        <button class="btn btn-md btn-success save pull-right" style="margin-left:10px;"> Сохранить</button>
    </div>
    <div class="row-fluid">&nbsp;</div>
</div>

<script>
    var currentEdit = {
        identifier : '',
        text : {},
    };

    $(document).ready(function() {
        $('#text').summernote({
            height: 200,           
        });
        $('#text').code('');
    });

    $('.lang').on('click', function() {
        var prevLang = $('.lang.active').data('lang');
        var currentLang = $(this).data('lang');

        currentEdit.text[prevLang] = $('#text').code();

        $('.lang').removeClass('active');
        $(this).addClass('active');

        if (currentEdit.text[currentLang]) {
            $('#text').code(currentEdit.text[currentLang]);
        } else {
            $('#text').code('');
        }
    });

    $('.save').on('click', function() {
        var currentLang = $('.lang.active').data('lang');
        var text = $('#text').code();

        currentEdit.text[currentLang] = text;

        $("#errorForm").hide();
        $(this).find('.glyphicon').remove();

        if (!$('input[name="id"]').val()) {
            showError('Identifier can\'t be empty');

            return false;
        }
        currentEdit.identifier = $('input[name="id"]').val();
        if (!currentEdit.text[currentLang]) {
            showError('Text can\'t be empty');

            return false;
        }
        
        $.ajax({
            url: "/private/texts/",
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
        currentEdit.identifier = $(this).parents('tr').find('.identifier').text();
        $(this).parents('tr').find('[data-lang]').each(function(id, obj) {
            currentEdit.text[$(obj).data('lang')] = $(obj).html();
        });

        $('#addForm').find('input[name="id"]').val(currentEdit.identifier);
        $('#text').code(currentEdit.text[$('.lang.active').data('lang')]);

        document.location.href = '#addForm';
    });

    $('.remove-text').on('click', function() {
        var row = $(this).parents('tr')
        var identifier = row.find('.identifier').text();
        $('#deleteConfirm').modal();
        $('#deleteConfirm').find('.btn-danger').off('click').on('click', function() {
             $.ajax({
                url: "/private/texts/" + identifier,
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