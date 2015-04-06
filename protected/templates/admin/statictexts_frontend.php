<? $categories = array(
    'menu'=>array('t'=>'Меню и навигация', 'i'=>'sitemap'),
    'button'=>array('t'=>'Кнопки', 'i'=>'hand-o-up'),
    'text'=>array('t'=>'Тексты, описания', 'i'=>'paragraph'),
    'popup'=>array('t'=>'Всплывающие сообщения', 'i'=>'comments'),
    'error'=>array('t'=>'Ошибки, предупре- ждения', 'i'=>'exclamation-triangle'),
    'seo'=>array('t'=>'SEO', 'i'=>'crosshairs'),
    'bonus'=>array('t'=>'Бонусы и выигрыши', 'i'=>'gift'),
    'promo'=>array('t'=>'Промо-страница', 'i'=>'home'),
    'holder'=>array('t'=>'Холдеры', 'i'=>'terminal'),
    'title'=>array('t'=>'Титулы', 'i'=>'terminal'),
    'placeholder'=>array('t'=>'Поля', 'i'=>'terminal'),
    'promo'=>array('t'=>'Тексты на промо', 'i'=>'home'),
); ?>
        <div class="modal fade texts" id="text-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="confirmLabel">Edit Text</h4>
                    </div>

                    <div class="modal-body">
                        <div class="row-fluid" id="addForm">
                            <form class="form">
                                <input name="id" type="hidden">
                                <div>
                                    <? foreach ($categories as $category => $options) { ?>
                                        <div data-category="<?=$category;?>" class="metal-gradient pointer category-trigger xs">
                                            <i class='fa fa-<?= $options['i'] ?>'></i>
                                            <span><?= $options['t'] ?></span>
                                        </div>
                                    <? } ?>
                                </div>
                                <div class="row-fluid clear">&nbsp;</div>
                                <div class="form-group">
                                    <label class="control-label">Идентификатор</label>
                                    <input type="text" name="key" value="" placeholder="Идентификатор" class="form-control" />
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
                                <? foreach (\CountriesModel::instance()->getLangs() as $lang) { ?>
                                    <button type="button" class="btn btn-md lang btn-default<?=($fst ? ' active' : '')?>" data-lang="<?=$lang?>"><?=strtoupper($lang)?></button>
                                    <? $fst = false;} ?>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success save">Сохранить</button>
                        <button type="button" class="btn btn-default cls">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
<script>
    var currentEdit = {
        id : 0,
        key : '',
        category : '',
        texts : {}
    };

    $(document).on('click', '#text-holder .category-trigger', function() {
        currentEdit.category = $(this).data('category');
        $('.category-trigger').removeClass('active');
        $(this).addClass('active');
    });

    $(document).on('click', '.text-trigger', function() {

        modal = $("#text-holder");
        currentEdit.texts = {};
        currentEdit.id = 0;
        currentEdit.key = '';
        category = currentEdit.category = '';

        modal.modal();
        $('.cls', modal).off('click').on('click', function() {modal.modal('hide');});

        $('#text', modal).code('');
        $('input[name="id"]',modal).val(0);
        $('input[name="key"]',modal).val('');
        if($(this).hasClass('debug'))
            $('input[name="key"]',modal).attr('disabled','disabled');
        $('input:visible, textarea', modal).val('');
        $('.lang', modal).removeClass('active');
        $('.category-trigger', modal).removeClass('active');

        if(lang=$('.multilanguage .flag.active').data('lang'))
            $('.lang[data-lang="'+lang+'"]', modal).addClass('active');
        else
            $('.lang', modal).first().addClass('active');

        if($(this).attr('data-key')) {

            $.ajax({
                url: "/private/statictexts/" + $(this).attr('data-key'),
                method: 'GET',
                async: true,
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        currentEdit.texts = data.data.texts ? data.data.texts : {};
                        currentEdit.id = data.data.id;
                        currentEdit.key = data.data.key;
                        category = currentEdit.category = data.data.category
                            ? data.data.category
                            :((((cat = (data.data.key).split('-')) && cat.length>1) ||
                            ((cat = (data.data.key).split('_')) && cat.length>1)) ? cat[0] : '');

                        $('.category-trigger[data-category="'+currentEdit.category+'"]', modal).addClass('active');
                        $('input[name="key"]',modal).val(currentEdit.key);

                        if(currentEdit.texts && currentEdit.texts[$('.lang.active', modal).data('lang')])
                            $('#text').code(currentEdit.texts[$('.lang.active', modal).data('lang')]);

                        if(currentEdit.id)
                            $('input[name="id"]',modal).val(currentEdit.id);

                    } else {
                        alert(data.message);
                    }
                },
                error: function () {
                    alert('Unexpected server error');
                }
            });
        }
        return false;
    });

    $(document).ready(function() {
        $('#text').summernote({height: 200});
        $('#text').code('');
    });

    $('#text-holder .lang').on('click', function() {
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

    $('#text-holder .save').on('click', function() {

        modal = $("#text-holder");
        var currentLang = $('.lang.active').data('lang');

        $("#errorForm").hide();
        $(this).find('.glyphicon').remove();

        var prevKey=currentEdit.key;
        currentEdit.id = $('input[name="id"]',modal).val();
        currentEdit.category = $('.category-trigger.active',modal).data('category');
        currentEdit.key = $('input[name="key"]', modal).val();
        currentEdit.texts[currentLang] = $('#text',modal).code();

        if (!currentEdit.id) {
            showError('Identifier can\'t be empty');
            return false;
        }

        if (!currentEdit.category) {
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

                    if(category && category!=currentEdit.category && (holder = $('tr[data-key="'+prevKey+'"]')).length){
                        holder.remove();
                        return;
                    }

                    text = currentEdit.texts[lang?lang:Object.keys(currentEdit.texts)[0]];

                    if ((holder = $('tr[data-key="' + prevKey + '"]')).length || (holder = $('tr[data-key="' + currentEdit.key + '"]')).length) {
                        holder.attr('data-key', currentEdit.key).find('td strong').text(currentEdit.key).parents('tr').find('div.text').html(text);
                    } else if ((holder=$('table.texts tbody')).length) {
                        $('<tr class="text-trigger pointer" data-key="' + currentEdit.key + '">' +
                        '<td><strong>' + currentEdit.key + '</strong></td>' +
                        '<td><div class="text">' + text + '</div></td>' +
                        '</tr>').appendTo(holder);
                    } else if ((holder = $('.text-trigger[data-key="' + prevKey + '"]')).length){
                        holder.attr('data-key', currentEdit.key).html(text);
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
</script>