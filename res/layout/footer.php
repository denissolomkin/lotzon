<!-- FOOTER -->
<footer class="footer clearfix">
    <div class="container">
        <a href="#top">
            <div class="go-to-top"></div>
        </a>
        <!-- Logo -->
        <a href="/" class="footer-logo"></a>

        <!-- Social icons -->
        <div class="footer-social-icons">
            <a href="#" class="fb"></a>
            <a href="#" class="vk"></a>
            <a href="#" class="ok"></a>
            <a href="#" class="tw"></a>
        </div>
    </div>
    <!-- .container -->
</footer>
<!-- end of FOOTER -->

<script>

    var
        menu = {
            navigation: {
                'menu-main': ['blog', 'lottery', 'games', 'communication', 'users', 'prizes'],
                'menu-profile': ['profile/details', 'profile/billing', 'profile/settings', 'profile/password', 'reports/referrals', 'profile/bonuses'],
                'menu-more': ['support/feedback', 'support/rules', 'support/faq', 'support/help'],
                'menu-logout': ['logout']
            }
        },

        device = {mobile: <?php echo json_encode($isMobile);?>},
        lottery = <?php echo json_encode($lottery, JSON_PRETTY_PRINT);?>,
        player = <?php echo json_encode($player, JSON_PRETTY_PRINT);?>,
        slider = <?php echo json_encode($slider, JSON_PRETTY_PRINT);?>,
        config = <?php echo json_encode($config, JSON_PRETTY_PRINT);;?>,
        timestamp = <?php echo time();?>,
        debug = {
            "config": {
                "stat": /192.168.56.101/.test(location.hostname),
                "alert": false,
                "render": true,
                "cache": false,
                "i18n": false,
                "func": true,
                "info": true,
                "warn": true,
                "error": true,
                "log": true,
                "clean": true,
                'content': true
            }
        };
</script>

<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function () {
            try {
                w.yaCounter33719044 = new Ya.Metrika({
                    id: 33719044,
                    clickmap: true,
                    trackLinks: true,
                    accurateTrackBounce: true,
                    webvisor: true,
                    trackHash: true
                });
            } catch (e) {
            }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () {
                n.parentNode.insertBefore(s, n);
            };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else {
            f();
        }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript>
    <div><img src="https://mc.yandex.ru/watch/33719044" style="position:absolute; left:-9999px;" alt=""/></div>
</noscript>
<!-- /Yandex.Metrika counter -->

<?php $dirs = array('libs', 'plugins', 'functions', 'controllers', 'models', 'core');
foreach ($dirs as $dir):
    if (!is_dir('./res/js/' . $dir . '/'))
        continue; ?>
    <!-- <?php echo $dir; ?> -->
    <?php $files = scandir('./res/js/' . $dir . '/');
    foreach ($files as $file):
        if ($file != "." && $file != ".." && strstr($file, '.js')): ?>
            <script src="/res/js/<?php echo $dir . '/' . $file; ?>"></script>
        <?php endif;
    endforeach;
endforeach;
?>

<script>

    $(function () {

        Tickets.init(lottery);  // extend tickets
        Player.init(player); // extend player
        D.init(debug); // init debugger
        Slider.init(slider); // echo slider
        Config.init(config); // init config
        Navigation.init(menu); // init navigation
        Livedate.init(timestamp); // update dates in realtime
        Device.init(device); // detect

        Cache.drop(); // init cache engine
        EventListener.init(); // init event engine
        Callbacks.init(); // init callbacks

    });

</script>

<button style="position: fixed;bottom:0;left:0" onclick="Content.style()">Desktop/Mobile</button>
<button style="position: fixed;bottom:0;left:110px;" onclick="Lottery.prepareData()">Lottery</button>
<div id="debug-stat-box"></div>

</div>
</body>
</html>