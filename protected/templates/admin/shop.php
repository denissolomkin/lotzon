<div class="modal fade" id="deleteConfirm" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Удаление новости</h4>
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
<div class="container-fluid">
    <div class="row-fluid">
        <h2>Товары</h2>
        <hr/>
    </div>

    <div class="row-fluid form-inline">
        <div class="btn-group">

            <? $fst = true; foreach ($shop as $category) { 
                if (!$currentCategory && $fst) {
                    $currentCategory = $category->getId();
                }
                ?>
                <button onclick="document.location.href='/private/shop/category/<?=$category->getId()?>'" class="btn btn-md category-btn btn-default<?=($currentCategory == $category->getId() ? ' active' : '')?>" data-order="<?=$category->getOrder();?>"><?=$category->getName()?></button>
            <? $fst = false;} ?>
        </div> 
        <button class="btn btn-md btn-success add-category"><i class="glyphicon glyphicon-plus"></i></button>
        <button class="btn btn-md btn-warning rename-category">Изменить</button>
    </div>
    <div class="row-fluid">&nbsp;</div>

    <? foreach ($shop[$currentCategory]->getItems() as $item) { ?>
        <div class="col-md-2">
            <div class="thumbnail" data-id="<?=$item->getId()?>">
                <img src="/filestorage/shop/<?=$item->getImage()?>" alt="...">
                <div class="caption clearfix" data-title="<?=$item->getTitle()?>" data-price="<?=$item->getPrice()?>" data-quantity="<?=$item->getQuantity()?>" data-countries="<?=($item->getCountries() ? implode(" ",$item->getCountries()) : null)?>">
                    <h4><?=$item->getTitle()?></h4>
                    <div class="btn-group pull-left"> <span><?=$item->getPrice()?>/<?=($item->getQuantity() ? $item->getQuantity() : "&infin;")?>
                        <?=($item->getCountries() ? "</br><span class='label label-primary'>".implode("</span><span class='label label-primary'>",$item->getCountries())."</span>" : null)?></div>
                    <div class="btn-group pull-right">
                        <button class="btn btn-warning btn-sm item-edit"><i class="glyphicon glyphicon-edit"></i></button>
                        <!-- button class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-eye-close"></i></button -->
                        <button class="btn btn-danger btn-sm item-remove"><i class="glyphicon glyphicon-remove"></i></button>
                    </div>
                </div>            
            </div>
        </div>    
    <? } ?>
    
    <!--div class="col-md-2">
        <div class="thumbnail">
            <img src="http://hilding-anders.ru/assets/i/jensen_mpb.jpg" alt="...">
            <div class="caption">
                <input type="text" name="title" class="form-control input-sm" placeholder="Название товара" value="Норм товар" />
                <div class="form-inline" style="margin-top:10px;">
                    <input style="width:40%" type="text" name="price" class="form-control input-sm" value="4000" /> / 
                    <input style="width:30%" type="text" name="quantity" class="form-control input-sm" value="100" />
                    <button class="btn btn-sm btn-success pull-right"><i class="glyphicon glyphicon-ok"></i>
                </div>
            </div>            
        </div>
    </div-->
    <div class="col-md-2">
        <div class="thumbnail">
            <img src="/theme/admin/img/photo-icon-plus.png" class="upload" alt="click to upload" style="cursor:pointer;">
            <div class="caption">
                <input type="text" name="title" class="form-control input-sm" placeholder="Название товара" value="" />
                <div class="form-inline" style="margin-top:10px;">
                    <input style="width:40%" type="text" name="price" class="form-control input-sm" value="" placeholder="Цена" /> /
                    <input style="width:25%" type="text" name="quantity" class="form-control input-sm" value="" placeholder="К-во" />
                    <button class="btn btn-sm btn-success pull-right save"><i class="glyphicon glyphicon-ok"></i>
                </div>

                <div class="form-inline" style="margin-top:10px;">
                    <button class="btn btn-md btn-default" onclick="$('#slct-cntrs').show();$(this).hide();"><i class="glyphicon glyphicons-globe-af"></i>Страны</button>
                <select style="width:100%;display: none;" id="slct-cntrs" name="countries" size="3" multiple="multiple" class="form-control input-sm" value="" placeholder="Страны" />
                    <? foreach ($supportedCountries as $country) { ?>
                        <option value="<?=$country->getCountryCode()?>"><?=$country->getTitle()?></option>
                    <? } ?>
                </select>
                </div>
            </div>            
        </div>
    </div>
