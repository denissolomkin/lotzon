<!--=========================================================================
                            PRIZES POPUP CODE
==========================================================================-->
<div class="gr-pp-bk popup" id="shop-items-popup">
    <div class="bl-pp_table">
        <div class="bl-pp_td">
            <section class="prize-info pop-box">
                <div class="cs"></div>
                <!-- Prize info block -->
                <div class="pz-ifo-bk">
                    <div class="pz-ph">
                        <img class="item-preview" src="/tpl/img/preview/catalog-img-6.jpg" />
                    </div>
                    <div class="pz-tl-bk">
                        <div class="pz-tl item-title">Кикерный стол</div>
                        <div class="pz-ps"><b class="item-price">2000</b> баллов</div>
                        <div class="px-des"><?=$staticTexts['prizes-popup-text'][$lang]->getText();?> </div>
                        <div class="pz-ifo-bt">обменять</div>
                    </div>
                </div>

                <!-- Prize info block -->
                <div class="pz-fm-bk">
                    <div class="fm-txt"><?=$staticTexts['prizes-order-popup'][$lang]->getText();?></div>
                    <div class="fm-inps-bk">
                        <div class="pi-inp-bk">
                            <div class="ph">Фамилия</div>
                            <input autocomplete="off" spellcheck="false" type="text" name="surname" value="<?=$player->getSurname();?>">
                        </div>
                        <div class="pi-inp-bk">
                            <div class="ph">Имя</div>
                            <input autocomplete="off" spellcheck="false" type="text" name="name" value="<?=$player->getName();?>">
                        </div>
                        <div class="pi-inp-bk">
                            <div class="ph">Область</div>
                            <input autocomplete="off" spellcheck="false" type="text" name="region">
                        </div>
                        <div class="pi-inp-bk td">
                            <div class="ph">Город / Село / Поселок</div>
                            <input autocomplete="off" spellcheck="false" type="text" name="city" placeholder="Город / Село / Поселок">
                        </div>
                        <div class="pi-inp-bk td">
                            <div class="ph">Адрес</div>
                            <input autocomplete="off" spellcheck="false" type="text" name="addr" placeholder="Адрес">
                        </div>
                        <div class="pi-inp-bk td">
                            <div class="ph">Телефон</div>
                            <input autocomplete="off" spellcheck="false" type="tel" name="phone" value="<?=$player->getPhone();?>" data-type="phone" placeholder="Телефон">
                        </div>
                    </div>
                    <div class="pz-ifo-bt">получить приз</div>
                </div>

                <!-- Prize report block -->
                <div class="pz-rt-bk" data-default="<?=$staticTexts['prizes-popup-success'][$lang]->getText()?>"></div>
            </section>
        </div>
    </div>
</div>


<!--=========================================================================
                            PROFILE HISTORY POPUP CODE
    ==========================================================================-->

<div class="wt-pp-bk popup" id="profile-history">
    <div class="bl-pp_table">
        <div class="bl-pp_td">
            <div class="ws-lt-bk pop-box">
                <div class="cs"></div>
                <i class="ar-l"></i>
                <i class="ar-r"></i>
                <section class="ws-pf-rt-bk pop-box">
                    <div class="ws-pf-tl">
                        <time class="ws-dt">22.08.2014</time>
                        <div class="yr-tt">
                            <div class="yr-tt-tn">лототрон</div>
                            <ul class="yr-tt-tr loto-holder">
                            </ul>
                        </div>
                    </div>
                    <div class="ws-yr-tks-bk">
                        <div class="wr-pf-ph">
                            <img src="/tpl/img/preview/profile-photo.jpg" />
                        </div>
                        <ul class="yr-tb">
                        </ul>
                    </div>
                </section>
                <time class="ws-dt ch-hide"></time>
                <ul class="ws-lt">
                </ul>
            </div>
        </div>
    </div>
</div>



<!--=========================================================================
                            CASH POPUP CODE
==========================================================================-->

