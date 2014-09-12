<div class="container-fluid">
    <div class="row-fluid">
        <h2>Товары</h2>
        <hr/>
    </div>

    <div class="row-fluid form-inline">
        <div class="btn-group">
            <button class="btn btn-md btn-default active">Популярные</button>
            <button class="btn btn-md btn-default">Продтовары</button>
            <button class="btn btn-md btn-default">Техника</button>
            <button class="btn btn-md btn-default">Услуги</button>
        </div> 
        <button class="btn btn-md btn-success add-category"><i class="glyphicon glyphicon-plus"></i></button>
    </div>
    <div class="row-fluid">&nbsp;</div>
    <div class="col-md-2">
        <div class="thumbnail">
            <img src="http://hilding-anders.ru/assets/i/jensen_mpb.jpg" alt="...">
            <div class="caption clearfix">
                <h4>Название товара</h4>
                <span>4000/&infin;</span>
                <div class="btn-group pull-right">
                    <button class="btn btn-warning btn-sm"><i class="glyphicon glyphicon-edit"></i></button>
                    <button class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-eye-close"></i></button>
                    <button class="btn btn-danger btn-sm"><i class="glyphicon glyphicon-remove"></i></button>
                </div>
            </div>            
        </div>
    </div>
    <div class="col-md-2">
        <div class="thumbnail">
            <img src="http://hilding-anders.ru/assets/i/jensen_mpb.jpg" alt="...">
            <div class="caption">
                <input type="text" name="title" class="form-control input-sm" placeholder="Название товара" value="Норм товар" />
                <div class="form-inline" style="margin-top:10px;">
                    <input style="width:40%" type="text" name="price" class="form-control input-sm" value="4000" /> / 
                    <input style="width:30%" type="text" name="price" class="form-control input-sm" value="100" />
                    <button class="btn btn-sm btn-success pull-right"><i class="glyphicon glyphicon-ok"></i>
                </div>
            </div>            
        </div>
    </div>
    <div class="col-md-2">
        <div class="thumbnail">
            <img src="/theme/admin/img/photo-icon-plus.png" alt="...">
            <div class="caption">
                <input type="text" name="title" class="form-control input-sm" placeholder="Название товара" value="" />
                <div class="form-inline" style="margin-top:10px;">
                    <input style="width:40%" type="text" name="price" class="form-control input-sm" value="" placeholder="Цена" /> / 
                    <input style="width:30%" type="text" name="price" class="form-control input-sm" value="" placeholder="К-во" />
                    <button class="btn btn-sm btn-success pull-right"><i class="glyphicon glyphicon-ok"></i>
                </div>
            </div>            
        </div>
    </div>
</div>

<script type="text/javascript">
    $('.add-category').on('click', showAddCategoryInput);

    function showAddCategoryInput()
    {
        var input = $('<input class="form-control input-md" value="" style="width:200px;" placeholder="Название категории">');
        var cnlButton = $('<button class="btn btn-md btn-danger"><i class="glyphicon glyphicon-remove"></i></button>');
        input.insertBefore($(this));
        cnlButton.insertAfter($(this));

        $(this).find('i').removeClass('glyphicon-plus').addClass('glyphicon-ok');

        $(this).off('click');
    }
</script>