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

    <!-- Include Summernote JS file -->
    <script src="/theme/admin/lib/summernote/summernote.js"></script>

  </head>
  <body style="">
      <div class="container-fluid text-center">
      <div class="row-fluid">&nbsp;</div>
        <div class="row-fluid">
          <ul class="nav nav-pills" role="tablist">
            <li<?=($activeMenu == 'game' ? ' class="active"' : '')?>><a href="/private/game">Розыгрыши</a></li>
            <li><a href="/private/users">Пользователи</a></li>
            <li<?=($activeMenu == 'comments' ? ' class="active"' : '')?>><a href="/private/comments">Комментарии</a></li>
            <li<?=($activeMenu == 'texts' ? ' class="active"' : '')?>><a href="/private/texts">Тексты</a></li>
            <li<?=($activeMenu == 'news' ? ' class="active"' : '')?>><a href="/private/news">Новости</a></li>
            <li><a href="/private/banners">Баннеры</a></li>
            <li<?=($activeMenu == 'shop' ? ' class="active"' : '')?>><a href="/private/shop">Товары</a></li>
            <li<?=($activeMenu == 'monetisation' ? ' class="active"' : '')?>><a href="/private/monetisation">Запросы <span class="label label-warning"><?=ShopOrdersModel::instance()->getProcessor()->getOrdersToProcessCount()?> / <?=MoneyOrderModel::instance()->getProcessor()->getOrdersToProcessCount()?></span></a></li>
            <!--li><a href="/private/stats">Статистика</a></li-->
            <li <?=($activeMenu == 'chances' ? ' class="active"' : '')?>><a href="/private/chances">Шансы</a></li>
            <li <?=($activeMenu == 'seo' ? ' class="active"' : '')?>><a href="/private/seo">SEO</a></li>
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
