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
                        <div class="pz-tl item-title"></div>
                        <div class="pz-ps"><b class="item-price"></b> <?=$MUI->getText('placeholder-points')?></div>
                        <div class="px-des"><?=$MUI->getText('prizes-popup-text')?></div>
                        <div class="pz-ifo-bt"><?=$MUI->getText('button-change')?></div>
                    </div>
                </div>

                <!-- Prize info block -->
                <div class="pz-fm-bk">
                    <div class="fm-txt"><?=$MUI->getText('prizes-order-popup')?></div>
                    <div class="fm-inps-bk">
                        <div class="pi-inp-bk">
                            <div class="ph"><?=$MUI->getText('placeholder-surname')?></div>
                            <input autocomplete="off" spellcheck="false" type="text" name="surname" value="<?=$player->getSurname();?>">
                        </div>
                        <div class="pi-inp-bk">
                            <div class="ph"><?=$MUI->getText('placeholder-name')?></div>
                            <input autocomplete="off" spellcheck="false" type="text" name="name" value="<?=$player->getName();?>">
                        </div>
                        <div class="pi-inp-bk">
                            <div class="ph"><?=$MUI->getText('placeholder-region')?></div>
                            <input autocomplete="off" spellcheck="false" type="text" name="region">
                        </div>
                        <div class="pi-inp-bk td">
                            <div class="ph"><?=$MUI->getText('placeholder-city')?></div>
                            <input autocomplete="off" spellcheck="false" type="text" name="city" placeholder="<?=$MUI->getText('placeholder-city')?>">
                        </div>
                        <div class="pi-inp-bk td">
                            <div class="ph"><?=$MUI->getText('placeholder-address')?></div>
                            <input autocomplete="off" spellcheck="false" type="text" name="addr" placeholder="<?=$MUI->getText('placeholder-address')?>">
                        </div>
                        <div class="pi-inp-bk td">
                            <div class="ph"><?=$MUI->getText('placeholder-phone')?></div>
                            <input autocomplete="off" spellcheck="false" type="tel" name="phone" value="<?=$player->getPhone();?>" data-type="phone" placeholder="<?=$MUI->getText('placeholder-phone')?>">
                        </div>
                    </div>
                    <div class="pz-ifo-bt"><?=$MUI->getText('button-get-prize')?></div>
                </div>

                <!-- Prize report block -->
                <div class="pz-rt-bk" data-default="<?=$MUI->getText('prizes-popup-success')?>"></div>
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
                            <div class="yr-tt-tn"><?=$MUI->getText('placeholder-lototron')?></div>
                            <ul class="yr-tt-tr loto-holder">
                            </ul>
                        </div>
                    </div>
                    <div class="ws-yr-tks-bk">
                        <div class="wr-pf-ph" style="background-image:url('/tpl/img/default.jpg');">
                            <!--img src="/tpl/img/preview/profile-photo.jpg" /-->
                        </div>
                        <div class="wr-pf-pr" style="background-image:url('/tpl/img/preloader.gif');">
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
                    <p id="csh-ch-txt"><?=$MUI->getText('title-money-output')?></p>
                    <p><?=$MUI->getText('holder-choose-method')?>:</p>
                    <ul class="csh-ch-lst">
                        <li>
                            <input type="radio" name="cash" id="cards" hidden />
                            <label for="cards">Пополнение мобильного счета</label>
                        </li>
                        <li>
                            <input type="radio" name="cash" id="qiwi" hidden />
                            <label for="qiwi">QIWI</label>
                        </li>
                        <li>
                            <input type="radio" name="cash" id="webmoney" hidden />
                            <label for="webmoney">WebMoney</label>
                        </li>
                        <li>
                            <input type="radio" name="cash" id="yandex" hidden />
                            <label for="yandex">Яндекс деньги</label>
                        </li>
                        <!--li>
                            <input type="radio" name="cash" id="p24" hidden />
                            <label for="p24">Приват 24</label>
                        </li-->
                    </ul>
                    <script>
                        $('input[name="cash"]').prop('checked', false);
                    </script>
                    <!-- CARDS FORM -->
                    <section class="cards form">
                        <form onsubmit="moneyOutput('phone', this); return false;" method="POST">
                            <div class="inp-bk">
                                <div class="ph">Номер телефона</div>
                                <input autocomplete="off" spellcheck="false" type="tel" placeholder="Номер телефона в международном формате" name="phone" data-title="Телефон" data-type="phone" class="m_input">
                            </div>
                            <div class="inp-bk last">
                                <div class="ph">Сумма</div>
                                <input autocomplete="off" spellcheck="false" type="text" placeholder="Сумма" data-title="Cумма" name="summ" data-type="number" class="m_input">
                            </div>
                            <!--div class="inp-fl-bk">
                                <div class="inp-fl-txt">Прикрепить фото первойстраницы папорта</div>
                                <div class="inp-fl-bt">прикрепить</div>
                                <input type="file" class="f_input" name="file" />
                            </div-->
                            <div class="s-b">
                                <input type="submit" value="вывести" class="sb_but">
                            </div>
                        </form>
                    </section>
                    <!-- QIWI FORM -->
                    <section class="qiwi form">
                        <form onsubmit="moneyOutput('qiwi', this); return false;"  method="POST">
                            <div class="inp-bk">
                                <div class="ph">Номер телефона</div>
                                <input autocomplete="off" spellcheck="false" type="tel" placeholder="Номер телефона в международном формате" name="phone" data-title="Телефон" data-type="phone" class="m_input">
                            </div>
                            <div class="inp-bk last">
                                <div class="ph">Сумма</div>
                                <input autocomplete="off" spellcheck="false" type="text" placeholder="Сумма" data-title="Cумма" name="summ" data-type="number" class="m_input">
                            </div>
                            <!--div class="inp-fl-bk">
                                <div class="inp-fl-txt">Прикрепить фото первойстраницы папорта</div>
                                <div class="inp-fl-bt">прикрепить</div>
                                <input type="file" class="f_input" name="file" />
                            </div-->
                            <div class="s-b">
                                <input type="submit" value="вывести" class="sb_but">
                            </div>
                        </form>
                    </section>

                    <!-- WEBMONEY FORM -->
                    <section class="webmoney form">
                        <form onsubmit="moneyOutput('webmoney', this); return false;"  method="POST">
                            <div class="purse-ch-bk">
                                <div class="ps-ch-tl">Валюта кошелька</div>
                                <div class="ps-r">
                                    <input type="radio" name="purse" data-currency="WMR" data-title="Валюта кошелька" id="wm-r" hidden />
                                    <label for="wm-r">R</label>
                                </div>
                                <div class="ps-u">
                                    <input type="radio" name="purse" data-currency="WMU" data-title="Валюта кошелька" id="wm-u" hidden />
                                    <label for="wm-u">U</label>
                                </div>
                            </div>
                            <div class="inp-bk">
                                <div class="ph">Номер кошелька</div>
                                <input autocomplete="off" spellcheck="false" type="text" placeholder="Номер кошелька" data-title="Номер кошелька" name="card-number" class="m_input">
                            </div>
                            <div class="inp-bk last">
                                <div class="ph">Сумма</div>
                                <input autocomplete="off" spellcheck="false" type="text" placeholder="Сумма" data-title="Cумма" name="summ" data-type="number" class="m_input">
                            </div>
                            <!--div class="inp-fl-bk">
                                <div class="inp-fl-txt">Прикрепить фото первойстраницы папорта</div>
                                <div class="inp-fl-bt">прикрепить</div>
                                <input type="file" class="f_input" name="file" />
                            </div-->
                            <div class="s-b">
                                <input type="submit" value="вывести" class="sb_but">
                            </div>
                        </form>
                    </section>
                    <!-- YANDEX FORM -->
                    <section class="yandex form">
                        <form onsubmit="moneyOutput('yandex', this); return false;" method="POST">
                            <div class="inp-bk">
                                <div class="ph">Номер счета</div>
                                <input autocomplete="off" spellcheck="false" type="text" placeholder="Номер счета" data-title="Номер счета" name="card-number" data-type="number" class="m_input">
                            </div>
                            <div class="inp-bk last">
                                <div class="ph">Сумма</div>
                                <input autocomplete="off" spellcheck="false" type="text" placeholder="Сумма" data-title="Cумма" data-type="number" name="summ" class="m_input">
                            </div>
                            <!--div class="inp-fl-bk">
                                <div class="inp-fl-txt">Прикрепить фото первойстраницы папорта</div>
                                <div class="inp-fl-bt">прикрепить</div>
                                <input autocomplete="off" spellcheck="false" type="file" class="f_input" name="file" />
                            </div-->
                            <div class="s-b">
                                <input autocomplete="off" spellcheck="false" type="submit" value="вывести" class="sb_but">
                            </div>
                        </form>
                    </section>

                    <!-- PRIVAT24 FORM -->
                    <!--section class="p24 form">
                        <form onsubmit="moneyOutput('private24', this); return false;" method="POST">
                            <div class="inp-bk">
                                <div class="ph">Номер карты</div>
                                <input autocomplete="off" spellcheck="false" type="text" placeholder="Номер карты" data-title="Номер карты" name="card-number" data-type="number" maxlength="16" class="m_input">
                            </div>
                            <div class="inp-bk">
                                <div class="ph">Имя Фамилия</div>
                                <input autocomplete="off" spellcheck="false" type="text" placeholder="Имя Фамилия" name="name" data-title="ФИО" class="m_input">
                            </div>
                            <div class="inp-bk last">
                                <div class="ph">Сумма</div>
                                <input autocomplete="off" spellcheck="false" type="text" placeholder="Сумма" data-type="number" data-title="Сумма" name="summ" class="m_input">
                            </div>
                            <!--div class="inp-fl-bk">
                                <div class="inp-fl-txt">Прикрепить фото первойстраницы папорта</div>
                                <div class="inp-fl-bt">прикрепить</div>
                                <input type="file" class="f_input" name="file" />
                            </div- ->
                            <div class="s-b">
                                <input type="submit" value="вывести" class="sb_but">
                            </div>
                        </form>
                    </section-->
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
                        <div class="yr-b-fb"><?=$MUI->getText('game-popup-win')?></div>
                    </div>
                    <div class="yr-s-i">
                        <div class="sb" style="visibility:hidden;">
                            <div class="sb-i">Поделиться хорошей новостью  с друзьями и получить +10 баллов</div>
                            <div class="sb-s"></div>
                            <div class="sb-l">                                
                                <!--a href="javascript:void(0)" class="sb-l_a tw"></a>
                                <a href="javascript:void(0)" class="sb-l_a gp"></a>
                                <a href="javascript:void(0)" class="sb-l_a vk"></a>
                                <a href="javascript:void(0)" class="sb-l_a fb"></a-->
                                <div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="twitter,gplus,vkontakte,facebook"></div>
                            </div>
                        </div>
                        <div class="yr-yw-b">
                            <div class="cb">
                                <div class="yw-t"><?=$MUI->getText('holder-points-win')?><b class="player-points plPointHolder"></b></div>
                                <a href="javascript:void(0);" onclick="location.hash='prizes';location.reload();" class="yw-b"><?=$MUI->getText('button-change')?></a>
                            </div>
                            <div class="mb">
                                <div class="yw-t"><?=$MUI->getText('holder-money-win')?><b class="player-money plMoneyHolder"></b></div>
                                <a href="javascript:void(0);" onclick="location.hash='money';location.reload();" class="yw-b"><?=$MUI->getText('button-output')?></a>
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
                        <?=$MUI->getText('game-popup-fail')?>
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

        </div>
    </div>