</div>
<div class="container-fluid">
    <button class="btn btn-md btn-danger pull-right remove-category"><i class="glyphicon glyphicon-remove"></i> Удалить категорию</button>
</div>
<script src="/theme/admin/lib/jquery.damnUploader.min.js"></script>

<script type="text/javascript">
    $('.add-category').on('click', showAddCategoryInput);

    function showAddCategoryInput()
    {
        var input = $('<input class="form-control input-md" value="" style="width:200px;" placeholder="Название категории">');
        var order = $('<input class="form-control input-md" value="" style="width:45px;" placeholder="№">');
        var cnlButton = $('<button class="btn btn-md btn-danger"><i class="glyphicon glyphicon-remove"></i></button>');
        var button = $(this);

        input.insertBefore($(this));
        order.insertBefore($(this));
        cnlButton.insertAfter($(this));

        $(this).find('i').removeClass('glyphicon-plus').addClass('glyphicon-ok');

        $(this).off('click').on('click', function() {
            var catName = input.val();
            var catOrder = order.val();

            if (!catName || !catOrder) {
                return false;
            }

            $.ajax({
                url: "/private/shop/addCategory",
                method: 'POST',
                data: {
                    name: catName,
                    order: catOrder
                },
                async: true,
                dataType: 'json',
                success: function(data) {
                    if (data.status == 1) {
                        document.location.href = '/private/shop/category/' + data.data.categoryId;
                    } else {
                        alert(data.message);
                    }
                }, 
                error: function() {
                    alert('Unexpected server error');
               }
            });
        });

        cnlButton.on('click', function() {
            input.remove();
            $(this).remove();

            button.find('i').removeClass('glyphicon-ok').addClass('glyphicon-plus');
            button.off('click').on('click', showAddCategoryInput);
        })
    }
    $('.rename-category').on('click', function() {
        var input = $('<input class="form-control input-md" value="'+$('.category-btn.active').text()+'" style="width:200px;" placeholder="Название категории">');
        var order = $('<input class="form-control input-md" value="'+$('.category-btn.active').data('order')+'" style="width:45px;" placeholder="№">');
        var sccButton = $('<button class="btn btn-md btn-success"><i class="glyphicon glyphicon-ok"></i></button>')
        var cnlButton = $('<button class="btn btn-md btn-danger"><i class="glyphicon glyphicon-remove"></i></button>');
        var button = $(this);

        input.insertBefore(button);
        order.insertBefore(button);
        sccButton.insertBefore(button);
        cnlButton.insertBefore(button);
        button.hide();

        cnlButton.on('click', function() {
            input.remove();
            sccButton.remove();
            cnlButton.remove();
            button.show();
        });

        sccButton.on('click', function() {
            $.ajax({
            url: "/private/shop/renameCategory",
            method: 'POST',
            data: {
                newName: input.val(),
                newOrder: order.val(),
                categoryId: '<?=$currentCategory?>'
            },
            async: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    document.location.reload();
                } else {
                    alert(data.message);
                }
            }, 
            error: function() {
                alert('Unexpected server error');
           }
        });
        });
    });

    var currentItem = {
        categoryId: '<?=$currentCategory?>',
        itemId: 0,
        image: '',
        title: '',
        price: 0,
        quantity: 0,
    };

    $('.upload').on('click', initUpload);

    function initUpload() {

        // create form
        var form = $('<form method="POST" enctype="multipart/form-data"><input type="file" name="image"/></form>');
        //$(button).parents('.photoalbum-box').prepend(form);

        var input = form.find('input[type="file"]').damnUploader({
            url: '/private/shop/uploadPhoto',
            fieldName: 'image',
            data: currentItem,
            dataType: 'json',
        });

        var image = $(this);

        input.off('du.add').on('du.add', function(e) {
            
            e.uploadItem.completeCallback = function(succ, data, status) {
                image.attr('src', data.imageWebPath);

                currentItem.image = data.imageName;
            }; 

            e.uploadItem.progressCallback = function(perc) {}

            e.uploadItem.addPostData('categoryId', currentItem.categoryId);
            e.uploadItem.addPostData('itemId', currentItem.itemId);
            e.uploadItem.upload();
        });

        form.find('input[type="file"]').click();        
    }

    $('.save').on('click', function() {
        var form = $(this).parents('.thumbnail');

        currentItem.title = $(form).find('input[name="title"]').val();
        currentItem.price = $(form).find('input[name="price"]').val();
        currentItem.quantity = $(form).find('input[name="quantity"]').val();
        currentItem.countries = $(form).find('select[name="countries"]').val();

        if (!currentItem.title) {
            alert('Название товара!');

            return false;
        }

        if (!currentItem.price) {
            alert('Цена!');

            return false;
        }

        if (!currentItem.image) {
            alert('Картинка!');

            return false;
        }

        $.ajax({
            url: "/private/shop/item",
            method: 'POST',
            data: currentItem,
            async: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    document.location.reload();
                } else {
                    alert(data.message);
                }
            }, 
            error: function() {
                alert('Unexpected server error');
           }
        });
    })

    $('.item-remove').on('click', function() {
        var id = $(this).parents('.thumbnail').data('id');

        $.ajax({
            url: "/private/shop/item",
            method: 'DELETE',
            data: {
                'itemId' : id,
            },
            async: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    document.location.reload();
                } else {
                    alert(data.message);
                }
            }, 
            error: function() {
                alert('Unexpected server error');
           }
        });
    });

    $('.remove-category').on('click', function() {
        $('#deleteConfirm').modal();
        $('#deleteConfirm').find('.btn-danger').off('click').on('click', function() {
             $.ajax({
                url: "/private/shop/deleteCategory",
                method: 'DELETE',
                data: {
                    categoryId: '<?=$currentCategory?>',
                },
                async: true,
                dataType: 'json',
                success: function(data) {
                    if (data.status == 1) {
                        $('#deleteConfirm').modal('hide')
                        document.location.href = '/private/shop';
                    } else {
                        alert(data.message);
                    }
                },
                error: function() {
                    alert('Unexpected server error');
               }
            });
        });
    });

    $('.item-edit').on('click', function() {
        var options = '';
        var title = $(this).parents('.caption').data('title');
        var price =  $(this).parents('.caption').data('price');
        var quantity = $(this).parents('.caption').data('quantity');
        var countries = $(this).parents('.caption').data('countries').split(' ');
        var supportedCountries = {
            <? foreach ($supportedCountries as $country) { ?>
            "<?=$country->getCountryCode()?>": "<?=$country->getTitle()?>",
            <? } ?>
        };

        $.each (supportedCountries, function(index, value) {
            options+='<option';
            if($.inArray(index, countries )>=0)
                options+=' selected ';
            options+=' value="'+index+'">'+value+'</option>';
        });

        var form = $('<input type="text" name="title" class="form-control input-sm" placeholder="Название товара" value="'+title+'" />' +
        '<div class="form-inline" style="margin-top:10px;"><input style="width:35%" type="text" name="price" class="form-control input-sm" value="'+price+'" /> / ' +
        '<input style="width:30%" type="text" name="quantity" class="form-control input-sm" value="'+quantity+'" />' +
        '<button class="btn btn-sm btn-success pull-right update-item"><i class="glyphicon glyphicon-ok"></i></button></div>' +
        '<div class="form-inline" style="margin-top:10px;"><select style="width:100%;" name="countries" size="3" multiple="multiple" class="form-control input-sm" value="" placeholder="Страны">' +
        options + '</select></div>');

        $(this).parents('.caption').removeClass('clearfix').html(form);

        form.find('.update-item').on('click', function() {
            var data = {
                id: $(this).parents('.thumbnail').data('id'),
                title: $(this).parents('.caption').find('input[name="title"]').val(),
                price: $(this).parents('.caption').find('input[name="price"]').val(),
                quantity: $(this).parents('.caption').find('input[name="quantity"]').val(),
                countries: $(this).parents('.caption').find('select[name="countries"]').val(),
            }
            
            $.ajax({
                url: "/private/shop/updateItem",
                method: 'POST',
                data: data,
                async: true,
                dataType: 'json',
                success: function(data) {
                    if (data.status == 1) {
                        document.location.reload();
                    } else {
                        alert(data.message);
                    }
                },
                error: function() {
                    alert('Unexpected server error');
               }
            });
        });
    })

</script>
