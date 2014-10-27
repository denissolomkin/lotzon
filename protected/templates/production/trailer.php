<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title></title>
        <meta name="description" content="">
        <meta name="keywords" content="" />
        <meta name="robots" content="all" />
        <meta name="publisher" content="" />
        <meta http-equiv="reply-to" content="" />
        <meta name="distribution" content="global" />
        <meta name="revisit-after" content="1 days" />

        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />

        <link rel="stylesheet" href="/tpl/css/normalize.css" />
        <link rel="stylesheet" href="/tpl/css/trailer.css" />

        <link rel="icon" href="" type="image/png" />
        <link rel="shortcut icon" href="" type="'image/x-icon"/>

        <!-- For iPhone 4 Retina display: -->
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="">
        <!-- For iPad: -->
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="">
        <!-- For iPhone: -->
        <link rel="apple-touch-icon-precomposed" href="">

        <script src="/tpl/js/lib/modernizr.js"></script>
        <script src="/tpl/js/lib/jquery.min.js"></script>
         <script src="/tpl/js/lib/jquery.min.js"></script>
        <script src="/tpl/js/trailer.js"></script>
        <script src="/tpl/js/lib/jquery.plugin.min.js"></script>
        <script src="/tpl/js/lib/jquery.countdown.min.js"></script>


    </head>
    <body>
    <main>
        <article>
            <div class="logo"><img src="/tpl/img/Lotzon-Logo-small.svg" width="235"></div>
            <div class="opening">до открытия</div>
            <div class="timer">
                
            </div>
            <div class="txt-box">
                <div class="text">Здесь каждый может выиграть деньги и ценные призы. Участие бесплатное.<br/><br/>Регистрируйтесь сейчас и после запуска проекта на Ваш счет будет зачислено 300 баллов, которые возможно обменивать на призы.</div>
            </div>
            <div class="form">
                <div class="pi-inp-bk td">
                    <div data-default="Имя" class="ph">Email</div>
                    <input type="text" placeholder="Email" value="" name="name" spellcheck="false" autocomplete="off">
                </div>
                <div class="submit">регистрация</div>

                <div class="msg">
                    <div class="txt">Мы отправили пароль на указанный email</div>
                </div>
            </div>
            <div class="social">
                <div class="title">Мы в соцсетях</div>
                <div class="links">
                    <a target="_blank" href="https://www.facebook.com/pages/Lotzon/714221388659166" class="fb"></a>
                    <a target="_blank" href="http://vk.com/lotzon" class="vk"></a>
                    <a target="_blank" href="https://plus.google.com/112273863200721967076/about" class="gp"></a>
                    <a target="_blank" href="https://twitter.com/LOTZON_COM" class="tw"></a>
                </div>
            </div>
        </article>
    </main>
    </body>
</html>

<script>
$(".timer").countdown({
    until: <?=$countdown?>,
    layout: '<span class="d">{dnn}</span><i>:</i><span class="a">{hnn}</span><i>:</i><span class="m">{mnn}</span><i>:</i><span class="s">{snn}</span>'
});
</script>