</div>


<!--=========================================================================
                            CASH EXCHANGE POPUP CODE
==========================================================================-->

<div class="wt-pp-bk popup" id="cash-exchange-popup">
    <div class="bl-pp_table">
        <div class="bl-pp_td">
            <section class="cash-exchange pop-box">
                <div class="cs"></div>
                <div class="padd">
                    <!-- exchange BLOCK -->
                        <div class="ttl-bk">
                            <div class="if l"><span class='plMoneyHolder'><?=Common::viewNumberFormat($player->getMoney())?></span> <i><?=$currency['many']?><br/><?=$MUI->getText('placeholder-on-balance')?></i></div>
                            <div class="if r"><span class='plPointHolder'><?=Common::viewNumberFormat($player->getPoints())?></span> <i><?=$MUI->getText('placeholder-points')?><br/><?=$MUI->getText('placeholder-on-balance')?></i></div>

                        </div>

                        <div class="fm-txt">
                              <p><?=$MUI->getText('placeholder-input-sum-rate-is')?> <?=$currency['iso']?> = <span id="rate"><?=($currency['rate'])?></span> <?=$MUI->getText('placeholder-points')?>
                        </div>


                        <div class="ttl-bk" id="exchange-input">
                            <div class="pi-inp-bk l">
                                <div class="ph"><?=$currency['iso']?></div>
                                <input autocomplete="off" spellcheck="false"  class="m_input" type="text" data-type="number" id="summ_exchange" name="summ" value="">
                            </div>

                            <div class="if r"><span id="points">0</span> <i><br/><?=$MUI->getText('placeholder-points')?></i></div>


                        </div>

                        <div class="hidden" id="exchange-result">
                            <div class="fm-txt">
                                <p><?=$MUI->getText('text-ready-money-convert-to-points')?></p>
                            </div>
                        </div>

                        <div class="bt">
                            <button class="if-bt" id="exchange-submit" onclick="moneyExchange();"><?=$MUI->getText('button-approve')?></button>
                        </div>

                </div>
            </section>
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
                    <div id="bonuses-h" class="bblock" data-currency="<?=LotterySettings::CURRENCY_POINT?>">
                        <div class="ttl-bk">
                            <div class="nm"><?=$MUI->getText('title-points')?></div>
                            <div class="if"><span class='plPointHolder'><?=Common::viewNumberFormat($player->getPoints())?></span> <i><?=$MUI->getText('placeholder-points')?><br/><?=$MUI->getText('placeholder-on-balance')?></i></div>
                        </div>
                        <div class="tb">
                            <? /* if(is_array($playerTransactions[LotterySettings::CURRENCY_POINT]))
                                foreach ($playerTransactions[LotterySettings::CURRENCY_POINT] as $transaction) { ?>
                                <div class="rw">
                                    <div class="nm td"><span><?=$transaction->getDescription()?></span></div>
                                    <div class="if td"><?=($transaction->getSum() > 0 ? '+' : '')?><?=($transaction->getSum() == 0 ? '' : Common::viewNumberFormat($transaction->getSum()))?></div>
                                    <div class="dt td"><span><?=date('d.m.Y', $transaction->getDate())?></span></div>
                                </div>
                            <? } */ ?>
                        </div>
                        <? /* if (count($playerTransactions[LotterySettings::CURRENCY_POINT]) == controllers\production\Index::TRANSACTIONS_PER_PAGE OR 1) { ?>
                            <div class="pz-more-bt"><?=$MUI->getText('button-more')?></div>
                            <div class="mr-cl-bt-bl">
                                <div class="cl"><?=$MUI->getText('button-hide')?></div>
                                <div class="mr"><?=$MUI->getText('button-more')?></div>
                            </div>
                        <? } */ ?>
                    </div>

                    <!-- CASH BLOCK -->
                    <div id="cash-h" class="bblock" data-currency="<?=LotterySettings::CURRENCY_MONEY?>">
                        <div class="ttl-bk">
                            <div class="nm"><?=$MUI->getText('title-money')?></div>
                            <div class="if"><span class='plMoneyHolder'><?=Common::viewNumberFormat($player->getMoney())?></span> <i><?=$currency['many']?><br/><?=$MUI->getText('placeholder-on-balance')?></i></div>
                        </div>
                        <div class="tb">
                            <? /* if(is_array($playerTransactions[LotterySettings::CURRENCY_MONEY]))
                                foreach ($playerTransactions[LotterySettings::CURRENCY_MONEY] as $transaction) { ?>
                                <div class="rw">
                                    <div class="nm td"><span><?=$transaction->getDescription()?></span></div>
                                    <div class="if td"><?=($transaction->getSum() > 0 ? '+' : '')?><?=Common::viewNumberFormat($transaction->getSum())?></div>
                                    <div class="dt td"><span><?=date('d.m.Y', $transaction->getDate())?></span></div>
                                </div>
                            <? } */ ?>
                        </div>
                        <? /* if (count($playerTransactions[LotterySettings::CURRENCY_MONEY]) == controllers\production\Index::TRANSACTIONS_PER_PAGE OR 1) { ?>
                            <div class="pz-more-bt"><?=$MUI->getText('button-more')?></div>
                            <div class="mr-cl-bt-bl">
                                <div class="cl"><?=$MUI->getText('button-hide')?></div>
                                <div class="mr"><?=$MUI->getText('button-more')?></div>
                            </div>
                        <? } */ ?>
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
                    <h2><?=$MUI->getText('placeholder-terms')?></h2>
                    <?=$MUI->getText('promo-login-rules')?>
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
                    <div class="txt"><?=$MUI->getText('title-logout')?></div>
                    <div class="buts">
                        <div class="exit"><?=$MUI->getText('button-logout')?></div>
                        <div class="back"><?=$MUI->getText('button-stay')?></div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<!--=========================================================================
                            MOMENT GAME POPUP CODE
    ==========================================================================-->
