<script id="tmpl-ticket-item" type="x-tmpl-mustache">
<div class="ticket-item clearfix">

<ul class="ticket-numbers ticket-balls clearfix">
{{{ ballsHTML }}}
</ul><!-- .ticket-numbers -->

{{#isDone}}
<div class="ticket-done">{{#i18n}}message-done-and-approved{{/i18n}}</div>
{{/isDone}}

{{^isDone}}

    <ul class="ticket-actions">
     <li class='ticket-random'>A
      <div class="after">{{#i18n}}message-autocomplete{{/i18n}}</div>
     </li>
     <li class="ticket-favorite ticket-fc-btn">
      <div class="after">{{#i18n}}message-favorite{{/i18n}}</div>
     </li>
    </ul>

    <div class="balls-count">{{#i18n}}message-numbers-yet{{/i18n}}</div>
    <div class="ticket-btn add-ticket">{{#i18n}}button-add-ticket{{/i18n}}</div>

{{/isDone}}

</div>
</script>

<script id="tmpl-ticket-tabs" type="x-tmpl-mustache">
    <ul class="ticket-tabs clearfix">
        {{{ tabsHTML }}}
    </ul>
    <!-- .ticket-tabs -->
</script>

<script id="tmpl-ticket-complete" type="x-tmpl-mustache">
<div class="ticket-items">

    <ul class="ticket-tabs clearfix">
    </ul>

<div class='ticket-result'>

    <div class='tickets-number-result'>
    <ul class='balls-result ticket-numbers'>

        {{{ completeHTML }}}

     </ul>
    </div>

    <div class='text-result'>
     <p class=text-result-top>ВСЕ 8 БИЛЕТОВ ПОДТВЕРЖДЕНЫ И ПРИНЯТЫ К РОЗЫГРЫШУ</p>
     <span><p>Каждые 20-25 минут появляется «Моментальный шанс». Проверьте свою интуицию и выиграйте баллы.
     <br>
     <p>В Кабинете, в разделе «Бонусы», приглашайте друзей и получайте баллы.</p>
     <br>
     <p>В разделе «Игры» играйте в игры и выигрывайте ценные призы.</p>
     <br>
     <p>Посетите наши страницы в социальных сетях.</p></span>
    </div>

   </div>
    <!-- .ticket-item -->

</div>
<!-- .ticket-items -->
</script>

<div class="content-top">
<div class="content-main">
<div>
    <div class="ticket-items">

        <ul class="ticket-tabs clearfix">
        </ul>
        <!-- .ticket-tabs -->

        <div class="ticket-item clearfix">
        </div>
        <!-- .ticket-item -->
    </div>
    <!-- .ticket-items -->
</div>

<div class="matches-inf-wrapper carousel clearfix">
    <div class="content-box matches-inf">
        <div class="content-box-header">1-7 Билет</div>
        <div class="content-box-content clearfix">
            <table>
                <tr>
                    <th>Совпадений в<br>одном билете</th>
                    <th>Сумма<br>выигрыша</th>
                </tr>
                <tr>
                    <td class="matches-balls">
                        <span class="true"></span>
                        <span class="true"></span>
                        <span class="true"></span>
                        <span class="true"></span>
                        <span class="true"></span>
                        <span class="true"></span>
                    </td>
                    <td>100 000 грн</td>
                </tr>
                <tr>
                    <td class="matches-balls">
                        <span class="true"></span>
                        <span class="true"></span>
                        <span class="true"></span>
                        <span class="true"></span>
                        <span class="true"></span>
                        <span></span>
                    </td>
                    <td>200 грн</td>
                </tr>
                <tr>
                    <td class="matches-balls">
                        <span class="true"></span>
                        <span class="true"></span>
                        <span class="true"></span>
                        <span class="true"></span>
                        <span></span>
                        <span></span>
                    </td>
                    <td>10 грн</td>
                </tr>
                <tr>
                    <td class="matches-balls">
                        <span class="true"></span>
                        <span class="true"></span>
                        <span class="true"></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </td>
                    <td>25 коп</td>
                </tr>
                <tr>
                    <td class="matches-balls">
                        <span class="true"></span>
                        <span class="true"></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </td>
                    <td>10 коп</td>
                </tr>
                <tr>
                    <td class="matches-balls">
                        <span class="true"></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </td>
                    <td>5 баллов</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="content-box matches-inf gold">
        <div class="content-box-header">8 Билет</div>
        <div class="content-box-content clearfix">
            <table>
                <tr>
                    <th>Совпадений в<br>одном билете</th>
                    <th>Сумма<br>выигрыша</th>
                </tr>
                <tr>
                    <td class="matches-balls">
                        <span class="true"></span>
                        <span class="true"></span>
                        <span class="true"></span>
                        <span class="true"></span>
                        <span class="true"></span>
                        <span class="true"></span>
                    </td>
                    <td>1 000 000 грн</td>
                </tr>
                <tr>
                    <td class="matches-balls">
                        <span class="true"></span>
                        <span class="true"></span>
                        <span class="true"></span>
                        <span class="true"></span>
                        <span class="true"></span>
                        <span></span>
                    </td>
                    <td>2 000 грн</td>
                </tr>
                <tr>
                    <td class="matches-balls">
                        <span class="true"></span>
                        <span class="true"></span>
                        <span class="true"></span>
                        <span class="true"></span>
                        <span></span>
                        <span></span>
                    </td>
                    <td>100 грн</td>
                </tr>
                <tr>
                    <td class="matches-balls">
                        <span class="true"></span>
                        <span class="true"></span>
                        <span class="true"></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </td>
                    <td>25 грн</td>
                </tr>
                <tr>
                    <td class="matches-balls">
                        <span class="true"></span>
                        <span class="true"></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </td>
                    <td>2 грн</td>
                </tr>
                <tr>
                    <td class="matches-balls">
                        <span class="true"></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </td>
                    <td>50 баллов</td>
                </tr>
            </table>
        </div>
    </div>
</div>


<div class="content-box tickets">

    <div class="content-box-header">
        <div class="content-box-tabs clearfix">
            <a href="#tickets-rules" class="content-box-tab active">Правила</a>
            <a href="#tickets-faq" class="content-box-tab">Частые вопросы</a>
            <a href="./rating/lotteries" class="content-box-tab">Рейтинг</a>
        </div>
    </div>

    <div class="content-box-content clearfix">
        <div class="content-box-item tickets-rules">

            <dl>
                <dt>Как принять участие в розыгрыше?</dt>
                <dd>
                    Принять участие в игре очень просто! Заполните игровой билет, выбрав 6 номеров из 49. Таких билетов
                    предоставляется пять, что увеличивает шансы на выигрыш!
                </dd>

                <dt>Как проводится розыгрыш?</dt>
                <dd>
                    Розыгрыш проводится каждый день в режиме онлайн по истечению времени на таймере.
                </dd>

                <dt>Как играть?</dt>
                <dd>
                    Во время розыгрыша выпадает 6 призовых шаров. Вы выиграли, если угадали один и более призовых
                    номеров в любом порядке.
                </dd>

                <dt>Это бесплатно?</dt>
                <dd>
                    Во время розыгрыша выпадает 6 призовых шаров. Вы выиграли, если угадали один и более призовых
                    номеров в любом порядке.
                </dd>

                <dt>Как принять участие в розыгрыше?</dt>
                <dd>
                    Принять участие в игре очень просто! Заполните игровой билет, выбрав 6 номеров из 49. Таких билетов
                    предоставляется пять, что увеличивает шансы на выигрыш!
                </dd>
            </dl>

        </div>

        <div class="content-box-item tickets-faq">

            <dl>
                <dt>Часто задаваемые вопросы</dt>
                <dd>
                    Принять участие в игре очень просто! Заполните игровой билет, выбрав 6 номеров из 49. Таких билетов
                    предоставляется пять, что увеличивает шансы на выигрыш!
                </dd>

                <dt>Как проводится розыгрыш?</dt>
                <dd>
                    Розыгрыш проводится каждый день в режиме онлайн по истечению времени на таймере.
                </dd>

                <dt>Как играть?</dt>
                <dd>
                    Во время розыгрыша выпадает 6 призовых шаров. Вы выиграли, если угадали один и более призовых
                    номеров в любом порядке.
                </dd>

                <dt>Это бесплатно?</dt>
                <dd>
                    Во время розыгрыша выпадает 6 призовых шаров. Вы выиграли, если угадали один и более призовых
                    номеров в любом порядке.
                </dd>

                <dt>Как принять участие в розыгрыше?</dt>
                <dd>
                    Принять участие в игре очень просто! Заполните игровой билет, выбрав 6 номеров из 49. Таких билетов
                    предоставляется пять, что увеличивает шансы на выигрыш!
                </dd>
            </dl>

        </div>
    </div>

</div>

</div>
<!-- .content-main -->
</div>
<!-- .content-top -->