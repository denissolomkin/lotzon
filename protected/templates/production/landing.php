<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?=$seo['title']?></title>
    <meta name="description" content="<?=$MUI->getText('seo-description')?>">
    <meta name="keywords" content="<?=$MUI->getText('seo-keywords')?>" />
    <meta name="robots" content="all" />
    <meta name="publisher" content="" />
    <meta http-equiv="reply-to" content="" />
    <meta name="distribution" content="global" />
    <meta name="revisit-after" content="1 days" />

    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="<?=$MUI->getText('seo-title')?>">
    <meta itemprop="description" content="Играл, играю и буду играть.">
    <meta itemprop="image" content="http://lotzon.com/tpl/img/social-share.jpg?rnd=<?=rand()?>">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="http://lotzon.com/tpl/img/social-share.jpg?rnd=<?=rand()?>">
    <meta name="twitter:title" content="<?=$MUI->getText('seo-title')?>">
    <meta name="twitter:description" content="Играл, играю и буду играть.">
    <!-- Twitter summary card with large image must be at least 280x150px -->
    <meta name="twitter:image:src" content="http://lotzon.com/tpl/img/social-share.jpg?rnd=<?=rand()?>">

    <!-- Open Graph data -->
    <meta property="og:title" content="<?=$MUI->getText('seo-title')?>" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="http://www.lotzon.com/" />
    <meta property="og:image" content="http://lotzon.com/tpl/img/social-share.jpg?rnd=<?=rand()?>" />
    <meta property="og:description" content="Играл, играю и буду играть." />
    <meta property="article:modified_time" content="<?=date('c', time())?>" />

    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />

    <link rel="stylesheet" href="/tpl/css/normalize.css" />
    <link rel="stylesheet" href="/tpl/css/landing.css" />

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
    <script src="/tpl/js/lib/jquery.magnific-popup.min.js"></script>

