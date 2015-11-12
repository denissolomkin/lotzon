<div class="container-fluid">
    <div class="row-fluid">
        <h2>Email рассылка > Шаблоны</h2>
        <hr />
    </div>
    <div class="row-fluid">
        <div class="btn-group">
            <button onclick="document.location.href='/private/maillist/tasks'" type="button" class="btn btn-md lang btn-default" data-lang="">Задания</button>
            <button onclick="document.location.href='/private/maillist/messages'" type="button" class="btn btn-md lang btn-default active" data-lang="">Шаблоны</button>
        </div>
        <button class="btn btn-md btn-success text-trigger" data-key="0"><i class="glyphicon glyphicon-plus"></i> Добавить</button>
    </div>
    <div class="row-fluid">&nbsp;</div>
    <div class="row-fluid">
        <table class="table table-striped">
            <thead>
            <th>#ID</th>
            <th>Описание</th>
            <th>Options</th>
            </thead>
            <tbody>
            <? foreach ($messages as $key=>$message) { ?>
                <tr>
                    <td class="id"><?=$message->getId()?></td>
                    <td width="80%" class="title"><strong><?=$message->getDescription()?></strong></td>
                    <td>
                        <button class="btn btn-md edit-text btn-warning text-trigger" data-key="<?=$key?>"><i class="glyphicon glyphicon-edit"></i></button>
                        <button class="btn btn-md remove-text btn-danger" data-target="#deleteConfirm"><i class="glyphicon glyphicon-remove"></i></button>
                    </td>
                </tr>
            <? } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ========================== DELETE ========================== -->
