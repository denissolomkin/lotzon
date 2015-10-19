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


    appName = 'Durak';
    onlineGame = appAudio = appModes = appVariations = {};

    OnlineGames = {
        Audio: [],
        Modes: [],
        Variations: []
    },

    OnlineGame = {
        name: 'Durak',
        id: '1'
    },

    Cache = {};

    Templates = <?php $templates = array();
            foreach(array(
                'menu-balance',
                'menu-slider',
                'lottery-ticket-complete',
                'lottery-ticket-item',
                'lottery-ticket-tabs'
            ) as $template)
                $templates[$template] = file_get_contents('./res/tmpl/'.str_replace('-','/',$template).'.html');
                echo json_encode($templates);
                ?>;

    Slider = {
        "sum": 353944,
        "winners": 34260,
        "jackpot": 100000,
        "players": 54000,
        "timer": 34260,
        "lottery": {
            "date": '20.41.2014',
            "balls": [5, 1, 32, 18, 14, 49]
        }
    };

    Player = {
        'id': 3628,
        'lang': 'RU',
        'img': 'res/img/user_img.jpg',
        'favorite': [],
        'title': {
            'name': "Сергей",
            'surname': "Шевченко",
            'patronymic': "Иванович",
            'nickname': "Участник 3628"
        },
        'currency': {
            'coefficient': "1",
            'few': "гривни",
            'iso': "грн",
            'many': "гривен",
            'one': "гривна",
            'rate': 100
        },
        'balance': {
            'points': 100,
            'money': 15.41,
            'lotzon': 1500
        },
        'addresse': {
            'country': "UA",
            'city': "Kyiv",
            'home': "Obolonska, 29"
        },
        'billing': {
            'webmoney': 'R333289102947',
            'yandexmoney': '411141590761950',
            'qiwi': null,
            'phone': null
        },
        'social': {
            'vk': 'R333289102947',
            'ok': '411141590761950',
            'gl': null,
            'tw': null
        }
    }

    Balance = Player.balance;

    Tickets = {
        "selectedBalls": 6,
        "totalBalls": 49,
        "totalTickets": 8,
        "balls": {
            "1": [1, 2, 3, 4, 5, 6],
            "2": [31, 22, 13, 44, 25, 9],
            "3": [3, 2, 14, 34, 15, 19],
            "4": [1, 2, 3, 4, 5, 6],
            "5": [31, 22, 13, 44, 25, 9],
            "6": [3, 2, 14, 34, 15, 19],
            "7": [31, 22, 13, 44, 25, 9]
        }
    };

    Texts = {
        "message-autocomplete": "АВТОЗАПОЛНЕНИЕ",
        "message-done-and-approved": "ПОДТВЕРЖДЕН И ПРИНЯТ К РОЗЫГРЫШУ",
        "message-yet": "ЕЩЕ",
        "message-numbers": "НОМЕРОВ",
        "message-favorite": "ЛЮБИМАЯ КОМБИНАЦИЯ",

        "button-set-favorite": "настраивается в кабинете",
        "button-more": "Подробнее",
        "button-add-ticket": "Подтвердить",
        "button-cashout": "Вывести",
        "button-convert": "Конвертировать",
        "button-transactions": "История транзакций",
        "button-payments": "История выплат",

        "title-points": "баллов",
        "title-ticket": "Билет",
        "title-prizes-draw": "Розыгрыш призов",
        "title-prizes-exchange": "Обмен на баллы",
        "title-limited-quantity": "Ограниченное количество",
        "title-pieces": "шт.",
        "title-games-online": "Игры Онлайн",
        "title-games-chance": "Шансы",
        "title-games-rating": "Рейтинг",
        "title-error": "Ошибка",
        "title-slider-jackpot": "Джекпот",
        "title-slider-players": "Участников",
        "title-slider-winners": "Победителей",
        "title-slider-sum": "Общая сумма выигрыша",
        "title-slider-timer": "До розыгрыша осталось",
        "title-slider-date": "Розыгрыш от",
    };
</script>

<?php $dirs = array('libs', 'plugins', 'functions', 'core', 'temp');
foreach ($dirs as $dir) {
    echo "<!-- {$dir} -->\r\n";
    $files = scandir('./res/js/' . $dir . '/');
    foreach($files as $file)
        if ($file != "." && $file != ".." && strstr($file, '.js'))
            echo '<script src="/res/js/' . $dir . '/' . $file . "\"></script>\r\n";
}?>

<script>
    $(function () {

        Navigation.init(); // init navigation
        D.init(); // init debugger
        R.init(); // init render engine
        C.init(); // init callbacks
        C.menu(); // menu callback

    });
</script>

</body>
</html>