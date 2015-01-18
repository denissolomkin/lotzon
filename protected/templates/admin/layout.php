<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=$title?></title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="/theme/admin/bootstrap/css/bootstrap.min.css">

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    
    <!-- Include Summernote CSS files -->
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
    
    <link href="/theme/admin/lib/summernote/summernote.css" rel="stylesheet">
    <link href="/theme/admin/lib/admin.css" rel="stylesheet">

    <!-- Include Summernote JS file -->
    <script src="/theme/admin/lib/summernote/summernote.js"></script>

  </head>
  <body style="">
      <div class="container-fluid text-center">
      <div class="row-fluid">&nbsp;</div>
        <div class="row-fluid">
            <ul class="nav nav-pills" role="tablist">

                <? foreach(Admin::$PAGES as $key=>$pages) : ?>
                    <? if(is_array($pages['pages'])) {
                        $menu=array();
                        foreach($pages['pages'] as $link=>$page)
                            if(Config::instance()->rights[Session2::connect()->get(Admin::SESSION_VAR)->getRole()][$link]) :
                                $menu[]='
                                <li'.($activeMenu == $link ? ' class="active"' : '').'><a href="/private/'.$link.'">'.
                                    (isset($page['icon'])?'<span class="glyphicon glyphicon-'.$page['icon'].'" aria-hidden="true"></span> ':'').
                                    ($page['name']?:'').'</a></li>';

                            endif ?>

                <? if(count($menu))
                            echo'
                        <li>
                            <div class="btn-group">
                                <button type="button" class="btn btn-info" style="padding: 9px 12px;" data-toggle="dropdown" aria-expanded="false">'.
                                (isset($pages['icon'])? '<span class="glyphicon glyphicon-'.$pages['icon'].'" aria-hidden="true"></span> ':'').
                                $key.' <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                '.implode('',$menu).'
                                </ul>
                            </div>
                        </li>';
                    } elseif(Config::instance()->rights[Session2::connect()->get(Admin::SESSION_VAR)->getRole()][$key]) { ?>

                        <li class="<?=($activeMenu == $key ? 'active ' : '')?><?=$pages['css']?:''?>"><a href="/private/<?=$key?>">
                                <? if($pages['icon']): ?> <span class="glyphicon glyphicon-<?=$pages['icon']?>" aria-hidden="true"></span>  <? endif ?>
                                <?=($pages['name']?:'')?>
                                <? if ($key=='users') : ?><span class="label label-warning"><?=PlayersModel::instance()->getProcessor()->getPlayersCount()?></span> <? endif ?>
                                <? if ($key=='reviews') : ?><span class="label label-warning"><?=ReviewsModel::instance()->getProcessor()->getCount(0)?></span> <? endif ?>
                                <? if ($key=='monetisation') : ?><span class="label label-warning"><?=ShopOrdersModel::instance()->getProcessor()->getOrdersToProcessCount()?> / <?=MoneyOrderModel::instance()->getProcessor()->getOrdersToProcessCount()?></span> <? endif ?>
                            </a></li>
                <? } ?>
                <? endforeach ?>
                <!--li><a href="/private/stats">Статистика</a></li-->



            </ul>
        </div>
      </div>
      <?=$yield?>
    <!-- Latest compiled and minified JavaScript -->
    <script src="/theme/admin/bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>
