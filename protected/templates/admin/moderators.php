<div class="container-fluid">
    <div class="row-fluid">
        <h2><?php echo $title;?></h2>
        <hr />
    </div>

    <div class="row-fluid">
        <form role="form" action="/private/<?php echo $activeMenu;?>" method="POST">
            <div class="form-group">
                <label for="title">Пользователи (через запятую)</label>
                <textarea oninput="this.value=this.value.replace(/[a-z -]|[\r\n]|[\.\/\\\+\=\[\]\`\;\:]|[а-я]+/g, '');" type="text" class="form-control" name="<?php echo $activeMenu;?>" ><?=is_array($list)?implode(',',$list):''?></textarea>
            </div>
            <button type="submit" class="btn btn-success">Сохранить</button>
        </form>
    </div>
</div>