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
                <li<?=($activeMenu == 'users' ? ' class="active"' : '')?>><a href="/private/users"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Пользователи</a></li>
                <li<?=($activeMenu == 'reviews' ? ' class="active"' : '')?>><a href="/private/reviews"><span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span> Отзывы <span class="label label-warning"><?=ReviewsModel::instance()->getProcessor()->getCount(0)?></span></a></li>
                <li<?=($activeMenu == 'comments' ? ' class="active"' : '')?>><a href="/private/comments"><span class="glyphicon glyphicon-comment" aria-hidden="true"></span> Комментарии</a></li>
                <li<?=($activeMenu == 'news' ? ' class="active"' : '')?>><a href="/private/news"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Новости</a></li>
                <!--li><a href="/private/banners">Баннеры</a></li-->
                <li<?=($activeMenu == 'shop' ? ' class="active"' : '')?>><a href="/private/shop"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Товары</a></li>
                <li<?=($activeMenu == 'monetisation' ? ' class="active"' : '')?>><a href="/private/monetisation">Запросы <span class="label label-warning"><?=ShopOrdersModel::instance()->getProcessor()->getOrdersToProcessCount()?> / <?=MoneyOrderModel::instance()->getProcessor()->getOrdersToProcessCount()?></span></a></li>
                <!--li><a href="/private/stats">Статистика</a></li-->

                <li>
                    <div class="btn-group">
                        <button type="button" class="btn btn-info" style='padding: 9px 12px;' data-toggle="dropdown" aria-expanded="false">
                            <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Настройки <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li<?=($activeMenu == 'game' ? ' class="active"' : '')?>><a href="/private/game"><span class="glyphicon glyphicon-gift" aria-hidden="true"></span> Розыгрыши</a></li>
                            <li <?=($activeMenu == 'chances' ? ' class="active"' : '')?>><a href="/private/chances"><span class="glyphicon glyphicon-star-empty" aria-hidden="true"></span> Шансы</a></li>
                            <li <?=($activeMenu == 'seo' ? ' class="active"' : '')?>><a href="/private/seo"><span class="glyphicon glyphicon-screenshot" aria-hidden="true"></span> SEO</a></li>
                            <li<?=($activeMenu == 'texts' ? ' class="active"' : '')?>><a href="/private/texts"><span class="glyphicon glyphicon-globe" aria-hidden="true"></span> Тексты</a></li>
                        </ul>
                    </div>
                </li>
                <!--li><a href="/private/ogames">Онлайн игры</a></li-->
                <!--li ><a href="/private/subscribes">Заявки</a></li-->

                <!--li><a href="/private/ogames">Онлайн игры</a></li-->
                <!--li <?=($activeMenu == 'subscribes' ? ' class="active"' : '')?>><a href="/private/subscribes">Заявки</a></li-->
                <li class="pull-right"><a class="glyphicon glyphicon-off" href="/private/logout"></a></li>
                <li class="<?=($activeMenu == 'admins' ? ' active ' : '')?>pull-right"><a href="/private/admins">Администраторы</a></li>
            </ul>
        </div>
      </div>
      <?=$yield?>
    <!-- Latest compiled and minified JavaScript -->
    <script src="/theme/admin/bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>