</head>
<body>
<header class="display-slide" id="slide1">
    <div class="h-dt-b">
        <div class="t-tr">
            <div class="t-tr_td">
                <div class="h-t-tcb">
                    <div class="h-t-tcb-l"><img src="/tpl/img/Lotzon-Logo.svg" width="238" height="60" /></div>
                    <div class="h-t-tcb-sb">
                        <a target="_blank" href="https://www.facebook.com/pages/Lotzon/714221388659166" class="h-t-tcb-sb-fb"></a>
                        <a target="_blank" href="http://vk.com/lotzon" class="h-t-tcb-sb-vk"></a>
                        <a target="_blank" href="http://ok.ru/group/52501162950725" class="h-t-tcb-sb-ok">
                        <!--a target="_blank" href="https://plus.google.com/112273863200721967076/about" class="h-t-tcb-sb-gp"></a-->
                        <a target="_blank" href="https://twitter.com/LOTZON_COM" class="h-t-tcb-sb-tw"></a>
                    </div>
                    <div class="h-t-tcb-t popup-vimeo" href="https://vimeo.com/114883943"><span><?=$MUI->getText('promo-clip')?></span></div>
                </div>
            </div>
        </div>
        <div class="m-tr">
            <div class="m-tr_td">
                <div class="h-t-mcb">
                    <div class="h-t-mcb-l">
                        <div class="h-t-mcb-l-wm">
                            <b class="h-t-mcb-l-wm_b" id="winners"><?=Common::viewNumberFormat($gameInfo['winners'])?></b>
                            <span class="h-t-mcb-l-wm_span"><?=$MUI->getText('placeholder-winners')?></span>
                        </div>
                        <div class="h-t-mcb-l-wm">
                            <b class="h-t-mcb-l-wm_b" id="participants"><?=Common::viewNumberFormat($gameInfo['participants'])?></b>
                            <span class="h-t-mcb-l-wm_span"><?=$MUI->getText('placeholder-participants')?></span>
                        </div>
                    </div>
                    <div class="h-t-mcb-r">
                        <div class="h-t-mcb-r-n"><?=$MUI->getText('placeholder-total-win')?></div>
                        <div class="h-t-mcb-r-i" id="win"><?=Common::viewNumberFormat(round($gameInfo['win']))?> <span><?=$currency?></span></div>
                    </div>
                </div>
                <div class="h-t-bcb">
                    <div class="h-t-bcb-l"><?=$MUI->getText('promo-top-2')?></div>
                    <div class="h-t-bcb-r">
                        <a href="javascript:void(0)" class="h-t-bcb-r-b go-play"><?=$MUI->getText('button-play')?></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="b-tr">
            <div class="b-tr_td">
                <div class="h-b">
                    <div class="h-b-c">
                        <div class="h-b-c-tb">
                            <div class="h-b-c-tb-l"><?=$MUI->getText('placeholder-until-lottery')?></div>
                            <div id="countdownHolder" class="h-b-c-tb-r"></div>
                        </div>
                        <a href="javascript:void(0)" class="h-b-c-bhg to-slide" data-slide="2"><span class="h-b-c-bhg_span"><?=$MUI->getText('button-how-play')?></span></a>
                        <div class="h-b-c-lg">
                            <div class="h-b-c-lg-t"><?=$MUI->getText('placeholder-lottery-from')?><?=($lastLottery ? date('d.m.Y', $lastLottery->getDate()) : '')?></div>
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
                        <div class="a-tb-tl"><?=$MUI->getText('title-game-mechanic')?></div>
                        <div class="a-tb-dr"><?=$MUI->getText('promo-game-mechanic')?></div>
                        <div class="a-tb-tr">
                            <a href="javascript:void(0)" class="a-tb-bt go-play"><?=$MUI->getText('button-play')?></a>
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
                                <div class="t"><?=$MUI->getText('promo-game-mechanic-1')?></div>
                            </li>
                            <li class="n_li">
                                <div class="n"><b>2</b></div>
                                <div class="t"><?=$MUI->getText('promo-game-mechanic-2')?></div>
                            </li>
                            <li class="n_li">
                                <div class="n"><b>3</b></div>
                                <div class="t"><?=$MUI->getText('promo-game-mechanic-3')?></div>
                            </li>
                            <li class="n_li">
                                <div class="n"><b>4</b></div>
                                <div class="t"><?=$MUI->getText('promo-game-mechanic-4')?></div>
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
                                    <div class="ri-e"><?=Common::viewNumberFormat($gameInfo['lotteryWins'][$i]['sum'])?> <?=($gameInfo['lotteryWins'][$i]['currency'] == LotterySettings::CURRENCY_POINT ? $MUI->getText('holder-points') : $currency)?></div>
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
                        <div class="a-tb-tl"><?=$MUI->getText('title-comments')?></div>
                        <div class="a-tb-dr"><?=$MUI->getText('promo-comments')?></div>
                        <div class="a-tb-tr">
                            <a href="javascript:void(0)" class="a-tb-bt go-play"><?=$MUI->getText('button-play')?></a>
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
                        <div class="tl-tr"><a href="javascript:void(0)" class="tl-bt" id="cf-ab"><?=$MUI->getText('button-contact-us')?></a></div>
                    </div>
                </div>
            </div>
            <div class="a-dt_tr">
                <div class="m_td">


                    <div class="fb-f-b">
                        <form name="feed-back-form">
                            <div class="m-b">
                                <textarea id="cti" class="i-b_ta" placeholder="<?=$MUI->getText('placeholder-message')?>" value="" maxlength="600"></textarea>
                            </div>
                            <div class="m-m-b">
                                <input autocomplete="off" spellcheck="false" type="email" name="mail" class="mmb_input" placeholder="Ваш email" />
                            </div>
                            <input type="submit" value="<?=$MUI->getText('button-send')?>" class="fb-f-s" />
                        </form>
                    </div>

                    <div class="fb-p-b">
                        <div><?=$MUI->getText('promo-partners')?></div>
                    <ul><li class="fb-p-b_li"><?=$MUI->getText('title-partners')?></li><li class="fb-p-b_li"></li><li class="fb-p-b_li"></li>
                        <? if(is_array($partners))
                            foreach($partners as $image=>$href){ ?>
                        <li class="fb-p-b_li"><a href="<?=$href?>" rel="nofollow" target="_blank"><img src="/tpl/img/partner-expl/<?=$image;?>" /></a></li>
                        <? } ?>
                    </ul>
                    </div>
                </div>
            </div>
        </div>
        <a href="javascript:void(0)" class="b-g-t to-slide" data-slide="1"><?=$MUI->getText('menu-begin')?></a>
    </div>
