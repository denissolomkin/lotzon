<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/tpl/img/favicon.png" type="image/png"/>

    <title>Lotzon</title>

    <link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700,400italic,700italic&subset=latin,cyrillic'
          rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/res/css/style.css">
</head>
<body>

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
            <a href="/" class="header-logo"></a>

            <!-- MENU -->
            <nav class="menu">

                <? $menu = array(
                    'menu-main' => array(
                        'home_blog' => 'Блог',
                        'tickets' => 'Билеты',
                        'prizes' => 'Призы',
                        'communication_comments' => 'Общение',
                        'games_online' => 'Игры',
                        'cabinet_game_history' => 'Кабинет',
                    ),

                    'menu-more' => array(
                        'settings' => 'Настройки',
                        'Обратная связь',
                        'Правила',
                        'Помощь',
                        'Выйти',
                    )
                );
                foreach ($menu as $ul => $items):?>
                    <ul class="<?= $ul ?>">
                        <? foreach ($items as $href => $title): ?>
                            <li>
                                <a<?= ($href === $page ? ' class="active"': '') ?> href="<?= $href ?>"><?= $title ?></a>
                            </li>
                        <? endforeach; ?>
                    </ul>
                <? endforeach; ?>
            </nav>
            <!-- end of MENU -->

            <!-- Menu Button -->
            <div class="menu-btn"></div>

            <!-- More Menu Button -->
            <div class="more-menu-btn"></div>

        </div>
        <!-- .header-left -->

        <!-- Header Right -->
        <div class="header-right">
            <div class="inf-slider"></div>
            <div class="timer clearfix">
                <div class="timer-title">
                    До розыгрыша<br>
                    осталось
                </div>
                <div class="timer-digits">15:09:40</div>
            </div>
        </div>
        <!-- .header-right -->

    </div>
    <!-- .container -->
</header>
<!-- end of HEADER -->