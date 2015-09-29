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
    <link rel="stylesheet" href="/res/css/animate.css">
    <link rel="stylesheet" href="/res/css/new.css">
    <link rel="stylesheet" href="/res/css/ZHENYA.css">
    <link rel="stylesheet" href="/res/css/olya.css">
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
            <a href="/blog" class="header-logo"></a>

            <!-- MENU -->
            <nav class="menu">

                <? $menu = array(
                    'menu-main' => array(
                        'blog' => 'Блог',
                        'lottery' => 'Лотерея',
                        'games' => 'Игры',
                        'communication' => 'Общение',
                        'users' => 'Друзья',
                        'prizes' => 'Витрина',
                    ),

                    'menu-profile menu-item' => array(
                        'settings' => 'Настройки',
                        'Выписки',
                        'Рефералы',
                        'Бонусы',
                        'Выйти',
                    ),

                    'menu-more menu-item' => array(
                        'Обратная связь',
                        'Правила',
                        'Помощь',
                    )
                );
                foreach ($menu as $ul => $items):?>
                    <ul class="<?= $ul ?>">
                        <? foreach ($items as $href => $title): ?>
                            <li>
                                <a<?= ($href === $page ? ' class="active"': '') ?> href="/<?= !is_numeric($href)?$href:'' ?>"><?= $title ?></a>
                            </li>
                        <? endforeach; ?>
                    </ul>
                <? endforeach; ?>
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

            <a href="#" class="balance-btn menu-btn-item">
                <span class="cabinet-balance-count">31,80<span>грн</span></span>
                <span class="cabinet-balance-count">32 320<span>баллов</span></span>
            </a>

            <!-- BALANCE MENU -->
            <div class="menu-balance menu-item">
                <div class="menu-balance-inf clearfix">
                    <div>34.80<br><span>гривен на счету</span></div>
                    <div>12 450<br><span>баллов на счету</span></div>
                </div>
                <div class="menu-balance-actions clearfix">
                    <a href="cabinet_cashout" class="menu-balance-item">Вывести</a>
                    <a href="cabinet_convert" class="menu-balance-item">Конвертировать</a>
                </div>
                <a href="cabinet_transaction_history" class="menu-balance-item">История транзакций</a>
                <a href="cabinet_payments_history" class="menu-balance-item active">История выплат</a>
            </div>
            <!-- end of BALANCE MENU -->

        </div>
        <!-- .header-right -->

    </div>
    <!-- .container -->
</header>
<!-- end of HEADER -->