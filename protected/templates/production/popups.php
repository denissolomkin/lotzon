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
                        <div class="px-des"><?=$staticTexts['prizes-popup-text'][$lang]->getText()?> </div>
                        <div class="pz-ifo-bt">обменять</div>
                    </div>
                </div>

                <!-- Prize info block -->
                <div class="pz-fm-bk">
                    <div class="fm-txt"><?=$staticTexts['prizes-order-popup'][$lang]->getText()?></div>
                    <div class="fm-inps-bk">
                        <div class="pi-inp-bk">
                            <div class="ph">Фамилия</div>
                            <input type="text" name="surname" value="<?=$player->getSurname();?>">
                        </div>
                        <div class="pi-inp-bk">
                            <div class="ph">Имя</div>
                            <input type="text" name="name" value="<?=$player->getName();?>">
                        </div>
                        <div class="pi-inp-bk">
                            <div class="ph">Область</div>
                            <input type="text" name="region">
                        </div>
                        <div class="pi-inp-bk td">
                            <div class="ph">Город / Село / Поселок</div>
                            <input type="text" name="nick" placeholder="Город / Село / Поселок">
                        </div>
                        <div class="pi-inp-bk td">
                            <div class="ph">Адрес</div>
                            <input type="text" name="nick" placeholder="Адрес" maxlength="40">
                        </div>
                        <div class="pi-inp-bk td">
                            <div class="ph">Телефон</div>
                            <input type="tel" name="nick" value="<?=$player->getPhone();?>" data-type="phone" placeholder="Телефон" maxlength="40">
                        </div>
                    </div>
                    <div class="pz-ifo-bt">получить приз</div>
                </div>

                <!-- Prize report block -->
                <div class="pz-rt-bk"><?=$staticTexts['prizes-popup-success'][$lang]->getText()?></div>
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
            <div class="ws-lt-bk">
                <div class="cs"></div>
                <section class="ws-pf-rt-bk pop-box">
                    <div class="ws-pf-tl">
                        <time class="ws-dt">22.08.2014</time>
                        <div class="yr-tt">
                            <div class="yr-tt-tn">лототрон</div>
                            <ul class="yr-tt-tr">
                                <li class="yr-tt-tr_li">3</li>
                                <li class="yr-tt-tr_li">32</li>
                                <li class="yr-tt-tr_li">41</li>
                                <li class="yr-tt-tr_li">16</li>
                                <li class="yr-tt-tr_li">8</li>
                                <li class="yr-tt-tr_li">6</li>
                            </ul>
                        </div>
                    </div>
                    <div class="ws-yr-tks-bk">
                        <i class="ar-l"></i>
                        <i class="ar-r"></i>
                        <div class="wr-pf-ph">
                            <img src="/tpl/img/preview/profile-photo.jpg" />
                        </div>
                        <ul class="yr-tb">
                            <li class="yr-tt">
                                <div class="yr-tt-tn">Билет #1</div>
                                <ul class="yr-tt-tr">
                                    <li class="yr-tt-tr_li">3</li>
                                    <li class="yr-tt-tr_li won">32</li>
                                    <li class="yr-tt-tr_li">41</li>
                                    <li class="yr-tt-tr_li">16</li>
                                    <li class="yr-tt-tr_li won">8</li>
                                    <li class="yr-tt-tr_li">6</li>
                                </ul>
                            </li>
                            <li class="yr-tt">
                                <div class="yr-tt-tn">Билет #2</div>
                                <ul class="yr-tt-tr">
                                    <li class="yr-tt-tr_li">3</li>
                                    <li class="yr-tt-tr_li won">32</li>
                                    <li class="yr-tt-tr_li won">41</li>
                                    <li class="yr-tt-tr_li won">16</li>
                                    <li class="yr-tt-tr_li">8</li>
                                    <li class="yr-tt-tr_li">6</li>
                                </ul>
                            </li>
                            <li class="yr-tt">
                                <div class="yr-tt-tn">Билет #3</div>
                                <ul class="yr-tt-tr">
                                    <li class="yr-tt-tr_li">3</li>
                                    <li class="yr-tt-tr_li">32</li>
                                    <li class="yr-tt-tr_li">41</li>
                                    <li class="yr-tt-tr_li">16</li>
                                    <li class="yr-tt-tr_li">8</li>
                                    <li class="yr-tt-tr_li">6</li>
                                </ul>
                            </li>
                            <li class="yr-tt">
                                <div class="yr-tt-tn">Билет #4</div>
                                <ul class="yr-tt-tr">
                                    <li class="yr-tt-tr_li">3</li>
                                    <li class="yr-tt-tr_li">32</li>
                                    <li class="yr-tt-tr_li">41</li>
                                    <li class="yr-tt-tr_li">16</li>
                                    <li class="yr-tt-tr_li">8</li>
                                    <li class="yr-tt-tr_li">6</li>
                                </ul>
                            </li>
                            <li class="yr-tt">
                                <div class="yr-tt-tn">Билет #5</div>
                                <ul class="yr-tt-tr">
                                    <li class="yr-tt-tr_li">3</li>
                                    <li class="yr-tt-tr_li">32</li>
                                    <li class="yr-tt-tr_li">41</li>
                                    <li class="yr-tt-tr_li">16</li>
                                    <li class="yr-tt-tr_li">8</li>
                                    <li class="yr-tt-tr_li">6</li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </section>
                <ul class="ws-lt">
                    <li>
                        <div class="tl">
                            <div class="ph"><img src="/tpl/img/comment-photo-2.jpg" /></div>
                            <div class="nm">Наум Коробочко</div>
                        </div>
                    </li>
                    <li>
                        <div class="tl">
                            <div class="ph"><img src="/tpl/img/comment-photo-3.jpg" /></div>
                            <div class="nm">Александер Шпиц</div>
                        </div>
                    </li>
                    <li class="you">
                        <div class="tl">
                            <div class="ph"><img src="/tpl/img/comment-photo-5.jpg" /></div>
                            <div class="nm">Антон Семеневич Шпак</div>
                        </div>
                    </li>
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
                                <input type="text" placeholder="Номер карты" name="card-number" data-type="number" maxlength="16" class="m_input">
                            </div>
                            <div class="inp-bk">
                                <div class="ph">Имя Фамилия</div>
                                <input type="text" placeholder="Имя Фамилия (латиницей)" name="name" class="m_input">
                            </div>
                            <div class="inp-bk last">
                                <div class="ph">Сумма</div>
                                <input type="text" placeholder="Сумма" name="summ" data-type="number" class="m_input">
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
                                <input type="tel" placeholder="Номер телефона в международном формате" name="phone" data-type="phone" class="m_input">
                            </div>
                            <div class="inp-bk last">
                                <div class="ph">Сумма</div>
                                <input type="text" placeholder="Сумма" name="summ" data-type="number" class="m_input">
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
                                <input type="text" placeholder="Номер кошелька" name="card-number" data-type="number" class="m_input">
                            </div>
                            <div class="inp-bk last">
                                <div class="ph">Сумма</div>
                                <input type="text" placeholder="Сумма" name="summ" data-type="number" class="m_input">
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
                                <input type="text" placeholder="Номер счета" name="card-number" data-type="number" class="m_input">
                            </div>
                            <div class="inp-bk last">
                                <div class="ph">Сумма</div>
                                <input type="text" placeholder="Сумма" data-type="number" name="summ" class="m_input">
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

                    <!-- PRIVAT24 FORM -->
                    <section class="p24 form">
                        <form>
                            <div class="inp-bk">
                                <div class="ph">Номер карты</div>
                                <input type="text" placeholder="Номер карты" name="card-number" data-type="number" maxlength="16" class="m_input">
                            </div>
                            <div class="inp-bk">
                                <div class="ph">Имя Фамилия</div>
                                <input type="text" placeholder="Имя Фамилия" name="name" class="m_input">
                            </div>
                            <div class="inp-bk last">
                                <div class="ph">Сумма</div>
                                <input type="text" placeholder="Сумма" data-type="number" name="summ" class="m_input">
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
                <a href="javascript:void(0)" class="b-c-p"></a>
                <ul class="g-oc-b">
                </ul>
                <div class="gw-c-b">
                    <div class="yr-b">
                        <ul class="yr-tb">
                        </ul>
                        <div class="yr-b-fb"><?=$staticTexts['game-popup-win'][$lang]->getText()?></div>
                    </div>
                    <div class="yr-s-i">
                        <div class="sb">
                            <div class="sb-i">Поделиться хорошей новостью  с друзьями и получить +10 баллов</div>
                            <div class="sb-s"></div>
                            <div class="sb-l">
                                <a href="javascript:void(0)" class="sb-l_a tw"></a>
                                <a href="javascript:void(0)" class="sb-l_a gp"></a>
                                <a href="javascript:void(0)" class="sb-l_a vk"></a>
                                <a href="javascript:void(0)" class="sb-l_a fb"></a>
                            </div>
                        </div>
                        <div class="yr-yw-b">
                            <div class="cb">
                                <div class="yw-t">баллов<br>на счету<b class="player-points"></b></div>
                                <a href="" class="yw-b">обменять</a>
                            </div>
                            <div class="mb">
                                <div class="yw-t">денег<br>на счету<b class="player-money"></b></div>
                                <a href="" class="yw-b">вывести</a>
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
                <a href="javascript:void(0)" class="b-c-p"></a>
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
                <div class="gp-bz"><img src="/tpl/img/banner.jpg" width="300" height="685" /></div>

                <ul class="g-oc-b">
                    <li class="g-oc_li goc-tm">
                        <div class="goc_li-nb">
                            <span class="g-oc_span unfilled"></span>
                        </div>
                        <div class="goc_li-sh"></div>
                    </li>
                    <li class="g-oc_li goc-tm">
                        <div class="goc_li-nb">
                            <span class="g-oc_span unfilled"></span>
                        </div>
                        <div class="goc_li-sh "></div>
                    </li>
                    <li class="g-oc_li goc-tm">
                        <div class="goc_li-nb">
                            <span class="g-oc_span unfilled"></span>
                        </div>
                        <div class="goc_li-sh"></div>
                    </li>
                    <li class="g-oc_li goc-tm">
                        <div class="goc_li-nb">
                            <span class="g-oc_span unfilled"></span>
                        </div>
                        <div class="goc_li-sh"></div>
                    </li>
                    <li class="g-oc_li goc-tm">
                        <div class="goc_li-nb">
                            <span class="g-oc_span unfilled"></span>
                        </div>
                        <div class="goc_li-sh"></div>
                    </li>
                    <li class="g-oc_li goc-tm">
                        <div class="goc_li-nb">
                            <span class="g-oc_span unfilled"></span>
                        </div>
                        <div class="goc_li-sh"></div>
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
                            PRIZES POPUP CODE
