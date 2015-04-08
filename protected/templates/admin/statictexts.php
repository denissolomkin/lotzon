
<? if($frontend) include($frontend.'_frontend.php') ?>

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

<div class="container-fluid texts">
    <div class="row-fluid">
        <h2>Список текстов на сайте <?if ($curCategory) {?>
            <button class="btn btn-md btn-primary" onClick="location.href='/private/<?=$activeMenu;?>'"><i class="fa fa-arrow-left"></i> Назад</button>
            <button class="btn btn-md btn-success text-trigger"><i class="glyphicon glyphicon-plus"></i> Добавить</button><? } ?></h2>
        <hr />
    </div>

    <? if(is_array($categories))
        foreach ($categories as $category => $options) { ?>
        <a href="?category=<?=$category;?>">
            <div class="metal-gradient <?=$curCategory?' small':'';?>" <?=$curCategory==$category?' style="background: gray !important;"':'';?>>

                <i class='fa fa-<?= $options['i'] ?>'></i>
                <span><?= $options['t'] ?></span>
            </div>
        </a>
    <? }

    if ($curCategory) { ?>
    <div class="row-fluid">&nbsp;</div>
    <div class="row-fluid">
        <table class="table table-striped texts">
            <thead>
                <th>Идентификатор</th>
                <th>Текст</th>
            </thead>
            <tbody>
                <?
                if(is_array($list))
                        foreach ($list as $key => $text) { ?>
                    <tr class="text-trigger pointer" data-key="<?=$key?>">
                        <td><strong><?=$key?></strong></td>
                        <td><div class="text"><? $text = $text->getText(); $text = reset($text); echo $text; ?></div></td>
                    </tr>
                <? } ?>
            </tbody>
        </table>
    </div>

    <? } ?>

</div>

<script>
/*
    var currentEdit = {
        id : 0,
        key : '',
        category : '<?=$curCategory;?>',
        texts : {}
    };

    $(document).on('click', '.category-trigger', function() {
        currentEdit.category = $(this).data('category');
        $('.category-trigger').removeClass('active');
        $(this).addClass('active');
    });

    $(document).on('click', '.text-trigger', function() {

        modal = $("#text-holder");
        currentEdit.texts = {};
        currentEdit.id = 0;
        currentEdit.key = '';
        currentEdit.category = '<?=$curCategory;?>';
        category = currentEdit.category;
        $('#text').code('');
        $('input[name="id"]',modal).val(0);
        $('input[name="key"]',modal).val('');

        modal.modal().find('input:visible, textarea').val('');
        modal.find('.cls').off('click').on('click', function() {
            modal.modal('hide');
        });

        $('.lang', modal).removeClass('active');
        $('.lang', modal).first().addClass('active');

        $('.category-trigger', modal).removeClass('active');
        $('.category-trigger[data-category="<?=$curCategory;?>"]', modal).addClass('active');

        if($(this).data('key')) {

            $.ajax({
                url: "/private/statictexts/" + $(this).data('key'),
                method: 'GET',
                async: true,
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        currentEdit.texts = data.data.texts;
                        currentEdit.id = data.data.id;
                        currentEdit.category = data.data.category;
                        currentEdit.key = data.data.key;
                        $('#text').code(currentEdit.texts[$('.lang.active', modal).data('lang')]);
                        $('input[name="id"]',modal).val(currentEdit.id);
                        $('input[name="key"]',modal).val(currentEdit.key);

                    } else {
                        alert(data.message);
                    }
                },
                error: function () {
                    alert('Unexpected server error');
                }
            });
        }
    });

    $(document).ready(function() {
        $('#text').summernote({height: 200});
        $('#text').code('');
    });

    $('.lang').on('click', function() {
        var prevLang = $('.lang.active').data('lang');
        var currentLang = $(this).data('lang');

        currentEdit.texts[prevLang] = $('#text').code();

        $('.lang').removeClass('active');
        $(this).addClass('active');

        if (currentEdit.texts[currentLang]) {
            $('#text').code(currentEdit.texts[currentLang]);
        } else {
            $('#text').code('');
        }
    });

    $('.save').on('click', function() {

        var currentLang = $('.lang.active').data('lang');

        $("#errorForm").hide();
        $(this).find('.glyphicon').remove();

        var prevKey=currentEdit.key;
        currentEdit.id = $('input[name="id"]').val();
        currentEdit.key = $('input[name="key"]').val();
        currentEdit.texts[currentLang] = $('#text').code();

        if (!currentEdit.id) {
            showError('Identifier can\'t be empty');
            return false;
        }

        if (!currentEdit.texts[currentLang]) {
            showError('Text can\'t be empty');
            return false;
        }

        $.ajax({
            url: "/private/statictexts/",
            method: 'POST',
            data: currentEdit,
            async: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {

                    $("#text-holder").modal('hide');
                    if(category!=currentEdit.category){
                        if((div = $('tr[data-key="'+prevKey+'"]')).length) {
                            div.remove();
                        }
                    } else {
                    text = currentEdit.texts[Object.keys(currentEdit.texts)[0]];

                    if((div = $('tr[data-key="'+prevKey+'"]')).length) {
                        div.attr('data-key',currentEdit.key).find('td strong').text(currentEdit.key).parents('tr').find('div.text').html(text);
                    } else
                        $('<tr class="text-trigger" data-key="'+currentEdit.key+'">'+
                            '<td><strong>'+currentEdit.key+'</strong></td>'+
                            '<td><div class="text">'+text+'</div></td>'+
                            '</tr>').appendTo($('table.texts tbody'));
                    }
                } else {
                    showError(data.message);
                }

            },
            error: function() {
                showError('Unexpected server error');
           }
        });
    });

    function showError(message) {
        $(".error-container").text(message);
        $("#errorForm").show();

        $('.save').removeClass('btn-success').addClass('btn-danger');
        $('.save').prepend($('<i class="glyphicon glyphicon-remove"></i>'));
    }
    */
</script>