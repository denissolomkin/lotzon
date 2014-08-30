<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=$title?></title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
  </head>
  <body style="">
      <div class="container-fluid text-center">
      <div class="row-fluid">&nbsp;</div>
        <div class="row-fluid">
          <ul class="nav nav-pills" role="tablist">
            <li class="active"><a href="/private/game">Розыгрыши</a></li>
            <li><a href="/private/users">Пользователи</a></li>
            <li><a href="/private/texts">Тексты</a></li>
            <li><a href="/private/news">Новости</a></li>
            <li><a href="/private/banners">Баннеры</a></li>
            <li><a href="/private/items">Товары</a></li>
            <li><a href="/private/monetisation">Запросы вывода средств</a></li>
            <li><a href="/private/stats">Статистика</a></li>
            <li><a href="/private/ogames">Онлайн игры</a></li>
            <li class="pull-right"><a class="glyphicon glyphicon-off" href="/private/logout"></a></li>
          </ul>
        </div>
      </div>
      <?=$yield?>
  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <!-- Latest compiled and minified JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
  </body>
</html>
