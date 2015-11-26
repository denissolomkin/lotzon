<div class="container-fluid">
    <div class="row-fluid">
        <h2>Link Redirect
        <hr />
    </div>
    <div class="row-fluid countries">
    </div>
</div>

<div class="linkRedirect">
    <div class="row-fluid">
        <form class="form form-inline col-md-12">
            <div class="input-group">
                <input class="form-control c" type="text" name="link" value="" size="100" placeholder="insert link">
            </div>
            <div class="input-group">
                <button type="button" class="btn btn-md btn-default save-link"><i class="fa fa-save"></i> Получить ссылку</button>
            </div>
        </form>
    </div>
    <br /><br />
    <div class="row-fluid col-md-12">
        <div class="input-group">
            <input class="form-control uin" type="text" name="uin" value="" size="100" placeholder="" readonly>
        </div>
    </div>
</div>

<script>
    $(document).on('click','.save-link', function() {
        var form = $(this).parents('form');
        $.ajax({
            url: "/private/linkredirect",
            method: 'POST',
            data: form.serialize(),
            async: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    $('[name="uin"]').val('<?php $result=''; if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=='on')) { $result .= 'https://'; } else { $result .= 'http://'; } $result .= $_SERVER['SERVER_NAME']; echo $result; ?>' + '/lnk/' + data.data);
                } else {
                    alert(data.message);
                }
            },
            error: function(data) {
                alert('Unexpected server error');
                console.log(data.responseText);
            }
        });
    });
    $(document).ready(function() {
        $(".form").keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                $(".save-link").click();
                return false;
            }
        });
    });
</script>
