
<div class="modal fade ogames" id="audio-modal" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Выбор аудио
                    <button type="button" class="btn btn-success add-audio"><i class="fa fa-plus"></i> Добавить</button></h4>
            </div>
            <div class="modal-body">
                <ul><?$openDir=opendir(dirname(__FILE__).'/../../../tpl/audio/');
                    while(($file=readdir($openDir)) !== false)
                        if($file != "." && $file != "..") {
                            echo '<li data-file="'.$file.'"><i class="fa fa-file-audio-o"> '.$file.' </i><i class="fa fa-play-circle audio-play"></i></li>';
                        }?></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<script src="/theme/admin/lib/jquery.damnUploader.min.js"></script>
<script>
$(function() {


    $('#editGame button.tab').on('click', function () {
        $("#editGame button.tab").removeClass("active");
        $("#editGame div.tab:visible").hide();
        $("#editGame div.tab#" + $(this).data("tab")).fadeIn(200);

        $(this).addClass("active");
    });

    $(document).on('click', '.remove', function () {
        if ($(this).parents('.row-prize').length)
            $(this).parent().parent().html($('<div class="empty-prize add-trigger"></div>'));
        else
            $(this).parent().remove();
    });


    $('.lang').on('click', function () {
        lang = $(this).data('lang');
        $('.lang').removeClass('active');
        $('.lang[data-lang="' + lang + '"]').addClass('active');
        $('.mui').hide();
        $('.mui[name$="[' + lang + ']"]').fadeIn(200);
    });

    $('#image').on('click', initUpload);
    function initUpload() {

        // create form
        var form = $('<form method="POST" enctype="multipart/form-data"><input type="file" name="image"/></form>');
        var image = $(this).find('img');
        var input = form.find('input[type="file"]').damnUploader({
            url: '/private/images?folder=games',
            fieldName: 'image',
            dataType: 'json'
        });

        input.off('du.add').on('du.add', function (e) {
            e.uploadItem.completeCallback = function (succ, data, status) {

                image.attr('src', 'http://<?=$_SERVER['SERVER_NAME']?>/' + data.imageWebPath + "?" + (new Date().getTime()));


                $('.game-build[data-id="' + image.parent().parent().find('input[name="game[Id]"]').val() + '"]').find('img').//$("img[src^=\"http://<?=$_SERVER['SERVER_NAME']?>/"+data.imageWebPath+"\"]").
                    attr('src', 'http://<?=$_SERVER['SERVER_NAME']?>/' + data.imageWebPath + "?" + (new Date().getTime()));
            };

            e.uploadItem.progressCallback = function (perc) {
            }
            e.uploadItem.addPostData('name', image.parent().prev().find('input').val() + '.png');
            e.uploadItem.upload();
        });

        form.find('input[type="file"]').click();
    }

    $(document).on('click', '.audio-play', function () {
        if ($(this).prop("tagName") == 'I')
            $('<audio src=""></audio>').attr('src', '../../../tpl/audio/' + $.trim($(this).parent().text())).trigger("play");
        else if ($(this).parent().prev().val()) {
            $('<audio src=""></audio>').attr('src', '../../../tpl/audio/' + $(this).parent().prev().val()).trigger("play");
        }
    });

    $('.audio-remove').on('click', function () {
        $(this).parent().prev().val('');
    });

    $('.audio-refresh').on('click', function () {
        var holder = $("#audio-modal");
        var input = $(this).parent().prev();
        holder.modal();
        $('li .fa-file-audio-o', holder).off().on('click', function () {
            input.val($.trim($(this).text()));
            holder.modal('hide');
        });
    });

    $('#audio-modal .add-audio').on('click', audioUpload);

    function audioUpload() {
        // create form
        var form = $('<form method="POST" enctype="multipart/form-data"><input type="file" name="audio"/></form>');
        var input = form.find('input[type="file"]').damnUploader({
            url: '/private/audio',
            fieldName: 'audio',
            dataType: 'json'
        });

        input.off('du.add').on('du.add', function (e) {
            e.uploadItem.completeCallback = function (succ, data, status) {
                $('#audio-modal ul li[data-file="' + data.audioName + '"]').remove();
                $('#audio-modal ul').append('<li data-file="' + data.audioName + '"><i class="fa fa-file-audio-o"> ' + data.audioName + ' </i><i class="fa fa-play-circle audio-play"></i></li>');
            };
            e.uploadItem.progressCallback = function (perc) {
            }
            e.uploadItem.upload();
        });
        form.find('input[type="file"]').click();
    };

});
</script>