<div class="bl-pp-bk popup" id="Moment-holder" style="display: none;">
    <div class="bl-pp_table">
        <div class="bl-pp_td">
            <section class="quickgame pop-box">
                <div class="cs" onclick="location.reload();"></div>
                <div class="qg-bk-pg" style="position:relative;">
                    <div class="qg-bk-tl"><?=$MUI->getText('title-moment')?></div>
                    <div class="qg-txt"></div>
                    <div>
                        <div class="qg-msg">
                            <div class="td">
                                <div class="txt"></div>
                                <div class="preloader"></div>
                            </div>
                        </div>
                        <ul class="qg-tbl" style="position: relative;">

                        </ul>
                        <div class="block" style="display:none;" ><?=$MUI->getText('placeholder-loading')?></div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>


<!--=========================================================================
                            QUICK GAME POPUP CODE
    ==========================================================================-->
<div class="bl-pp-bk popup" id="QuickGame-holder" style="display: none;">
    <div class="bl-pp_table">
        <div class="bl-pp_td">
            <section class="quickgame pop-box">
                <div class="cs" onclick="location.reload();"></div>
                <div class="qg-bk-pg" style="position:relative;">
                    <div class="qg-bk-tl"><?=$MUI->getText('title-random')?></div>
                    <div class="qg-txt"></div>
                    <div>
                        <div class="qg-msg">
                            <div class="td">
                                <div class="txt"></div>
                                <div class="preloader"></div>
                            </div>
                        </div>
                        <ul class="qg-tbl" style="position: relative;">

                        </ul>
                        <div class="block" style="display:none;" ><?=$MUI->getText('placeholder-loading')?></div>
                    </div>
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
                <div class="txt" data-default="<?=$MUI->getText('prizes-popup-success')?>"></div>
            </section>
        </div>
    </div>
</div>
