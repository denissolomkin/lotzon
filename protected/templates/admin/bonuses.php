<div class="container-fluid">
    <div class="row-fluid">
        <h2>Бонусы</h2>
        <hr />
    </div>
    <div class="row-fluid">
        <form role="form" action="/private/bonuses" method="POST">
        <? foreach(array('bonus_email_invite','bonus_referal_invite','bonus_registration','bonus_social_profile','bonus_social_post','bonus_social_registration') as $bonus) { ?>
            <div class="form-group">
                <label for="title"><?=\StaticTextsModel::instance()->getText($bonus);?></label>
                <input type="text" class="form-control" name="bonuses[<?=$bonus?>]" value="<?=$list[$bonus]?>">
            </div>
        <? } ?>

          <button type="submit" class="btn btn-success">Сохранить</button>
        </form>
    </div>
</div>


<? if($frontend) include($frontend.'_frontend.php') ?>