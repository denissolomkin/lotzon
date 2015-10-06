<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/res/img/favicon.png" type="image/png"/>

    <title>Lotzon</title>

    <link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700,400italic,700italic&subset=latin,cyrillic'
          rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/res/css/style.css">
    <link rel="stylesheet" href="/res/css/animate.css">
    <link rel="stylesheet" href="/res/css/new.css">
    <link rel="stylesheet" href="/res/css/slots.css" type="text/css">

    <link rel="stylesheet" href="/res/css/olya.css">

</head>
<body>
<span class="js-detect"></span>

<!-- SITE TOP -->
<div class="site-top">
    <div class="container">
        <div class="banner-3">
            <?php include "banner_3.php"; ?>
        </div>
    </div>
</div>
<!-- end of SITE TOP -->

<!-- HEADER -->
<header class="header clearfix">
    <div class="container">

        <!-- Header Left -->
        <div class="header-left clearfix">

            <!-- Logo -->
            <a href="/blog" class="header-logo"></a>

            <!-- MENU -->
            <nav class="menu">

<? $menu = array(
                    'menu-main' => array(
                        '/blog' => 'Блог',
                        '/lottery' => 'Лотерея',
                        '/games' => 'Игры',
                        '/communication' => 'Общение',
                        '/users' => 'Друзья',
                        '/prizes' => 'Витрина',
                    ),

                    'menu-profile' => array(

                        '/profile/details' => 'Контактные данные',
                        '/profile/billing' => 'Платежные данные',
                        '/profile/settings' => 'Настройки',
                        '/profile/referrals' => 'Рефералы',
                        '/profile/bonuses' => 'Бонусы',
                        '/logout' => 'Выйти',
                    ),

                    'menu-more' => array(
                        '/support/feedback' => 'Обратная связь',
                        '/support/rules' => 'Правила',
                        '/support/help' => 'Помощь',
                    )
                );
    foreach ($menu as $ul => $items):?>
              <ul class="<?= $ul ?>">
<?      foreach ($items as $href => $title): ?>
                <li><a<?= ($href === $page ? ' class="active"' : '') ?> href="<?= !is_numeric($href) ? $href : '' ?>"><?= $title ?></a></li>
<?      endforeach; ?>
              </ul>
<?  endforeach; ?>
            </nav>
            <!-- end of MENU -->

            <div class="menu-btns">
                <!-- Menu Button -->
                <div class="menu-btn menu-btn-item"></div>

                <!-- Profile Menu Button -->
                <div class="menu-profile-btn menu-btn-item"></div>

                <!-- Balance Menu Button -->
                <div class="menu-balance-btn menu-btn-item"></div>
            </div>


        </div>
        <!-- .header-left -->

        <!-- Header Right -->
        <div class="header-right">

            <div class="inf-slider"></div>

            <div class="balance"></div>

        </div>
        <!-- .header-right -->

    </div>
    <!-- .container -->
</header>
<!-- end of HEADER -->