<div class="bl-pp-bk popup" id="cash-output-popup">
    <div class="bl-pp_table">
        <div class="bl-pp_td">
            <section class="csh-ch-bk pop-box">
                <div class="cl-bt cs"></div>
                <div class="padd">
                    <p id="csh-ch-txt">Поступление денег производится в течение 3-х банковских дней бла-бла-бла. Для вывода суммы превышающей 200 грн. необходимо прикрепить фото документа удостоверяющего личность бла-бла-бла</p>
                    <p>Выберите способ вывода денежных стредств:</p>
                    <ul class="csh-ch-lst">
                        <li>
                            <input type="radio" name="cash" id="cards" hidden />
                            <label for="cards">VISA / MasterCard</a></label>
                        </li>
                        <li>
                            <input type="radio" name="cash" id="qiwi" hidden />
                            <label for="qiwi">QIWI</a></label>
                        </li>
                        <li>
                            <input type="radio" name="cash" id="webmoney" hidden />
                            <label for="webmoney">WebMoney</a></label>
                        </li>
                        <li>
                            <input type="radio" name="cash" id="yandex" hidden />
                            <label for="yandex">Яндекс деньги</a></label>
                        </li>
                        <li>
                            <input type="radio" name="cash" id="p24" hidden />
                            <label for="p24">Приват 24</a></label>
                        </li>
                    </ul>
                    <script>
                        $('input[name="cash"]').prop('checked', false);
                    </script>
                    <!-- CARDS FORM -->
                    <section class="cards form">
                        <form>
                            <div class="inp-bk">
                                <div class="ph">Номер</div>
                                <input autocomplete="off" spellcheck="false" type="text" placeholder="Номер карты" name="card-number" data-type="number" maxlength="16" class="m_input">
                            </div>
                            <div class="inp-bk">
                                <div class="ph">Имя Фамилия</div>
                                <input autocomplete="off" spellcheck="false" type="text" placeholder="Имя Фамилия (латиницей)" name="name" class="m_input">
                            </div>
                            <div class="inp-bk last">
                                <div class="ph">Сумма</div>
                                <input autocomplete="off" spellcheck="false" type="text" placeholder="Сумма" name="summ" data-type="number" class="m_input">
                            </div>
                            <div class="inp-fl-bk">
                                <div class="inp-fl-txt">Прикрепить фото первойстраницы папорта</div>
                                <div class="inp-fl-bt">прикрепить</div>
                                <input type="file" class="f_input" name="file" />
                            </div>
                            <div class="s-b">
                                <input type="submit" value="вывести" class="sb_but">
                            </div>
                        </form>
                    </section>
                    <!-- QIWI FORM -->
                    <section class="qiwi form">
                        <form>
                            <div class="inp-bk">
                                <div class="ph">Номер телефона</div>
                                <input autocomplete="off" spellcheck="false" type="tel" placeholder="Номер телефона в международном формате" name="phone" data-type="phone" class="m_input">
                            </div>
                            <div class="inp-bk last">
                                <div class="ph">Сумма</div>
                                <input autocomplete="off" spellcheck="false" type="text" placeholder="Сумма" name="summ" data-type="number" class="m_input">
                            </div>
                            <div class="inp-fl-bk">
                                <div class="inp-fl-txt">Прикрепить фото первойстраницы папорта</div>
                                <div class="inp-fl-bt">прикрепить</div>
                                <input type="file" class="f_input" name="file" />
                            </div>
                            <div class="s-b">
                                <input type="submit" value="вывести" class="sb_but">
                            </div>
                        </form>
                    </section>

                    <!-- WEBMONEY FORM -->
                    <section class="webmoney form">
                        <form>
                            <div class="purse-ch-bk">
                                <div class="ps-ch-tl">Валюта кошелька</div>
                                <div class="ps-r">
                                    <input type="radio" name="purse" id="wm-r" hidden />
                                    <label for="wm-r">R</a></label>
                                </div>
                                <div class="ps-u">
                                    <input type="radio" name="purse" id="wm-u" hidden />
                                    <label for="wm-u">U</a></label>
                                </div>
                                <div class="ps-b">
                                    <input type="radio" name="purse" id="wm-b" hidden />
                                    <label for="wm-b">B</a></label>
                                </div>
                            </div>
                            <div class="inp-bk">
                                <div class="ph">Номер кошелька</div>
                                <input autocomplete="off" spellcheck="false" type="text" placeholder="Номер кошелька" name="card-number" data-type="number" class="m_input">
                            </div>
                            <div class="inp-bk last">
                                <div class="ph">Сумма</div>
                                <input autocomplete="off" spellcheck="false" type="text" placeholder="Сумма" name="summ" data-type="number" class="m_input">
                            </div>
                            <div class="inp-fl-bk">
                                <div class="inp-fl-txt">Прикрепить фото первойстраницы папорта</div>
                                <div class="inp-fl-bt">прикрепить</div>
                                <input type="file" class="f_input" name="file" />
                            </div>
                            <div class="s-b">
                                <input type="submit" value="вывести" class="sb_but">
                            </div>
                        </form>
                    </section>
                    <!-- YANDEX FORM -->
                    <section class="yandex form">
                        <form>
                            <div class="inp-bk">
                                <div class="ph">Номер счета</div>
                                <input autocomplete="off" spellcheck="false" type="text" placeholder="Номер счета" name="card-number" data-type="number" class="m_input">
                            </div>
                            <div class="inp-bk last">
                                <div class="ph">Сумма</div>
                                <input autocomplete="off" spellcheck="false" type="text" placeholder="Сумма" data-type="number" name="summ" class="m_input">
                            </div>
                            <div class="inp-fl-bk">
                                <div class="inp-fl-txt">Прикрепить фото первойстраницы папорта</div>
                                <div class="inp-fl-bt">прикрепить</div>
                                <input autocomplete="off" spellcheck="false" type="file" class="f_input" name="file" />
                            </div>
                            <div class="s-b">
                                <input autocomplete="off" spellcheck="false" type="submit" value="вывести" class="sb_but">
                            </div>
                        </form>
                    </section>

                    <!-- PRIVAT24 FORM -->
                    <section class="p24 form">
                        <form>
                            <div class="inp-bk">
                                <div class="ph">Номер карты</div>
                                <input autocomplete="off" spellcheck="false" type="text" placeholder="Номер карты" name="card-number" data-type="number" maxlength="16" class="m_input">
                            </div>
                            <div class="inp-bk">
                                <div class="ph">Имя Фамилия</div>
                                <input autocomplete="off" spellcheck="false" type="text" placeholder="Имя Фамилия" name="name" class="m_input">
                            </div>
                            <div class="inp-bk last">
                                <div class="ph">Сумма</div>
                                <input autocomplete="off" spellcheck="false" type="text" placeholder="Сумма" data-type="number" name="summ" class="m_input">
                            </div>
                            <div class="inp-fl-bk">
                                <div class="inp-fl-txt">Прикрепить фото первойстраницы папорта</div>
                                <div class="inp-fl-bt">прикрепить</div>
                                <input type="file" class="f_input" name="file" />
                            </div>
                            <div class="s-b">
                                <input type="submit" value="вывести" class="sb_but">
                            </div>
                        </form>
                    </section>
                </div>
            </section>
        </div>
    </div>
