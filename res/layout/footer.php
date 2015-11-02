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
        <button class="tmp_but" onclick="Lottery.prepareData()">Start</button>
    </div>
    <!-- .container -->
</footer>
<!-- end of FOOTER -->

<script>

    App = {
        id: null,
        name: 'Durak',
        mode: null,
        variation: null
    };

    Apps = {
        audio: [],
        modes: [],
        variations: []
    };

    Slider = {
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
    };

    Player = {
        "id": 3628, 
        "lang": "RU",
        "img": "res/img/user_img.jpg",
        "favorite": [1,2],
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
        "settings":{
        }
    }

    Tickets = {
        "lastLotteryId": 353,
        "selectedTab": null,
        "totalBalls": 49,
        "requiredBalls": 6,
        "totalTickets": 8,
        "filledTickets": {
            "1": [1, 2, 3, 4, 5, 6],
            "2": [3, 2, 14, 34, 15, 19],
            "3": null,
            "4": [1, 2, 3, 4, 5, 6],
            "5": [31, 22, 13, 44, 25, 9],
            "6": [3, 2, 14, 34, 15, 19],
            "7": false,
            "8": false
        }
    };

    var templates = <?php $templates = array();
            foreach(array(
                'menu-balance',
                'menu-slider',
                'lottery-ticket-complete',
                'lottery-ticket-item',
                'lottery-ticket-tabs'
            ) as $template)
                $templates[$template] = file_get_contents('./res/tmpl/'.str_replace('-','/',$template).'.html');
                echo json_encode($templates);
                ?>,

        texts = {
            'RU': {
                "message-autocomplete": "АВТОЗАПОЛНЕНИЕ",
                "message-done-and-approved": "ПОДТВЕРЖДЕН И ПРИНЯТ К РОЗЫГРЫШУ",
                "message-yet": "ЕЩЕ",
                "message-numbers": "НОМЕРОВ",
                "message-favorite": "ЛЮБИМАЯ КОМБИНАЦИЯ",
                "message-cashout-success": "Поздравляем, деньги успешно выведены и будут зачислены в течение 7 рабочих дней",
                "message-ticket-4": "для доступа к билету №4 вы должны принять участие в 100 розыгрышах",
                "message-ticket-5": "для доступа к билету №5 вы должны принять участие в 100 розыгрышах",
                "message-ticket-6": "для доступа к билету №6 вы должны принять участие в 100 розыгрышах",
                "message-ticket-7": "для доступа к билету №7 вы должны принять участие в 100 розыгрышах",

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
            }
        };
</script>

<?php $dirs = array('libs', 'plugins', 'controllers', 'models', 'functions', 'core', 'temp');
foreach ($dirs as $dir) {
    if(!is_dir('./res/js/' . $dir . '/'))
        continue;
    echo "<!-- {$dir} -->\r\n";
    $files = scandir('./res/js/' . $dir . '/');
    foreach($files as $file)
        if ($file != "." && $file != ".." && strstr($file, '.js'))
            echo '<script src="/res/js/' . $dir . '/' . $file . "\"></script>\r\n";
}?>

<script>
    $(function () {

        localStorage.cacheStorage = null;
        localStorage.templateStorage = null;

        Cache.init(); // init cache engine
        M.init(texts); // compile templates
        Template.init(templates); // compile templates
        Menu.init(); // echo menu
        Slider.init(); // echo slider
        Navigation.init(); // init navigation
        D.init(); // init debugger
        Callbacks.init(); // init callbacks

    });
</script>

</body>
</html>