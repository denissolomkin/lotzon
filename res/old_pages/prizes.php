<script id="tmpl-prizes-exchange" type="x-tmpl-mustache">

<div class="content-box-item with-cat prizes-for-points">
    <div class="content-box-item-top clearfix">
        <div class="content-box-cat">
            {{#categories}}
            <a href="#" data-category={{ id }}>{{ title }}</a>
            {{/categories}}
        </div>
    </div>

    <div class="content-box-item-content">
        {{#items}}
            <div class="prize category-{{ category }}">
        <a href="prize_exchange">
                <div class="prize-content">
                    <div class="prize-img">
                        <img src="{{ img }}" alt="">
                    </div>
                    <div class="prize-name">{{ title }}</div>
                    <div class="prize-price">{{ price }}<span>баллов</span></div>
                </div>
                <div class="prize-inf">Ограниченное кол-во<br>25 шт.</div>
        </a>
            </div>
        {{/items}}
        {{^items}}No prizes :({{/items}}
    </div>
</div>

</script>

<div class="content-top">
    <div class="content-main">

        <div class="content-box">

            <div class="content-box-header">
                <div class="content-box-tabs clearfix">
                    <a href="prizes/exchange" class="content-box-tab active">Призы за баллы</a>
                    <a data-json="prizes-draw" href="#" class="content-box-tab">Розыгрыш призов</a>
                </div>
            </div>

            <div class="content-box-content clearfix">
                <div class="loading"><div></div></div>
            </div>

        </div>

    </div>
    <!-- .content-main -->
</div><!-- .content-top -->