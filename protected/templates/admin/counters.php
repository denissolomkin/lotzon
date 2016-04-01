<div class="container-fluid">
    <form role="form" action="/private/counters" method="POST">
    <div class="row-fluid">
        <h2>Счетчики <button type="submit" class="right btn btn-success">Сохранить</button>
           </h2>
        <hr />
    </div>

    <div class="row-fluid">
            <? foreach($counters as $index => $title) {

                if(is_numeric($index)) {echo "</div><div class='col-md-3 row-banner'><h1>$title</h1>";} else {?>
                <div class="form-group">
                    <label for="title"><?=$title;?></label>
                    <input type="text" class="form-control" name="counters[<?=$index?>]" value="<?=$list[$index]?>">
                </div>
            <? }
            } ?>
    </div>
    </form>
</div>


<? if($frontend) include($frontend.'_frontend.php') ?>