</footer>



<!-- ==========================================================================
                                LOGIN POPUP
========================================================================== -->

<div class="login-popup popup" id="login-block" <?=$showEmail ? 'style="display:block"' : ''?>>
    <div class="lb-table">
        <div class="lb-tr">
            <div class="lb-td">
                <div class="lp-b">
                    <div class="pu-b-c" id="lb-close"></div>
                    <div class="b-m <?=$showEmail || $showLoginScreen ? 'login' : 'registration'?>" id="cl-check">
                        <div class="rules-bk">
                            <div class="rb-cs-bt"></div>
                            <div class="rb-pg">
                                <h2><?=$MUI->getText('title-rules')?></h2>
                                <?=$MUI->getText('promo-login-rules')?>
                            </div>
                        </div>

                        <!-- add class "registration" or "login" -->
                        <div class="t-b">
                            <a href="javascript:void(0)" class="tb_a-l swap-form"><?=$MUI->getText('button-enter')?></a>
                            <a href="javascript:void(0)" class="tb_a-r swap-form"><?=$MUI->getText('button-registration')?></a>
                        </div>
                        <!-- REGISTRATION FORM -->
                        <form name="register" data-ref="<?=$ref?>">
                            <div id="reg-form">
                                <div class="rf-txt"><?=$MUI->getText('text-input-email')?></div>
                                <div class="ib-l">
                                    <div class="ph"><?=$MUI->getText('placeholder-your-email')?></div>
                                    <input autocomplete="off" spellcheck="false" type="email" class="m_input" name="login" placeholder="Ваш email" />
                                    <div class="e-t"><?=$MUI->getText('message-email-exists')?></div>
                                </div>


                                <div class="s-b">
                                    <input type="submit" disabled class="sb_but disabled" value="<?=$MUI->getText('button-register')?>" />
                                </div>
                                <!-- Add class "disabled" -->
                                <div class="sl-bk">
                                    <div class="sl-bk-tl"><?=$MUI->getText('text-register-by-social')?></div>
                                    <div>
                                        <a href="./auth/Facebook?method=user<?=($ref?'&ref='.$ref:'')?>" class="fb"></a>
                                        <a href="./auth/Vkontakte?method=user<?=($ref?'&ref='.$ref:'')?>" class="vk"></a>
                                        <a href="./auth/Odnoklassniki?method=user<?=($ref?'&ref='.$ref:'')?>" class="ok"></a>
                                        <a href="./auth/Google?method=user<?=($ref?'&ref='.$ref:'')?>" class="gp"></a>
                                        <a href="./auth/Twitter?method=user<?=($ref?'&ref='.$ref:'')?>" class="tw"></a>
                                    </div>
                                </div>
                                <div class="ch-b"><?=$MUI->getText('text-you-accept-rules')?></div>
                            </div>
                            <div class="hidden" id="reg-succ-txt"><?=$MUI->getText('message-information-sent-to-email')?></div>
                        </form>

                        <!-- LOGIN FORM -->
                        <form id="login-block-form" name="login" <?=$showEmail || $showLoginScreen ? 'style="display:block"' : ''?>>
                            <div id="login-form">
                                <div class="ib-l">
                                    <div class="ph"><?=$MUI->getText('placeholder-your-email')?></div>
                                    <input autocomplete="off" spellcheck="false" type="email" class="m_input" name="login" placeholder="Ваш email" value="<?=$showEmail?>" />
                                </div>
                                <div class="ib-p">
                                    <div class="ph"><?=$MUI->getText('placeholder-password')?></div>
                                    <input autocomplete="off" spellcheck="false" type="password" class="m_input" name="password"  placeholder="Пароль" />
                                </div>
                                <div class="ch-b-bk">
                                    <div class="e-t"><?=$MUI->getText('message-email-not-found')?></div>
                                    <div class="ch-b">
                                        <input type="checkbox" id="remcheck" hidden />
                                        <label for="remcheck"><?=$MUI->getText('holder-remember-me')?></a></label>
                                    </div>
                                    <a href="javascript:void(0)" id="rec-pass" class="r-p"><?=$MUI->getText('button-remind-password')?></a>
                                </div>
                                <div class="s-b">
                                    <input type="submit" class="sb_but disabled" disabled value="Играть" />
                                </div>
                                <div class="sl-bk">
                                    <a href="./auth/Facebook?method=log-in<?=($ref?'&ref='.$ref:'')?>" class="fb"></a>
                                    <a href="./auth/Vkontakte?method=log-in<?=($ref?'&ref='.$ref:'')?>" class="vk"></a>
                                    <a href="./auth/Odnoklassniki?method=log-in<?=($ref?'&ref='.$ref:'')?>" class="ok"></a>
                                    <a href="./auth/Google?method=log-in<?=($ref?'&ref='.$ref:'')?>" class="gp"></a>
                                    <a href="./auth/Twitter?method=log-in<?=($ref?'&ref='.$ref:'')?>" class="tw"></a>
                                </div>
                            </div>
                        </form>

                        <!-- REPASSWORD FORM -->
                        <form name="rec-pass">
                            <div id="pass-rec-form">
                                <div class="rf-txt"><?=$MUI->getText('text-input-email')?></div>
                                <div class="ib-l">
                                    <div class="ph"><?=$MUI->getText('placeholder-your-email')?></div>
                                    <input autocomplete="off" spellcheck="false" type="email" class="m_input" name="login" placeholder="<?=$MUI->getText('placeholder-your-email')?>" />
                                    <div class="e-t"></div>
                                </div>
                                <div class="s-b">
                                    <input type="submit" class="sb_but disabled" disabled value="<?=$MUI->getText('button-send-password')?>" />
                                </div>
                            </div>
                            <div class="hidden" id="pass-rec-txt"><?=$MUI->getText('message-new-password-sent')?></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/tpl/js/backend.js"></script>