</div>




<!-- ==========================================================================
                                        GAME POPUP
========================================================================== -->

<div class="bl-pp-bk popup" id="game-itself">
    <div class="bl-pp_table">
        <div class="bl-pp_td">


        <!-- WON BLOCK -->
        <div id="game-won" class="pop-box">
            <section class="gpc-pad">
                <a href="javascript:void(0)" class="b-c-p cs" onclick="document.location.reload();"></a>
                <ul class="g-oc-b">
                </ul>
                <div class="gw-c-b">
                    <div class="yr-b">
                        <ul class="yr-tb">
                        </ul>
                        <div class="gw-sep"></div>
                        <div class="yr-b-fb"><?=$staticTexts['game-popup-win'][$lang]->getText()?></div>
                    </div>
                    <div class="yr-s-i">
                        <div class="sb">
                            <div class="sb-i">Поделиться хорошей новостью  с друзьями и получить +10 баллов</div>
                            <div class="sb-s"></div>
                            <div class="sb-l">                                
                                <!--a href="javascript:void(0)" class="sb-l_a tw"></a>
                                <a href="javascript:void(0)" class="sb-l_a gp"></a>
                                <a href="javascript:void(0)" class="sb-l_a vk"></a>
                                <a href="javascript:void(0)" class="sb-l_a fb"></a-->
                                <!--div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="twitter,gplus,vkontakte,facebook"></div-->
                            </div>
                        </div>
                        <div class="yr-yw-b">
                            <div class="cb">
                                <div class="yw-t">баллов<br>на счету<b class="player-points"></b></div>
                                <a href="javascript:void(0);" onclick="location.hash='prizes';location.reload();" class="yw-b">обменять</a>
                            </div>
                            <div class="mb">
                                <div class="yw-t">денег<br>на счету<b class="player-money"></b></div>
                                <a href="javascript:void(0);" onclick="location.hash='money';location.reload();" class="yw-b">вывести</a>
                            </div>
                        </div>
                    </div>
                    <div class="b-cl-block"></div>
                </div>
            </section>

        </div>
        <!-- END WON BLOCK -->

        <!-- END BLOCK -->
        <div id="game-end" class="pop-box">
            <section class="gpc-pad">
                <a href="javascript:void(0)" onclick="document.location.reload();" class="b-c-p"></a>
                <ul class="g-oc-b">
                </ul>
                <div class="gw-c-b">
                    <div class="yr-b">
                        <ul class="yr-tb">
                        </ul>
                        <div class="yr-b-fb"></div>
                    </div>
                    <div class="yr-s-i">
                        <?=$staticTexts['game-popup-fail'][$lang]->getText()?>
                    </div>
                    <div class="b-cl-block"></div>
                </div>
            </section>

        </div>
        <!-- END END BLOCK -->

        <!-- END BLOCK -->
        <div id="game-process"  class="pop-box">
            <section class="gpc-pad">
                <div class="gp-bz"><img src="/tpl/img/baners/Plug-300х665.png" width="300" /></div>

                <ul class="g-oc-b">
                    <li class="g-oc_li">
                        <div class="goc_li-nb">
                            <span class="g-oc_span unfilled"></span>
                        </div>
                        <div class="goc_li-sh"></div>
                        <div class="goc_li-sh2"></div>
                    </li>
                    <li class="g-oc_li">
                        <div class="goc_li-nb">
                            <span class="g-oc_span unfilled"></span>
                        </div>
                        <div class="goc_li-sh "></div>
                        <div class="goc_li-sh2"></div>
                    </li>
                    <li class="g-oc_li">
                        <div class="goc_li-nb">
                            <span class="g-oc_span unfilled"></span>
                        </div>
                        <div class="goc_li-sh"></div>
                        <div class="goc_li-sh2"></div>
                    </li>
                    <li class="g-oc_li">
                        <div class="goc_li-nb">
                            <span class="g-oc_span unfilled"></span>
                        </div>
                        <div class="goc_li-sh"></div>
                        <div class="goc_li-sh2"></div>
                    </li>
                    <li class="g-oc_li">
                        <div class="goc_li-nb">
                            <span class="g-oc_span unfilled"></span>
                        </div>
                        <div class="goc_li-sh"></div>
                        <div class="goc_li-sh2"></div>
                    </li>
                    <li class="g-oc_li">
                        <div class="goc_li-nb">
                            <span class="g-oc_span unfilled"></span>
                        </div>
                        <div class="goc_li-sh"></div>
                        <div class="goc_li-sh2"></div>
                    </li>
                </ul>
                <div class="gw-c-b">
                    <div class="yr-b">
                        <ul class="yr-tb"></ul>
                        <div class="yr-b-fb"></div>
                    </div>
                    <div class="b-cl-block"></div>
                </div>
            </section>
        </div>
        <!-- END END BLOCK -->
