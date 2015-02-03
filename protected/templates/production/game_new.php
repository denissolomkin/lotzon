<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" xmlns="http://www.w3.org/1999/html"> <!--<![endif]-->
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

        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />

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


        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="/theme/admin/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="/tpl/css/normalize.css" />
        <link rel="stylesheet" href="/tpl/css/slick.css" />
        <link rel="stylesheet" href="/tpl/css/main.css" />


        <link rel="icon" href="/tpl/img/favicon.png" type="image/png" />
        <!--link rel="shortcut icon" href="" type="'image/x-icon"/-->

        <!-- For iPhone 4 Retina display: -->
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="">
        <!-- For iPad: -->
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="">
        <!-- For iPhone: -->
        <link rel="apple-touch-icon-precomposed" href="">

        <script src="//vk.com/js/api/openapi.js" type="text/javascript"></script>
        <script src="/tpl/js/lib/modernizr.js"></script>
        <script src="/tpl/js/lib/jquery.min.js"></script>
        <script src="/tpl/js/lib/jquery-ui.min.js"></script>
        <script src="/tpl/js/lib/slick.min.js"></script>
        <script src="/tpl/js/lib/jquery.plugin.min.js"></script>
        <script src="/tpl/js/lib/jquery.cookie.js"></script>
        <script src="/tpl/js/lib/jquery.countdown.min.js"></script>
        <script src="/tpl/js/lib/jquery.damnUploader.min.js"></script>
        <script src="/tpl/js/social.js" charset="utf-8"></script>

        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>

        <?
        echo getScripts($banners['HeaderScripts'],$player);
        ?>

        <?
        function getBanners($sector, &$player, &$bannerScript){
            $res='';
            if(is_array($sector))
                foreach($sector as $group) {
                    if (is_array($group)) {
                        shuffle($group);
                        foreach ($group as $banner) {
                            if (is_array($banner['countries']) and !in_array($player->getCountry(), $banner['countries']))
                                continue;
                            $bannerScript .= '<!-- ' . $banner['title'] . ' -->
                        ' . $banner['script'];
                            $res .= '<!-- ' . $banner['title'] . ' -->
                        ' . $banner['div'];
                            break;
                        }
                    }
                }
            return $res;
        }

        function getScripts($script,&$player){
            $res='';
            if(is_array($script))
                foreach($script as $group) {
                    if (is_array($group))
                        foreach ($group as $banner) {
                            if (is_array($banner['countries']) and !in_array($player->getCountry(), $banner['countries']))
                                continue;
                            $res.=    '<!-- '.$banner['title'].' -->
                        '.$banner['script'];
                        }
                }
            return $res;
        }
        ?>


    </head>
    <body>
    <script type="text/javascript">
        window.fbAsyncInit = function() {
            FB.init({
              appId      : '865579400127881',
              xfbml      : false,
              version    : 'v2.2'
            });
          };

          (function(d, s, id){
             var js, fjs = d.getElementsByTagName(s)[0];
             if (d.getElementById(id)) {return;}
             js = d.createElement(s); js.id = id;
             js.src = "//connect.facebook.net/en_US/sdk.js";
             fjs.parentNode.insertBefore(js, fjs);
           }(document, 'script', 'facebook-jssdk'));
    </script>
    <div class="wrap">
        <header>
            <div class="hr-br">
            <aside class="lbs">

                <? echo getBanners($banners['Header'],$player,$bannerScript); ?>

            </div>
            <div class="hr-io-bk">
                <div id="hr-io-slider">
                    <div class="pw-gm-rt">
                        <div class="ct">
                            <div class="tl">Джекпот<br/><br/></div>
                            <b class="n"><?=Common::viewNumberFormat($gameInfo['lotteryWins'][6]['sum'])?> <span><?=$currency?></span></b>
                        </div>
                    </div>
                    <div class="pw-gm-rt">
                        <div class="ct">
                            <? foreach ($lotteries as $lottery) { ?>
                                <div class="tl">розыгрыш от<br/><?=date('d.m.Y', $lottery->getDate())?></div>
                                <ul class="rt-bk">
                                    <? foreach ($lottery->getCombination() as $num) { ?>
                                        <li class="rt-bk_li"><?=$num?></li>
                                    <? } ?>
                                </ul>
                                <? break; } ?>
                        </div>
                    </div>
                    <div class="pw-gm-rt">
                        <div class="ct">
                            <div class="tl">участников<br/><br/></div>
                            <b class="n"><?=Common::viewNumberFormat($gameInfo['participants'])?></b>
                        </div>
                    </div>
                    <div class="pw-gm-rt">
                        <div class="ct">
                            <div class="tl">Денежные выигрыши<br/><br/></div>
                            <b class="n"><?=Common::viewNumberFormat($gameInfo['winners'])?></b>
                        </div>
                    </div>
                    <div class="pw-gm-rt">
                        <div class="ct">
                            <div class="tl">ОБЩАЯ СУММА ВЫИГРЫША<br/><br/></div>
                            <b class="n"><?=Common::viewNumberFormat(round($gameInfo['win']))?> <span><?=$currency?></span></b>
                        </div>
                    </div>
                </div>
            </div>
            <div class="b-cl-block"></div>
        </header>

        <nav class="top-nav">
            <div class="tn-box">
                <div id="logo-gotop"></div>
                <? if($page) :?>
                <ul class="tn-mbk">
                    <li id="tickets-but" data-href="tickets" class="tn-mbk_li<?=($page=='tickets'?' now':'')?>"><a href="/tickets">РОЗЫГРЫШ</a></li>
                    <li id="prizes-but" data-href="prizes" class="tn-mbk_li<?=($page=='prizes'?' now':'')?>"><a href="/prizes">призы</a></li>
                    <!--li id="news-but" data-href="news" class="tn-mbk_li"><a href="#news">новости</a></li-->
                    <li id="reviews-but" data-href="reviews" class="tn-mbk_li<?=($page=='reviews'?' now':'')?>"><a href="/reviews">комментарии</a></li>
                    <li id="rules-but" data-href="rules" class="tn-mbk_li<?=($page=='rules'?' now':'')?>"><a href="/rules">правила</a></li>
                    <li id="profile-but" data-href="profile" class="tn-mbk_li<?=($page=='profile'?' now':'')?>"><a href="/profile">кабинет<span class='notice-unread'><?=$notices?></span></a></li>
                    <li id="chance-but" data-href="chance" class="tn-mbk_li<?=($page=='chance'?' now':'')?>"><a href="/chance">Игры</a></li>
                    <li id="logout" class="tn-mbk_li exit" data-href="logout" ><a href="javascript:void(0)">Выйти</a></li>
                </ul>
                <? else :?>
                    <ul class="tn-mbk">
                        <li id="tickets-but" data-href="tickets" class="tn-mbk_li"><a href="#tickets">РОЗЫГРЫШ</a></li>
                        <li id="prizes-but" data-href="prizes" class="tn-mbk_li"><a href="#prizes">призы</a></li>
                        <!--li id="news-but" data-href="news" class="tn-mbk_li"><a href="#news">новости</a></li-->
                        <li id="reviews-but" data-href="reviews" class="tn-mbk_li"><a href="#reviews">комментарии</a></li>
                        <li id="rules-but" data-href="rules" class="tn-mbk_li"><a href="#rules">правила</a></li>
                        <li id="profile-but" data-href="profile" class="tn-mbk_li"><a href="#profile">кабинет<span class='notice-unread'><?=$notices?></span></a></li>
                        <li id="chance-but" data-href="chance" class="tn-mbk_li"><a href="#chance">Игры</a></li>
                        <li id="logout" class="tn-mbk_li exit" data-href="logout" ><a href="javascript:void(0)">Выйти</a></li>
                    </ul>
                <? endif ?>
                <div class="tn-tr-bk">
                    <div class="tn-tr-tt">До розыгрыша<br/>осталось</div>
                    <div id="countdownHolder" class="tn-tr"></div>
                </div>
            </div>
        </nav>

        <article>
        <!--=====================================================================
                                TIKETS & PRIZES BLOCK
        ======================================================================-->
        <? if(in_array($page,array('prizes','tickets')) OR !$page) :?>
            <section class="wings">
                <aside class="lbs">

                    <? echo getBanners($banners['TicketsLeft'],$player,$bannerScript); ?>

                </aside>
                <aside class="rbs">

                    <? echo getBanners($banners['TicketsRight'],$player,$bannerScript); ?>

                </aside>
                <div class="w-ct">
                    <? if($page=='tickets' OR !$page) :?>
                    <section class="tickets">
                        <? $filledTicketsCount = count($tickets); ?>
                        <? if (count($tickets) < 5) { ?>
                            <ul class="tb-tabs">
                            <? $fst = true;
                            ?>
                            <? for ($i = 1; $i <= 5; ++$i) { ?>
                                <?  $nums = array();
                                if (count($tickets)) {
                                    if (isset($tickets[$i])) {
                                        $ticket = $tickets[$i];
                                        $nums = $ticket->getCombination();
                                    }
                                } ?>
                                <li class="tb-tabs_li<?=($fst ? " now" : "")?><?=(count($nums) ? " done" : "")?>" data-ticket="<?=$i?>"><a href="javascript:void(0)"><span>Билет </span>#<?=$i?></a></li>
                                <? $fst = false; ?>
                            <? } ?>
                            </ul>
                            <div class="tb-slides">
                                <? for ($i = 1; $i <= 5; ++$i) { ?>
                                    <?  $nums = array();
                                    if (count($tickets)) {
                                        if (isset($tickets[$i])) {
                                            $ticket = $tickets[$i];
                                            $nums = $ticket->getCombination();
                                        }
                                    } ?>
                                    <div class="tb-slide" id="tb-slide<?=$i?>" data-ticket="<?=$i?>">
                                        <ul class="tb-loto-tl">
                                            <? for ($j = 1; $j <= 49; ++$j) { ?>
                                                <li class="loto-tl_li loto-<?=$j?><?=(count($nums) && in_array($j, $nums) ? ' select' : '')?>"><?=$j?></li>
                                            <? } ?>
                                        </ul>
                                        <div class="bm-pl">
                                            <? if (count($nums) != 6) { ?>
                                            <ul class="tb-fs-tl">
                                                <li class="loto-tl_li ticket-random">
                                                    A
                                                    <div class="after">автозаполнение</div>
                                                </li>
                                                <li class="loto-tl_li heart ticket-favorite">
                                                    <img src="/tpl/img/ticket-heart-but.png" width="16" height="14">
                                                    <div class="after">
                                                        <b>любимая комбинация</b>
                                                        <span>Настраивается в <i data-href="profile">кабинете</i></span>
                                                    </div>
                                                </li>
                                            </ul>
                                            <? } ?>
                                            <div class="tb-st-bk">
                                                <? if (count($nums) == 6) { ?>
                                                    <div class="tb-st-done">подтвержден и принят к розыгрышу</div>
                                                <? } else { ?>
                                                    <div class="sm-but add-ticket">подтвердить</div>
                                                    <div class="tb-ifo">еще <b><?=(6 - count($nums))?></b> номера</div>
                                                <? } ?>
                                            </div>
                                            <div class="b-cl-block"></div>
                                        </div>
                                    </div>
                                <? } ?>
                            </div>
                            <div class="atd-bk">

                                <div class="atd-txt-bk">
                                    <div class="ttl">все 5 билетов подтверждены и приняты к розыгрышу</div>
                                    <div class="txt"><?=$staticTexts['tickets-complete-text'][$lang]->getText()?></div>
                                </div>
                            </div>
                        <? } else { ?>
                            <div class="atd-bk" style="display:block">
                                <ul class="yr-tb">
                                    <? for ($i = 1; $i <= 5; ++$i) { ?>
                                        <? $ticket = array_shift($tickets);
                                           $nums = $ticket->getCombination(); ?>
                                        <li class="yr-tt">
                                            <div class="yr-tt-tn">Билет #<?=$i?></div>
                                            <ul class="yr-tt-tr">
                                                <? foreach ($nums as $num) { ?>
                                                    <li class="yr-tt-tr_li"><?=$num?></li>
                                                <? } ?>
                                            </ul>
                                        </li>
                                    <? } ?>
                                </ul>
                                <div class="atd-txt-bk">
                                    <div class="ttl">все 5 билетов подтверждены и приняты к розыгрышу</div>
                                    <div class="txt"><?=$staticTexts['tickets-complete-text'][$lang]->getText()?></div>
                                </div>
                            </div>
                        <? } ?>
                    </section>
                    <? endif ?>

                    <? if($page=='prizes' OR !$page) :?>
                    <section class="prizes">
                        <div class="sbk-tl-bk">
                            <div class="sbk-tl">Призы</div>
                            <div class="pbk-pi">на счету <b class="plPointHolder"><?=Common::viewNumberFormat($player->getPoints())?></b> баллов</div>
                        </div>
                        <div class="pbk-ct">
                            <div class="ptt"><?=$staticTexts['main-prizes'][$lang]->getText()?></div>
                            <ul class="pz-nav">
                                <? $fst = true; ?>
                                <? foreach ($shop as $category) {?>
                                    <li data-id="<?=$category->getId()?>" class="shop-category pz-nav_li<?=($fst ? " now" : "");?>"><?=$category->getName()?></li>
                                    <? $fst = false; ?>
                                <? } ?>
                            </ul>
                            <? $fst = true; ?>
                            <? $showMoreButton = false; ?>
                            <? foreach ($shop as $category) { ?>
                                <? if ($fst && count($category->getItems()) > controllers\production\Index::SHOP_PER_PAGE) {
                                    $showMoreButton = true;
                                } ?>
                                <ul class="shop-category-items pz-cg" data-category="<?=$category->getId()?>"  <?=(!$fst ? 'style="display:none"':'')?>>
                                <? $pager = controllers\production\Index::SHOP_PER_PAGE ?>
                                <? $i = 0; ?>
                                <? foreach ($category->getItems() as $item) { ?>
                                    <? if ($i == $pager) {
                                        break;
                                    } ?>
                                    <? if (is_array($item->getCountries()) and !in_array($player->getCountry(),$item->getCountries())) {
                                        continue;
                                    } ?>
                                    <li class="pz-cg_li" data-item-id="<?=$item->getId()?>">
                                        <? if ($item->getQuantity()) {?>
                                            <div class="pz-lim">
                                                <span>ограниченное количество</span>
                                                <b><?=$item->getQuantity()?> шт</b>
                                            </div>
                                        <? } ?>
                                        <div class="im-ph"><img src="/filestorage/shop/<?=$item->getImage()?>" /></div>
                                        <div class="im-tl"><?=$item->getTitle()?></div>
                                        <div class="im-bn">
                                            <b><?=Common::viewNumberFormat($item->getPrice())?></b>
                                            <span>обменять на баллов</span>
                                        </div>
                                    </li>
                                    <? $i++; ?>
                                <? } ?>
                                </ul>
                                <? $fst = false; ?>
                            <? } ?>
                            <div class="pz-more-bt" style="display:<?=$showMoreButton ? 'block' : 'none'?>">ПОКАЗАТЬ ЕЩЕ</div>
                            <div class="mr-cl-bt-bk">
                                <div class="cl scrollto" data-href="prizes">свернуть</div>
                                <div class="mr">ПОКАЗАТЬ ЕЩЕ</div>
                            </div>
                        </div>
                    </section>
                    <? endif ?>
                </div>
                <div class="b-cl-block"></div>
            </section>
        <? endif ?>
        <!--=====================================================================
                                NEWS & RULEZ BLOCK
        ======================================================================-->
        <? if(in_array($page,array('reviews','rules'))) :?>
            <section class="infos">
                <div class="i-lbk">
                    <section class="i-v-bk">

                        <? echo getBanners($banners['Video'],$player,$bannerScript); ?>

                    </section>
                    <section class="rules">
                        <div class="sbk-tl-bk">
                            <div class="sbk-tl">правила и часто задаваемые вопросы</div>
                        </div>
                        <div class="rules-ct">
                            <div class="win-tbl">
                                <div class="c-l">
                                    <div class="wt-t">
                                        <?=$staticTexts['main-rules'][$lang]->getText()?>
                                    </div>
                                </div>
                                <ul class="c-r">
                                    <? for ($i = 6; $i >= 1; --$i) { ?>
                                        <li class="c-r_li">
                                            <ul class="tb">
                                                 <? for ($j = 1; $j <= $i; ++$j) { ?>
                                                    <li class="tb_li ch"></li>
                                                <? } ?>
                                                <? for ($z = $j; $z <= 6; ++$z) { ?>
                                                    <li class="tb_li"></li>
                                                <? } ?>
                                            </ul>
                                            <div class="tb-t"><?=Common::viewNumberFormat($gameInfo['lotteryWins'][$i]['sum'])?> <?=($gameInfo['lotteryWins'][$i]['currency'] == GameSettings::CURRENCY_POINT ? 'баллов' : $currency)?></div>
                                        </li>
                                    <? } ?>
                                </ul>
                                <div class="b-cl-block"></div>
                            </div>
                            <ul class="faq">
                                <?=$staticTexts['main-faq'][$lang]->getText()?>
                            </ul>
                            <div class="r-add-but show">ЧИТАТЬ ДАЛЬШЕ</div>
                            <div class="r-add-but close scrollto" data-href="rules" style="display:none;">спрятать</div>
                        </div>
                    </section>
                </div>

                <!--div class="i-rbk">
                    <section class="news">
                        <div class="sbk-tl-bk">
                            <div class="sbk-tl">новости</div>
                        </div>
                        <div class="n-items">
                            <div class="h-ch">
                                <? foreach ($news as $newsItem) { ?>
                                    <div class="n-item">
                                        <div class="n-i-tl"><?=$newsItem->getTitle()?> • <?=date('d.m.Y', $newsItem->getDate())?></div>
                                        <div class="n-i-txt"><?=$newsItem->getText()?></div>
                                    </div>
                                <? } ?>
                            </div>
                         </div>
                        <div class="n-add-but">ЧИТАТЬ ЕЩЕ</div>
                        <div class="n-mr-cl-bt-bk">
                            <div class="cl scrollto" data-href="news">свернуть</div>
                            <div class="mr">ЧИТАТЬ ЕЩЕ</div>
                        </div>
                    </section>
                </div-->

                <div class="i-rbk">
                    <section class="reviews">
                        <div class="sbk-tl-bk">
                            <div class="sbk-tl">Комментарии</div>
                        </div>
                        <div class="rv-items">
                            <div class="h-ch">
                                <? foreach ($reviews as $reviewItem) { ?>
                                    <div class="rv-item">
                                        <div class="rv-i-avtr">
                                            <? if ($reviewItem->getPlayerAvatar()) {?>
                                                <img src="/filestorage/avatars/<?=ceil($reviewItem->getPlayerId() / 100)?>/<?=$reviewItem->getPlayerAvatar()?>">
                                            <? } else { ?>
                                                <img src="/tpl/img/default.jpg">
                                            <? } ?>
                                        </div>
                                        <div class="rv-i-tl"><?=$reviewItem->getPlayerName()?> • <?=date('d.m.Y', $reviewItem->getDate())?></div>
                                        <div class="rv-i-txt"><?=$reviewItem->getText()?></div>
                                            <? if ($reviewItem->getImage()) {?>
                                            <div class="rv-i-img">
                                                <img src="/filestorage/reviews/<?=$reviewItem->getImage()?>">
                                            </div>
                                            <? }?>
                                    </div>
                                <? } ?>
                            </div>
                        </div>
                        <div class="rv-add-but">ЧИТАТЬ ЕЩЕ</div>
                        <div class="rv-mr-cl-bt-bk">
                            <div class="cl scrollto" data-href="reviews">свернуть</div>
                            <div class="mr">ЧИТАТЬ ЕЩЕ</div>
                        </div>

                        <div class="rv-add-frm">
                            <div class="rv-image">
                                <img class="upload">
                            </div>
                            <div class="rv-sc">Ваш комментарий отправлен на премодерацию</div>
                            <div class="rv-form">
                                <div class="rv-usr-avtr">
                                    <? if ($player->getAvatar()) {?>
                                        <img src="/filestorage/avatars/<?=ceil($player->getId() / 100)?>/<?=$player->getAvatar()?>">
                                    <? } else { ?>
                                        <img src="/tpl/img/default.jpg">
                                    <? } ?>
                                </div>
                                <div class="rv-bg-add">
                                    <img src="/tpl/img/bg-add-review.png"></div>
                                <div class="rv-txt">
                                    <div class="textarea" contenteditable></div>
                                </div>
                                <div class="rv-upld-img">
                                    <img src="/tpl/img/but-upload-review.png">
                                </div>
                                <div class="rv-but-add">
                                    отправить
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="b-cl-block"></div>
            </section>

        <? endif ?>
            <section class="banner100">

                <? echo getBanners($banners['Banner100'],$player,$bannerScript); ?>

            </section>

        <!--=====================================================================
                                PROFILE BLOCK
        ======================================================================-->
        <? if($page=='profile' OR !$page) :?>
            <section class="profile">
                <div class="p-bk">
                    <div class="p-tl-bk">
                        <div class="p-tl-nm">кабинет</div>
                        <!--div class="p-exit-bt">выйти</div-->
                        <div class="p-tl-ml" id="profile_email"><?=$player->getEmail()?></div>
                    </div>
                    <div class="p-cnt">
                        <aside>
                            <ul>
                                <li class="ul_li now" data-link="profile-history">история розыгрышей</li>
                                <li class="ul_li" data-link="profile-bonuses">бонусы</li>
                                <li class="ul_li" data-link="profile-info">мои данные</li>
                                <li class="ul_li" data-link="profile-notice">уведомления<span class='notice-unread' id="notice-unread"><?=$notices?></span></li>
                            </ul>


                            <div class="p-stat-bk">
                                <!--div class="gm-st"><b><?=$player->getGamesPlayed();?></b>игр сыграно</div-->
                                <div class="cr-st-bk">
                                    <div class="ifo"><b class="plPointHolder"><?=Common::viewNumberFormat($player->getPoints())?></b> баллов на счету</div>
                                    <div class="bt" id="exchange" data-href="prizes">обменять</div>
                                </div>

                                <div class="hand" id="cash-exchange"><img src="/tpl/img/but-exchange.png"></div>

                                <div class="cr-st-bk">
                                    <div class="ifo"><b class="plMoneyHolder"><?=Common::viewNumberFormat($player->getMoney())?></b><?=$player->getCountry() == 'UA' ? 'гривен' : 'рублей'?> на счету</div>
                                    <div class="bt" id="cash-output">вывести</div>
                                </div>
                                <div class="st-hy-bt"><span>история транзакций</span></div>
                            </div>
                        </aside>

                        <div class="sp-cnt">

                            <section class="_section profile-history">
                                <ul class="ph-fr-bk">
                                    <li class="bt-om"><a href="javascript:void(0)">только мои</a></li>
                                    <li class="bt-all sel"><a href="javascript:void(0)">все</a></li>
                                </ul>
                                <div class="ht-tl-bk">
                                    <div class="dt-tl">дата<br/>розыгрыша</div>
                                    <div class="wc-tl">выигрышная<br/>комбинация</div>
                                    <div class="nw-tl">количество<br/>победителей</div>
                                </div>
                                <ul class="ht-bk">
                                    <? foreach ($lotteries as $lottery) { ?>
                                        <li class="lot-container <?=(isset($playerPlayedLotteries[$lottery->getId()]) ? "win" : "")?>">
                                            <div class="dt"><?=$lottery->getDate('d.m.Y')?></div>
                                            <ul class="ht-ct">
                                                <? foreach ($lottery->getCombination() as $num) { ?>
                                                    <li><?=$num?></li>
                                                <? } ?>
                                            </ul>
                                            <div class="nw"><?=($lottery->getWinnersCount()+($lottery->getId()>84?1750:($lottery->getId()>76?1000:0)))?></div>
                                            <? if ($lottery->getWinnersCount() > 0 && isset($playerPlayedLotteries[$lottery->getId()])) { ?>
                                                <div class="aw-bt" data-lotid="<?=$lottery->getId()?>">
                                                    <a href="javascript:void(0)"></a>
                                                </div>
                                            <? } ?>
                                        </li>
                                    <? } ?>
                                </ul>

                                <!-- КНОПКА ЗАГРУЗИТЬ ЕЩЕ -->
                                <div class="mr-bt">ПОКАЗАТЬ ЕЩЕ</div>

                                <!-- КНОПКИ СВЕРНУТЬ И ЗАГРУЗИТЬ ЕЩЕ-->
                                <div class="mr-cl-bt-bl">
                                    <div class="cl scrollto" data-href="profile">свернуть</div>
                                    <div class="mr">ПОКАЗАТЬ ЕЩЕ</div>
                                </div>
                            </section>

                            <section class="_section profile-bonuses">
                                <div class="pb-txt"><?=$staticTexts['profile-bonus'][$lang]->getText()?></div>
                                <div class="if-bk">
                                    <div class="if-tl"><nobr>Пригласить друга +<?=EmailInvite::INVITE_COST?> баллов</nobr><br/><nobr>(приглашений на этой неделе <span class="invites-count"><?=$player->getInvitesCount()?></span>)</nobr></div>
                                    <div class="fm-bk">
                                        <div class="inp-bk">
                                            <input type="email" name="email" autocomplete="off" spellcheck="false" placeholder="Email друга" />
                                        </div>
                                        <div class="if-bt send-invite">пригласить</div>
                                    </div>
                                </div>
                                <!--div class="sn-bt-bk">
                                    <div class="fb"><span>пригласить</span></div>
                                    <div class="vk"><span>пригласить</span></div>
                                    <div class="gp"><span>пригласить</span></div>
                                    <div class="tw"><span>пригласить</span></div>
                                </div-->
                                <div class="rp-bk">
                                    <div class="rp-txt">Опубликовать пост с реферальной ссылкой +<?=Player::SOCIAL_POST_COST?> баллов <br/> (постов на этой неделе <span class="sposts-count"><?=$player->getSocialPostsCount()?></span>)</div>
                                    <div class="rp-sl-bk">
                                        <!--a href="javascript:void(0)" class="tw"></a>
                                        <a href="javascript:void(0)" class="gp"></a>
                                        <div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="twitter,gplus"></div-->
                                        <a href="javascript:void(0)" class="vk vk-share"></a>
                                        <!--a href="javascript:void(0)" class="fb fb-share"></a-->
                                    </div>
                                </div>
                                <div class="rp-bk ref">
                                    <div class="rp-txt">Регистрация по вашей ссылке +<?=Player::REFERAL_INVITE_COST?> баллов</div>
                                    <div class="rp-sl-bk">http://lotzon.com/?ref=<?=$player->getId()?></div>
                                </div>
                            </section>

                            <section class="_section profile-info">
                                <form name="profile">
                                    <div class="pi-lt">
                                        <!-- ЕСЛИ ФОТКА ЕСТЬ, ТО К КЛАССУ "pi-ph" ДОБАВЛЯКМ КЛАСС "true" -->
                                        <div class="pi-ph <?=$player->getAvatar() ? 'true' : ''?>">
                                            <i></i>
                                            <? if ($player->getAvatar()) {?>
                                                <img src="/filestorage/avatars/<?=ceil($player->getId() / 100)?>/<?=$player->getAvatar()?>">
                                            <? } ?>
                                        </div>
                                        <div class="pi-cs-bk">
                                            <? if(count($player->getAdditionalData())<5) {?>
                                            <div class="txt">Привязать соцсеть и получить бонус 40 баллов за каждую.</div>
                                            <? } ?>
                                            <? if(array_key_exists('Facebook', $player->getAdditionalData())
                                                && $player->getAdditionalData()['Facebook']['enabled'])
                                                echo '<div data-provider="Facebook" class="cs-int-bt fb int"></div>';
                                            else
                                                echo '<a href="./auth/Facebook?method=link"><div class="cs-int-bt fb"></div></a>';
                                            ?>
                                            <? if(array_key_exists('Vkontakte', $player->getAdditionalData())
                                                && $player->getAdditionalData()['Vkontakte']['enabled'])
                                                echo '<div data-provider="Vkontakte" class="cs-int-bt vk int"></div>';
                                            else
                                                echo '<a href="./auth/Vkontakte?method=link"><div class="cs-int-bt vk"></div></a>';
                                            ?>
                                            <? if(array_key_exists('Odnoklassniki', $player->getAdditionalData())
                                                && $player->getAdditionalData()['Odnoklassniki']['enabled'])
                                                echo '<div data-provider="Odnoklassniki" class="cs-int-bt ok int"></div>';
                                            else
                                                echo '<a href="./auth/Odnoklassniki?method=link"><div class="cs-int-bt ok"></div></a>';
                                            ?>
                                            <? if(array_key_exists('Google', $player->getAdditionalData())
                                                && $player->getAdditionalData()['Google']['enabled'])
                                                echo '<div data-provider="Google" class="cs-int-bt gp int"></div>';
                                            else
                                                echo '<a href="./auth/Google?method=link"><div class="cs-int-bt gp"></div></a>';
                                            ?>
                                            <? if(array_key_exists('Twitter', $player->getAdditionalData())
                                                && $player->getAdditionalData()['Twitter']['enabled'])
                                                echo '<div data-provider="Twitter" class="cs-int-bt tw int"></div>';
                                            else
                                                echo '<a href="./auth/Twitter?method=link"><div class="cs-int-bt tw"></div></a>';
                                            ?>
                                        </div>
                                    </div>
                                    <div class="pi-et-bk">
                                        <div class="pi-inp-bk">
                                            <div class="ph" data-default="Никнейм">Никнейм</div>
                                            <input autocomplete="off" spellcheck="false" type="text" name="nick" data-valid="<?=($player->getNicName() ? $player->getNicName() : 'id' . $player->getId())?>" value="<?=($player->getNicName() ? $player->getNicName() : 'id' . $player->getId())?>" />
                                        </div>
                                        <div class="pi-inp-bk">
                                            <div class="ph" data-default="Фамилия">Фамилия</div>
                                            <input autocomplete="off" spellcheck="false" type="text" name="surname" data-valid="<?=$player->getSurname()?>" value="<?=$player->getSurname()?>"/>
                                        </div>
                                        <div class="pi-inp-bk">
                                            <div class="ph" data-default="Имя">Имя</div>
                                            <input autocomplete="off" spellcheck="false" type="text" name="name" data-valid="<?=$player->getName()?>" value="<?=$player->getName()?>"/>
                                        </div>
                                        <div class="pi-inp-bk td">
                                            <div class="ph" data-default="Телефон">Телефон</div>
                                            <input autocomplete="off" spellcheck="false" placeholder="Телефон" type="tel" name="phone" data-valid="<?=$player->getPhone()?>" value="<?=$player->getPhone()?>"/>
                                        </div>
                                        <div class="pi-inp-bk td">
                                            <div class="ph" data-default="Дата рождения">Дата рождения</div>
                                            <input autocomplete="off" spellcheck="false" maxlength="10" placeholder="Дата рождения в формате ДД.ММ.ГГГГ" type="text" name="bd" data-valid="<?=($player->getBirthday() ? $player->getBirthday('d.m.Y') : '')?>" value="<?=($player->getBirthday() ? $player->getBirthday('d.m.Y') : '')?>"/>
                                        </div>
                                        <div class="pi-inp-bk td">
                                            <div class="ph" data-default="Пароль">Пароль</div>
                                            <input type="text" name="plug" data-valid="" style="display: none;"/>
                                            <input type="password" name="plug" data-valid="" style="display: none;"/>
                                            <input autocomplete="off" spellcheck="false" placeholder="Пароль" type="password" value="*********" name="password" data-valid="" />
                                        </div>
                                        <div class="fc-bk">
                                            <div class="fc-nbs-bk">
                                                <ul>
                                                    <? for ($i = 1; $i <= 49; ++$i) { ?>
                                                        <li<?=(in_array($i, $player->getFavoriteCombination()) ? ' class="dis"' : '')?>><?=$i?></li>
                                                    <? } ?>
                                                </ul>
                                            </div>
                                            <div class="fc-tl">любимая комбинация</div>
                                            <ul class="fc-nrch-bk">
                                                <? for ($i=0; $i<6;++$i) {?>
                                                    <li>
                                                        <i></i>
                                                        <span data-valid="<?=(isset($player->getFavoriteCombination()[$i]) ? $player->getFavoriteCombination()[$i] : '')?>"><?=(isset($player->getFavoriteCombination()[$i]) ? $player->getFavoriteCombination()[$i] : '')?></span>
                                                    </li>
                                                <? } ?>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="save-bk">
                                        <div class="sb-ch-td">
                                            <input type="checkbox" name="visible" id="rulcheck" hidden <?=($player->getVisibility() ? "checked" : "")?> />
                                            <label for="rulcheck">Показывать мое имя<br/>в списке победителей</label>
                                        </div>
                                        <div class="sb-ch-td">
                                            <div class="but" onclick="$(this).parents('form').submit(); return false;">сохранить</div>
                                        </div>
                                    </div>
                                </form>
                            </section>

                            <section class="_section profile-notice">
                                <div class="notices">
                                    <div class="n-items">

                                    </div>
                                </div>
                            </section>

                        </div>
                        <div class="b-cl-block"></div>
                    </div>
                </div>
                <div class="pr-br">

                    <? echo getBanners($banners['Profile'],$player,$bannerScript); ?>

                </div>
                <div class="b-cl-block"></div>
            </section>
        <? endif ?>


        <!--=====================================================================
                                CHANCE BLOCK
        ======================================================================-->
        <? if($page=='chance' OR !$page) :?>
        <section class="chance">
        <div class="ch-br-bk">

            <? echo getBanners($banners['Games'],$player,$bannerScript); ?>

        </div>
        <div class="ch-lot-bk">
        <div class="sbk-tl-bk">
        <div class="sbk-tl">игры</div>
        <div class="b-cntrl-block"><span class="glyphicon glyphicon-volume-up audio" aria-hidden="true"></span></div>

        <!-- CHANCE PREVIEW -->
        <div class="ch-bk" style="display:<?=($currentChanceGame && in_array($currentChanceGame['id'], array('33','44','55')) ? 'none' : 'block')?>;">
            <div class="ch-txt"><?=$staticTexts['chance-game'][$lang]->getText()?></div>
            <div class="ch-gm-tbl">
                <div class="td l">
                    <div class="gm-if-bk">
                        <div class="l"><?=$chanceGames['33']->getGameTitle();?></div>
                        <div class="r"><b><?=$chanceGames['33']->getGamePrice();?></b>баллов</div>
                    </div>
                    <div class="gm-bt" data-game="33"><img src="tpl/img/game-3x3.png"></div>
                </div>
                <div class="td c">
                    <div class="gm-if-bk">
                        <div class="l"><?=$chanceGames['44']->getGameTitle();?></div>
                        <div class="r"><b><?=$chanceGames['44']->getGamePrice();?></b>баллов</div>
                    </div>
                    <div class="gm-bt" data-game="44"><img src="tpl/img/game-4x4.png"></div>
                </div>
                <div class="td r">
                    <div class="gm-if-bk">
                        <div class="l"><?=$chanceGames['55']->getGameTitle();?></div>
                        <div class="r"><b><?=$chanceGames['55']->getGamePrice();?></b>баллов</div>
                    </div>
                    <div class="gm-bt" data-game="55"><img src="tpl/img/game-5x5.png"></div>
                </div>
            </div>
            <div class="ch-gm-tbl">
                <div class="td l">
                    <div class="gm-if-bk">
                        <div class="l">Кто больше</div>
                        <div class="r"></div>
                    </div>
                    <div class="ngm-bt" data-game="WhoMore"><img src="tpl/img/game-who-more.png"></div>
                </div>
                <div class="td c">
                    <div class="gm-if-bk">
                        <div class="l">Морской бой</div>
                        <div class="r"></div>
                    </div>
                    <div class="ngm-bt" data-game="SeaBattle"><img src="tpl/img/game-sea-battle.jpg"></div>
                </div>
                <div class="td r">
                    <!--div class="gm-if-bk">
                        <div class="l">Скоро</div>
                        <div class="r"></div>
                    </div-->
                    <img src="tpl/img/game-dots.png">
                </div>
            </div>
        </div>

        <!-- CHANCE GAME -->
        <div class="game-bk" style="display:<?=(!$currentChanceGame || !in_array($currentChanceGame['id'], array('33','44','55')) ? 'none' : 'block')?>;">
            <div class="l-bk">
                <div class="rw-t">
                    <div class="bk-bt"><spn>назад<br/>к списку игр</spn></div>
                </div>
                <div class="gm-if-bk">
                    <div class="tb">
                        <!-- FIX HERE -->
                        <div class="l"><?=($currentChanceGame ? $chanceGames[$currentChanceGame['id']]->getGameTitle() : '')?></div>
                        <div class="r"><b><?=($currentChanceGame ? $chanceGames[$currentChanceGame['id']]->getGamePrice() : '')?></b>баллов</div>
                    </div>
                </div>
                <div style="display:none" id="game-rules">
                    <div data-game="33">
                        <?=$staticTexts['chance-game-33'][$lang]->getText()?>
                    </div>
                    <div data-game="44">
                        <?=$staticTexts['chance-game-44'][$lang]->getText()?>
                    </div>
                    <div data-game="55">
                        <?=$staticTexts['chance-game-55'][$lang]->getText()?>
                    </div>
                </div>
                <div class="l-bk-txt">Описание. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis adipiscing libero magna, vel venenatis nisl adipiscing id. Aenean ipsum lorem, laoree. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis adipiscing libero magna, vel venenatis nisl adipiscing id. Aenean ipsum lorem, laoree. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis adipiscing </div>
                <div class="rw-b">
                    <? foreach (array('33','44','55') as $game) { ?>
                        <? if ($currentChanceGame && $currentChanceGame['id'] != $game) { continue; } ?>
                        <div class="tb" style="display:<?=(!$currentChanceGame ? 'none' : '')?>" data-game="<?=$game?>">
                            <? $order = array('l', 'c', 'r'); ?>
                            <? foreach ($chanceGames[$game]->loadPrizes() as $prize) { ?>
                                <div class="td <?=($game == '55' ? array_shift($order) : 'c')?> sel">
                                    <img src="/filestorage/shop/<?=$prize->getImage();?>" />
                                </div>
                            <? } ?>
                        </div>
                    <? } ?>
                </div>
            </div>
            <div class="gm-tb-bk">

                <!-- Блок "ВЫИГРАЛ" -->
                <div class="msg-tb won" style="display:none">
                    <div class="td">
                        <div class="pz-ph">
                            <img src="/tpl/img/preview/catalog-img-5.jpg" />
                        </div>
                        <div class="tl">
                            <span>Выигрыш</span>
                            <b>Планшет Lenovo</b>
                        </div>
                        <div class="bt">получить</div>
                    </div>
                </div>

                <!-- Кнопка "Играть" -->
                <div class="msg-tb play" <?=($currentChanceGame ? 'style="display:none"' : '')?>>
                    <div class="td">
                        <div class="bt">играть</div>
                    </div>
                </div>

                <!-- Кнопка "Проиграл, играть еще" -->
                <div class="msg-tb los" style="display:none;">
                    <div class="td">
                        <div class="los-msg">В этот раз вы<br/>не выиграли</div>
                        <div class="bt">играть еще раз за <span></span> баллов</div>
                    </div>
                </div>

                <!-- Кнопка "Выиграл, играть еще" -->
                <!--div class="msg-tb los" style="display:none;">
                    <div class="td">
                        <div class="bt">играть еще раз за 600 баллов</div>
                    </div>
                </div-->

                <!-- GAME 3x3 -->
                <ul class="gm-tb g-3x3" data-game="33" data-price="<?=$chanceGames['33']->getGamePrice()?>" style="display:<?=($currentChanceGame && $currentChanceGame['id'] == '33' ? 'block' : 'none')?>">
                    <? for($i = 1; $i <=3; ++$i) { ?>
                        <? for($j = 1; $j <=3; ++$j) { ?>
                            <li data-coord="<?=$i?>x<?=$j?>"></li>
                        <? } ?>
                    <? } ?>
                    <!--li class="won"></li-->
                    <!--li class="los"></li-->
                </ul>
                <!-- END GAME 3x3 -->

                <!-- GAME 4x4 -->
                <ul class="gm-tb g-4x4" data-game="44" data-price="<?=$chanceGames['44']->getGamePrice()?>" style="display:<?=($currentChanceGame && $currentChanceGame['id'] == '44' ? 'block' : 'none')?>">
                    <? for($i = 1; $i <= 4; ++$i) { ?>
                        <? for($j = 1; $j <= 4; ++$j) { ?>
                            <li data-coord="<?=$i?>x<?=$j?>"></li>
                        <? } ?>
                    <? } ?>
                </ul>
                <!-- END GAME 4x4 -->

                <!-- GAME 5x5 -->
                <ul class="gm-tb g-5x5" data-game="55" data-price="<?=$chanceGames['55']->getGamePrice()?>" style="display:<?=($currentChanceGame && $currentChanceGame['id'] == '55' ? 'block' : 'none')?>">
                    <? for($i = 1; $i <= 5; ++$i) { ?>
                        <? for($j = 1; $j <= 5; ++$j) { ?>
                            <li data-coord="<?=$i?>x<?=$j?>"></li>
                        <? } ?>
                    <? } ?>
                    <!--li class="won"></li-->
                    <!--li class="los"></li-->
                </ul>
                <!-- END GAME 5x5 -->
            </div>
        </div>



        <!-- NEW GAME CODE -->
        <div class="ngm-bk">
            <!-- правила -->
            <div class="ngm-rls">
                <div class="ngm-rls-bk">

                    <div class="prc-l">
                        <div class="rw-t">
                            <div class="bk-bt-rl"><spn>назад<br/>к описанию игры</spn></div>
                        </div>
                        <div class="gm-if-bk">
                            <div class="l"></div>
                        </div>

                        <div class="prc-bl">
                            <div class="prc-txt-bk">
                                <div class="all">Выберите тип и размер ставки или играйте бесплатно</div>
                            </div>
                            <div class="prc-but-cover"></div>
                            <div class="prc-but-bk">

                                <div class="prc-bt">баллы</div>
                                <div class="prc-sel">
                                    <div class="prc-tl">баллы</div>
                                    <div class="prc-vl" data-price='POINT-500'>500</div>
                                    <div class="prc-vl" data-price='POINT-200'>200</div>
                                    <div class="prc-vl" data-price='POINT-100'>100</div>
                                    <div class="prc-vl" data-price='POINT-75'>75</div>
                                    <div class="prc-vl" data-price='POINT-50'>50</div>
                                    <div class="prc-vl" data-price='POINT-25'>25</div>
                                </div>


                                <div class="prc-bt">деньги</div>
                                <div class="prc-sel">
                                    <div class="prc-tl">деньги</div>
                                    <div class="prc-vl" data-price='MONEY-1'><?=(1*$gameInfo['coefficient'])?></div>
                                    <div class="prc-vl" data-price='MONEY-0.5'><?=(0.5*$gameInfo['coefficient'])?></div>
                                    <div class="prc-vl" data-price='MONEY-0.25'><?=(0.25*$gameInfo['coefficient'])?></div>
                                    <div class="prc-vl" data-price='MONEY-0.1'><?=(0.1*$gameInfo['coefficient'])?></div>
                                </div>


                                <div class="prc-bt">бесплатно</div>
                                <div class="prc-sel"><div data-price='POINT-0'>бесплатно</div></div>

                                <!--div class="ngm-cncl">отмена</div-->
                                <div class="ngm-go">играть</div>
                            </div>
                        </div>

                    </div>

                    <div class="rls-l">
                        <div class="rw-t">
                            <div class="bk-bt"><spn>назад<br/>к списку игр</spn></div>
                        </div>
                        <div class="gm-if-bk">
                            <div class="l"></div>
                        </div>

                        <div class="rls-bl">
                            <div class="rls-bt-bk">
                                <div id="newgame-fields" style="display:none">
                                    <div data-game="WhoMore">
                                        <ul class="mx WhoMore">
                                            <? for($i=1;$i<=7;$i++)
                                                for($j=1;$j<=7;$j++)
                                                    echo "<li data-cell='{$j}x{$i}'></li>";
                                            ?>
                                        </ul>
                                    </div>

                                    <div data-game="SeaBattle">
                                        <ul class="mx SeaBattle m">
                                            <? for($i=1;$i<=24;$i++)
                                                for($j=1;$j<=11;$j++)
                                                    echo "<li data-coor='{$j}x{$i}'></li>";
                                            ?>
                                        </ul>
                                        <div class="place">Расставьте корабли в необходимом порядке.<br><br>Что бы изменить ориентацию корабля, кликните по нему дважды.
                                            <div class="sb-random but">случайно</div>
                                            <div class="sb-ready but">готово</div>
                                            <div class="sb-wait">ожидаем соперника</div>
                                        </div>
                                        <ul class="mx SeaBattle o">
                                            <? for($i=1;$i<=24;$i++)
                                                for($j=1;$j<=11;$j++)
                                                    echo "<li data-coor='{$j}x{$i}'></li>";
                                            ?>
                                        </ul>

                                    </div>
                                </div>


                                <div style="display:none" id="newgame-rules">
                                    <div data-game="WhoMore">
                                        Выберите ставку и нажмите кнопку «Играть»<br/><br/>
                                        Поле состоит из 49 ячеек за каждой из которых скрыта цифра от 1 до 49. Игрок выбирает свою ставку. Каждому игроку дается возможность открыть шесть ячеек.
                                        <br/><br/>
                                        По итогам шести ходов сравнивается количество набраных очков каждым игроком, побеждает тот у кого их больше и он забирает ставку противника.
                                    </div>
                                    <div data-game="SeaBattle">
                                        Выберите ставку и нажмите кнопку «Играть»<br/><br/>
                                        Игра для двух участников, цель которой первым потопить корабли соперника.
                                        <br/><br/>
                                        На расстановку кораблей отведено 90 секунд.
                                        Каждому игроку дается 15 секунд на ход. Если игрок шесть раз пропускает свой ход, это считается поражением.<br/><br/>
                                        Лучший игрок месяца получает бонус 3000 баллов.
                                    </div>
                                </div>

                                <div class="l">
                                    <div class="ngm-price">ставка</div>
                                </div>

                                <div class="r">
                                    <div class="online">игроков онлайн <i>&bull;</i> <span></span></div>
                                    <div class="all">всего сыграно игр <b>:</b> <span></span></div>
                                </div>
                            </div>
                            <div class="rls-txt-bk">

                            </div>
                        </div>
                    </div>




                    <div class="rls-r">

                        <div class="rls-r-t">ВЫ<b>:</b> 0 <b>•</b> 0</div>
                        <div class="rls-r-ts"><div class="rls-r-search"><div class="loader"></div><b>Поиск</b></div><div class="ngm-cncl">отмена</div></div>


                        <div class="rls-r-ws">
                            <b>победители</b>
                            <span>рейтиHг <i>=</i> сыграHо игр <i>•</i> побед</span>
                        </div>
                        <ul class="rls-r-prs">

                        </ul>
                    </div>

                    <div class="b-cl-block"></div>
                </div>
            </div>


            <div class="ngm-gm">
                <div class="tm" id="tm">00:55</div>

                <!-- класс для (gm-pr) "move" если ход, Класс "winner" если победитель -->
                <div class="gm-pr l">
                    <div class="pr-ph-bk">
                        <div class="mt">ход</div>
                        <div class="wt">победитель</div>
                        <div class="pr-ph"></div>
                    </div>
                    <div class="pr-nm">Вы</div>
                    <div class="pr-fa"></div>
                    <div class="pr-cl">
                        <b>0</b>
                        <span>ходов<br/>осталось</span>
                    </div>
                    <div class="pr-pt">
                        <b>0</b>
                        <span>очков<br/>набрано</span>
                    </div>
                    <!--div class="pr-surr">сдаться</div-->
                </div>

                <div class="gm-mx">
                    <div class="gm-fld">
                    <!-- MATRIX -->


                    </div>

                    <!-- Equal massage-->
                    <div class="msg equal">
                        <div class="tbl">
                            <div class="td">
                                <div class="txt">Hабрано одинаковое кол-во очков.<br>Решающий ход.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Cancel massage-->
                    <div class="msg ca">
                        <div class="tbl">
                            <div class="td">
                                <div class="txt">Вы уверены, что хотите сдаться?<br>Вам будет засчитано поражение.</div>
                                <div class="bt-bk">
                                    <div class="l">ДА</div>
                                    <div class="r">НЕТ</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Winner massage-->
                    <div class="msg winner">
                        <div class="tbl">
                            <div class="td">
                                <div class="ot-exit">соперник вышел</div>
                                <div class="re">Повторить</div>
                                <div class="ch-ot">другой соперник</div>
                                <div class="exit">Выйти</div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- класс для (gm-pr) "move" если ход, Класс "winner" если победитель -->
                <div class="gm-pr r">
                    <div class="pr-ph-bk">
                        <div class="mt">ход</div>
                        <div class="wt">победитель</div>
                        <div class="pr-ph"></div>
                    </div>
                    <div class="pr-nm"></div>
                    <div class="pr-fa"></div>
                    <div class="pr-cl">
                        <b>0</b>
                        <span>ходов<br/>осталось</span>
                    </div>
                    <div class="pr-pt">
                        <b>0</b>
                        <span>очков<br/>набрано</span>
                    </div>
                </div>
                <div class="b-cl-block"></div>
            </div>
        </div>


        <!-- END GAME CODE -->



        </div>
        </div>
        <div class="b-cl-block"></div>
        </section>
        <? endif ?>

        <script>
            $('.ch-gm-tbl .gm-bt').click(function(){
                $(this).closest('.ch-bk').fadeOut(200);
                setTimeout(function(){
                    $('.game-bk').fadeIn(200);
                }, 200);
            });
        </script>
        </article>
        <!--=====================================================================
                                    FOOTER BLOCK
            ======================================================================-->
        <footer>
            <section class="fr-br-bk">
                <img src="/tpl/img/baners/goroskop.jpg" width="1280" height="257" /-->
            </section>
            <div class="fr-cnt-bk">
                <a href="javascript:void(0)" class="ts-lk" id="terms-bt">Условия участия</a>
                <div class="ct-bk">
                    <a target="_blank" href="https://www.facebook.com/pages/Lotzon/714221388659166" class="ct-sl fb"></a>
                    <a target="_blank" href="http://vk.com/lotzon" class="ct-sl vk"></a>
                    <a target="_blank" href="http://ok.ru/group/52501162950725" class="ct-sl ok"></a>
                    <a target="_blank" href="https://plus.google.com/112273863200721967076/about" class="ct-sl gp"></a>
                    <a target="_blank" href="https://twitter.com/LOTZON_COM" class="ct-sl tw"></a>
                    <a target="_blank" href="mailto:info@lotzon.com" class="mail">info@lotzon.com</a>
                </div>
            </div>
        </footer>

        <? /*
        <div style="z-index: 100;position: fixed;padding: 5px;left: 0;overflow-x: auto;overflow-y: auto;bottom: 0;height: 300px;width: 300px;background: white;">
            <span id="chatStatus"></span>
            <span style="cursor:pointer;right:5px;position:absolute;" onclick="$('#chatStatus').parent('div').hide();$('#chatStatusShow').show();"><b>x</b></span>
            <span style="bottom: 0;left: 0;position: fixed;padding: 5px;background: inherit;">
                <input style="width:210px;" id="chatMessage"> <button id="chatButton">Отправить</button>
            </span>
        </div>

        <div id="chatStatusShow" style="z-index: 50;position: fixed;padding: 5px;left: 0;overflow-x: auto;overflow-y: hidden;bottom: 0;height: 10px;width: 10px;background: white;">
            <span style="cursor:pointer;right:5px;position:absolute;" onclick="$('#chatStatus').parent('div').show();$('#chatStatusShow').hide();"><b>^</b></span>
        </div>


        <div  style="display:none;z-index: 100;position: fixed;padding: 5px;right: 0;overflow-x: auto;overflow-y: hidden;bottom: 0;height: 350px;width: 300px;background: white;">
            <span style="cursor:pointer;right:5px;position:absolute;" onclick="$('#wsStatus').parent('div').hide()"><b>x</b></span>
            <span id="wsStatus"></span>
        </div>

        <div  style="z-index: 50;position: fixed;padding: 5px;right: 0;overflow-x: auto;overflow-y: hidden;bottom: 0;height: 10px;width: 10px;background: white;">
            <span style="cursor:pointer;right:5px;position:absolute;" onclick="$('#wsStatus').parent('div').show();$('#wsStatusShow').hide();"><b>^</b></span>
            <span id="wsStatusShow"></span>
        </div>
        <div id="ads">ads</div>

*/ ?>
        <audio id="a-WhoMore-start" src="/tpl/audio/complete.ogg"></audio>
        <audio id="a-WhoMore-win" src="/tpl/audio/metal.ogg"></audio>
        <audio id="a-SeaBattle-start" src="/tpl/audio/complete.ogg"></audio>
        <audio id="a-SeaBattle-win" src="/tpl/audio/metal.ogg"></audio>
        <audio id="a-SeaBattle-d" src="/tpl/audio/a-SeaBattle-d.ogg"></audio>
        <audio id="a-SeaBattle-e" src="/tpl/audio/a-SeaBattle-e.ogg"></audio>
        <audio id="a-SeaBattle-k" src="/tpl/audio/a-SeaBattle-k.ogg"></audio>
        </div>
        <script src="/tpl/js/lib/jquery.damnUploader.min.js"></script>
        <script src="/tpl/js/backend.js"></script>
        <script src="/tpl/js/main.js"></script>
        <script src="/tpl/js/ws.js"></script>
        <script src="/tpl/js/ads.js"></script>

        <? include('popups.php') ?>

    <? echo getScripts($banners['BodyScripts'],$player); ?>

    <script>

        VK.init({
            apiId: 4617228,
            scope: 'wall,photos'
        });

        filledTicketsCount = <?=($filledTicketsCount?:0);?>;
        var playerFavorite = [];
        var playerPoints   = <?=$player->getPoints()?>;
        var playerMoney   = <?=$player->getMoney()?>;
        var playerCurrency = '<?=$player->getCountry() == 'UA' ? 'гривен' : 'рублей'?>';
        var playerId   = <?=$player->getId()?>;
        var coefficient   = <?=$gameInfo['coefficient']?>;
        var ws = 0;
        var page = <?=($page?1:0)?>;
        var online   = 1;
        var appId   = 0;
        var appName   = '';
        var appMode   = 0;
        var appModes = {
            'WhoMore': ['POINT-0', 'POINT-25', 'POINT-50', 'POINT-75', 'POINT-100', 'MONEY-0.1', 'MONEY-0.25', 'MONEY-0.5', 'MONEY-1'],
            'SeaBattle': ['POINT-0', 'POINT-25', 'MONEY-0.1']
        };
        var unreadNotices = <?=$notices?>;
        var bannerTicketLast = (<?=json_encode((is_array($banners['TicketLast']) && $ticketBanner=(array_shift($banners['TicketLast'])[0]))?$ticketBanner['div'].$ticketBanner['script']:'');?>);
        var bannerTicketLastTimer = <?=(is_numeric($ticketBanner['title'])?$ticketBanner['title']:30)?>;
        var bannerTicketLastNum = (5-Math.ceil(Math.random() * (5-<?=($filledTicketsCount?:1);?>)));
        var url = 'ws://<?=$_SERVER['SERVER_NAME'];?>:8080';

        updateNotices(unreadNotices);
        var posts = {
            fb : {
                link : 'http://lotzon.com/?ref=<?=$player->getId()?>',
                picture: 'http://lotzon.com/tpl/img/social-share.jpg',
                name : 'Lotzon.com',
                description : 'Играю и буду играть!',
                caption : '',
            },
            vk : {
                link : 'http://lotzon.com/?ref=<?=$player->getId()?>',
                message : 'Играю и буду играть!'
            }
        }

        <? foreach ($player->getFavoriteCombination() as $num) { ?>
        playerFavorite.push(<?=$num?>);
        <? } ?>
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



            $("#countdownHolder").countdown({
                until: (<?=($gameInfo['nextLottery'])?>),
                layout: '{hnn}<span>:</span>{mnn}<span>:</span>{snn}',
                onExpiry: showGameProccessPopup
            });

            if (document.location.hash == "#money") {
                $("#cash-output").click();
                location.hash = "";
            }
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
                window.fbAsyncInit = function() {
                    FB.init({
                            appId      : 'your-app-id',
                            xfbml      : true,
                            version    : 'v2.1'
                        });
                };
    </script>
    <script type="text/javascript" src="//yastatic.net/share/share.js" charset="utf-8"></script>
    <?=$bannerScript;?>
    </body>

</html>