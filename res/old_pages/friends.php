<script id="tmpl-friends-list" type="x-tmpl-mustache">
<div class="content-box-item c-friends">

    <div class="content-box-item-top">
        <div class="friends-count">Друзей<span>{{friends}}</span></div>
        <div class="friends-search">
            <input type="text" class="friends-search-input" placeholder="Поиск среди друзей">
        </div>
    </div>

    <div>
        {{#items}}
        <div class="c-friend online clearfix">
            <div class="c-friend-img">
                <img src="{{ img }}" alt="">
            </div>
            <div class="c-friend-description">
                <div class="c-friend-name">{{ title }}</div>
                <div class="c-friend-inf">
                    <div>Игр: {{ games }}</div>
                    <div>Денег: {{ money }}</div>
                    <div>Баллов: {{ points }}</div>
                </div>
                <a href="message" data-json="friends-get-{{ id }}" class="block c-friend-msg">Написать сообщение</a>
            </div>
        </div>
        {{/items}}
        {{^items}}No friends :({{/items}}

    </div>
</div>
</script>

<script id="tmpl-friends-chronicle" type="x-tmpl-mustache">
<div class="content-box-item c-chronicle">
    <div>
        {{#items}}
        <div class="c-friend online clearfix">
            <div class="c-friend-img">
                <img src="{{ img }}" alt="">
            </div>
            <div class="c-friend-description">
                <div class="c-friend-name">{{ title }}</div>
                <div class="c-friend-date">{{ date }}</div>
                <div class="c-friend-news">{{ news }}</div>
                <a href="message" data-block="friends-get-{{ id }}" class="block c-friend-msg">Написать сообщение</a>
            </div>
        </div>
        {{/items}}
        {{^items}}No friends :({{/items}}
    </div>
</div>
</script>

<script id="tmpl-friends-birthdays" type="x-tmpl-mustache">
<div class="content-box-item c-birthdays">
    <div>
        {{#items}}

                        <div class="c-friend clearfix">
                            <div class="c-friend-img">
                                <img src="{{ img }}" alt="">
                            </div>
                            <div class="c-friend-description">
                                <div class="c-friend-name">{{ title }}</div>
                                <div class="c-friend-date">{{ date }}</div>
                                <a href="communication_gift" class="c-friend-gift">Подарить подарок</a>
                                <a href="communication_new_message_selected" class="c-friend-msg">Написать сообщение</a>
                            </div>
                        </div>
        {{/items}}
        {{^items}}No friends :({{/items}}
    </div>
</div>
</script>

<script id="tmpl-friends-requests" type="x-tmpl-mustache">
<div class="content-box-item c-requests">
    <div>
        {{#items}}

                        <div class="c-friend clearfix">
                            <div class="c-friend-img">
                                <img src="{{ img }}" alt="">
                            </div>
                            <div class="c-friend-description">
                                <div class="c-friend-name">{{ title }}</div>
                                <div class="c-friend-date">{{ date }}</div>
                                <div class="c-friend-news">Предлагает дружить</div>
                                <div class="c-friend-actions">
                                    <a href="#" class="yes">Дружить</a>
                                    <a href="#" class="no">Отказаться</a>
                                </div>
                            </div>
                        </div>
        {{/items}}
        {{^items}}No friends :({{/items}}
    </div>
</div>
</script>

<script id="tmpl-friends-search" type="x-tmpl-mustache">
<div class="content-box-item c-search">

                    <div class="content-box-item-top">
                        <div class="friends-search">
                            <input type="text" class="friends-search-input" placeholder="Поиск">
                            <input type="text" class="friends-search-input" placeholder="Поиск по городу">
                        </div>
                    </div>
    <div>

        {{#items}}

                        <div class="c-friend clearfix">
                            <div class="c-friend-img">
                                <img src="{{ img }}" alt="">
                            </div>
                            <div class="c-friend-description">
                                <div class="c-friend-name">{{ title }}</div>
                                <a href="#" class="c-friend-pal">Дружить</a>
                            </div>
                        </div>

        {{/items}}
        {{^items}}No friends :({{/items}}
    </div>
</div>
</script>
<div class="content-top">
	<div class="content-main">

		<div class="content-box communication communication-friends">

			<div class="content-box-header">

				<!-- TABS -->
				<div class="content-box-tabs clearfix">
                    <a href="friends/list" class="content-box-tab communication-friends-tab"><span>Друзья</span></a>
                    <a href="friends/chronicle" class="content-box-tab communication-friends-tab"><span>Хроника</span><span class="chronicle-count">4</span></a>
                    <a href="friends/birthdays" class="content-box-tab communication-friends-tab"><span>Дни рождения</span></a>
                    <a href="friends/requests" class="content-box-tab communication-friends-tab"><span>Запросы</span></a>
                    <a href="friends/search" class="content-box-tab communication-friends-tab"><span>Найти друзей</span></a>
				</div>
				<!-- end of TABS -->

			</div>

			<div class="content-box-content clearfix">

                <div class="loading"><div></div></div>

			</div>

		</div>

	</div><!-- .content-main -->
</div><!-- .content-top -->