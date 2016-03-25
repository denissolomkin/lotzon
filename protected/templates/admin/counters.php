<div class="container-fluid">
    <div class="row-fluid">
        <h2>Счетчики</h2>
        <hr />
    </div>

    <div class="row-fluid">
        <form role="form" action="/private/counters" method="POST">
            <div>
            <? foreach(
                array(

                    '<span class="fa fa-gift"></span> ЛОТЕРЕЯ',

                    'MONEY_ADD_INCREMENT'=>'Добавление к общей сумме выигрыша (ежедневное увеличение)',
                    'MONEY_ADD'=>'Добавление к общей сумме выигрыша',
                    'WINNERS_ADD'=>'Добавление к общему количеству победителей',
                    'HOURS_ADD'=>'Добавление ко времени на сайте (часов)',
                    'MIN_MONEY_OUTPUT'=>'Минимальное количество денег для вывода',

                    '<span class="fa fa-dashboard"></span> СЧЕТЧИКИ',
                    'YANDEX_METRIKA'=>'Yandex.Metrika',
                    'GOOGLE_ANALYTICS'=>'Google Analytics',

                    '<span class="fa fa-cogs"></span> НАСТРОЙКИ',

                    'PLAYER_TIMEOUT'=>'Таймаут, через который игрок считается оффлайн (сек)',
                    'USER_REVIEW_DEFAULT'=>'Админ по умолчанию для ответов на отзывы',
                    'USER_ORDERS_DEFAULT'=>'Админ для отправки подтверждения выплат',
                    'INVITES_PER_WEEK'=>'Количество приглашений по email в неделю',
                    'APPROVES_TO_AUTOPUBLISH' => '<span class="fa fa-thumbs-o-up"></span> Опубликованных комментариев для автомодерации',
                    'BOT_TIMEZONES'=>'<span class="fa fa-plug"></span> Количество временных зон для ботов',
                    'MESSAGE_BADGE_TIMEOUT'=>'Таймаут на удаление плашки сообщения (сек)',

                    '<span class="fa fa-th-list"></span> КОНТЕНТ',

                    'NEWS_PER_PAGE'=>'Новостей на странице',
                    'SHOP_PER_PAGE'=>'<span class="fa fa-shopping-cart"></span> Товаров на странице',
                    'LOTTERIES_PER_PAGE'=>'<span class="fa fa-gift"></span> Лотерей на странице',
                    'TRANSACTIONS_PER_PAGE'=>'<span class="fa fa-tag"></span> Транзакций на странице',
                    'RATING_PER_PAGE' => '<span class="fa fa-user"></span> Игроков в рейтинге на странице',
                    'POSTS_PER_PAGE' => '<span class="fa fa-calendar"></span> Статей в блоге на странице',
                    'FRIENDS_PER_PAGE' => '<span class="fa fa-user"></span> Друзей на странице',
                    'COMMENTS_PER_PAGE'=>'<span class="fa fa-comment"></span> Комментариев на странице',
                    'REVIEWS_PER_PAGE'=>'<span class="fa fa-thumbs-o-up"></span> Отзывов на странице',
                    'NOTIFICATIONS_PER_PAGE' => '<span class="glyphicon glyphicon-bell" ></span> Уведомлений на странице',
                    'MESSAGES_PER_PAGE' => '<span class="fa fa-envelope"></span> Сообщений на странице',

                    '<span class="fa fa-user-secret"></span> АДМИНКА',

                    'TRANSACTIONS_PER_ADMIN'=>'Транзакций в админке',
                    'PLAYERS_PER_ADMIN'=>'Пользователей в админке',
                    'ORDERS_PER_ADMIN'=>'Заявок в админке',
                    'REVIEWS_PER_ADMIN'=>'Отзывов в админке',
                    'DANGER_MAX_WIN' => 'Верхний порог побед для подозрения неладного (%)',
                    'DANGER_MIN_WIN' => 'Нижний порог побед для подозрения неладного (%)',

                    '<span class="fa fa-code"></span> НАКЛИКИВАНИЕ',
                    'TeaserClick'=>'Пауза для накликивания (сек)',
                    'TEASER_CLICK_MIN_GAME'=>'Минимальное количество сыгранных игр',

                ) as $index => $title) {

                if(is_numeric($index)) {echo "</div><div class='col-md-3 row-banner'><h1>$title</h1>";} else {?>
                <div class="form-group">
                    <label for="title"><?=$title;?></label>
                    <input type="text" class="form-control" name="counters[<?=$index?>]" value="<?=$list[$index]?>">
                </div>
            <? }
            } ?>
            </div>
            <button type="submit" class="btn btn-success">Сохранить</button>
        </form>
    </div>
</div>


<? if($frontend) include($frontend.'_frontend.php') ?>