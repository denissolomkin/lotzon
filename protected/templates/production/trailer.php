<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?=$seo['title']?></title>
        <meta name="description" content="<?=$seo['desc']?>">
        <meta name="keywords" content="<?=$seo['kw']?>" />
        <meta name="robots" content="all" />
        <meta name="publisher" content="" />
        <meta http-equiv="reply-to" content="" />
        <meta name="distribution" content="global" />
        <meta name="revisit-after" content="1 days" />

        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />

        <!-- Schema.org markup for Google+ -->
        <meta itemprop="name" content="<?=$seo['Title']?>">
        <meta itemprop="description" content="Играл, играю и буду играть.">
        <meta itemprop="image" content="http://lotzon.com/tpl/img/social-share.jpg?rnd=<?=rand()?>">

        <!-- Twitter Card data -->
        <meta name="twitter:card" content="http://lotzon.com/tpl/img/social-share.jpg?rnd=<?=rand()?>">
        <meta name="twitter:title" content="<?=$seo['Title']?>">
        <meta name="twitter:description" content="Играл, играю и буду играть.">
        <!-- Twitter summary card with large image must be at least 280x150px -->
        <meta name="twitter:image:src" content="http://lotzon.com/tpl/img/social-share.jpg?rnd=<?=rand()?>">

        <!-- Open Graph data -->
        <meta property="og:title" content="<?=$seo['Title']?>" />
        <meta property="og:type" content="article" />
        <meta property="og:url" content="http://www.lotzon.com/" />
        <meta property="og:image" content="http://lotzon.com/tpl/img/social-share.jpg?rnd=<?=rand()?>" />
        <meta property="og:description" content="Играл, играю и буду играть." />
        <meta property="article:modified_time" content="<?=date('c', time())?>" />

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
            <div class="logo"><img src="/tpl/img/Lotzon-Logo-small.svg" width="235" height="63"></div>
            <div class="opening">до открытия</div>
            <div class="timer">
                
            </div>
            <div class="txt-box">
                <div class="text">Добро пожаловать в игровое пространство Lotzon. Играйте, побеждайте в викторинах, получайте призы, выигрывайте деньги и срывайте JackPot.<br/><br/>Пожалуйста, оставьте свой email, чтобы одним из первых узнать о нашем открытии. Первую тысячу счастливчиков ждет приятный сюрприз. Удачи!</div>
            </div>
            <div class="form">
                <div class="pi-inp-bk td">
                    <div data-default="Имя" class="ph">Email</div>
                    <input type="text" placeholder="Email" value="" name="email" spellcheck="false" autocomplete="off">
                </div>
                <div class="submit">отправить</div>

                <div class="msg">
                    <div class="txt">Спасибо! Вскоре, мы оповестим Вас об открытии. До встречи!</div>
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
location.href = '/';
$(".timer").countdown({
    until: <?=$countdown?>,
    layout: '<span class="d">{dnn}</span><i>:</i><span class="a">{hnn}</span><i>:</i><span class="m">{mnn}</span><i>:</i><span class="s">{snn}</span>'
});

(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-56113090-1', 'auto');
ga('send', 'pageview');

(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter26806191 = new Ya.Metrika({id:26806191,
                    webvisor:true,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>