<div class="modal fade" id="deleteConfirm" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Удаление шаблона</h4>
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
<script>
    $('.remove-text').on('click', function() {
        var row = $(this).parents('tr');
        var identifier = row.find('td.id').text();
        $('#deleteConfirm').modal();
        $('#deleteConfirm').find('.btn-danger').off('click').on('click', function() {
            $.ajax({
                url: "/private/maillist/messages/" + identifier,
                method: 'DELETE',
                data: {},
                async: true,
                dataType: 'json',
                success: function(data) {
                    if (data.status == 1) {
                        $('#deleteConfirm').modal('hide');
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
</script>
<!-- ========================= /DELETE ========================== -->

<!-- =========================== EDIT =========================== -->
<div class="modal fade texts" id="text-holder" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Редактирование шаблона</h4>
                <div class="row-fluid" id="errorForm" style="display:none">
                    <div class="alert alert-danger" role="alert">
                        <span class="error-container"></span>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="row-fluid" id="addForm">
                    <form class="form">
                        <input name="id" type="hidden">
                        <div class="form-group">
                            <label class="control-label">Описание</label>
                            <input type="text" name="description" value="" placeholder="Описание" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label class="control-label">Отправлять от</label>
                            <select class="form-control" name="from">
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Язык для отправки по умолчанию</label>
                            <select class="form-control" name="defaultLanguage">
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">html-шаблон</label>
                            <select class="form-control" name="templateId">
                            </select>
                        </div>
                        <div class="form-group">
                            <div id="preview"></div>
                        </div>
                    </form>
                </div>

                <div class="row-fluid">
                    <div class="btn-group">
                        <? $fst = true; ?>
                        <? foreach (\LanguagesModel::instance()->getList() as $lang) { ?>
                            <button type="button" class="btn btn-md lang btn-default<?=($fst ? ' active' : '')?>" data-lang="<?=$lang->getCode()?>"> <?=strtoupper($lang->getCode())?><span class="badge"></span></button>
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
        id          : 0,
        description : '',
        templateId  : '',
        values      : {},
        settings    : {},
        template    : {},
        preview     : ''
    };

    function getTemplate() {
        $.ajax({
            url: "/private/maillist/template/" + currentEdit.templateId,
            method: 'GET',
            async: true,
            dataType: 'json',
            success: function (data) {
                if (data.status == 1) {
                    currentEdit.template = data.data.template ? data.data.template : {};
                    currentEdit.preview  = data.data.preview;
                    drawPreview();
                }
            }
        });
    }

    function recountLanguage() {
        all = Object.keys(currentEdit.template.Variables).length;
        <? foreach (\LanguagesModel::instance()->getList() as $lang) { ?>
            count = Object.keys(currentEdit.values['<?=$lang->getCode()?>']).length;
            if (count>0)
                $('.lang[data-lang="<?=$lang->getCode()?>"] .badge').html(count+" / "+all);
            else
                $('.lang[data-lang="<?=$lang->getCode()?>"] .badge').html("");
        <? } ?>
    }

    /**
     * Draw template preview with values of selected language
     */
    function drawPreview() {
        preview = currentEdit.preview;
        for(var variable in currentEdit.template.Variables) {
            if(currentEdit.values[$('.lang.active', modal).data('lang')][variable])
                value = currentEdit.values[$('.lang.active', modal).data('lang')][variable];
            else
                value = '';

            if(currentEdit.template.Variables[variable].default)
                if(currentEdit.template.Variables[variable].default[$('.lang.active', modal).data('lang')])
                    defaultValue = currentEdit.template.Variables[variable].default[$('.lang.active', modal).data('lang')];
                else
                    defaultValue = '%'+variable+'%';
            else
                defaultValue = '%'+variable+'%';

            preview = preview.replace('%'+variable+'%', '<span href="#" id="'+variable+'" data-unsavedclass="test" style="cursor:pointer;" data-send="never" data-type="'+currentEdit.template.Variables[variable].type+'" data-pk="1" data-url="/post" data-emptytext="'+defaultValue+'" data-title="'+currentEdit.template.Variables[variable].description+'">'+value+'</span>');
            preview = preview + '<scr'+'ipt> $(document).ready(function() { $("#'+variable+'").editable( { success: function(response, newValue) { if (newValue) currentEdit.values[$(".lang.active", modal).data("lang")]["'+variable+'"] = newValue; else delete currentEdit.values[$(".lang.active", modal).data("lang")]["'+variable+'"]; recountLanguage(); } }  ); });</scr'+'ipt>';
        }
        $('#preview', modal).html(preview);
        recountLanguage();
    }

    $('select[name="templateId"]').on('change', function() {
        /* add old template values to array*/
        currentEdit.valuesarr[currentEdit.templateId] = currentEdit.values;

        /* new template */
        currentEdit.templateId = $('select[name="templateId"]').val();

        if (!currentEdit.valuesarr[currentEdit.templateId]) {
            currentEdit.values = {}
        } else {
            currentEdit.values = currentEdit.valuesarr[currentEdit.templateId];
        }

        /* add not set language array to values */
        <? foreach (\LanguagesModel::instance()->getList() as $lang) { ?>
        if (!currentEdit.values["<?=$lang->getCode()?>"])
            currentEdit.values["<?=$lang->getCode()?>"] = {};
        <? } ?>

        getTemplate();
    });

    $(document).on('click', '.text-trigger', function() {
        currentEdit.id          = 0;
        currentEdit.description = '';
        currentEdit.templateId  = '';
        currentEdit.values      = {};
        currentEdit.settings    = {};
        currentEdit.templates   = {};
        currentEdit.template    = {};
        currentEdit.preview     = '';

        /**
         *  array of values for all templates
         *  use for saving values when change template
         */
        currentEdit.valuesarr   = {};

        modal = $("#text-holder");
        modal.modal();
        $('.cls', modal).off('click').on('click', function() {modal.modal('hide');});

        $('.lang', modal).removeClass('active');
        $('.category-trigger', modal).removeClass('active');

        /* language switcher init */
        if(lang=$('.multilanguage .flag.active').data('lang'))
            $('.lang[data-lang="'+lang+'"]', modal).addClass('active');
        else
            $('.lang', modal).first().addClass('active');

        if($(this).attr('data-key')) {
            $.ajax({
                url: "/private/maillist/messages/" + $(this).attr('data-key'),
                method: 'GET',
                async: true,
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        currentEdit.id          = data.data.message.Id;
                        currentEdit.description = data.data.message.Description;
                        currentEdit.templateId  = data.data.message.TemplateId;
                        currentEdit.values      = data.data.message.Values ? data.data.message.Values : {};
                        currentEdit.settings    = data.data.message.Settings ? data.data.message.Settings : {};
                        currentEdit.templates   = data.data.templates ? data.data.templates : {};

                        $('input[name="description"]', modal).val(currentEdit.description);

                        /* forming select of from  */
                        $('select[name="from"]', modal).empty();
                        <? foreach (\Config::instance()->mailServers as $key=>$value) { ?>
                            $('select[name="from"]', modal).append($('<option value="<?=$key?>"><?=$key?></option>'));
                        <? } ?>
                        if (currentEdit.settings.from) {
                            $('select[name="from"] [value="'+currentEdit.settings.from+'"]').attr("selected", "selected");
                        } else {
                            $('select[name="from"] [value="<?=\Config::instance()->defaultMailServer?>"]').attr("selected", "selected");
                        }

                        /* forming select of defaultLanguages  */
                        $('select[name="defaultLanguage"]', modal).empty();
                        $('select[name="defaultLanguage"]', modal).append($('<option value="">не отправлять с несовпадающим языком</option>'));
                        <? foreach (\LanguagesModel::instance()->getList() as $lang) { ?>
                            $('select[name="defaultLanguage"]', modal).append($('<option value="<?=$lang->getCode()?>"><?=$lang->getCode()?></option>'));
                        <? } ?>
                        if (currentEdit.settings.defaultLanguage) {
                            $('select[name="defaultLanguage"] [value="'+currentEdit.settings.defaultLanguage+'"]').attr("selected", "selected");
                        }

                        /* forming select of templates  */
                        $('select[name="templateId"]', modal).empty();
                        for(key in currentEdit.templates) {
                            template = currentEdit.templates[key];
                            $('select[name="templateId"]', modal).append($('<option value="'+template['Id']+'">'+template['Description']+'</option>'));
                        }
                        $('select[name="templateId"] [value="'+currentEdit.templateId+'"]').attr("selected", "selected");
                        $('select[name="templateId"]').change();
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

    $('.save').on('click', function() {
        if (!$('input[name="description"]').val()) {
            showError('Описание не может быть пустым');
            return false;
        }

        description = $('input[name="description"]', modal).val();
        settings = {
            defaultLanguage: $('select[name="defaultLanguage"]').val(),
            from: $('select[name="from"]').val()
        };

        $.ajax({
            url: "/private/maillist/messages",
            method: 'POST',
            data:
                {
                    id:          currentEdit.id,
                    description: description,
                    templateId:  currentEdit.templateId,
                    values:      currentEdit.values,
                    settings:    settings
                },
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

    /* click language switcher */
    $('#text-holder .lang').on('click', function() {
        var prevLang = $('.lang.active').data('lang');
        var currentLang = $(this).data('lang');

        $('.lang').removeClass('active');
        $(this).addClass('active');

        drawPreview();
    });

    function showError(message) {
        $(".error-container").text(message);
        $("#errorForm").show();

        $('.save').removeClass('btn-success').addClass('btn-danger');
        $('.save').prepend($('<i class="glyphicon glyphicon-remove"></i>'));
    }
</script>
<!-- ========================== /EDIT =========================== -->
