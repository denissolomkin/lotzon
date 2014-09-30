<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title></title>
        <meta name="description" content="">
        <meta name="keywords" content="" />
        <meta name="robots" content="all" />
        <meta name="publisher" content="" />
        <meta http-equiv="reply-to" content="" />
        <meta name="distribution" content="global" />
        <meta name="revisit-after" content="1 days" />

        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />

        <link rel="stylesheet" href="/tpl/css/normalize.css" />
        <link rel="stylesheet" href="/tpl/css/promo.css" />

        <link rel="icon" href="" type="image/png" />
        <link rel="shortcut icon" href="" type="'image/x-icon"/>

        <!-- For iPhone 4 Retina display: -->
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="">
        <!-- For iPad: -->
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="">
        <!-- For iPhone: -->
        <link rel="apple-touch-icon-precomposed" href="">

        <script src="/tpl/js/lib/modernizr.js"></script>
        <script src="/tpl/js/lib/jquery.min.js"></script>
        <script src="/tpl/js/lib/jquery-ui.min.js"></script>
        <script src="/tpl/js/lib/jquery.plugin.min.js"></script>
        <script src="/tpl/js/lib/jquery.countdown.min.js"></script>

    </head>
    <body>
        <header class="display-slide" id="slide1">
            <div class="h-dt-b">
                <div class="t-tr">
                    <div class="t-tr_td">
                        <div class="h-t-tcb">
                            <div class="h-t-tcb-l"><a href="/"><img src="/tpl/img/promo-header-logo.png" width="238" height="60" /></a></div>
                            <div class="h-t-tcb-sb">
                                <a href="" class="h-t-tcb-sb-fb"></a>
                                <a href="" class="h-t-tcb-sb-vk"></a>
                                <a href="" class="h-t-tcb-sb-gp"></a>
                                <a href="" class="h-t-tcb-sb-tw"></a>
                            </div>
                            <div class="h-t-tcb-t"><?=$staticTexts['promo-top'][$lang]->getText()?></div>
                        </div>
                    </div>
                </div>
                <div class="m-tr">
                    <div class="m-tr_td">
                        <div class="h-t-mcb">
                            <div class="h-t-mcb-l">
                                <div class="h-t-mcb-l-wm">
                                    <b class="h-t-mcb-l-wm_b"><?=number_format($gameInfo['winners'], 0, '.', ' ')?></b>
                                    <span class="h-t-mcb-l-wm_span">Победителей</span>
                                </div>
                                <div class="h-t-mcb-l-wm">
                                    <b class="h-t-mcb-l-wm_b"><?=number_format($gameInfo['participants'], 0, '.', ' ')?></b>
                                    <span class="h-t-mcb-l-wm_span">участников</span>
                                </div>
                            </div>
                            <div class="h-t-mcb-r">
                                <div class="h-t-mcb-r-n">Общая сумма выигрыша</div>
                                <div class="h-t-mcb-r-i"><?=number_format($gameInfo['win'], 0, '.', ' ')?></div>
                            </div>
                        </div>
                        <div class="h-t-bcb">
                            <div class="h-t-bcb-l"><?=$staticTexts['promo-top-2'][$lang]->getText()?></div>
                            <div class="h-t-bcb-r">
                                <a href="javascript:void(0)" class="h-t-bcb-r-b go-play">Играть</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="b-tr">
                    <div class="b-tr_td">
                        <div class="h-b">
                            <div class="h-b-c">
                                <div class="h-b-c-tb">
                                    <div class="h-b-c-tb-l">До следующего розыгрыша осталось</div>
                                    <div id="countdownHolder" class="h-b-c-tb-r"></div>
                                </div>
                                <a href="javascript:void(0)" class="h-b-c-bhg to-slide" data-slide="2"><span class="h-b-c-bhg_span">как играть<br/> и выигрывать</span></a>
                                <div class="h-b-c-lg">
                                    <div class="h-b-c-lg-t">последний<br/>розыгрыш <?=date('d.m.Y')?></div>
                                    <ul class="h-b-c-lg_ul">
                                        <li class="h-b-c-lg_ul_li">0</li>
                                        <li class="h-b-c-lg_ul_li">0</li>
                                        <li class="h-b-c-lg_ul_li">0</li>
                                        <li class="h-b-c-lg_ul_li">0</li>
                                        <li class="h-b-c-lg_ul_li">0</li>
                                        <li class="h-b-c-lg_ul_li">0</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!--div class="h-t">
                <div class="h-t-tcb">
                    <div class="h-t-tcb-l"><a href=""><img src="img/promo-header-logo.png" width="238" height="60" /></a></div>
                    <div class="h-t-tcb-sb">
                        <a href="" class="h-t-tcb-sb-fb"></a>
                        <a href="" class="h-t-tcb-sb-vk"></a>
                        <a href="" class="h-t-tcb-sb-gp"></a>
                        <a href="" class="h-t-tcb-sb-tw"></a>
                    </div>
                    <div class="h-t-tcb-t">Lotzon — необычный проект, сутью которого бесплатная игра, участники которой могут стать обладателями денежных участники которой могут стать обладателями денежных и других ценных призов участники которой могут стать обладателями денежных и других ценных</div>
                </div>
                <div class="h-t-mcb">
                    <div class="h-t-mcb-l">
                        <div class="h-t-mcb-l-wm">
                            <b class="h-t-mcb-l-wm_b">430</b>
                            <span class="h-t-mcb-l-wm_span">Победителей</span>
                        </div>
                        <div class="h-t-mcb-l-wm">
                            <b class="h-t-mcb-l-wm_b">100 500</b>
                            <span class="h-t-mcb-l-wm_span">участников</span>
                        </div>
                    </div>
                    <div class="h-t-mcb-r">
                        <div class="h-t-mcb-r-n">Общая сумма выигрыша</div>
                        <div class="h-t-mcb-r-i">3 525 321</div>
                    </div>
                </div>
                <div class="h-t-bcb">
                    <div class="h-t-bcb-l">Каждый день у нас новые победители, которые выигрывают денежные и другие призы. Участие в розыгрыше всегда будет бесплатным. Присоединяйтесь.</div>
                    <div class="h-t-bcb-r">
                        <a href="" class="h-t-bcb-r-b">Играть</a>
                    </div>
                </div>
            </div>
            <div class="h-b">
                <div class="h-b-c">
                    <div class="h-b-c-tb">
                        <div class="h-b-c-tb-l">До следующего розыгрыша осталось</div>
                        <div class="h-b-c-tb-r">11<i class="h-b-c-tb-r_i">:</i>43<i class="h-b-c-tb-r_i">:</i>59</div>
                    </div>
                    <a href="" class="h-b-c-bhg"><span class="h-b-c-bhg_span">как играть<br/> и выигрывать</span></a>
                    <div class="h-b-c-lg">
                        <div class="h-b-c-lg-t">последний<br/>розыгрыш 24.08.2014</div>
                        <ul class="h-b-c-lg_ul">
                            <li class="h-b-c-lg_ul_li">2</li>
                            <li class="h-b-c-lg_ul_li">14</li>
                            <li class="h-b-c-lg_ul_li">36</li>
                            <li class="h-b-c-lg_ul_li">6</li>
                            <li class="h-b-c-lg_ul_li">11</li>
                            <li class="h-b-c-lg_ul_li">39</li>
                        </ul>
                    </div>
                </div>
            </div-->
        </header>

        <article>
            <section id="slide2" class="display-slide">
                <div class="a-dt-b">
                    <div class="a-dt_tr">
                        <div class="t_td">
                            <div class="a-tb">
                                <div class="a-tb-tl">Механика игры</div>
                                <div class="a-tb-dr"><?=$staticTexts['promo-game-mechanic'][$lang]->getText()?></div>
                                <a href="javascript:void(0)" class="a-tb-bt go-play">Играть</a>
                            </div>
                        </div>
                    </div>
                    <div class="a-dt_tr">
                        <div class="m_td">
                            <div class="a-tbl">
                                <ul class="l_ul">
                                    <li class="n_li">
                                        <div class="n">1</div>
                                        <div class="t"><?=$staticTexts['promo-game-mechanic-1'][$lang]->getText()?></div>
                                    </li>
                                    <li class="n_li">
                                        <div class="n">2</div>
                                        <div class="t"><?=$staticTexts['promo-game-mechanic-2'][$lang]->getText()?></div>
                                    </li>
                                    <li class="n_li">
                                        <div class="n">3</div>
                                        <div class="t"><?=$staticTexts['promo-game-mechanic-3'][$lang]->getText()?></div>
                                    </li>
                                    <li class="n_li">
                                        <div class="n">4</div>
                                        <div class="t"><?=$staticTexts['promo-game-mechanic-4'][$lang]->getText()?></div>
                                    </li>
                                </ul>
                                <ul class="r_ul">
                                    <? for ($i = 6; $i >= 1; --$i) { ?>
                                        <li class="r_li">
                                            <ul class="i_ul">
                                                 <? for ($j = 1; $j <= $i; ++$j) { ?>
                                                    <li class="i_li a_li"></li>
                                                <? } ?>
                                                <? for ($z = $j; $z <= 6; ++$z) { ?>
                                                    <li class="i_li"></li>
                                                <? } ?>
                                            </ul>
                                        <div class="ri-e"><?=$gameInfo['lotteryWins'][$i]['sum']?> <?=($gameInfo['lotteryWins'][$i]['currency'] == GameSettings::CURRENCY_POINT ? 'баллов' : $currency)?></div>
                                    </li>    
                                    <? } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="javascript:void(0)" class="_a-b-b to-slide" data-slide="3"></a>
            </section>
            <section id="slide3" class="display-slide">
                <div class="a-dt-b">
                    <div class="a-dt_tr">
                        <div class="t_td">
                            <div class="a-tb">
                                <div class="a-tb-tl">отзывы игроков</div>
                                <div class="a-tb-dr"><?=$staticTexts['promo-comments'][$lang]->getText()?></div>
                                <a href="javascript:void(0)" class="a-tb-bt go-play">Играть</a>
                            </div>
                        </div>
                    </div>
                    <div class="a-dt_tr">
                        <div class="m_td">
                            <div class="a-cb">
                                <section class="cb-pack">
                                    <ul class="_ul-cb-l">
                                        <li class="_li-cb">
                                            <div class="cb-ph"><img src="/tpl/img/comment-photo-1.jpg" class="ph_img" /></div>
                                            <div class="cb-ct">
                                                <div class="ct-i">Гарик Корогодский &bull; 16.04.2014</div>
                                                <div class="ct-t">Для того, чтобы сделать покупку, не нужно выходить из дому — заходите в наш магазин, выбирайте приглянувшийся товар и делайте заказ! Через короткое время курьерская служба доставит его вам. Для того, чтобы сделать покупку, не нужно выходить из дому — заходите в наш магазин, выбирайте!</div>
                                            </div>
                                        </li>
                                        <li class="_li-cb">
                                            <div class="cb-ph"><img src="/tpl/img/comment-photo-2.jpg" class="ph_img" /></div>
                                            <div class="cb-ct">
                                                <div class="ct-i">Гарик Корогодский &bull; 16.04.2014</div>
                                                <div class="ct-t">Для того, чтобы сделать покупку, не нужно выходить из дому — заходите в наш магазин, выбирайте приглянувшийся товар и делайте </div>
                                            </div>
                                        </li>
                                        <li class="_li-cb">
                                            <div class="cb-ph"><img src="/tpl/img/comment-photo-3.jpg" class="ph_img" /></div>
                                            <div class="cb-ct">
                                                <div class="ct-i">Гарик Корогодский &bull; 16.04.2014</div>
                                                <div class="ct-t">Для того, чтобы сделать покупку, не нужно выходить из дому — заходите в наш магазин, выбирайте приглянувшийся товар и делайте заказ! Через короткое время курьерская служба доставит его вам. Для того, чтобы сделать покупку, не нужно выходить из дому!</div>
                                            </div>
                                        </li>
                                        <li class="_li-cb">
                                            <div class="cb-ph"><img src="/tpl/img/comment-photo-4.jpg" class="ph_img" /></div>
                                            <div class="cb-ct">
                                                <div class="ct-i">Гарик Корогодский &bull; 16.04.2014</div>
                                                <div class="ct-t">Для того, чтобы сделать покупку, не нужно выходить из дому — заходите в наш магазин, выбирайте приглянувшийся товар и делайте </div>
                                            </div>
                                        </li>
                                    </ul>
                                    <ul class="_ul-cb-r">
                                        <li class="_li-cb">
                                            <div class="cb-ph"><img src="/tpl/img/comment-photo-1.jpg" class="ph_img" /></div>
                                            <div class="cb-ct">
                                                <div class="ct-i">Гарик Корогодский &bull; 16.04.2014</div>
                                                <div class="ct-t">Для того, чтобы сделать покупку, не нужно выходить из дому — заходите в наш магазин, выбирайте приглянувшийся товар и делайте заказ! Через короткое время курьерская служба доставит его вам. Для того, чтобы сделать покупку, не нужно выходить из дому — заходите в наш магазин, выбирайте!</div>
                                            </div>
                                        </li>
                                        <li class="_li-cb">
                                            <div class="cb-ph"><img src="/tpl/img/comment-photo-2.jpg" class="ph_img" /></div>
                                            <div class="cb-ct">
                                                <div class="ct-i">Гарик Корогодский &bull; 16.04.2014</div>
                                                <div class="ct-t">Для того, чтобы сделать покупку, не нужно выходить из дому — заходите в наш магазин, выбирайте приглянувшийся товар и делайте </div>
                                            </div>
                                        </li>
                                        <li class="_li-cb">
                                            <div class="cb-ph"><img src="/tpl/img/comment-photo-3.jpg" class="ph_img" /></div>
                                            <div class="cb-ct">
                                                <div class="ct-i">Гарик Корогодский &bull; 16.04.2014</div>
                                                <div class="ct-t">Для того, чтобы сделать покупку, не нужно выходить из дому — заходите в наш магазин, выбирайте приглянувшийся товар и делайте заказ! Через короткое время курьерская служба доставит его вам. Для того, чтобы сделать покупку, не нужно выходить из дому!</div>
                                            </div>
                                        </li>
                                        <li class="_li-cb">
                                            <div class="cb-ph"><img src="/tpl/img/comment-photo-4.jpg" class="ph_img" /></div>
                                            <div class="cb-ct">
                                                <div class="ct-i">Гарик Корогодский &bull; 16.04.2014</div>
                                                <div class="ct-t">Для того, чтобы сделать покупку, не нужно выходить из дому — заходите в наш магазин, выбирайте приглянувшийся товар и делайте </div>
                                            </div>
                                        </li>
                                    </ul>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="javascript:void(0)" class="_a-b-b to-slide" data-slide="4"></a>
            </section>
        </article>

        <!--  ct-on -->
        <footer id="slide4" class="display-slide">
            <div class="f-c-b">
                <div class="a-dt-b">
                    <div class="a-dt_tr">
                        <div class="t_td">
                            <div class="f-tl-b">
                                <div class="tl-tl">наши партнеры</div>
                                <div class="tl-tt"><?=$staticTexts['promo-partners'][$lang]->getText()?></div>
                                <a href="javascript:void(0)" class="tl-bt" id="cf-ab">связаться с нами</a>
                            </div>
                        </div>
                    </div>
                    <div class="a-dt_tr">
                        <div class="m_td">


                            <div class="fb-f-b">
                                <form name="feed-back-form">
                                    <div class="m-b">
                                        <textarea id="cti" class="i-b_ta" placeholder="Сообщение" value="" maxlength="600"></textarea>
                                    </div>
                                    <div class="m-m-b">
                                        <input type="email" name="mail" class="mmb_input" placeholder="Ваш email" />
                                    </div>
                                    <input type="submit" value="Отправить" class="fb-f-s" />
                                </form>
                            </div>

                            <ul class="fb-p-b">
                                <li class="fb-p-b_li"><a href=""><img src="/tpl/img/partner-expl/adidas.png" /></a></li>
                                <li class="fb-p-b_li"><a href=""><img src="/tpl/img/partner-expl/buduvaiser.png" /></a></li>
                                <li class="fb-p-b_li"><a href=""><img src="/tpl/img/partner-expl/cincert-ua.png" /></a></li>
                                <li class="fb-p-b_li"><a href=""><img src="/tpl/img/partner-expl/forf.png" /></a></li>
                                <li class="fb-p-b_li"><a href=""><img src="/tpl/img/partner-expl/nokia.png" /></a></li>
                                <li class="fb-p-b_li"><a href=""><img src="/tpl/img/partner-expl/ray-ban.png" /></a></li>
                                <li class="fb-p-b_li"><a href=""><img src="/tpl/img/partner-expl/buduvaiser.png" /></a></li>
                                <li class="fb-p-b_li"><a href=""><img src="/tpl/img/partner-expl/ray-ban.png" /></a></li>
                                <li class="fb-p-b_li"><a href=""><img src="/tpl/img/partner-expl/adidas.png" /></a></li>
                                <li class="fb-p-b_li"><a href=""><img src="/tpl/img/partner-expl/cincert-ua.png" /></a></li>
                                <li class="fb-p-b_li"><a href=""><img src="/tpl/img/partner-expl/forf.png" /></a></li>
                                <li class="fb-p-b_li"><a href=""><img src="/tpl/img/partner-expl/nokia.png" /></a></li>
                                <li class="fb-p-b_li"><a href=""><img src="/tpl/img/partner-expl/adidas.png" /></a></li>
                                <li class="fb-p-b_li"><a href=""><img src="/tpl/img/partner-expl/ray-ban.png" /></a></li>
                                <li class="fb-p-b_li"><a href=""><img src="/tpl/img/partner-expl/adidas.png" /></a></li>
                            </ul>

                        </div>
                    </div>
                </div>
                <a href="javascript:void(0)" class="b-g-t to-slide" data-slide="1">К началу</a>
            </div>
        </footer>



        <!-- ==========================================================================
                                        LOGIN POPUP
        ========================================================================== -->

        <div class="login-popup" id="login-block">
            <div class="lp-b">
                <div class="pu-b-c" id="lb-close"></div>
                    <div class="b-m login" id="cl-check">
                        <div class="rules-bk">
                            <div class="rb-cs-bt"></div>
                            <div class="rb-pg">
                                <h2>правила<br/>игры</h2>
                                <?=$staticTexts['promo-login-rules'][$lang]->getText()?>                                
                            </div>
                        </div>

                        <!-- add class "registration" or "login" -->
                        <div class="t-b">
                            <a href="javascript:void(0)" class="tb_a-l swap-form">вход</a>
                            <a href="javascript:void(0)" class="tb_a-r swap-form">регистрация</a>
                        </div>
                        <!-- REGISTRATION FORM -->
                        <form name="register">
                            <div id="reg-form">
                                <div class="rf-txt">Укажите ваш электронный ящик. На этот адрес будет выслан пароль, который вам понадобиться ввести при входе в слдующий раз</div>
                                <div class="ib-l">
                                    <div class="ph">Ваш email</div>
                                    <input type="email" class="m_input" name="login" placeholder="Ваш email" />
                                </div>

                                <div class="ch-b">
                                    <div class="e-t"></div>
                                    <input type="checkbox" id="rulcheck" hidden />
                                    <label for="rulcheck">Я ознакомился и согласен с <a href="javascript:void(0)" class="rs-sw">правилами игры</a></label>
                                </div>
                                <div class="s-b">
                                    <input type="submit" class="sb_but" value="Играть" />
                                </div>
                            </div>
                        </form>
                        <!-- LOGIN FORM -->
                        <form name="login">
                            <div id="login-form">
                                <div class="ib-l">
                                    <div class="ph">Ваш email</div>
                                    <input type="email" class="m_input" name="login" placeholder="Ваш email" />
                                </div>
                                <div class="ib-p">
                                    <div class="ph">Пароль</div>
                                    <input type="password" class="m_input" name="password"  placeholder="Пароль" />
                                </div>
                                <div class="ch-b-bk">
                                    <div class="e-t">Такой email не зарегистрирован или пароль не верен</div>
                                    <div class="ch-b">
                                        <input type="checkbox" id="remcheck" hidden />
                                        <label for="remcheck">Запомнить<br/>меня</a></label>
                                    </div>
                                    <a href="javascript:void(0)" id="rec-pass" class="r-p">Я забыл пароль</a>
                                </div>
                                <div class="s-b">
                                    <input type="submit" class="sb_but" value="Играть" />
                                </div>
                            </div>
                        </form>

                        <!-- REPASSWORD FORM -->
                        <form name="register">
                            <div id="pass-rec-form">
                                <div class="rf-txt">Укажите ваш электронный ящик. На этот адрес будет выслан новый пароль, который позволит войти в профиль через форму входа.</div>
                                <div class="ib-l">
                                    <div class="ph">Ваш email</div>
                                    <input type="email" class="m_input" name="login" placeholder="Ваш email" />
                                </div>
                                <div class="s-b">
                                    <input type="submit" class="sb_but" value="Играть" />
                                </div>
                            </div>
                        </form>
                    </div>
            </div>
        </div>


        <!-- ==========================================================================
                                        GAME POPUP
        ========================================================================== -->
        <div id="game-popup-block">
            <div class="g-pup-tl">
                <div class="g-pup_td">
                    <div class="g-pup-cnt">

                        <!-- WON BLOCK -->
                        <div id="game-won">
                            <section class="gpc-pad">
                                <a href="javascript:void(0)" class="b-c-p"></a>
                                <ul class="g-oc-b">
                                    <li class="g-oc_li"><span class="g-oc_span">12</span></li>
                                    <li class="g-oc_li"><span class="g-oc_span">32</span></li>
                                    <li class="g-oc_li"><span class="g-oc_span">4</span></li>
                                    <li class="g-oc_li"><span class="g-oc_span">48</span></li>
                                    <li class="g-oc_li"><span class="g-oc_span">15</span></li>
                                    <li class="g-oc_li"><span class="g-oc_span">6</span></li>
                                </ul>
                                <div class="gw-c-b">
                                    <div class="yr-b">
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
                                                <div class="yr-tt-tc">50 баллов</div>
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
                                                <div class="yr-tt-tc">10 грн </div>
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
                                                <div class="yr-tt-tc"></div>
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
                                                <div class="yr-tt-tc"></div>
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
                                                <div class="yr-tt-tc"></div>
                                            </li>
                                        </ul>
                                        <div class="yr-b-fb">Вы всегда можете вывести деньги на странице своего профиля и обменять баллы на странице призов. Бла бла бла.</div>
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
                                                <div class="yw-t">баллов<br>на счету<b>230</b></div>
                                                <a href="" class="yw-b">обменять</a>
                                            </div>
                                            <div class="mb">
                                                <div class="yw-t">денег<br>на счету<b>2430</b></div>
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
                        <div id="game-end">
                            <section class="gpc-pad">
                                <a href="javascript:void(0)" class="b-c-p"></a>
                                <ul class="g-oc-b">
                                    <li class="g-oc_li"><span class="g-oc_span">12</span></li>
                                    <li class="g-oc_li"><span class="g-oc_span">32</span></li>
                                    <li class="g-oc_li"><span class="g-oc_span">4</span></li>
                                    <li class="g-oc_li"><span class="g-oc_span">48</span></li>
                                    <li class="g-oc_li"><span class="g-oc_span">15</span></li>
                                    <li class="g-oc_li"><span class="g-oc_span">6</span></li>
                                </ul>
                                <div class="gw-c-b">
                                    <div class="yr-b">
                                        <ul class="yr-tb">
                                            <li class="yr-tt">
                                                <div class="yr-tt-tn">Билет #1</div>
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
                                                <div class="yr-tt-tn">Билет #2</div>
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
                                        <div class="yr-b-fb"></div>
                                    </div>
                                    <div class="yr-s-i">
                                        <span>Не повезло, ты лузер, но не расстраивайся, в следующий раз получится бла бла бла.</span>
                                        <b>Полезный совет:</b>
                                        Комбинации, которые могут встречаться в билетах бла-бла-бла. Которые встречаются в билетах бла-бла-бла. А что если комбинации, которые могут встречаться в билетах бла-бла-бла.
                                        Комбинации, бла-бла-бла. Которые встречаются в билетах бла-бла-бла. А что если комбинации, которые могут встречаться в билетах бла-бла-бла. Комбинации, которые могут встречаться в билетах бла-бла-бла. Которые встречаются в билетах бла-бла-бла. А что если комбинации, которые бла-бла. Комбинации, которые могут встречаться. Которые встречаются в билетах бла-бла-бла. А что если комбинации, которые могут встречаться в билетах бла-бла-бла. Комбинации, бла-бла-бла. Которые встречаются в билетах бла-бла-бла. А что если комбинации, которые могут встречаться в билетах бла-бла-бла. Комбинации, которые могут встречаться бла-бла-бла.
                                    </div>
                                    <div class="b-cl-block"></div>
                                </div>
                            </section>

                        </div>
                        <!-- END END BLOCK -->

                        <!-- END BLOCK -->
                        <div id="game-process">
                            <section class="gpc-pad">
                                <div class="gp-bz"><img src="/tpl/img/banner.jpg" width="300" height="685" /></div>

                                <ul class="g-oc-b">
                                    <li class="g-oc_li">
                                        <div class="goc_li-nb goc-nb-act">
                                            <span class="g-oc_span">12</span>
                                        </div>
                                        <div class="goc_li-sh"></div>
                                    </li>
                                    <li class="g-oc_li goc-tm">
                                        <div class="goc_li-nb">
                                            <span class="g-oc_span">6</span>
                                        </div>
                                        <div class="goc_li-sh"></div>
                                    </li>
                                    <li class="g-oc_li goc-tm">
                                        <div class="goc_li-nb">
                                            <span class="g-oc_span">18</span>
                                        </div>
                                        <div class="goc_li-sh"></div>
                                    </li>
                                    <li class="g-oc_li goc-tm">
                                        <div class="goc_li-nb">
                                            <span class="g-oc_span">31</span>
                                        </div>
                                        <div class="goc_li-sh"></div>
                                    </li>
                                    <li class="g-oc_li goc-tm">
                                        <div class="goc_li-nb">
                                            <span class="g-oc_span">5</span>
                                        </div>
                                        <div class="goc_li-sh"></div>
                                    </li>
                                    <li class="g-oc_li goc-tm">
                                        <div class="goc_li-nb">
                                            <span class="g-oc_span">15</span>
                                        </div>
                                        <div class="goc_li-sh"></div>
                                    </li>
                                </ul>
                                <div class="gw-c-b">
                                    <div class="yr-b">
                                        <ul class="yr-tb">
                                            <li class="yr-tt">
                                                <div class="yr-tt-tn">Билет #1</div>
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
                                                <div class="yr-tt-tn">Билет #2</div>
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
        </div>

        <script src="/tpl/js/backend.js"></script>
        <script src="/tpl/js/promo.js"></script>
    <script>
        $(function(){
            var ios;
            var mac = navigator.userAgent.indexOf('Mac OS');
            if(mac > -1)ios = true;
            var ie = (function(){
                var ua= navigator.userAgent, tem,
                        M= ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
                if(/trident/i.test(M[1])){
                    tem=  /\brv[ :]+(\d+)/g.exec(ua) || [];
                    return 'MSIE '+(tem[1] || '');
                }
                if(M[1]=== 'Chrome'){
                    tem= ua.match(/\bOPR\/(\d+)/)
                    if(tem!= null) return 'Opera '+tem[1];
                }
                M= M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
                if((tem= ua.match(/version\/(\d+)/i))!= null) M.splice(1, 1, tem[1]);
                return M.join(' ');
            })();
            ie = ie.toLowerCase();
            if(ios || ie == 'msie 11' || ie == 'msie 10' || ie == 'msie 9')$('html').addClass('font-fix');

            // countdown
            $("#countdownHolder").countdown({
                until: (<?=($gameInfo['nextLottery'])?>), 
                format: 'HMS', 
                layout: '{hnn}<i class="h-b-c-tb-r_i">:</i>{mnn}<i class="h-b-c-tb-r_i">:</i>{snn}'}
            );
        });
    </script>
    </body>
</html>
