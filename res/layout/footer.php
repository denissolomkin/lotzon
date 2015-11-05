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
        navigation = {
            menu: {
                'menu-main': ['blog', 'lottery', 'games', 'communication', 'users', 'prizes'],
                'menu-profile': ['profile/details', 'profile/billing', 'profile/settings', 'reports/referrals', 'profile/bonuses', 'logout'],
                'menu-more': ['support/feedback', 'support/rules', 'support/help']
            }
        },

        tickets = {
            "lastLotteryId": 353,
            "selectedTab": null,
            "totalBalls": 49,
            "requiredBalls": 6,
            "totalTickets": 8,
            "filledTickets": {
                "1": [37, 2, 3, 4, 5, 6],
                "2": [3, 2, 8, 34, 15, 19],
                "3": null,
                "4": [1, 2, 3, 4, 16, 6],
                "5": [31, 22, 13, 44, 25, 9],
                "6": [32, 2, 14, 34, 15, 19],
                "7": false,
                "8": false
            }
        },

        player = {
            "id": 3628,
            "lang": "RU",
            "language": {
                "current": "RU",
                "available": {
                    "RU": "Русский",
                    "EN": "English",
                    "UA": "Украiнська"
                }
            },
            "img": "res/img/user_img.jpg",
            "favorite": [1, 2],
            "title": {
                "name": "Сергей",
                "surname": "Шевченко",
                "patronymic": "Иванович",
                "nickname": "Участник 3628"
            },
            "location": {
                "country": "UA",
                "city": "Kyiv",
                "home": "Obolonska, 29"
            },
            "balance": {
                "points": 100,
                "money": 15.41,
                "lotzon": 1500
            },
            "currency": {
                "coefficient": "1",
                "few": "гривни",
                "iso": "грн",
                "many": "гривен",
                "one": "гривна",
                "rate": 100
            },
            "billing": {
                "webMoney": "R111289102111",
                "yandexMoney": "410011141000",
                "qiwi": null,
                "phone": null
            },
            "social": {
                "vk": "R333289102947",
                "ok": "411141590761950",
                "gl": null,
                "tw": null
            },
            "settings": {}
        },

        slider = {
            "sum": 353944,
            "winners": 34260,
            "jackpot": 100000,
            "players": 54000,
            "timer": 353944,
            "lottery": {
                "id": 345,
                "date": '20.41.2014',
                "balls": [5, 1, 32, 18, 14, 49]
            }
        },

        debug = {
            "config": {
                "alert": false,
                "render": true,
                "cache": true,
                "i18n": false,
                "func": true,
                "info": true,
                "warn": true,
                "error": true,
                "log": true,
                "clean": true
            }
        };
</script>

<?php $dirs = array('libs', 'plugins', 'controllers', 'functions', 'models', 'core');
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

        Tickets.init(tickets);  // extend tickets
        Player.init(player); // extend player
        D.init(debug); // init debugger
        Slider.init(slider); // echo slider
        Navigation.init(navigation); // init navigation

        Cache.drop().init(); // init cache engine
        Callbacks.init(); // init callbacks

    });

</script>

</body>
</html>