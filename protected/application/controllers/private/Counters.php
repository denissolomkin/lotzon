<?php
namespace controllers\admin;

use \Application, \PrivateArea, \SettingsModel, \Session2, \Admin;
use Ratchet\Wamp\Exception;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class Counters extends PrivateArea
{
    public $activeMenu = 'counters';

    public function init()
    {
        parent::init();

        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {

        $list = SettingsModel::instance()->getSettings($this->activeMenu)->getValue();
        $counters =
            array(
                '<span class="fa fa-th-list"></span> КОНТЕНТ',
                'SHOP_PER_PAGE'=>'<span class="fa fa-shopping-cart"></span> Товаров на странице',
                'TRANSACTIONS_PER_PAGE'=>'<span class="fa fa-tag"></span> Транзакций на странице',
                'RATING_PER_PAGE' => '<span class="fa fa-user"></span> Игроков в рейтинге на странице',
                'POSTS_PER_PAGE' => '<span class="fa fa-calendar"></span> Статей в блоге на странице',
                'FRIENDS_PER_PAGE' => '<span class="fa fa-user"></span> Друзей на странице',
                'REVIEWS_PER_PAGE'=>'<span class="fa fa-thumbs-o-up"></span> Отзывов на странице',
                'NOTIFICATIONS_PER_PAGE' => '<span class="glyphicon glyphicon-bell" ></span> Уведомлений на странице',
                'MESSAGES_PER_PAGE' => '<span class="fa fa-envelope"></span> Сообщений на странице',
                'MESSAGE_BADGE_TIMEOUT'=>'Таймаут на удаление плашки сообщения (сек)',


                '<span class="fa fa-cogs"></span> НАСТРОЙКИ',
                'PLAYER_TIMEOUT'=>'Таймаут, через который игрок считается оффлайн (сек)',
                'USER_REVIEW_DEFAULT'=>'Админ по умолчанию для ответов на отзывы',
                'USER_ORDERS_DEFAULT'=>'Админ для отправки подтверждения выплат',
                'INVITES_PER_WEEK'=>'Количество приглашений по email в неделю',
                'APPROVES_TO_AUTOPUBLISH' => '<span class="fa fa-thumbs-o-up"></span> Опубликованных комментариев для автомодерации',
                'BOT_TIMEZONES'=>'<span class="fa fa-plug"></span> Количество временных зон для ботов',

                '<span class="fa fa-user-secret"></span> АДМИНКА',
                'TRANSACTIONS_PER_ADMIN'=>'Транзакций в админке',
                'PLAYERS_PER_ADMIN'=>'Пользователей в админке',
                'ORDERS_PER_ADMIN'=>'Заявок в админке',
                'REVIEWS_PER_ADMIN'=>'Отзывов в админке',
                'DANGER_MAX_WIN' => 'Верхний порог побед для подозрения неладного (%)',
                'DANGER_MIN_WIN' => 'Нижний порог побед для подозрения неладного (%)',

                '<span class="fa fa-gift"></span> ЛОТЕРЕЯ',
                'MONEY_ADD'=>'Добавление к общей сумме выигрыша',
                'WINNERS_ADD'=>'Добавление к общему количеству победителей',
                'HOURS_ADD'=>'Добавление ко времени на сайте (часов)',
                'MIN_MONEY_OUTPUT'=>'Минимальное количество денег для вывода',
                'LOTTERIES_PER_PAGE'=>'<span class="fa fa-gift"></span> Лотерей на странице',
                'HOLIDAY_LOTTERY_ID'=>'Id праздничной лотереи с 7 билетами',

                '<span class="fa fa-code"></span> НАКЛИКИВАНИЕ',
                'TeaserClick'=>'Пауза для накликивания (сек)',
                'TEASER_CLICK_MIN_GAME'=>'Минимальное количество сыгранных игр',

                '<span class="fa fa-dashboard"></span> МЕТРИКА',
                'YANDEX_METRIKA'=>'Yandex.Metrika',
                'GOOGLE_ANALYTICS'=>'Google Analytics',

            );

        $this->render('admin/'.$this->activeMenu, array(
            'title'      => 'Счетчики',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'list' => $list,
            'counters' => $counters,
            'frontend' => 'statictexts',
        ));
    }

    public function saveAction()
    {

        if($this->request()->post($this->activeMenu)) {

            SettingsModel::instance()->getSettings($this->activeMenu)->setValue($this->request()->post($this->activeMenu))->create();
        }

        $this->redirect('/private/'.$this->activeMenu);
    }
}