<script src="/tpl/js/promo.js"></script>
<? if($debug) { ?>
    <!-- Latest compiled and minified JavaScript -->
    <script src="/theme/admin/bootstrap/js/bootstrap.min.js"></script>

    <!-- Include Summernote JS file -->
    <script src="/theme/admin/lib/summernote/summernote.js"></script>
    <script src="/theme/admin/datepicker/js/bootstrap-datepicker.js"></script>

    <!-- Latest compiled and minified CSS -->
    <link href="/theme/admin/lib/admin.css" rel="stylesheet">
    <link rel="stylesheet" href="/theme/admin/bootstrap/css/bootstrap.min.css">
    <link href="/theme/admin/lib/summernote/summernote.css" rel="stylesheet">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <? include('./protected/templates/admin/statictexts_frontend.php');
}?>

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

    <? if(!$metrikaDisabled):?>
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
    <? endif; ?>
</script>




<!--=========================================================================
                            Game-3.5.2-Email-Confirmation.psd
==========================================================================-->
<style>
    .bl-pp-bk {position:fixed;top:0;left:0;width:100%;height:100%;background-color:rgba(0, 0, 0, 0.8);z-index:1000;overflow-x:hidden;overflow-y:auto;}
    .bl-pp_table {display:table;width:100%;height:100%;}
    .bl-pp_td {display:table-cell;vertical-align:middle;}
    .ml-cn {margin:auto;background-color:#fff;width:460px;}
    .ml-cn-padd {padding:30px;}
    .ml-cn-padd .ml-cn-txt {font:18px/36px Handbook-regular;color:#000;}

    .ml-cn .e-t {font:13px/1 Handbook-regular;color:#c51e1e;position:absolute;top:5px;left:0;display:none;}

    .ml-cn .pi-inp-bk {height:29px;border-bottom:1px solid #e3e3e3;margin:45px 0 38px 0;position:relative;}
    .ml-cn .pi-inp-bk.error {border-color:#c51e1e!important;color:#c51e1e!important;}
    .ml-cn .pi-inp-bk.error > input, .prize-info .pz-fm-bk .pi-inp-bk.error .ph {color:#c51e1e!important;}
    .ml-cn .pi-inp-bk.focus {border-bottom:1px solid #000;}

    .ml-cn .pi-inp-bk > input {border:none;background:transparent;height:29px;width:350px;font:18px/29px Handbook-regular;color:#000;outline:none;}
    .ml-cn .pi-inp-bk.td > input {width:300px;}
    .ml-cn .pi-inp-bk > input::-webkit-input-placeholder {font:18px/29px Handbook-italic;color:#d2d2d2;opacity:1;}
    .ml-cn .pi-inp-bk > input:-moz-placeholder {font:18px/29px Handbook-italic;color:#d2d2d2;opacity:1;}
    .ml-cn .pi-inp-bk > input::-moz-placeholder {font:18px/29px Handbook-italic;color:#d2d2d2;opacity:1;}
    .ml-cn .pi-inp-bk > input:-ms-input-placeholder {font:18px/29px Handbook-italic;color:#d2d2d2;opacity:1;}
    .ml-cn .pi-inp-bk.focus > input::-webkit-input-placeholder {opacity:0;}
    .ml-cn .pi-inp-bk.focus > input:-moz-placeholder {opacity:0;}
    .ml-cn .pi-inp-bk.focus > input::-moz-placeholder {opacity:0;}
    .ml-cn .pi-inp-bk.focus > input:-ms-input-placeholder {opacity:0;}

    .ml-cn .pi-inp-bk .ph {height:29px;font:18px/29px Handbook-italic;color:#d2d2d2;position:absolute;top:0;right:0px;}
    .ml-cn .pi-inp-bk.td .ph {display:none;}

    .ml-cn .pi-inp-bk.focus .ph {color:#000;display:block;}

    .ml-cn .ml-cn-but {border: 1px solid #000;cursor: pointer;font: 15px/48px Handbook-bold;height: 48px;margin: 0 0 0 auto;text-align: center;text-transform: uppercase;width:auto;}
    .ml-cn .ml-cn-but:hover {background-color:#000;color:#fff;}
    .ml-cn .ml-cn-but.disabled {opacity:0.25;}
    .ml-cn .ml-cn-but.disabled:hover {background-color:#fff;color:#000;}
    .ml-cn .pu-b-c {width: 30px;
        height: 30px;
        background: url("/tpl/img/bg-but-close-rules.png") no-repeat 0 0;
        position: absolute;
        top: 0;
        right: -50px;
        cursor: pointer;

 }
</style>


<div class="bl-pp-bk popup" id="mail-conf">
    <div class="bl-pp_table">
        <div class="bl-pp_td">
            <section class="ml-cn pop-box">
                <div class="pu-b-c" id="mb-close"></div>
                <div class="ml-cn-padd">
                    <div class="ml-cn-txt"><?=$MUI->getText('text-input-email')?></div>
                    <div class="pi-inp-bk td">
                        <div class="ph"><?=$MUI->getText('placeholder-your-email')?></div>
                        <input type="text" placeholder="Email" name="addr" spellcheck="false" autocomplete="off">
                    </div>
                    <div class="pi-inp-bk td" style="display:none">
                        <div class="ph"><?=$MUI->getText('placeholder-password')?></div>
                        <input type="text" placeholder="<?=$MUI->getText('placeholder-password')?>" name="addr" spellcheck="false" autocomplete="off">
                    </div>
                    <div class="e-t"></div>
                    <div class="ml-cn-but disabled"><?=$MUI->getText('button-approve')?></div>
                </div>
            </section>
        </div>
    </div>
</div>


<script>
    $('.pi-inp-bk input').on('focus', function(){
        $(this).closest('.pi-inp-bk').addClass('focus')
        if($(this).attr('name') == 'date')$(this).attr('type','date');
        $('.profile-info .save-bk .sb-ch-td .but').addClass('save');
    });

    $('.pi-inp-bk input').on('blur', function(){
        $(this).closest('.pi-inp-bk').removeClass('focus')
        if($(this).attr('name') == 'date')$(this).attr('type','text');
    });

    $('#mail-conf .pi-inp-bk input').on('keyup', function(){
        var val = $.trim($(this).val().length);
        if(val > 0){
            $(this).closest('.ml-cn-padd').find('.ml-cn-but').removeClass('disabled');
        }else{
            $(this).closest('.ml-cn-padd').find('.ml-cn-but').addClass('disabled');
        }
    });
    // registration handler
    $('#mail-conf .ml-cn-but').on('click', function(e) {
        var form = $('#login-block form[name="register"]');
        var email = $('#mail-conf').find('input[name="addr"]').val();
        var rulesAgree = 1;
        var ref = form.data('ref');
        registerPlayer({'email':email, 'agree':rulesAgree, 'ref':ref}, function(data){
            // success
        }, function(data){
            $('#mail-conf .e-t').text(data.message);
        }, function(data) {});
        return false;
    });
    <? if($rules) :?>
    $(function(){
        $(".go-play, .rs-sw").click();
    });
    <? endif;?>
</script>

<!--=========================================================================
                             END   Game-3.5.2-Email-Confirmation.psd
    ==========================================================================-->


<!-- ==========================================================================
                                ERROR POPUP
========================================================================== -->
<? if($error) :?>
<div class="mail-popup popup" id="error-block" style="display:block">
    <div class="mb-table">
        <div class="mb-tr">
            <div class="mb-td">
                <div class="lp-b">
                    <div class="pu-b-c" id="mb-close" onclick="$('#error-block').fadeOut(200);"></div>
                    <div class="b-m">

                        <!-- add class "registration" or "login" -->


                        <div class="t-b">
                            <?=$error;?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<? endif ?>


<!-- ==========================================================================
                                MAIL POPUP
========================================================================== -->

<div class="mail-popup popup" id="mail-block" <?=($socialIdentity)? 'style="display:block"' : ''?>>
    <div class="mb-table">
        <div class="mb-tr">
            <div class="mb-td">
                <div class="lp-b">
                    <div class="pu-b-c" id="mb-close" onclick="$('.mail-popup').fadeOut(200);"></div>
                    <div class="b-m">

                        <!-- add class "registration" or "login" -->


                        <div class="t-b">
                            <?=$MUI->getText('text-input-email')?>
                        </div>
                        <!-- MAIL FORM -->
                        <form id="mail-block-form" name="mail" style="display:block;">
                            <div id="mail-form">
                                <div class="ib-l">
                                    <div class="ph"><?=$MUI->getText('placeholder-your-email')?></div>
                                    <input autocomplete="off" spellcheck="false" type="email" class="m_input" name="login" placeholder="<?=$MUI->getText('placeholder-your-email')?>" value="<?=$showEmail?>" />
                                </div>
                                <div class="ch-b-bk">
                                    <div class="e-t"><?=$MUI->getText('message-email-not-found')?></div>
                                </div>
                                <div class="s-b">
                                    <input type="submit" class="sb_but disabled" disabled value="<?=$MUI->getText('button-register')?>" />
                                </div>
                            </div>
                        </form>

                        <!-- MAIL LOGIN FORM -->
                        <form id="mail-block-form" name="login" style="display:none;">
                            <div id="mail-form">
                                <div class="ib-l">
                                    <div class="ph"><?=$MUI->getText('placeholder-your-email')?></div>
                                    <input autocomplete="off" spellcheck="false" type="email" class="m_input" name="login" placeholder="<?=$MUI->getText('placeholder-your-email')?>" value="<?=$showEmail?>">
                                </div>
                                <div class="ib-p">
                                    <div class="ph"><?=$MUI->getText('placeholder-password')?></div>
                                    <input autocomplete="off" spellcheck="false" type="password" class="m_input" name="password"  placeholder="<?=$MUI->getText('placeholder-password')?>" />
                                </div>
                                <div class="ch-b-bk">
                                    <div class="e-t"><?=$MUI->getText('message-email-not-found')?></div>
                                </div>
                                <div class="s-b">

                                    <input type="submit" class="sb_but disabled" disabled value="<?=$MUI->getText('button-enter')?>" />
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


</body>
</html>
