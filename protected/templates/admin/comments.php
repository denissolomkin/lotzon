<div class="container-fluid">
    <div class="row-fluid">
        <h2>Комментарии</h2>
        <hr />
    </div>
    <div class="row-fluid">
        <table class="table table-striped">
            <thead>
                <th>#ID</th>
                <th>Аватар</th>
                <th>Автор</th>
                <th>Текст</th>
                <th>Дата</th>
                <th>Options</th>
            </thead>
            <tbody>
                <? foreach ($comments as $comment) { ?>
                    <tr>
                        <td class="id"><?=$comment->getId()?></td>
                        <td class="title"><img src="/filestorage/avatars/comments/<?=$comment->getAvatar();?>"></td>
                        <td><a href="<?=$comment->getLink()?>" target="_blank"><?=$comment->getAuthor()?></a></td>
                        <td width="50%"><?=$comment->getText()?></td>
                        <td><?=date('d.m.Y', $comment->getDate())?></td>
                        <td>
                            <button class="btn btn-md remove-text btn-danger" onclick="location.href='/private/comments/<?=$comment->getId()?>/delete';"><i class="glyphicon glyphicon-remove"></i></button>
                        </td>
                    </tr>
                <? } ?>
            </tbody>
        </table>
    </div>  
    <div class="row-fluid">
        <h2>Добавить комментарий</h2>
        <hr />
    </div>    
    <div class="row-fluid" id="errorForm" style="display:none">
        <div class="alert alert-danger" role="alert">
          <span class="error-container"></span>
        </div>
    </div>
    <form class="form" method="POST" action="/private/comments" enctype="multipart/form-data">
        <div class="row-fluid" id="addForm">
            <div class="form-group">
                <label class="control-label">Автор (Фамилия Имя)</label>
                <input type="text" name="author" value="" placeholder="Автор" class="form-control" />
            </div>
            <div class="form-group">
                <label class="control-label">Ссылка на профиль в соцсети</label>
                <input type="text" name="link" value="" placeholder="Профиль" class="form-control" />
            </div>
            <div class="form-group">
                <label class="control-label">Аватар</label>
                <input type="file" name="avatar" value="" placeholder="Аватар" />
            </div>
            <div class="form-group">
                <label class="control-label">Дата (дд.мм.гггг)</label>
                <input type="text" name="date" value="" placeholder="Дата" class="form-control" />
            </div>
            <div class="form-group">
                <label class="control-label">Текст</label>
                <textarea class="form-control" name="text" rows="5"></textarea></div>          
            </div>
        </div>

        <div class="row-fluid">
            <button type="submit" class="btn btn-md btn-success save pull-right"> Сохранить</button>
        </div>
        <div class="row-fluid">&nbsp;</div>
        <div class="row-fluid">&nbsp;</div>
        <div class="row-fluid">&nbsp;</div>
    </form>        
</div>