<!doctype html>
<html style="overflow: auto;">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0 minimum-scale=1, maximum-scale=1"> 
        <meta name="mobile-web-app-capable" content="yes" />
        <meta name="theme-color" content="#ffe700" />
        
        <link rel="icon" href="/res/img/favicones/favicon.png_128x128.png?v=666" type="image/png"/>
        <meta content="/res/img/favicones/favicon.png_128x128.png?v=666" itemprop="image">
        <link href="/res/img/favicones/favicon.png.ico?v=666" rel="shortcut icon">
        
        <title>Lotzon</title>

        <link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700,400italic,700italic&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="/res/css/style.css">
        <?php if (isset($isMobile)) {
            if ($isMobile) {
                ?>
                <link rel="stylesheet" href="/res/css/mobile/style.css">
            <?php } else { ?>
                <link rel="stylesheet" href="/res/css/screen/style.css">
            <?php }
        }
        ?>
        <link rel="stylesheet" href="/res/css/animate.css">
        <link rel="stylesheet" href="/res/css/slots.css" type="text/css">
        <link rel="stylesheet" href="/res/css/social-likes_birman.css">
        <link rel="stylesheet" href="/res/css/denis.css">
    </head>

    <body>
        <div id="menu-navigation-mobile" class="menu-mobile pushy pushy-left">
        </div>
        <div class="site-overlay"></div>
        <div class="wrapper clearfix">
            <span class="js-detect"></span>

            <!-- SITE TOP -->
            <div class="site-top">
                <div id="banner-desktop-top" class="banner-3" style="height: 150px;">
                </div>
            </div>
            <!-- end of SITE TOP -->

            <!-- HEADER -->
            <header class="header clearfix">
                <div class="sticky_box">
                    <div class="container clearfix">

                        <!-- Header Left -->
                        <div class="header-left clearfix">

                            <!-- Logo -->
                            <span class="header-logo i-lotzon"></span>

                            <!-- MENU -->
                            <nav id="menu-navigation" class="menu"></nav>
                            <!-- end of MENU -->

                            <!-- MENU buttons -->
                            <div id="menu-buttons" class="menu-btns"></div>
                            <!-- end of MENU buttons -->


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
                </div>
            </header>
            <!-- end of HEADER -->