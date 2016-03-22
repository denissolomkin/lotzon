</div>
<!--END wrapper-->
<!-- FOOTER -->
<footer class="footer clearfix">
    <div class="container">
        <a href="#top">
            <div class="go-to-top"></div>
        </a>
        <!-- Logo -->
        <div class="footer-logo"></div>

        <!-- Social icons -->
        <div class="footer-social-icons">
            <a href="https://www.facebook.com/pages/Lotzon/714221388659166" target="_blank" class="fb"></a>
            <a href="http://vk.com/lotzon" target="_blank" class="vk"></a>
            <a href="http://ok.ru/group/52501162950725" target="_blank" class="ok"></a>
            <a href="https://twitter.com/LOTZON_COM" target="_blank" class="tw"></a>
        </div>
    </div>
    <!-- Popup msgs '.show - show  .timer - hide close button'-->
    <div id="popup-message" class="">
        <div class="inner">
            <i class="i-x-slim" onclick="document.getElementById('popup-message').removeAttribute('class')"></i>
            <div class="body">
                <!-- msg -->
            </div>
        </div>
    </div>
    <!-- END Popup msgs -->
    
    <!-- zhaloba -->
    <div id="zhgi">
        <div class="arrow"><i class="i-arrow-slim-left"></i></div>
        <div class="content">
            <p>ЗАМЕТИЛИ ОШИБКУ?</p>
            <a href="/users/0/messages">напишите нам</a>
        </div>
    </div>
    <!-- zhaloba -->
    
    <!-- .container -->
</footer>
<!-- end of FOOTER -->

<script>

    var
        device = {mobile: <?php echo json_encode($isMobile); ?>},
        error = <?php echo json_encode($error);?>,
        lottery = <?php echo json_encode($lottery, JSON_PRETTY_PRINT); ?>,
        player = <?php echo json_encode($player, JSON_PRETTY_PRINT); ?>,
        slider = <?php echo json_encode($slider, JSON_PRETTY_PRINT); ?>,
        config = <?php echo json_encode($config, JSON_PRETTY_PRINT); ?>,
        timestamp = <?php echo time(); ?>,
        menu = {
            navigation: {
                'menu-main': ['blog', 'lottery', 'games', 'communication', 'users', 'prizes'],
                'menu-profile': ['profile/edit', 'profile/billing', 'profile/settings', 'reports/referrals', 'profile/bonuses'],
                'menu-more': [
                    'users/0/messages',
                    'support/rules',
                    'support/faq',
                    /*'support/help',*/
                    'logout'
                ]
            }
        },
        debugConf = <?php echo json_encode($debug, JSON_PRETTY_PRINT); ?>

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

<?php
$dirs = array('libs', 'plugins', 'functions', 'controllers', 'games', 'models', 'core');
foreach ($dirs as $dir):
    if (!is_dir('./res/js/' . $dir . '/'))
        continue;
    ?>
    <!-- <?php echo $dir; ?> -->
    <?php
    $files = scandir('./res/js/' . $dir . '/');
    foreach ($files as $file):
        if ($file != "." && $file != ".." && strstr($file, '.js')):
            ?>
            <script src="/res/js/<?php echo $dir . '/' . $file; ?>"></script>
            <?php
        endif;
    endforeach;
endforeach;
?>

<script>

    $(function () {

        Config.init(config); // init config
        Tickets.init(lottery);  // extend tickets
        Player.init(player); // extend player
        D.init(debugConf); // init debugger
        U.init(); // init url handler
        Slider.init(slider); // echo slider
        Navigation.init(menu); // init navigation
        Livedate.init(timestamp); // update dates in realtime
        Device.init(device); // detect

        Cache.drop(); // init cache engine
        EventHandler.init(); // init event engine
        Callbacks.init(); // init callbacks

    });

</script>
<style>
    #R2D2 {
    <?php if($_SERVER['HTTP_HOST']=='new.lotzon.com' || $_SERVER['HTTP_HOST']=='lotzon.com'):?>
        display: none;
    <?php endif; ?>
        position: relative;
        z-index: 999999999;
        font-size: 16px;
        position: fixed;
        bottom: 0;
        /* padding-right: 30px; */
        border-right: 20px solid #666;
        border-radius: 0em 50em 50em 0em;
        left: -180px;

        box-shadow: 1px 1px 7px #000;
        transition: all 1.5s ease-in-out;
        min-width: 200px;
        background: #000;
    }

    #R2D2:hover {
        left: 0;
    }

    #R2D2 button {
        padding: 2px 5px;
        cursor: pointer;
        border-left: 2px dashed #0088cc;
        border-right: 2px dashed #0088cc;
        background: #094984;
        color: #fff;
        transition: all .4s ease;
        display: block;
        margin: 2px 25px 2px 2px;

    }

    #R2D2 button:hover {
        border-left: 2px dashed #00b7ec;
        border-right: 2px dashed #00b7ec;
        background: tomato;
        color: #000;

    }
</style>
<div id="R2D2">
    <button accesskey="d" onclick="Content.style()">Desktop/Mobile</button>
    <button accesskey="l" onclick="Lottery.prepareData()">Lottery</button>
    <button accesskey="p" onclick="Player.ping()">Ping</button>
    <button accesskey="s" onclick="WebSocketAjaxClient()">WebSocket</button>
</div>

</body>
</html>