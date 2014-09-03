<div class="container-fluid">
    <div class="row-fluid">
        <h2 class="heading">Администраторы</h2>
        <hr/>
    </div>
    <div class="row-fluid">
        <table  id="tableList" class="table table-striped">
            <thead>
                <tr>
                    <th>Login</th>
                    <th>Password</th>
                    <th>Role</th>
                    <th>Last login date</th>
                    <th>Last enter IP</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody>
                <? foreach ($adminsList as $admin) { ?>
                    <tr>
                        <td class="login"><?=$admin->getLogin()?></td>
                        <td title="click to edit" class="passInput" style="cursor:pointer;">************</td>
                        <td title="click to edit" class="roleInput" style="cursor:pointer;"><?=($admin->getRole() == Admin::ROLE_ADMIN ? 'Администратор' : 'Менеджер')?></td>
                        <td><?=($admin->getLastLogin() ? date('D (d/m) H:i', $admin->getLastLogin()) : '<span class="label label-warning">еще не заходил</span>')?></td>
                        <td><?=$admin->getLastLoginIp()?></td>
                        <td><button class="btn btn-md btn-danger btn-delete"><i class="glyphicon glyphicon-remove"></i></button></td>
                    </tr>
                <? } ?>
            </tbody>
        </table>
    </div>
    <div class="row-fluid">
        <button id="createAdmin" class="btn btn-success"><i class="glyphicon glyphicon-plus"></i> Добавить</button>
    </div>
</div>
<script>

$("#createAdmin").on('click', function(){
    var tableRow =  $('<tr><td><input type="text" class="form-control" placeholder="Login" name="login"></input></td><td><input class="form-control" placeholder="Password" type="password" name="password"></input></td><td><select class="form-control" name="role"><option value="<?=Admin::ROLE_ADMIN?>">Администратор</option><option value="<?=Admin::ROLE_MANAGER?>">Менеджер</option></select></td><td colspan="2" class="err-field"></td><td><button class="btn btn-md btn-success new-add"><i class="glyphicon glyphicon-ok"></i></button> &nbsp;<button class="btn btn-md btn-danger new-remove"><i class="glyphicon glyphicon-remove"></i></button></td></tr>');

    $("table tbody").append(tableRow);    
    tableRow.find('.new-remove').on('click', function() {
        $(this).parents('tr').fadeOut(300, function() {
            $(this).remove();
        });
    });
    tableRow.find('.new-add').on('click', function() {
        var admin = {
            login: tableRow.find('input[name="login"]').val(),
            password: tableRow.find('input[name="password"]').val(),
            role: tableRow.find('select[name="role"]').val(),
        }

        if (!admin.login) {
            tableRow.find('input[name="login"]').parent().addClass('has-error');
            return false;
        } else {
            tableRow.find('input[name="login"]').parent().removeClass('has-error').addClass('has-success');
        }
        if (!admin.password) {
            tableRow.find('input[name="password"]').parent().addClass('has-error');
            return false;
        } else {
            tableRow.find('input[name="password"]').parent().removeClass('has-error').addClass('has-success');
        }

        $.ajax({
            url: "/private/admins",
            method: 'POST',
            data: admin,
            async: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    tableRow.find('input[name="login"]').parent().html(admin.login).addClass('login');
                    tableRow.find('input[name="password"]').parent().html("************").addClass("passInput").attr('title', 'click to edit');
                    tableRow.find('select[name="role"]').parent().html(admin.role == '<?=Admin::ROLE_ADMIN?>' ? 'Администратор' : 'Менеджер').addClass("roleInput").attr('title', 'click to edit');
                    tableRow.find('.err-field').html('<span class="label label-success"> success </span>');    
                } else {
                    tableRow.find('.err-field').html('<span class="label label-danger"> ' + data.message + ' </span>');    
                }
            }, 
            error: function() {
                tableRow.find('.err-field').html('<span class="label label-danger"> Unexpected server error </span>');
            }
        });
    });
});

$('.passInput').on('click', tdClickInputs);
$('.roleInput').on('click', tdClickInputs);

function tdClickInputs() {
    var td = $(this);

    var inputRow = $('<div class="form-inline"><input class="form-control" type="password" value="" name="password" placeholder="New password" />&nbsp;<button class="btn btn-md btn-success"><i class="glyphicon glyphicon-ok"></i></button>&nbsp;<button class="btn btn-md btn-danger"><i class="glyphicon glyphicon-remove"></i></button></div>');
    if ($(this).hasClass('roleInput')) {
        inputRow = $('<div class="form-inline"><select class="form-control" name="role"><option value="<?=Admin::ROLE_ADMIN?>">Администратор</option><option value="<?=Admin::ROLE_MANAGER?>">Менеджер</option></select>&nbsp;<button class="btn btn-md btn-success"><i class="glyphicon glyphicon-ok"></i></button>&nbsp;<button class="btn btn-md btn-danger"><i class="glyphicon glyphicon-remove"></i></button></div>')
    }

    inputRow.on('click', function(e) {
        e.stopPropagation();
    })
    
    var cacheValue = td.html();

    td.html(inputRow);
    if (td.hasClass('passInput')) {
        inputRow.find('input').focus();    
    } else {
        inputRow.find('select').focus();
    }

    td.find('.btn-danger').on('click', function(e) {
        e.stopPropagation();

        td.html(cacheValue);
    });

    td.find('.btn-success').on('click', function(e) {
        e.stopPropagation();

        var value = td.hasClass('passInput') ? td.find('input').val() : td.find('select').val();
        var field = td.hasClass('passInput') ? 'password' : 'role';
        data = {};
        data[field] = value;
        $.ajax({
            url: "/private/admins/" + td.parent().find('.login').text(),
            method: 'PUT',
            data: data,

            async: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    if (td.hasClass('passInput')) {
                        td.html("************");
                    } else {
                        td.html(value == '<?=Admin::ROLE_ADMIN?>' ? 'Администратор' : 'Менеджер');
                    }
                } else {
                    if (!td.find('.label-danger').length) {
                        td.append($('<span class="label label-danger"> ' + data.message + ' </span>'));    
                    } else {
                        td.find('.label-danger').text(" Unexpected server error ");
                    }
                }
            }, 
            error: function() {
                if (!td.find('.label-danger').length) {
                    td.append($('<span class="label label-danger"> Unexpected server error </span>'));    
                } else {
                    td.find('.label-danger').text(" Unexpected server error ");
                }
            }
        });

    })
}

$(".btn-delete").on("click", function() {
    var button = $(this);
    $.ajax({
        url: "/private/admins/" + $(this).parents('tr').find('.login').text(),
        method: 'DELETE',

        async: true,
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                button.parents('tr').fadeOut('300', function() {
                    $(this).remove();
                });
            } else {
                alert(data.message);
            }
        }, 
        error: function() {
            alert('Unexpected server error');
       }
    });
});
</script>