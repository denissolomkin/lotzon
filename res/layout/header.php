<!doctype html>
<html style="overflow: auto;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/res/img/favicon.png" type="image/png"/>

    <title>Lotzon</title>

    <link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700,400italic,700italic&subset=latin,cyrillic'
          rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/res/css/style.css">

    <?php if (isset($isMobile)) { if($isMobile) { ?>
        <link rel="stylesheet" href="/res/css/mobile/style.css">
    <?php } else { ?>
        <link rel="stylesheet" href="/res/css/screen/style.css">
    <?php } } ?>

    <link rel="stylesheet" href="/res/css/animate.css">
    <link rel="stylesheet" href="/res/css/denis.css">

    <link rel="stylesheet" href="/res/css/slots.css" type="text/css">
    <link rel="stylesheet" href="/res/css/social-likes_birman.css">

    <link rel="stylesheet" href="/res/css/olya.css">
    <link rel="stylesheet" href="/res/css/zerg.css">
</head>

<body>
<div id="menu-navigation-mobile" class="menu-mobile pushy pushy-left">
</div>
<div class="site-overlay"></div>
<div class="wrapper">
<span class="js-detect"></span>

<!-- SITE TOP -->
<div class="site-top">
    <div class="container">
        <div id="banner-desktop-top" class="banner-3">
	        <div style="height: 100%; background: #ccc;"></div>
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
            <span class="header-logo i-lotzon"></span>

            <!-- MENU -->
            <nav id="menu-navigation" class="menu"></nav>
            <!-- end of MENU -->

            <div class="menu-btns">
                <!-- Menu Button -->

                <div class="menu-btn menu-btn-item"><i class="i-menu"></i></div>
                <!-- Profile Menu Button -->
                <div class="menu-profile-btn menu-btn-item"><i class="i-person"></i></div>

                <!-- Balance Menu Button -->
                <div class="menu-balance-btn menu-btn-item"></div>
                 <div class="menu-logout-btn menu-btn-item"></div>
            </div>


        </div>
        <!-- .header-left -->

        <!-- Header Right -->
        <div class="header-right">

            <div id="menu-slider" class="inf-slider"></div>

            <div id="menu-balance"></div>

        </div>
        <!-- .header-right -->

    </div>
    <!-- .container -->
</header>
<!-- end of HEADER -->