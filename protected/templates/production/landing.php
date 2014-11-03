<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?=$seo['title']?></title>
        <meta name="description" content="<?=$seo['desc']?>">
        <meta name="keywords" content="<?=$seo['kw']?>" />
        <meta name="robots" content="all" />
        <meta name="publisher" content="" />
        <meta http-equiv="reply-to" content="" />
        <meta name="distribution" content="global" />
        <meta name="revisit-after" content="1 days" />

         <!-- Schema.org markup for Google+ -->
        <meta itemprop="name" content="<?=$seo['Title']?>">
        <meta itemprop="description" content="Играл, играю и буду играть.">
        <meta itemprop="image" content="http://lotzon.com/tpl/img/social-share.jpg?rnd=<?=rand()?>">

        <!-- Twitter Card data -->
        <meta name="twitter:card" content="http://lotzon.com/tpl/img/social-share.jpg?rnd=<?=rand()?>">
        <meta name="twitter:title" content="<?=$seo['Title']?>">
        <meta name="twitter:description" content="Играл, играю и буду играть.">
        <!-- Twitter summary card with large image must be at least 280x150px -->
        <meta name="twitter:image:src" content="http://lotzon.com/tpl/img/social-share.jpg?rnd=<?=rand()?>">

        <!-- Open Graph data -->
        <meta property="og:title" content="<?=$seo['Title']?>" />
        <meta property="og:type" content="article" />
        <meta property="og:url" content="http://www.lotzon.com/" />
        <meta property="og:image" content="http://lotzon.com/tpl/img/social-share.jpg?rnd=<?=rand()?>" />
        <meta property="og:description" content="Играл, играю и буду играть." />
        <meta property="article:modified_time" content="<?=date('c', time())?>" />

        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />

        <meta property="og:url" content="http://samples.ogp.me/136756249803614" /> 
        <meta property="og:title" content="Chocolate Pecan Pie" />
        <meta property="og:description" content="This pie is delicious!" /> 
        <meta property="og:image" content="https://fbcdn-dragon-a.akamaihd.net/hphotos-ak-prn1/851565_496755187057665_544240989_n.jpg" /> 

        <link rel="stylesheet" href="/tpl/css/normalize.css" />
        <link rel="stylesheet" href="/tpl/css/promo.css" />

        <link rel="icon" href="/tpl/img/favicon.png" type="image/png" />
        <!--link rel="shortcut icon" href="" type="'image/x-icon"/-->

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
                            <div class="h-t-tcb-l"><a href="/"><img src="/tpl/img/Lotzon-Logo.svg" width="238" height="60" /></a></div>
                            <div class="h-t-tcb-sb">
                                <a target="_blank" href="https://www.facebook.com/pages/Lotzon/714221388659166" class="h-t-tcb-sb-fb"></a>
                                <a target="_blank" href="http://vk.com/lotzon" class="h-t-tcb-sb-vk"></a>
                                <a target="_blank" href="https://plus.google.com/112273863200721967076/about" class="h-t-tcb-sb-gp"></a>
                                <a target="_blank" href="https://twitter.com/LOTZON_COM" class="h-t-tcb-sb-tw"></a>
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
                                    <b class="h-t-mcb-l-wm_b" id="winners"><?=number_format($gameInfo['winners'], 0, '.', ' ')?></b>
                                    <span class="h-t-mcb-l-wm_span">Победителей</span>
                                </div>
                                <div class="h-t-mcb-l-wm">
                                    <b class="h-t-mcb-l-wm_b" id="participants"><?=number_format($gameInfo['participants'], 0, '.', ' ')?></b>
                                    <span class="h-t-mcb-l-wm_span">участhиков</span>
                                </div>
                            </div>
                            <div class="h-t-mcb-r">
                                <div class="h-t-mcb-r-n">Общая сумма выигрыша</div>
                                <div class="h-t-mcb-r-i" id="win"><?=number_format($gameInfo['win'], 0, '.', ' ')?> <span><?=$currency?></span></div>
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
                                <a href="javascript:void(0)" class="h-b-c-bhg to-slide" data-slide="2"><span class="h-b-c-bhg_span">как играть</span></a>
                                <div class="h-b-c-lg">
                                    <div class="h-b-c-lg-t">розыгрыш от <?=($lastLottery ? date('d.m.Y', $lastLottery->getDate()) : '')?></div>
                                    <ul class="h-b-c-lg_ul">
                                        <? if ($lastLottery) { ?>
                                            <? foreach ($lastLottery->getCombination() as $num) { ?>
                                                <li class="h-b-c-lg_ul_li"><?=$num?></li>
                                            <? } ?>
                                        <? } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <article>
            <section id="slide2" class="display-slide">
                <div class="a-dt-b">
                    <div class="a-dt_tr top">
                        <div class="t_td">
                            <div class="a-tb">
                                <div class="a-tb-tl">МехаHика<br/>игры</div>
                                <div class="a-tb-dr"><?=$staticTexts['promo-game-mechanic'][$lang]->getText()?></div>
                                <div class="a-tb-tr">
                                    <a href="javascript:void(0)" class="a-tb-bt go-play">Играть</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="a-dt_tr">
                        <div class="m_td">
                            <div class="a-tbl">
                                <ul class="l_ul">
                                    <li class="n_li">
                                        <div class="n"><b>1</b></div>
                                        <div class="t"><?=$staticTexts['promo-game-mechanic-1'][$lang]->getText()?></div>
                                    </li>
                                    <li class="n_li">
                                        <div class="n"><b>2</b></div>
                                        <div class="t"><?=$staticTexts['promo-game-mechanic-2'][$lang]->getText()?></div>
                                    </li>
                                    <li class="n_li">
                                        <div class="n"><b>3</b></div>
                                        <div class="t"><?=$staticTexts['promo-game-mechanic-3'][$lang]->getText()?></div>
                                    </li>
                                    <li class="n_li">
                                        <div class="n"><b>4</b></div>
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
                                        <div class="ri-e"><?=number_format($gameInfo['lotteryWins'][$i]['sum'], 0, '.', ' ')?> <?=($gameInfo['lotteryWins'][$i]['currency'] == GameSettings::CURRENCY_POINT ? 'баллов' : $currency)?></div>
                                    </li>
                                    <? } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <a href="javascript:void(0)" class="_a-b-b to-slide" data-slide="3"></a>
                </div>
            </section>
            <section id="slide3" class="display-slide">
                <div class="a-dt-b">
                    <div class="a-dt_tr top">
                        <div class="t_td">
                            <div class="a-tb">
                                <div class="a-tb-tl">отзывы<br/>игроков</div>
                                <div class="a-tb-dr"><?=$staticTexts['promo-comments'][$lang]->getText()?></div>
                                <div class="a-tb-tr">
                                    <a href="javascript:void(0)" class="a-tb-bt go-play">Играть</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="a-dt_tr">
                        <div class="m_td">
                            <div class="a-cb">
                                <section class="cb-pack">
                                    <? $comments = array_chunk($comments, ceil(count($comments)/2)); ?>
                                    <ul class="_ul-cb-l">
                                        <? foreach ($comments[0] as $comment) { ?>
                                            <li class="_li-cb">
                                                <div class="cb-ph"><img src="/filestorage/avatars/comments/<?=$comment->getAvatar();?>" class="ph_img" /></div>
                                                <div class="cb-ct">
                                                    <div class="ct-i"><a href="<?=$comment->getLink()?>" target="_blank"><?=$comment->getAuthor()?></a> &bull; <?=date('d.m.Y', $comment->getDate())?></div>
                                                    <div class="ct-t"><?=$comment->getText()?></div>
                                                </div>
                                            </li>
                                        <? } ?>
                                    </ul>
                                    <ul class="_ul-cb-r">
                                        <? foreach ($comments[1] as $comment) { ?>
                                            <li class="_li-cb">
                                                <div class="cb-ph"><img src="/filestorage/avatars/comments/<?=$comment->getAvatar();?>" class="ph_img" /></div>
                                                <div class="cb-ct">
                                                    <div class="ct-i"><a href="<?=$comment->getLink()?>" target="_blank"><?=$comment->getAuthor()?></a> &bull; <?=date('d.m.Y', $comment->getDate())?></div>
                                                    <div class="ct-t"><?=$comment->getText()?></div>
                                                </div>
                                            </li>
                                        <? } ?>
                                    </ul>
                                </section>
                            </div>
                        </div>
                    </div>
                    <a href="javascript:void(0)" class="_a-b-b to-slide" data-slide="4"></a>
                </div>
            </section>
        </article>

        <!--  ct-on -->
        <footer id="slide4" class="display-slide">
            <div class="f-c-b">
                <div class="a-dt-b">
                    <div class="a-dt_tr top">
                        <div class="t_td">
                            <div class="f-tl-b">
                                <div class="tl-tl">наши партHеры</div>
                                <div class="tl-tt"><?=$staticTexts['promo-partners'][$lang]->getText()?></div>
                                <div class="tl-tr"><a href="javascript:void(0)" class="tl-bt" id="cf-ab">связаться с нами</a></div>
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
                                        <input autocomplete="off" spellcheck="false" type="email" name="mail" class="mmb_input" placeholder="Ваш email" />
                                    </div>
                                    <input type="submit" value="Отправить" class="fb-f-s" />
                                </form>
                            </div>

                            <ul class="fb-p-b">
                                <!--li class="fb-p-b_li"><a href="http://musiclife.kiev.ua/" target="_blank"><img src="/tpl/img/partner-expl/musiclife.png" /></a></li>
                                <li class="fb-p-b_li"><a href="http://muzikant.ua/" target="_blank"><img src="/tpl/img/partner-expl/muzikant.ua.png" /></a></li>
                                <li class="fb-p-b_li"><a href="http://hypermarket.ua/" target="_blank"><img src="/tpl/img/partner-expl/hypermarket.png" /></a></li-->
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

        <div class="login-popup popup" id="login-block">
            <div class="lb-table">
                <div class="lb-tr">
                    <div class="lb-td">
                        <div class="lp-b">
                            <div class="pu-b-c" id="lb-close"></div>
                            <div class="b-m registration" id="cl-check">
                                <div class="rules-bk">
                                    <div class="rb-cs-bt"></div>
                                    <div class="rb-pg">
                                        <h2>правила<br/>участия</h2>
                                        <?=$staticTexts['promo-login-rules'][$lang]->getText()?>
                                    </div>
                                </div>

                                <!-- add class "registration" or "login" -->
                                <div class="t-b">
                                    <a href="javascript:void(0)" class="tb_a-l swap-form">вход</a>
                                    <a href="javascript:void(0)" class="tb_a-r swap-form">регистрация</a>
                                </div>
                                <!-- REGISTRATION FORM -->
                                <form name="register" data-ref="<?=$ref?>">
                                    <div id="reg-form">
                                        <div class="rf-txt">Укажите Ваш email. <b>На него будет выслан пароль,</b> который понадобится ввести при входе в следующий раз.</div>
                                        <div class="ib-l">
                                            <div class="ph">Ваш email</div>
                                            <input autocomplete="off" spellcheck="false" type="email" class="m_input" name="login" placeholder="Ваш email" />
                                        </div>

                                        <div class="ch-b">
                                            <div class="e-t"></div>
                                            <input type="checkbox" id="rulcheck" hidden />
                                            <label for="rulcheck">Я ознакомился и согласен с <a href="javascript:void(0)" class="rs-sw">правилами участия</a></label>
                                        </div>
                                        <div class="s-b">
                                            <input type="submit" disabled class="sb_but disabled" value="Играть" />
                                        </div>
                                    </div>
                                </form>
                                <!-- LOGIN FORM -->
                                <form name="login">
                                    <div id="login-form">
                                        <div class="ib-l">
                                            <div class="ph">Ваш email</div>
                                            <input autocomplete="off" spellcheck="false" type="email" class="m_input" name="login" placeholder="Ваш email" />
                                        </div>
                                        <div class="ib-p">
                                            <div class="ph">Пароль</div>
                                            <input autocomplete="off" spellcheck="false" type="password" class="m_input" name="password"  placeholder="Пароль" />
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
                                            <input type="submit" class="sb_but disabled" disabled value="Играть" />
                                        </div>
                                    </div>
                                </form>

                                <!-- REPASSWORD FORM -->
                                <form name="rec-pass">
                                    <div id="pass-rec-form">
                                        <div class="rf-txt">Укажите ваш email. Hа него будет выслан новый пароль.</div>
                                        <div class="ib-l">
                                            <div class="ph">Ваш email</div>
                                            <input autocomplete="off" spellcheck="false" type="email" class="m_input" name="login" placeholder="Ваш email" />
                                        </div>
                                        <div class="s-b">
                                            <input type="submit" class="sb_but disabled" disabled value="Отправить пароль" />
                                        </div>
                                    </div>
                                    <div id="pass-rec-txt">Новый пароль выслан на указанный email. </div>
                                </form>
                            </div>
                        </div>
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
            //if(ios || ie == 'msie 11' || ie == 'msie 10' || ie == 'msie 9')$('html').addClass('font-fix');

            // countdown
            $("#countdownHolder").countdown({
                until: (<?=($gameInfo['nextLottery'])?>), 
                format: 'HMS', 
                layout: '{hnn}<i class="h-b-c-tb-r_i">:</i>{mnn}<i class="h-b-c-tb-r_i">:</i>{snn}'}
            );

            window.setInterval(function() {
                getLandingStats(function(data) {
                    $("#participants").text(data.res.participants);
                    $("#winners").text(data.res.winners);
                    $("#win").html(data.res.win);
                }, function() {}, function() {})
            }, 15000);
        });
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-56113090-1', 'auto');
        ga('send', 'pageview');

        (function (d, w, c) {
            (w[c] = w[c] || []).push(function() {
                try {
                    w.yaCounter26806191 = new Ya.Metrika({id:26806191,
                            webvisor:true,
                            clickmap:true,
                            trackLinks:true,
                            accurateTrackBounce:true});
                } catch(e) { }
            });

            var n = d.getElementsByTagName("script")[0],
                s = d.createElement("script"),
                f = function () { n.parentNode.insertBefore(s, n); };
            s.type = "text/javascript";
            s.async = true;
            s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

            if (w.opera == "[object Opera]") {
                d.addEventListener("DOMContentLoaded", f, false);
            } else { f(); }
        })(document, window, "yandex_metrika_callbacks");
    </script>
    </body>
</html>