<script>
    $('.g-oc_li').click(function(){
        var li = $(this);
        $(this).find('.goc_li-nb').addClass('goc-nb-act');
        setTimeout(function(){
            li.removeClass('goc-tm');
        }, 1000);
    });
</script>

        </div>
    </div>
</div>



<!--=========================================================================
                            TRANSACTIONS HISTORY POPUP CODE
==========================================================================-->
<div class="wt-pp-bk popup" id="ta-his-popup">
    <div class="bl-pp_table">
        <div class="bl-pp_td">
            <section class="cash-history pop-box">
                <div class="cs"></div>
                <div class="padd">

                    <!-- BONUSES BLOCK -->
                    <div id="bonuses-h" class="bblock" data-currency="<?=GameSettings::CURRENCY_POINT?>">
                        <div class="ttl-bk">
                            <div class="nm">баллы</div>
                            <div class="if"><?=number_format($player->getPoints(), 0, '.', ' ')?> <i>баллов<br/>на счету</i></div>
                            <div class="bt">
                                <div class="if-bt points-get" onclick="$('#ta-his-popup').hide();$('#exchange').click();">обменять</div>
                            </div>
                        </div>
                        <div class="tb">
                            <? foreach ($playerTransactions[GameSettings::CURRENCY_POINT] as $transaction) { ?>
                                <div class="rw">
                                    <div class="nm td"><span><?=$transaction->getDescription()?></span></div>
                                    <div class="if td"><?=($transaction->getSum() > 0 ? '+' : '')?><?=($transaction->getSum() == 0 ? '' : $transaction->getSum())?></div>
                                    <div class="dt td"><span><?=date('d.m.Y', $transaction->getDate())?></span></div>
                                </div>
                            <? } ?>
                        </div>
                        <? if (count($playerTransactions[GameSettings::CURRENCY_POINT]) == controllers\production\Index::TRANSACTIONS_PER_PAGE) { ?>
                            <div class="pz-more-bt">ПОКАЗАТЬ ЕЩЕ</div>
                            <div class="mr-cl-bt-bl">
                                <div class="cl">свернуть</div>
                                <div class="mr">ПОКАЗАТЬ ЕЩЕ</div>
                            </div>
                        <? } ?>
                    </div>

                    <!-- BONUSES BLOCK -->
                    <div id="cash-h" class="bblock" data-currency="<?=GameSettings::CURRENCY_POINT?>">
                        <div class="ttl-bk">
                            <div class="nm">деньги</div>
                            <div class="if"><?=number_format($player->getMoney(), 0, '.', ' ')?> <i><?=$country == 'UA' ? 'гривен' : 'рублей'?><br/>на счету</i></div>
                            <div class="bt">
                                <div class="if-bt  money-get" onclick="$('#ta-his-popup').hide();$('#cash-output').click();">вывести</div>
                            </div>
                        </div>
                        <div class="tb">
                            <? foreach ($playerTransactions[GameSettings::CURRENCY_MONEY] as $transaction) { ?>
                                <div class="rw">
                                    <div class="nm td"><span><?=$transaction->getDescription()?></span></div>
                                    <div class="if td"><?=($transaction->getSum() > 0 ? '+' : '')?><?=$transaction->getSum()?></div>
                                    <div class="dt td"><span><?=date('d.m.Y', $transaction->getDate())?></span></div>
                                </div>
                            <? } ?>
                        </div>
                        <? if (count($playerTransactions[GameSettings::CURRENCY_MONEY]) == controllers\production\Index::TRANSACTIONS_PER_PAGE) { ?>
                            <div class="pz-more-bt">загрузить еще</div>
                            <div class="mr-cl-bt-bl">
                                <div class="cl">свернуть</div>
                                <div class="mr">загрузить еще</div>
                            </div>
                        <? } ?>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>


