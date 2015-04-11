<div class="container-fluid">
    <div class="row-fluid">
        <h2>Счетчики</h2>
        <hr />
    </div>

    <div class="row-fluid">
        <form role="form" action="/private/counters" method="POST">
            <? foreach(
                array(
                    'MONEY_ADD_INCREMENT'=>'Добавление к общей сумме выигрыша (ежедневное увеличение)',
                    'MONEY_ADD'=>'Добавление к общей сумме выигрыша',
                    'WINNERS_ADD'=>'Добавление к общему количеству победителей',

                    'PLAYER_TIMEOUT'=>'Таймаут, через который игрок считается оффлайн (сек)',
                    'USER_REVIEW_DEFAULT'=>'Пользователь по умолчанию для ответов на отзывы',

                    'NEWS_PER_PAGE'=>'Новостей на странице',
                    'SHOP_PER_PAGE'=>'Товаров на странице',
                    'LOTTERIES_PER_PAGE'=>'Лотерей на странице',
                    'TRANSACTIONS_PER_PAGE'=>'Транзакций на странице',
                    'COMMENTS_PER_PAGE'=>'Комментариев на странице',
                    'REVIEWS_PER_PAGE'=>'Отзывов на странице',

                    'TRANSACTIONS_PER_ADMIN'=>'Транзакций в админке',
                    'PLAYERS_PER_ADMIN'=>'Пользователей в админке',
                    'ORDERS_PER_ADMIN'=>'Заявок в админке',
                    'REVIEWS_PER_ADMIN'=>'Отзывов в админке',

                    'TeaserClick'=>'Пауза для накликивания (сек)',
                    'TEASER_CLICK_MIN_GAME'=>'Минимальное количество сыгранных игр',
                ) as $index => $title) { ?>
                <div class="form-group">
                    <label for="title"><?=$title;?></label>
                    <input type="text" class="form-control" name="counters[<?=$index?>]" value="<?=$list[$index]?>">
                </div>
            <? } ?>

            <button type="submit" class="btn btn-success">Сохранить</button>
        </form>
    </div>
</div>


<? if($frontend) include($frontend.'_frontend.php') ?>