==========================================================================-->
<div class="wt-pp-bk popup" id="shop-items-popup">
    <div class="bl-pp_table">
        <div class="bl-pp_td">
            <section class="cash-history pop-box">
                <div class="cs"></div>
                <div class="padd">

                    <!-- BONUSES BLOCK -->
                    <div id="bonuses-h">
                        <div class="ttl-bk">
                            <div class="nm">баллы</div>
                            <div class="if">4 600 <i>баллов<br/>на счету</i></div>
                            <div class="bt">
                                <div class="if-bt">обменять</div>
                            </div>
                        </div>
                        <div class="tb">
                            <div class="rw">
                                <div class="nm td"><span>Приглашение друга pavel@mail.ru</span></div>
                                <div class="if td">+10</div>
                                <div class="dt td"><span>29.09.2014</span></div>
                            </div>
                            <div class="rw">
                                <div class="nm td"><span>Приглашение друга pavel@mail.ru</span></div>
                                <div class="if td">-210</div>
                                <div class="dt td"><span>29.09.2014</span></div>
                            </div>
                            <div class="rw">
                                <div class="nm td"><span>Приглашение друга pavel@mail.ru</span></div>
                                <div class="if td">+150</div>
                                <div class="dt td"><span>29.09.2014</span></div>
                            </div>
                            <div class="rw">
                                <div class="nm td"><span>Лотерея шанс (выигрыш iPhone)</span></div>
                                <div class="if td">-410</div>
                                <div class="dt td"><span>29.09.2014</span></div>
                            </div>
                            <div class="rw">
                                <div class="nm td"><span>Приглашение друга pavel@mail.ru</span></div>
                                <div class="if td">+10</div>
                                <div class="dt td"><span>29.09.2014</span></div>
                            </div>
                        </div>
                        <div class="pz-more-bt">загрузить еще</div>
                    </div>

                    <!-- BONUSES BLOCK -->
                    <div id="cash-h">
                        <div class="ttl-bk">
                            <div class="nm">деньги</div>
                            <div class="if">4 600 <i>гривен<br/>на счету</i></div>
                            <div class="bt">
                                <div class="if-bt">вывести</div>
                            </div>
                        </div>
                        <div class="tb">
                            <div class="rw">
                                <div class="nm td"><span>Приглашение друга pavel@mail.ru</span></div>
                                <div class="if td">+10</div>
                                <div class="dt td"><span>29.09.2014</span></div>
                            </div>
                            <div class="rw">
                                <div class="nm td"><span>Обмен на приз: часы Seiko </span></div>
                                <div class="if td">+30</div>
                                <div class="dt td"><span>29.09.2014</span></div>
                            </div>
                            <div class="rw">
                                <div class="nm td"><span>Приглашение друга pavel@mail.ru</span></div>
                                <div class="if td">-110</div>
                                <div class="dt td"><span>29.09.2014</span></div>
                            </div>
                            <div class="rw">
                                <div class="nm td"><span>Приглашение друга pavel@mail.ru</span></div>
                                <div class="if td">+10</div>
                                <div class="dt td"><span>29.09.2014</span></div>
                            </div>
                            <div class="rw">
                                <div class="nm td"><span>Приглашение друга pavel@mail.ru</span></div>
                                <div class="if td">+10</div>
                                <div class="dt td"><span>29.09.2014</span></div>
                            </div>
                        </div>
                        <div class="pz-more-bt">загрузить еще</div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