<!--=========================================================================
                            TERMS POPUP CODE
==========================================================================-->

<div class="bl-pp-bk popup" id="terms">
    <div class="bl-pp_table">
        <div class="bl-pp_td">
            <section class="rules-bk">
                <div class="cs"></div>
                <div class="rb-pg">
                    <h2>УСЛОВИЯ<br/>УЧАСТИЯ</h2>
                    <?=$staticTexts['promo-login-rules'][$lang]->getText()?>
                </div>
            </section>
        </div>
    </div>
</div>



<!--=========================================================================
                            LOGOUT POPUP CODE
==========================================================================-->

<div class="bl-pp-bk popup" id="logout-popup">
    <div class="bl-pp_table">
        <div class="bl-pp_td">
            <section class="logout-bk pop-box">
                <div class="cs"></div>
                <div class="cnt">
                    <div class="txt"><?=$staticTexts['logout'][$lang]->getText();?></div>
                    <div class="buts">
                        <div class="exit">выйти</div>
                        <div class="back">остаться</div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>


<!--=========================================================================
                            MOMENTUM POPUP CODE
    ==========================================================================-->
<div class="bl-pp-bk popup" id="mchance" data-points-win="<b><?=$chanceGames['moment']->getPointsWin()?></b> баллов">
    <div class="bl-pp_table">
        <div class="bl-pp_td">
            <section class="momentum pop-box">
                <!--div class="cs"></div-->
                <div class="mm-bk-pg">
                    <div class="mm-bk-tl">Моментальный шанс</div>
                    <div class="mm-txt"><?=$staticTexts['moment-chance'][$lang]->getText();?></div>
                    <ul class="mm-tbl">
                        <li class="" data-num="1"></li>
                        <li class="" data-num="2"></li>
                        <li class="" data-num="3"></li>
                    </ul>
                </div>
            </section>
        </div>
    </div>
</div>



<!--=========================================================================
                            REPORT POPUP CODE
==========================================================================-->
<div class="gr-pp-bk popup" id="report-popup">
    <div class="bl-pp_table">
        <div class="bl-pp_td">
            <section class="report-block pop-box">
                <div class="cs"></div>
                <!-- Prize report block -->
                <div class="txt" data-default="<?=$staticTexts['prizes-popup-success'][$lang]->getText()?>">a as dasd asd </div>
            </section>
        </div>
    </div>
</div>

