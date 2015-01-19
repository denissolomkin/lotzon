<div class="container-fluid">
    <form role="form" action="/private/gamebots" method="POST">
        <div class="row-fluid">
            <h2>Bots
                <button type="submit" class="btn btn-success right">Сохранить</button>
                <button type="button" data-sector="<?=$key?>" class="btn btn-success add-one"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button>
            </h2>
            <hr />
        </div>
        <div class="row-fluid" id="bots">
            <? $used_ids=array();
            if(is_array($list))
                foreach ($list as $key=>$bot) :
                    $used_ids[]=$key; ?>


            <div class="col-md-2">
                <div class="thumbnail" data-id="<?=$key?>">
                    <img src='<?=($bot['avatar']?'../filestorage/avatars/'.(ceil($key / 100)) . '/'.$bot['avatar']:'/theme/admin/img/photo-icon-plus.png')?>' data-id="<?=$key?>" data-image="<?=$bot['avatar']?>" class="upload" alt="...">
                </div>
                <div class="flex">
                    <button type="button" data-sector="<?=$key?>" class="btn btn-danger del-one"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>
                    <input placeholder="Name" class="form-control input-md" name=bots[<?=$key?>][name] value="<?=$bot['name'];?>"</input>
                    <input type="hidden" name="bots[<?=$key?>][id]" value="<?=$key?>">
                    <input type="hidden" name="bots[<?=$key?>][bot]" value="1">
                    <input type="hidden" name="bots[<?=$key?>][avatar]" value="<?=$bot['avatar']?>">
                </div>

            </div>

            <? endforeach ?>
        </div>
    </form>
</div>

<script src="/theme/admin/lib/jquery.damnUploader.min.js"></script>
<script>

    var ids=new Array(<?=implode(",", array_diff(
    array(18,406,410,536,590,598,622,977,1235,1545,1572,1699,1709,2377,2440,2494,2547,3286,3469,4607,4783,5170,5172,5344,6163,6739,6745,6798,7755,7950,8127,8987,8991,8994,9001,9057,9129,9135,10438,11389,11398,11419,11472,11508,11547,11555,12159,12247,12662,12697,12841,12911,13378,3863,5222,2488,4617,2356,5928,6030,2538,12216,12743,13294,5794,11529,12831,14050,8137,6731,6855,13283,4913,6453,5242,5291,7057,4789,5001,12660,12197,12715,8960,9734,12221,12525,6255,7001,6913,9139,6571,12785,13126,13756,11346,12811,11154,12241,12207,12202,9326,9004,11425,8998,9021,9053,13168,13889,10966,9565,10976,9732,12528,11412,11432,10959,11161,11010,10843,10907,11164,11379,11438,12482,12618,13027,11368,12205,10795,10856,12754,11517,12212,11354,11361,11449,11514,11444,11461,12703,12554,12586,13670,13245,13142,13929),
    $used_ids)); ?>);

    $( ".add-one" ).on( "click", function( event ) {
        var id=$(this).data('sector');
        var cnt = ids.shift();//$('#'+id).children().last().data('id')+1;



        $("#bots").append('<div class="col-md-2">' +
        '<div class="thumbnail" data-id="'+cnt+'">' +
        '<img src="/theme/admin/img/photo-icon-plus.png" class="upload" data-id="'+cnt+'" data-image="" alt="...">' +
        '<input type="hidden" name="bots['+cnt+'][id]" value="'+cnt+'">' +
        '<input type="hidden" name="bots['+cnt+'][bot]" value="1">' +
        '<input type="hidden" name="bots['+cnt+'][avatar]" value="">' +
        '</div>' +
        '' +
        '<div class="flex">' +
        '<button type="button" class="btn btn-danger del-one"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>' +
        '<input placeholder="Name" class="form-control input-md" name=bots['+cnt+'][name] value="Участник '+cnt+'"</input>' +
        '</div>' +
        '' +
        '</div>');

        $('.upload').off('click').on('click', initUpload);
    });

    $(document ).on( "click",".del-one", function( event ) {
        $(this).parent().parent().remove();
    });



    $('.upload').on('click', initUpload);

    function initUpload() {

        // create form
        var form = $('<form method="POST" enctype="multipart/form-data"><input type="file" name="image"/></form>');
        var image = $(this);
        var input = form.find('input[type="file"]').damnUploader({
            url: '/private/gamebots/uploadPhoto',
            fieldName: 'image',
            dataType: 'json'
        });

        input.off('du.add').on('du.add', function(e) {

            e.uploadItem.completeCallback = function(succ, data, status) {
                image.attr('src', data.imageWebPath+"?"+(new Date().getTime()));
                image.attr('data-image', data.imageName);
                $('input[name="bots['+image.attr('data-id')+'][avatar]"]').val(data.imageName);
            };

            e.uploadItem.progressCallback = function(perc) {}

            e.uploadItem.addPostData('imageName', image.attr('data-image'));
            e.uploadItem.addPostData('Id', image.attr('data-id'));
            e.uploadItem.upload();
        });

        form.find('input[type="file"]').click();
    }
    /*
     $( "form" ).on( "submit", function( event ) {

     event.preventDefault();
     $.ajax({
     url: "/private/banners/",
     method: 'POST',
     data: $( this ).serialize(),
     async: true,
     dataType: 'json',
     success: function(data) {
     console.log(data)
     },
     error: function() {

     }
     });

     });
     */
</script>