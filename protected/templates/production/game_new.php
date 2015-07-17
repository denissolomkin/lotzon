<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" xmlns="http://www.w3.org/1999/html"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?=$MUI->getText('seo-title')?></title>
        <meta name="description" content="<?=$MUI->getText('seo-description')?>">
        <meta name="keywords" content="<?=$MUI->getText('seo-keywords')?>" />
        <meta name="robots" content="all" />
        <meta name="publisher" content="" />
        <meta http-equiv="reply-to" content="" />
        <meta name="distribution" content="global" />
        <meta name="revisit-after" content="1 days" />

        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />

        <!-- Schema.org markup for Google+ -->
        <meta itemprop="name" content="<?=$MUI->getText('seo-title')?>">
        <meta itemprop="description" content="Играл, играю и буду играть.">
        <meta itemprop="image" content="http://lotzon.com/tpl/img/social-share.jpg?rnd=<?=rand()?>">

        <!-- Twitter Card data -->
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="<?=$MUI->getText('seo-title')?>">
        <meta name="twitter:description" content="Играл, играю и буду играть.">
        <!-- Twitter summary card with large image must be at least 280x150px -->
        <meta name="twitter:image" content="http://lotzon.com/tpl/img/social-share.jpg?rnd=<?=rand()?>">

        <!-- Open Graph data -->
        <meta property="og:title" content="<?=$MUI->getText('seo-title')?>" />
        <meta property="og:type" content="article" />
        <meta property="og:url" content="<?php echo 'http://lotzon.com/?ref='.$player->getId(); ?>" />
        <meta property="og:image" content="http://lotzon.com/tpl/img/social-share.jpg?rnd=<?=rand()?>" />
        <link rel="image_src" href="http://lotzon.com/tpl/img/social-share.jpg?rnd=<?=rand()?>" />
        <meta property="og:description" content="Играл, играю и буду играть." />
        <meta property="article:modified_time" content="<?=date('c', time())?>" />

        <!-- Include Summernote CSS files -->

        <link rel="stylesheet" href="/tpl/css/simple-line-icons.css">
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
        <script src="/tpl/js/lib/jquery.min.js"></script>
        <!--script src="/tpl/js/lib/modernizr.js"></script>
        <script src="/tpl/js/lib/jquery-ui.min.js"></script>
        <script src="/tpl/js/lib/jquery.inputmask.js"></script>
        <script src="/tpl/js/lib/jquery.bind-first-0.1.min.js"></script>
        <script src="/tpl/js/lib/jquery.inputmask-multi.js"></script>
        <script src="/tpl/js/lib/slick.min.js"></script>
        <script src="/tpl/js/lib/jquery.plugin.min.js"></script>
        <script src="/tpl/js/lib/jquery.cookie.js"></script>
        <script src="/tpl/js/lib/jquery.countdown.min.js"></script>
        <script src="/tpl/js/lib/jquery.damnUploader.min.js"></script>
        <script src="/tpl/js/lib/social-likes.min.js"></script>
        <script src="/tpl/js/lib/doT.min.js"></script-->
        <script src="/tpl/js/lib/plugins.js" charset="utf-8"></script>
        <script src="/tpl/js/social.js" charset="utf-8"></script>



        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <? if($debug){ ?>
            <!-- Latest compiled and minified JavaScript -->
            <script src="/theme/admin/bootstrap/js/bootstrap.min.js"></script>

            <!-- Include Summernote JS file -->
            <script src="/theme/admin/lib/summernote/summernote.js"></script>
            <script src="/theme/admin/datepicker/js/bootstrap-datepicker.js"></script>

            <!-- Latest compiled and minified CSS -->
            <link rel="stylesheet" href="/theme/admin/bootstrap/css/bootstrap.min.css">
            <link rel="stylesheet" href="/tpl/css/font-awesome.min.css">
            <link href="/theme/admin/lib/admin.css" rel="stylesheet">
            <link href="/theme/admin/lib/summernote/summernote.css" rel="stylesheet">
        <? } ?>

        <? echo getScripts($banners['HeaderScripts'],$player); ?>

        <? function getBanners($sector, &$player, &$bannerScript){
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
                        <div' .
                        (($banner['chance'] AND !rand(0, $banner['chance'] - 1))?' class="teaser"':'').'>'.$banner['div'].'</div>';
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
        <style>



        <? if(isset($gameSettings['ChanceGame']) && $games = $gameSettings['ChanceGame']->getGames()){}
            if(is_array($games))
            foreach($games as $game): ?>
            .chance .ch-lot-bk .game-bk .ul-hld .qg-tbl.chance<?=$game?> {
                width:<?=($quickGames[$game]->getOption('w')+$quickGames[$game]->getOption('r'))*$quickGames[$game]->getOption('x')-1?>px;
                height:<?=($quickGames[$game]->getOption('h')+$quickGames[$game]->getOption('b'))*$quickGames[$game]->getOption('y')?>px;
            }
            .chance .ch-lot-bk .game-bk .ul-hld .qg-tbl.chance<?=$game?> > li {
                width:<?=$quickGames[$game]->getOption('w')?>px;
                height:<?=$quickGames[$game]->getOption('h')?>px;
                margin:0 <?=$quickGames[$game]->getOption('r')?>px <?=$quickGames[$game]->getOption('b')?>px 0;
            }
            .chance .ch-lot-bk .game-bk .ul-hld .qg-tbl.chance<?=$game?> > li:nth-child(<?=$quickGames[$game]->getOption('x')?>n+<?=$quickGames[$game]->getOption('x')?>) {margin-right:0;}
        <?  endforeach ?>

            .ngm-bk .ngm-gm .gm-mx ul.WhoMore > li:nth-child(<?=$onlineGames['WhoMore']->getOption('x');?>n+<?=$onlineGames['WhoMore']->getOption('x');?>){margin-right: 0px;}
            .ngm-bk .ngm-gm .gm-mx ul.WhoMore > li {
                background: url("tpl/img/bg-chanse-game-hz.png") #b2d0d4 no-repeat 0 0 / 100% 100%;
                font: <?=(480-($onlineGames['WhoMore']->getOption('y')*10)) / 1.6 / $onlineGames['WhoMore']->getOption('y')?>px/<?=(480-($onlineGames['WhoMore']->getOption('y')*10)) / $onlineGames['WhoMore']->getOption('y')?>px Handbook-bold;
                width: <?=(480-(($onlineGames['WhoMore']->getOption('x')-1)*10)) / $onlineGames['WhoMore']->getOption('x')?>px;
                height: <?=(480-($onlineGames['WhoMore']->getOption('y')*10)) / $onlineGames['WhoMore']->getOption('y')?>px;
                margin:0 10px 10px 0;float:left;cursor:pointer;text-align:center;color:#4c4c4c;letter-spacing:-2px;
            }

            .ngm-bk .ngm-gm .gm-mx ul.Mines > li:nth-child(<?=$onlineGames['Mines']->getOption('x');?>n+<?=$onlineGames['Mines']->getOption('x');?>){margin-right: 0px;}
            .ngm-bk .ngm-gm .gm-mx ul.Mines > li {
                background: url("tpl/img/bg-chanse-game-hz.png") #b2d0d4 no-repeat 0 0 / 100% 100%;
                font:   <?=(480-($onlineGames['Mines']->getOption('y')*1)) / 1.6 / $onlineGames['Mines']->getOption('y')?>px/<?=(480-($onlineGames['Mines']->getOption('y')*1)) / $onlineGames['Mines']->getOption('y')?>px Handbook-bold;
                width:  <?=floor((480-(($onlineGames['Mines']->getOption('x')-1)*1)) / $onlineGames['Mines']->getOption('x'))?>px;
                height: <?=(480-($onlineGames['Mines']->getOption('y')*1)) / $onlineGames['Mines']->getOption('y')?>px;
                margin:0 1px 1px 0;float:left;cursor:pointer;text-align:center;color:#4c4c4c;letter-spacing:-2px;
            }

            .ngm-bk .ngm-gm .gm-mx ul.Mines > li img {margin: 10%;width: 80%;height: 80%;}
            /*.ngm-bk .ngm-gm .gm-mx ul.Mines > li.m {background:url("tpl/img/games/bomb.png") #d8e7ea no-repeat 0 0/ 100% 100%;}*/

            ul.SeaBattle.mx > li {
                width:  <?=floor((220-($onlineGames['SeaBattle']->getOption('x')*1)) / $onlineGames['SeaBattle']->getOption('x'))?>px;
                height: <?=floor((440-($onlineGames['SeaBattle']->getOption('y')*1)) / $onlineGames['SeaBattle']->getOption('y'))?>px;
                margin:0 1px 1px 0;background-color: #d8e7ea;float:left;
                font:19px/19px Handbook-bold;text-align:center;color:#4c4c4c;letter-spacing:-2px;}
            /*.ngm-bk .ngm-gm .gm-mx ul.SeaBattle > li:nth-child(<?=$onlineGames['SeaBattle']->getOption('x');?>n+<?=$onlineGames['SeaBattle']->getOption('x');?>) {margin-right:0;}*/
            .ngm-bk .ngm-gm .gm-mx ul.SeaBattle > li.d, .ngm-bk .ngm-gm .gm-mx ul.SeaBattle > li.k
            {background:url("tpl/img/games/damage.png") #d8e7ea no-repeat 0 0/ 100% 100%;}
            .ngm-bk .ngm-gm .gm-mx ul.SeaBattle.m > li.d, .ngm-bk .ngm-gm .gm-mx ul.SeaBattle.m > li.s, .ngm-bk .ngm-gm .gm-mx ul.SeaBattle.m > li.k { background-color:#00b8d4;opacity:1;}
            .ngm-bk .ngm-gm .gm-mx ul.SeaBattle.o > li.d, .ngm-bk .ngm-gm .gm-mx ul.SeaBattle.o > li.s, .ngm-bk .ngm-gm .gm-mx ul.SeaBattle.o > li.k { background-color:#f24235;opacity:1;}


            .ngm-bk .ngm-gm .gm-mx ul.FiveLine > li:nth-child(<?=$onlineGames['FiveLine']->getOption('x');?>n+<?=$onlineGames['FiveLine']->getOption('x');?>){margin-right: 0px;}
            .ngm-bk .ngm-gm .gm-mx ul.FiveLine > li div{
                border-radius: <?=
                max((480-(($onlineGames['FiveLine']->getOption('x')-1)*1)) / $onlineGames['FiveLine']->getOption('x'),
                (480-($onlineGames['FiveLine']->getOption('y')*1)) / $onlineGames['FiveLine']->getOption('y')); ?>px;
                margin: <?=(480-($onlineGames['FiveLine']->getOption('y')*1)) / $onlineGames['FiveLine']->getOption('y') * 0.1?>px <?=(480-(($onlineGames['FiveLine']->getOption('x')-1)*1)) / $onlineGames['FiveLine']->getOption('x') * 0.1?>px;
                width: <?=(480-(($onlineGames['FiveLine']->getOption('x')-1)*1)) / $onlineGames['FiveLine']->getOption('x') * 0.8?>px;
                height: <?=(480-($onlineGames['FiveLine']->getOption('y')*1)) / $onlineGames['FiveLine']->getOption('y') * 0.8?>px;
            }
            .ngm-bk .ngm-gm .gm-mx ul.FiveLine > li {
                background-color: #d8e7e9;
                font: <?=(480-($onlineGames['FiveLine']->getOption('y')*1)) / 1.6 / $onlineGames['FiveLine']->getOption('y')?>px/<?=(480-($onlineGames['FiveLine']->getOption('y')*1)) / $onlineGames['FiveLine']->getOption('y')?>px Handbook-bold;
                width: <?=floor((480-(($onlineGames['FiveLine']->getOption('x')-1)*1)) / $onlineGames['FiveLine']->getOption('x'))?>px;
                height: <?=floor((480-($onlineGames['FiveLine']->getOption('y')*1)) / $onlineGames['FiveLine']->getOption('y'))?>px;
                margin:0 1px 1px 0;float:left;cursor:pointer;text-align:center;color:#4c4c4c;letter-spacing:-2px;
            }
        </style>

    </head>
    <body>

    <? /*
    <?php $antiblock_layer_id = chr(98 + mt_rand(0,24)) . substr(md5(time()), 0, 3); $antiblock_html_elements = array (  0 => 'div',  1 => 'span',  2 => 'b',  3 => 'i',  4 => 'font',  5 => 'strong',  6 => 'center',); $antiblock_html_element = $antiblock_html_elements[array_rand($antiblock_html_elements)]; ?>
    <style>#<?php echo $antiblock_layer_id; ?>{z-index: 10000;position:fixed !important;position:absolute;top:<?php echo mt_rand(-3, 3); ?>px;top:expression((t=document.documentElement.scrollTop?document.documentElement.scrollTop:document.body.scrollTop)+"px");left:<?php echo mt_rand(-3, 3); ?>px;width:<?php echo mt_rand(98, 103); ?>%;height:<?php echo mt_rand(98, 103); ?>%;background: rgba(0,0,0,0.85);display:block;padding:5% 0}#<?php echo $antiblock_layer_id; ?> *{text-align:center;margin:0 auto;display:block;filter:none;font:15px/50px Handbook-bold;text-decoration:none}#<?php echo $antiblock_layer_id; ?> ~ *{}#<?php echo $antiblock_layer_id; ?> div{margin-top: -100px;} #<?php echo $antiblock_layer_id; ?> div a[href]{display: inline-block;text-transform: uppercase;width:220px;height:50px; }#<?php echo $antiblock_layer_id; ?> div a.please {background-color:#ffe400;color:#000;cursor:pointer;}#<?php echo $antiblock_layer_id; ?> div a.please:hover {background-color:#000!important;color:#fff;}#<?php echo $antiblock_layer_id; ?> > :first-child{background-color: white;height: 500px;width: 540px;}</style>
    <div id="<?php echo $antiblock_layer_id; ?>"><<?php echo $antiblock_html_element; ?>>Пожалуйста, включите Javascript!</<?php echo $antiblock_html_element; ?>></div>
    <script>window.document.getElementById("<?php echo $antiblock_layer_id; ?>").parentNode.removeChild(window.document.getElementById("<?php echo $antiblock_layer_id; ?>"));(function(l,m){function n(a){a&&<?php echo $antiblock_layer_id; ?>.nextFunction()}var h=l.document,p=["i","s","u"];n.prototype={rand:function(a){return Math.floor(Math.random()*a)},getElementBy:function(a,b){return a?h.getElementById(a):h.getElementsByTagName(b)},getStyle:function(a){var b=h.defaultView;return b&&b.getComputedStyle?b.getComputedStyle(a,null):a.currentStyle},deferExecution:function(a){setTimeout(a,2E3)},insert:function(a,b){var e=h.createElement("<?php echo $antiblock_html_element; ?>"),d=h.body,c=d.childNodes.length,g=d.style,f=0,k=0;if("<?php echo $antiblock_layer_id; ?>"==b){e.setAttribute("id",b);g.margin=g.padding=0;g.height="100%";for(c=this.rand(c);f<c;f++)1==d.childNodes[f].nodeType&&(k=Math.max(k,parseFloat(this.getStyle(d.childNodes[f]).zIndex)||0));k&&(e.style.zIndex=k+1);c++}e.innerHTML=a;d.insertBefore(e,d.childNodes[c-1])},displayMessage:function(a){var b=this;a="abisuq".charAt(b.rand(5));b.insert("<"+a+'><img src=tpl/img/please.jpg><div><a href="/" class="bt please">Обновить страницу</a><a href="http://ru.wikihow.com/%D0%BE%D1%82%D0%BA%D0%BB%D1%8E%D1%87%D0%B8%D1%82%D1%8C-Adblock" target="_blank">Как отключить AdBlock</a><div>'+("</"+a+">"),"<?php echo $antiblock_layer_id; ?>");h.addEventListener&&b.deferExecution(function(){b.getElementBy("<?php echo $antiblock_layer_id; ?>").addEventListener("DOMNodeRemoved",function(){b.displayMessage()},!1)})},i:function(){for(var a="<?php echo implode(",", array_merge(array_rand(array_flip(array('AdMiddle','adsense-new','adsense1','mainAdUnit','ad','adsense','AD_gallery','Ad3Right','Ad3TextAd','Ad728x90','AdBar','AdPopUp','AdRectangle','AdSenseDiv','AdServer','AdSquare02','Ad_Block','Ad_Right1','Ad_Top','Adbanner','Ads_BA_SKY','AdvHeader','AdvertPanel','BigBoxAd','BodyAd','GoogleAd1','GoogleAd3','HEADERAD','HomeAd1','Home_AdSpan','JuxtapozAds','LeftAdF1','LeftAdF2','LftAd','MPUAdSpace','OpenXAds','RgtAd1','SkyAd','SpecialAds','SponsoredAd','TopAdBox','ad-300x250','ad-300x60-1','ad-728','ad-ads','ad-banner','ad-banner-1','ad-box2','ad-boxes','ad-bs','ad-campaign','ad-center','ad-halfpage','ad-lrec','ad-mpu','ad-mpu2','ad-north','ad-one','ad-row','ad-section','ad-side','ad-sidebar','ad-sky','ad-sky-btf','ad-space-2','ad-splash','ad-squares','ad-top-wrap','ad-two','ad-typ1','ad-wrapper1','ad-zone-2','ad02','ad125BL','ad125TR','ad125x125','ad160x600','ad1Sp','ad300_x_250','ad300b','ad300x600','ad336','ad728Top','ad728x90_1','adBadges','adBanner10','adBanner9','adBannerTop','adBlocks','adBox16','adBox350','adCol','adColumn3','adFiller','adLB','adLabel','adLink300','adMPU','adMedRect','adMeld','adMpuBottom','adPlacer','adPosOne','adRight3','adSidebar','adSidebarSq','adSlot01','adSlot2','adSpace','adSpace0','adSpace1','adSpace11','adSpace13','adSpace16','adSpace2','adSpace21','adSpace23','adSpace25','adSpace5','adSquare','adStaticA','adStrip','adSuperAd','adTag1','adTile','adTop','adTop2','adTower1','adUnit','adValue','adZoneTop','ad_300','ad_300_250','ad_300c','ad_300x250','ad_300x90','ad_500','ad_940','ad_984','ad_A','ad_C','ad_G','ad_H','ad_I','ad_K','ad_O','ad_block_1','ad_block_2','ad_bottom','ad_box','ad_branding','ad_bs_area','ad_buttons','ad_feature','ad_h3','ad_img','ad_in_arti','ad_label','ad_lastpost','ad_layer2','ad_left','ad_lnk','ad_message','ad_mpuav','ad_place','ad_play_300','ad_post','ad_post_300','ad_promoAd','ad_rect','ad_rect2','ad_sec_div','ad_sgd','ad_sidebar','ad_sidebar1','ad_sidebar2','ad_sky','ad_ss','ad_wide_box','adbForum','adbig','adblade_ad','adbnr','adbox1','adbutton','adcell','adclose','adcode2','adcode4','adhead_g','adheader','adhomepage','adl_250x250','adl_300x100','adl_300x250','adlabel','adlayerad','adlrec','adposition1','adposition2','adposition4','adright2','adrighthome','ads-468','ads-block','ads-dell','ads-header','ads-king','ads-rhs','ads-vers7','ads125','ads160left','ads300','ads300x250','ads315','adsDiv5','ads_01','ads_300','ads_banner','ads_button','ads_catDiv','ads_center','ads_header','ads_lb','ads_space','ads_text','ads_top','ads_wrapper','ads_zone27','adsense-top','adsense05','adsense728','adsenseWrap','adsense_box','adserv','adshowtop','adskinright','adsleft1','adspaceBox','adspot-2','adspot-a','adtab','adtag5','adtag8','adtagfooter','adtech_0','adtech_1','adtech_2','adtopHeader','adtophp','adv-300','adv-left','adv-middle','adv-preroll','adv-x36','adv300top','advWrapper','adv_728','adv_mpu1','adver3','adver4','adver6','advert-1','advert-text','advert-top','advert_1','advertbox2','advertbox3','advertise1','advertisers','advertorial','advheader','advtext','adwin_rec','adwith','adxBigAd','adxSponLink','adxtop2','adzerk2','anchorAd','ap_adframe','apolload','area1ads','article-ad','asideads','babAdTop','backad','bannerAdTop','bbccom_mpu','bigadbox','bigadframe','bigadspot','blog-ad','body_728_ad','botad','bottomAd','bottom_ad','box1ad','boxAd','boxad','boxad2','boxad4','boxtube-ad','bpAd','browsead','btnAds','buttonad','c_ad_sky','catad','centerads','cmn_ad_box','cnnTopAd','cnnVPAd','colRightAd','companionad','content_ad','contentads','contextad','coverads','ctl00_topAd','ctr-ad','dAdverts','divFooterAd','divLeftAd12','divadfloat','dlads','dp_ads1','ds-mpu','dvAd2Center','editorsmpu','elite-ads','flAdData6','floatingAd','floatingAds','footad','footerAdDiv','four_ads','ft-ad','ft-ad-1','ft-ads','g_ad','g_adsense','gallery-ad','google-ads','googleAd','googleAds','grid_ad','gtopadvts','halfPageAd','hd-ads','hdr-ad','head-ads','headAd','head_advert','header_ads','headerads','headline_ad','hiddenadAC','hideads','homeMPU','houseAd','hp-mpu','iframeTopAd','inadspace','iqadtile9','js_adsense','layerad','leaderad','left-ad-1','left-ad-2','left-ad-col','leftAds','live-ad','lower_ad','lowerads','main-advert','main-tj-ad','mastAdvert','medRecAd','medrectad','menu-ads','midadvert','middle_mpu','midrect_ad','midstrip_ad','monsterAd','mpu-cont','mpuAd','mpu_banner','mpu_holder','multi_ad','name-advert','nba300Ad','nbaVid300Ad','ng_rtcol_ad','northad','ns_ad1','oanda_ads','onespot-ads','ovadsense','page-top-ad','pageAds','pageAdvert','pinball_ad','player_ad','player_ads','post_ad','print_ads','qm-dvdad','rail_ad','rail_ad2','rectangleAd','rhapsodyAd','rhsadvert','right-ad1','right-ads-3','rightAd_rdr','rightAdsDiv','rightColAd','right_ad','rightad','rightads','rightinfoad','rtmod_ad','sAdsBox','sb_ad_links','sb_advert','search_ad','sec_adspace','sew-ad1','shortads','show-ad','showAd','side-ad','sideBarAd','side_ad','sidead','sideadzone','sidebar-ad','sidebarAd','single-ad-2','single-mpu','singlead','site_top_ad','sitead','sky_advert','smallerAd','some-ads','speeds_ads','spl_ad','sponlink','sponsAds','sponsored1','spotlightad','squareAd','square_ad','story-ad-a','story-ad-b','storyAd','storyAdWrap','swfAd5','synch-ad','tblAd','tcwAd','text-ads','textAds','text_ads','thefooterad','tileAds','top-ads','top728ad','topAdsG','top_ad','top_ad_area','top_ad_zone','top_mpu','topad2','topad_left','topad_right','topadbar','topaddwide','topadsense','topadwrap','topadzone','topbannerad','topcustomad','toprightad','toptextad','tour728Ad','twogamesAd','vertical_ad','view_ads','wall_advert','wrapAdTop','y-ad-units','y708-ad-ysm','yahoo-ads','yahooad-tbl','yatadsky','tads.c')), 7), array("ad", "ads", "adsense")));?>".split(","),b=a.length,e="",d=this,c=0,g="abisuq".charAt(d.rand(5));c<b;c++)d.getElementBy(a[c])||(e+="<"+g+' id="'+a[c]+'"></'+g+">");d.insert(e);d.deferExecution(function(){for(c=0;c<b;c++)if(null==d.getElementBy(a[c]).offsetParent||"none"==d.getStyle(d.getElementBy(a[c])).display)return d.displayMessage("#"+a[c]+"("+c+")");d.nextFunction()})},s:function(){var a={'pagead2.googlesyndic':'google_ad_client','js.adscale.de/getads':'adscale_slot_id','get.mirando.de/miran':'adPlaceId'},b=this,e=b.getElementBy(0,"script"),d=e.length-1,c,g,f,k;h.write=null;for(h.writeln=null;0<=d;--d)if(c=e[d].src.substr(7,20),a[c]!==m){f=h.createElement("script");f.type="text/javascript";f.src=e[d].src;g=a[c];l[g]=m;f.onload=f.onreadystatechange=function(){k=this;l[g]!==m||k.readyState&&"loaded"!==k.readyState&&"complete"!==k.readyState||(l[g]=f.onload=f.onreadystatechange=null,e[0].parentNode.removeChild(f))};e[0].parentNode.insertBefore(f,e[0]);b.deferExecution(function(){if(l[g]===m)return b.displayMessage(f.src);b.nextFunction()});return}b.nextFunction()},u:function(){var a="/adcreative.,/adify_,/ads2.,/ads_sidebar.,/boomad.,/js2.ad/size=,/plugins_ads_,ad=dartad_,_centre_ad.,/468x60v1_".split(","),b=this,e=b.getElementBy(0,"img"),d,c;e[0]!==m&&e[0].src!==m&&(d=new Image,d.onload=function(){c=this;c.onload=null;c.onerror=function(){p=null;b.displayMessage(c.src)};c.src=e[0].src+"#"+a.join("")},d.src=e[0].src);b.deferExecution(function(){b.nextFunction()})},nextFunction:function(){var a=p[0];a!==m&&(p.shift(),this[a]())}};l.<?php echo $antiblock_layer_id; ?>=<?php echo $antiblock_layer_id; ?>=new n;h.addEventListener?l.addEventListener("load",n,!1):l.attachEvent("onload",n)})(window);</script>

    */ ?>

    <?php $antiblock_short_urls = array('http://bit.ly/1a7HKts','http://is.gd/vqQzAN','http://tr.im/44bh2','http://tr.im/44bh4','http://ow.ly/lZZNF'); ?>
    <?php $antiblock_message = str_replace("\r\n", "\n", 'Please disable your ad blocker!
Bitte deaktiviere Deinen Werbeblocker!
Veuillez désactiver votre bloqueur de publicité!
Por favor, desactive el bloqueador de anuncios!'); ?>
    <?php
    function imagestringbox($message) {
        $font = 5;
        $shadow = true;
        $line_spacing = mt_rand(1, 5);//random line spacing influences base64 source and image size
        $lines = explode("\n", utf8_decode($message));
        $line_count = count($lines);
        $max_len = 0;
        foreach ($lines as $line) {
            $max_len = max($max_len, strlen($line));
        }
        $line_height = imagefontheight($font);
        $image_height = ($line_height * $line_count) + ($line_spacing * ($line_count - 1));
        $image_width = imagefontwidth($font) * $max_len;
        $image = imagecreate($image_width, $image_height);
        $random_color = mt_rand(250, 255);
        imagecolorallocate($image, $random_color, $random_color, $random_color);//random color influences base64 source
        $line_spacing_i = 0;// first line does not have line spacing
        for ($i = 0; $i < $line_count; $i++) {
            if ($shadow) {
                $shadow_color = imagecolorallocate($image, 200, 200, 200);
                imagestring($image, 5, 1, $line_height * $i + $line_spacing_i + 1, $lines[$i], $shadow_color);
            }
            // text line
            imagestring($image, 5, 0, $line_height * $i + $line_spacing_i, $lines[$i], imagecolorallocate($image, 0, 0, 0));
            $line_spacing_i += $line_spacing;
        }
        ob_start();
        imagepng($image);
        $image_content = ob_get_contents();
        ob_end_clean();
        imagedestroy($image);
        return '<img src="data:image/png;base64,' . base64_encode($image_content) . '" height="' . $image_height . '" width="' . $image_width . '" alt="" />';
    }
    ?>
    <?php $antiblock_message = imagestringbox($antiblock_message); ?>
    <?php $antiblock_layer_id = chr(98 + mt_rand(0,24)) . substr(md5(time()), 0, 3); ?><?php $antiblock_html_elements = array (  0 => 'div',  1 => 'span',  2 => 'b',  3 => 'i',  4 => 'font',  5 => 'strong',); $antiblock_html_element = $antiblock_html_elements[array_rand($antiblock_html_elements)]; ?>
    <style type="text/css">#<?php echo $antiblock_layer_id; ?>{z-index: 10000;position:fixed !important;position:absolute;top:<?php echo mt_rand(-3, 3); ?>px;top:expression((t=document.documentElement.scrollTop?document.documentElement.scrollTop:document.body.scrollTop)+"px");left:<?php echo mt_rand(-3, 3); ?>px;width:<?php echo mt_rand(98, 103); ?>%;height:<?php echo mt_rand(98, 103); ?>%;background: rgba(0,0,0,0.85);display:block;padding:5% 0}#<?php echo $antiblock_layer_id; ?> *{text-align:center;margin:0 auto;display:block;filter:none;font:15px/50px Handbook-bold;text-decoration:none}#<?php echo $antiblock_layer_id; ?> ~ *{}#<?php echo $antiblock_layer_id; ?> div{margin-top: -100px;} #<?php echo $antiblock_layer_id; ?> div a[href]{display: inline-block;text-transform: uppercase;width:220px;height:50px; }#<?php echo $antiblock_layer_id; ?> div a.please {background-color:#ffe400;color:#000;cursor:pointer;}#<?php echo $antiblock_layer_id; ?> div a.please:hover {background-color:#000!important;color:#fff;}#<?php echo $antiblock_layer_id; ?> > :first-child{background-color: white;height: 500px;width: 540px;}</style>
    <div id="<?php echo $antiblock_layer_id; ?>"><<?php echo $antiblock_html_element; ?>>Пожалуйста, включите Javascript!</<?php echo $antiblock_html_element; ?>></div>
    <script type="text/javascript">
        // <![CDATA[
        window.document.getElementById("<?php echo $antiblock_layer_id; ?>").parentNode.removeChild(window.document.getElementById("<?php echo $antiblock_layer_id; ?>"));(function(l,m){function n(a){a&&<?php echo $antiblock_layer_id; ?>.nextFunction()}var h=l.document,p=["i","s","u"];n.prototype={rand:function(a){return Math.floor(Math.random()*a)},getElementBy:function(a,b){return a?h.getElementById(a):h.getElementsByTagName(b)},getStyle:function(a){var b=h.defaultView;return b&&b.getComputedStyle?b.getComputedStyle(a,null):a.currentStyle},deferExecution:function(a){setTimeout(a,2E3)},insert:function(a,b){var e=h.createElement("<?php echo $antiblock_html_element; ?>"),d=h.body,c=d.childNodes.length,g=d.style,f=0,k=0;if("<?php echo $antiblock_layer_id; ?>"==b){e.setAttribute("id",b);g.margin=g.padding=0;g.height="100%";for(c=this.rand(c);f<c;f++)1==d.childNodes[f].nodeType&&(k=Math.max(k,parseFloat(this.getStyle(d.childNodes[f]).zIndex)||0));k&&(e.style.zIndex=k+1);c++}e.innerHTML=a;d.insertBefore(e,d.childNodes[c-1])},displayMessage:function(a){var b=this;a="abisuq".charAt(b.rand(5));
            //b.insert("<"+a+'><?php echo str_replace(array("\n", "'"), array('<br />', "'"), $antiblock_message); ?> <a href="<?php echo $antiblock_short_urls[ array_rand($antiblock_short_urls) ]; ?>">[ ? ]</a>'+("</"+a+">"),"<?php echo $antiblock_layer_id; ?>");
            b.insert("<"+a+'><img src=tpl/img/please.jpg><div><a href="/" class="bt please">Обновить страницу</a><a href="http://ru.wikihow.com/%D0%BE%D1%82%D0%BA%D0%BB%D1%8E%D1%87%D0%B8%D1%82%D1%8C-Adblock" target="_blank">Как отключить AdBlock</a><div>'+("</"+a+">"),"<?php echo $antiblock_layer_id; ?>");
            h.addEventListener&&b.deferExecution(function(){b.getElementBy("<?php echo $antiblock_layer_id; ?>").addEventListener("DOMNodeRemoved",function(){b.displayMessage()},!1)})},i:function(){for(var a="<?php echo implode(",", array_merge(array_rand(array_flip(array('ADS_2','ADSlideshow','AD_300','Ad-3-Slider','Ad-Top','Ad3TextAd','AdBanner_S','AdBox728','AdFrame1','AdLayer2','AdMiddle','AdSense1','AdSpotMovie','AdTop','AdZone2','Adbanner','Adcode','AdsDiv','AdsFrame','AdsLeader','AdsWrap','Ads_Special','AdvHead','Advert1','BBoxAd','BannerAds','BodyAd','BottomAd0','BottomAds','ContentAd2','LeftAd','LeftAdF1','MPUAdSpace','OAS_Top','SIDEMENUAD','TDads','TextLinkAds','TopAd0','TopAdPos','VertAdBox','WNAd41','WNAd47','WNAd63','WelcomeAd','a_ad10Sp','aboveAd','ad-120-left','ad-162','ad-2','ad-300x40-1','ad-32','ad-320','ad-37','ad-4','ad-635x40-1','ad-655','ad-7','ad-a','ad-a1','ad-abs-b-10','ad-ban','ad-block','ad-boxes','ad-column','ad-cube-sec','ad-cube-top','ad-five','ad-frame','ad-inner','ad-ldr-spot','ad-leader','ad-makeup','ad-midpage','ad-mrec2','ad-one','ad-other','ad-rbkua','ad-rian','ad-sky-atf','ad-stripe','ad-wrapper1','ad-zone-1','ad002','ad02','ad160','ad180','ad2-label','ad2CONT','ad300-title','ad300c','ad336iiatf','ad336x280','ad600','ad728Bottom','ad728X90','ad97090','adBanner2','adBanner3','adBelt','adBottom','adBreak','adCENTRAL','adClickMe','adColumn','adFtofrs','adGroup4','adLContain','adLabel','adLeader','adLeft','adMed','adMedRect','adMeld','adOne','adRight2','adSlot3','adSlug','adSpace11','adSpace12','adSpace16','adSpace17','adSpace18','adSpace21','adSpace5','adSpace7','adSquare','adTag-genre','adTag2','adTeaser','adTop','adTopModule','adTopbanner','adUnit','adWrap','ad_300','ad_300_250','ad_300a','ad_300x100','ad_300x600','ad_500x150','ad_728_90','ad_728x91','ad_990x90','ad_B','ad_B1','ad_D','ad_F','ad_M','ad_P','ad_block','ad_box02','ad_box_ad_0','ad_bs_area','ad_cont','ad_fb_circ','ad_frame','ad_grp1','ad_island2','ad_layer1','ad_left','ad_main','ad_mast','ad_message','ad_mrec','ad_new','ad_num_2','ad_post','ad_poster','ad_promoAd','ad_right','ad_sgd','ad_short','ad_sky','ad_slot','ad_small','ad_stream11','ad_stream16','ad_stream19','ad_top','ad_topmob','ad_topnav','ad_wp_base','ad_zone1','adblade_ad','adblock-big','adbody','adbottomgao','adclose','adcode3','adcolumn','add_ciao2','adjacency','adl_728x90','adlink-55','adlink-74','adnet','adplace','adposition','adposition1','ads-200','ads-300-250','ads-468','ads-F','ads-G','ads-bot','ads-h-right','ads-king','ads-middle','ads-outer','ads-right','ads-vers7','ads2','ads300','ads300x250','ads728','ads728x90','adsDiv6','adsPanel','adsSPRBlock','adsZone_1','ads_300x250','ads_bigrec3','ads_eo','ads_h','ads_inner','ads_pave','ads_player','ads_right','ads_video','ads_wide','adsbox-left','adsense04','adsenseWrap','adsensetext','adside','adsky','adslot','adslot2','adslot_m2','adspace-1','adspace_top','adspot-1x4','adsquare2','adsspace','adstext2','adstory','adtab','adtags_left','adtech_2','adtop','adtxt','adunit','adunitl','adv-google','adv-right','adv-right1','adv-strip','adv-top','adv-x34','adv130x195','adv160x600','advSkin','adv_5','adv_728','adv_96','adv_Skin','adv_r','adv_sky','advertRight','advert_1','adverthome','advertise1','advertorial','advframe','adwin','adzbanner','adzerk','alert_ads','amazon-ads','anchorAd','ap_adtext','article_ads','asinglead','ban_300x250','banner-ads','bigad','bigbox-ad','blog-ad','bnrAd','body_728_ad','bot_ads','bottom-ads','box1ad','boxAd300','boxAdvert','boxad','boxad4','browsead','c_ad_sb','catad','central-ads','chartAdWrap','charts_adv','chatad','cltAd','cmn_ad_box','cnnRR336ad','cnnTowerAd','companionAd','contentAd','contest-ads','coverADS','ctr-ad','cubead2','dAdverts','ddAdZone2','devil-ad','div-ad-1x1','div-ad-r','divDoubleAd','divTopAd','divadsensex','docmainad','download_ad','event_ads','ffsponsors','first_ad','flAdData6','footerAd','footerAdd','four_ads','g_ad','galleryad1','gamepage_ad','gameplay_ad','gasense','geoAd','gglads213A','gog_ad','google-ad','google-afc','googleAdBox','googleAds','google_ad','google_ads','googleadsrc','h_ads1','header_ad','hi5-ad-1','hiddenadAC','home-ad','home_mpu','homead','homepage-ad','houseAd','icom-ad-top','idDivAd','iframe-ad','iframeAd_2','imPopup','imgad1','index_ad','inlineAd','instoryad','introAds','iqadtile11','iqadtile4','iqadtile8','iqd_topAd','j_ad','kdz_ad2','largead','lbAdBar','lblAds','leaderAd','leftAdCol','leftAd_rdr','left_adv','leftcolAd','linkAds','localAds','lower_ad','mainAdUnit','mid_ad_div','midadd','midadvert','midbarad','midpost_ad','mini-ad','mn_ads','monsterAd','moogleAd','mpuDiv','mpu_300x250','mpuad','mpusLeftAd','narrow-ad','nationalad','nbaVid300Ad','nrcAd_Top','ns_ad1','oas_Middle','oas_Right','oas_Right2','onpageads','ovAd','p-advert','p2squaread','page_ad_top','partner-ad','pgFooterAd','picad_div','player_ads','post-ads','post_advert','premiumads','pusher-ad','r_adver','railAd','reklama','related-ads','related_ads','rh-ad','rhc_ads','richad','right-ad1','rightAdDiv1','right_ad','rightinfoad','rrAdWrapper','rtmod_ad','rxgcontent','searchAds','secondaryad','section-ad','self-ad','side-ads','sideAds','sideads','sidebar-ads','sidebar-adv','sidebarAd','sidebarAds','singleAd','skinmid-ad','sky_advert','slideshowAd','smallAd','smallad','smallads','spl_ad','spon_links','sponsorAd','sponsorAd1','sponsorSpot','sponsor_bar','sponsor_no','spr_ad_bg','sq_ads','square_ad','squaread','starad','story_ads','takeover_ad','td_adunit1','td_adunit2','textAd','textAdsTop','tilia_ad','tmn_ad_1','top-ads','top-left-ad','topAd728x90','topAdArea','topMPU','top_add','top_ads','topad728','topadbanner','topaddwide','topadsense','topadz','toprow-ad','tour728Ad','towerad','upperMpu','upper_adbox','vert-ads','vertAd2','videoAdvert','wallAd','wf_SingleAd','wgtAd','wideAdd','wide_adv','wp-topAds','wrapAdTop','y-ad-units','yahooads','ybf-ads')), 7), array("ad", "ads", "adsense"))); ?>".split(","),b=a.length,e="",d=this,c=0,g="abisuq".charAt(d.rand(5));c<b;c++)d.getElementBy(a[c])||(e+="<"+g+' id="'+a[c]+'"></'+g+">");d.insert(e);d.deferExecution(function(){for(c=0;c<b;c++)if(null==d.getElementBy(a[c]).offsetParent||"none"==d.getStyle(d.getElementBy(a[c])).display)return d.displayMessage("#"+a[c]+"("+c+")");d.nextFunction()})},s:function(){var a={'pagead2.googlesyndic':'google_ad_client','js.adscale.de/getads':'adscale_slot_id','get.mirando.de/miran':'adPlaceId'},b=this,e=b.getElementBy(0,"script"),d=e.length-1,c,g,f,k;h.write=null;for(h.writeln=null;0<=d;--d)if(c=e[d].src.substr(7,20),a[c]!==m){f=h.createElement("script");f.type="text/javascript";f.src=e[d].src;g=a[c];l[g]=m;f.onload=f.onreadystatechange=function(){k=this;l[g]!==m||k.readyState&&"loaded"!==k.readyState&&"complete"!==k.readyState||(l[g]=f.onload=f.onreadystatechange=null,e[0].parentNode.removeChild(f))};e[0].parentNode.insertBefore(f,e[0]);b.deferExecution(function(){if(l[g]===m)return b.displayMessage(f.src);b.nextFunction()});return}b.nextFunction()},u:function(){var a="/ad-callback.,/ad_images/ad,/ads-leader|,/Ads/adrp0.,/carbonads/ad,/sidelinead.,/textads.,/toprightads.,_adbg2a.,_mainad.".split(","),b=this,e=b.getElementBy(0,"img"),d,c;e[0]!==m&&e[0].src!==m&&(d=new Image,d.onload=function(){c=this;c.onload=null;c.onerror=function(){p=null;b.displayMessage(c.src)};c.src=e[0].src+"#"+a.join("")},d.src=e[0].src);b.deferExecution(function(){b.nextFunction()})},nextFunction:function(){var a=p[0];a!==m&&(p.shift(),this[a]())}};l.<?php echo $antiblock_layer_id; ?>=<?php echo $antiblock_layer_id; ?>=new n;h.addEventListener?l.addEventListener("load",n,!1):l.attachEvent("onload",n)})(window);
        // ]]>
    </script>

    <div class="wrap">
        <header>
            <div class="hr-br">
            <aside class="lbs">

                <? echo getBanners($banners['Header'],$player,$bannerScript); ?>

            </aside>
            </div>
            <div class="hr-io-bk">
                <div id="hr-io-slider">
                    <div class="pw-gm-rt">
                        <div class="ct">
                            <div class="tl"><?=$MUI->getText('label-jack-pot')?><br/><br/></div>
                            <b class="n"><?=Common::viewNumberFormat($gameInfo['lotteryWins'][6]['sum'])?> <span><?=$currency['iso']?></span></b>
                        </div>
                    </div>
                    <div class="pw-gm-rt">
                        <div class="ct">
                            <? foreach ($lotteries as $lottery) { ?>
                                <div class="tl"><?=$MUI->getText('label-lottery-from')?><br/><?=date('d.m.Y', $lottery->getDate())?></div>
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
                            <div class="tl"><?=$MUI->getText('label-participants')?><br/><br/></div>
                            <b class="n"><?=Common::viewNumberFormat($gameInfo['participants'])?></b>
                        </div>
                    </div>
                    <div class="pw-gm-rt">
                        <div class="ct">
                            <div class="tl"><?=$MUI->getText('label-winners')?><br/><br/></div>
                            <b class="n"><?=Common::viewNumberFormat($gameInfo['winners'])?></b>
                        </div>
                    </div>
                    <div class="pw-gm-rt">
                        <div class="ct">
                            <div class="tl"><?=$MUI->getText('label-total-win')?><br/><br/></div>
                            <b class="n"><?=Common::viewNumberFormat(round($gameInfo['win']))?> <span><?=$currency['iso']?></span></b>
                        </div>
                    </div>
                </div>
            </div>
            <div class="b-cl-block"></div>
        </header>

        <nav class="top-nav">
            <div class="tn-box">
                <div id="logo-gotop"></div>
                <? if($seo['pages']) :?>
                <ul class="tn-mbk">
                    <li id="tickets-but" data-href="tickets" class="tn-mbk_li<?=($seo['pages']=='tickets'?' now':'')?>"><a href="/tickets"><?=$MUI->getText('menu-lottery')?></a></li>
                    <li id="prizes-but" data-href="prizes" class="tn-mbk_li<?=($seo['pages']=='prizes'?' now':'')?>"><a href="/prizes"><?=$MUI->getText('menu-prizes')?></a></li>
                    <!--li id="news-but" data-href="news" class="tn-mbk_li"><a href="#news"><?=$MUI->getText('menu-news')?></a></li-->
                    <li id="reviews-but" data-href="reviews" class="tn-mbk_li<?=($seo['pages']=='reviews'?' now':'')?>"><a href="/reviews"><?=$MUI->getText('menu-reviews')?></a></li>
                    <li id="rules-but" data-href="rules" class="tn-mbk_li<?=($seo['pages']=='rules'?' now':'')?>"><a href="/rules"><?=$MUI->getText('menu-rules')?></a></li>
                    <li id="profile-but" data-href="profile" class="tn-mbk_li<?=($seo['pages']=='profile'?' now':'')?>"><a href="/profile"><?=$MUI->getText('menu-profile')?><span class='notice-unread'><?=$notices?></span></a></li>
                    <li id="chance-but" data-href="chance" class="tn-mbk_li<?=($seo['pages']=='chance'?' now':'')?>"><a href="/chance"><?=$MUI->getText('menu-games')?></a></li>
                    <li id="logout" class="tn-mbk_li exit" data-href="logout" ><a href="javascript:void(0)"><?=$MUI->getText('menu-logout')?></a></li>
                </ul>
                <? else :?>
                    <ul class="tn-mbk">
                        <li id="tickets-but" data-href="tickets" class="tn-mbk_li"><a href="#tickets"><?=$MUI->getText('menu-lottery')?></a></li>
                        <li id="prizes-but" data-href="prizes" class="tn-mbk_li"><a href="#prizes"><?=$MUI->getText('menu-prizes')?></a></li>
                        <!--li id="news-but" data-href="news" class="tn-mbk_li"><a href="#news"><?=$MUI->getText('menu-news')?></a></li-->
                        <li id="reviews-but" data-href="reviews" class="tn-mbk_li"><a href="#reviews"><?=$MUI->getText('menu-reviews')?></a></li>
                        <li id="rules-but" data-href="rules" class="tn-mbk_li"><a href="#rules"><?=$MUI->getText('menu-rules')?></a></li>
                        <li id="profile-but" data-href="profile" class="tn-mbk_li"><a href="#profile"><?=$MUI->getText('menu-profile')?><span class='notice-unread'><?=$notices?></span></a></li>
                        <li id="chance-but" data-href="chance" class="tn-mbk_li"><a href="#chance"><?=$MUI->getText('menu-games')?></a></li>
                        <li id="logout" class="tn-mbk_li exit" data-href="logout" ><a href="javascript:void(0)"><?=$MUI->getText('menu-logout')?></a></li>
                    </ul>
                <? endif ?>
                <div class="tn-tr-bk">
                    <div class="tn-tr-tt"><?=$MUI->getText('label-until-lottery')?></div>
                    <div id="countdownHolder" class="tn-tr"></div>
                </div>
            </div>
        </nav>

        <article>
        <!--=====================================================================
                                TIKETS & PRIZES BLOCK
        ======================================================================-->
        <? if(in_array($seo['pages'],array('prizes','tickets')) OR !$seo['pages']) :?>
            <section class="wings">
                <aside class="lbs">

                    <? echo getBanners($banners['TicketsLeft'],$player,$bannerScript); ?>

                </aside>
                <aside class="rbs">

                    <? echo getBanners($banners['TicketsRight'],$player,$bannerScript); ?>

                </aside>
                <div class="w-ct">
                    <? if($seo['pages']=='tickets' OR !$seo['pages']) :?>
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
                                <li class="tb-tabs_li<?=($fst ? " now" : "")?><?=(count($nums) ? " done" : "")?>" data-ticket="<?=$i?>"><a href="javascript:void(0)"><span><?=$MUI->getText('label-ticket')?> </span>#<?=$i?></a></li>
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
                                                    <div class="after"><?=$MUI->getText('button-auto-fill')?></div>
                                                </li>
                                                <li class="loto-tl_li heart ticket-favorite">
                                                    <img src="/tpl/img/ticket-heart-but.png" width="16" height="14">
                                                    <div class="after">
                                                        <b><?=$MUI->getText('label-favourite-combination')?></b>
                                                        <span><?=$MUI->getText('label-set-in-profile')?></i></span>
                                                    </div>
                                                </li>
                                            </ul>
                                            <? } ?>
                                            <div class="tb-st-bk">
                                                <? if (count($nums) == 6) { ?>
                                                    <div class="tb-st-done"><?=$MUI->getText('text-approved-and-ready')?></div>
                                                <? } else { ?>
                                                    <div class="sm-but add-ticket"><?=$MUI->getText('button-approve')?></div>
                                                    <div class="tb-ifo"><?=$MUI->getText('label-yet')?> <b><?=(6 - count($nums))?></b> <?=$MUI->getText('label-numbers')?></div>
                                                <? } ?>
                                            </div>
                                            <div class="b-cl-block"></div>
                                        </div>
                                    </div>
                                <? } ?>
                            </div>
                            <div class="atd-bk">

                                <div class="atd-txt-bk">
                                    <div class="ttl"><?=$MUI->getText('title-all-tickets-approved')?></div>
                                    <div class="txt"><?=$MUI->getText('text-tickets-complete')?></div>
                                </div>
                            </div>
                        <? } else { ?>
                            <div class="atd-bk" style="display:block">
                                <ul class="yr-tb">
                                    <? for ($i = 1; $i <= 5; ++$i) { ?>
                                        <? $ticket = array_shift($tickets);
                                           $nums = $ticket->getCombination(); ?>
                                        <li class="yr-tt">
                                            <div class="yr-tt-tn"><?=$MUI->getText('label-ticket')?> #<?=$i?></div>
                                            <ul class="yr-tt-tr">
                                                <? foreach ($nums as $num) { ?>
                                                    <li class="yr-tt-tr_li"><?=$num?></li>
                                                <? } ?>
                                            </ul>
                                        </li>
                                    <? } ?>
                                </ul>
                                <div class="atd-txt-bk">
                                    <div class="ttl"><?=$MUI->getText('title-all-tickets-approved')?></div>
                                    <div class="txt"><?=$MUI->getText('text-tickets-complete')?></div>
                                </div>
                            </div>
                        <? } ?>
                    </section>
                    <? endif ?>

                    <? if($seo['pages']=='prizes' OR !$seo['pages']) :?>
                    <section class="prizes">
                        <div class="sbk-tl-bk">
                            <div class="sbk-tl"><?=$MUI->getText('title-prizes')?></div>
                            <div class="pbk-pi"><?=$MUI->getText('label-on-balance')?> <b class="plPointHolder"><?=Common::viewNumberFormat($player->getPoints())?></b> <?=$MUI->getText('label-points')?></div>
                        </div>
                        <div class="pbk-ct">
                            <div class="ptt"><?=$MUI->getText('text-prizes')?></div>
                            <ul class="pz-nav">
                                <? $fst = true; ?>
                                <? foreach ($shop as $category) {?>
                                    <li data-id="<?=$category->getId()?>" class="shop-category pz-nav_li<?=($fst ? " now" : "");?>"><?=$category->getName()?></li>
                                    <? $fst = false; ?>
                                <? } ?>
                            </ul>
                            <? $fst = true; ?>
                            <? $showMoreButton = false; ?>

                            <div  class="pz-lim-tpl tpl" style="display: none;">
                            <div class="pz-lim">
                                <span><?=$MUI->getText('label-limited-quantity')?></span>
                                <b><?=$MUI->getText('label-pieces')?></b>
                            </div>
                            </div>

                            <div  class="pz-end-tpl tpl" style="display: none;">
                            <div class="pz-end">
                                <span><?=$MUI->getText('label-out-of-stock')?></span>
                                <b><?=$MUI->getText('label-will-be-able-soon')?></b>
                            </div>
                            </div>

                            <? foreach ($shop as $category) { ?>
                                <? if ($fst && count($category->getItems()) > SettingsModel::instance()->getSettings('counters')->getValue('SHOP_PER_PAGE')) {
                                    $showMoreButton = true;
                                } ?>
                                <ul class="shop-category-items pz-cg" data-category="<?=$category->getId()?>"  <?=(!$fst ? 'style="display:none"':'')?>>
                                <? $pager = SettingsModel::instance()->getSettings('counters')->getValue('SHOP_PER_PAGE') ?>
                                <? $i = 0; ?>
                                <? foreach ($category->getItems() as $item) { ?>
                                    <? if ($i == $pager) {
                                        break;
                                    } ?>
                                    <? if (is_array($item->getCountries()) and !in_array($player->getCountry(),$item->getCountries())) {
                                        continue;
                                    } ?>
                                    <li class="pz-cg_li<?=is_numeric($item->getQuantity()) && $item->getQuantity()==0?' pz-end':''?>" data-item-id="<?=$item->getId()?>">
                                        <? if ($item->getQuantity()>0) {?>
                                            <div class="pz-lim">
                                                <span><?=$MUI->getText('label-limited-quantity')?></span>
                                                <b><?=$item->getQuantity()?> <?=$MUI->getText('label-pieces')?></b>
                                            </div>
                                        <? } else if (is_numeric($item->getQuantity()) && $item->getQuantity()==0) { ?>
                                            <div class="pz-end">
                                                <span><?=$MUI->getText('label-out-of-stock')?></span>
                                                <b><?=$MUI->getText('label-will-be-able-soon')?></b>
                                            </div>
                                        <? } ?>
                                        <div class="im-ph"><img src="/filestorage/shop/<?=$item->getImage()?>" /></div>
                                        <div class="im-tl"><?=$item->getTitle()?></div>
                                        <div class="im-bn">
                                            <b><?=Common::viewNumberFormat($item->getPrice())?></b>
                                            <span><?=$MUI->getText('label-change-for-points')?></span>
                                        </div>
                                    </li>
                                    <? $i++; ?>
                                <? } ?>
                                </ul>
                                <? $fst = false; ?>
                            <? } ?>
                            <div class="pz-more-bt" style="display:<?=$showMoreButton ? 'block' : 'none'?>"><?=$MUI->getText('button-more')?></div>
                            <div class="mr-cl-bt-bk">
                                <div class="cl scrollto" data-href="prizes"><?=$MUI->getText('button-cut')?></div>
                                <div class="mr"><?=$MUI->getText('button-more')?></div>
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
        <? if(in_array($seo['pages'],array('reviews','rules'))) :?>
            <section class="infos">
                <div class="i-lbk">
                    <section class="i-v-bk">
                        <? echo getBanners($banners['Video'],$player,$bannerScript); ?>
                    </section>
                    <section class="rules">
                        <div class="sbk-tl-bk">
                            <div class="sbk-tl"><?=$MUI->getText('title-faq')?></div>
                        </div>
                        <div class="rules-ct">
                            <div class="win-tbl">
                                <div class="c-l">
                                    <div class="wt-t">
                                        <?=$MUI->getText('text-faq-short')?>
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
                                            <div class="tb-t"><?=Common::viewNumberFormat($gameInfo['lotteryWins'][$i]['sum'])?> <?=($gameInfo['lotteryWins'][$i]['currency'] == LotterySettings::CURRENCY_POINT ? 'баллов' : $currency['iso'])?></div>
                                        </li>
                                    <? } ?>
                                </ul>
                                <div class="b-cl-block"></div>
                            </div>
                            <ul class="faq">
                                <?=$MUI->getText('text-faq')?>
                            </ul>
                            <div class="r-add-but more"><?=$MUI->getText('button-read')?></div>
                            <div class="r-add-but less scrollto" data-href="rules" style="display:none;"><?=$MUI->getText('button-hide')?></div>
                        </div>
                    </section>
                </div>

                <? /* <div class="i-rbk">
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
                </div> */ ?>

                <div class="i-rbk">
                    <section class="reviews">
                        <div class="sbk-tl-bk">
                            <div class="sbk-tl"><?=$MUI->getText('title-reviews')?></div>
                        </div>
                        <div class="rv-items">
                            <div class="h-ch">
                                <? /*
                                    while ($reviewData = array_pop($reviews))
                                    foreach ($reviewData as $reviewItem) { ?>
                                    <div data-id="<?=$reviewItem->getReviewId()?:$reviewItem->getId();?>" class="rv-item<?=($reviewItem->getReviewId()?' rv-answer':'');?>">
                                        <div class="rv-i-avtr">
                                            <? if ($reviewItem->getPlayerAvatar()) {?>
                                                <img src="/filestorage/avatars/<?=ceil($reviewItem->getPlayerId() / 100)?>/<?=$reviewItem->getPlayerAvatar()?>">
                                            <? } else { ?>
                                                <img src="/tpl/img/default.jpg">
                                            <? } ?>
                                        </div>
                                        <div class="rv-i-tl"><span class="rv-i-pl"><?=$reviewItem->getPlayerName()?></span> • <span class="rv-i-dt"><?=date('d.m.Y H:i', $reviewItem->getDate()+SettingsModel::instance()->getSettings('counters')->getValue('HOURS_ADD')*3600)?></span> <span class="rv-i-ans"> <?=$MUI->getText('button-answer')?></span></div>
                                        <div class="rv-i-txt"><?=$reviewItem->getText()?></div>
                                            <? if ($reviewItem->getImage()) {?>
                                            <div class="rv-i-img">
                                                <img src="/filestorage/reviews/<?=$reviewItem->getImage()?>">
                                            </div>
                                            <? }?>
                                    </div>
                                <? } */ ?>
                            </div>
                        </div>
                        <div class="rv-add-but"><?=$MUI->getText('button-read')?></div>
                        <div class="rv-mr-cl-bt-bk">
                            <div class="cl scrollto" data-href="reviews"><?=$MUI->getText('button-cut')?></div>
                            <div class="mr"><?=$MUI->getText('button-read')?></div>
                        </div>

                        <div class="rv-add-frm">
                            <div class="rv-image">
                                <img class="upload">
                            </div>
                            <div class="rv-sc"><?=$MUI->getText('message-review-approved')?></div>
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
                                    <?=$MUI->getText('button-send')?>
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
        <? if($seo['pages']=='profile' OR !$seo['pages']) :?>
            <section class="profile">
                <div class="p-bk">
                    <div class="p-tl-bk">
                        <div class="p-tl-nm"><?=$MUI->getText('title-profile')?></div>
                        <!--div class="p-exit-bt">выйти</div-->
                        <div class="p-tl-ml" id="profile_email"><?=$player->getEmail()?></div>
                    </div>
                    <div class="p-cnt">
                        <aside>
                            <ul>
                                <li class="ul_li now" data-link="profile-history"><?=$MUI->getText('menu-history')?></li>
                                <li class="ul_li" data-link="profile-bonuses"><?=$MUI->getText('menu-bonuses')?></li>
                                <li class="ul_li" data-link="profile-info"><?=$MUI->getText('menu-info')?></li>
                                <li class="ul_li" data-link="profile-notice"><?=$MUI->getText('menu-notices')?><span class='notice-unread' id="notice-unread"><?=$notices?></span></li>
                            </ul>


                            <div class="p-stat-bk">
                                <!--div class="gm-st"><b><?=$player->getGamesPlayed();?></b>игр сыграно</div-->
                                <div class="cr-st-bk">
                                    <div class="ifo"><b class="plPointHolder"><?=Common::viewNumberFormat($player->getPoints())?></b> <?=$MUI->getText('label-points-balance')?></div>
                                    <div class="bt" id="exchange" data-href="prizes"><?=$MUI->getText('button-change')?></div>
                                </div>

                                <div class="hand" id="cash-exchange"><img src="/tpl/img/but-exchange.png"></div>

                                <div class="cr-st-bk">
                                    <div class="ifo"><b class="plMoneyHolder"><?=Common::viewNumberFormat($player->getMoney())?></b><?=$currency['many']?> <?=$MUI->getText('label-cash-balance')?></div>
                                    <div class="bt" id="cash-output"><?=$MUI->getText('button-output')?></div>
                                </div>
                                <div class="st-hy-bt"><span><?=$MUI->getText('button-transactions')?></span></div>
                            </div>
                        </aside>

                        <div class="sp-cnt">

                            <section class="_section profile-history">
                                <ul class="ph-fr-bk">
                                    <li class="bt-om"><a href="javascript:void(0)"><?=$MUI->getText('button-only-me')?></a></li>
                                    <li class="bt-all sel"><a href="javascript:void(0)"><?=$MUI->getText('button-all')?></a></li>
                                </ul>
                                <div class="ht-tl-bk">
                                    <div class="dt-tl"><?=$MUI->getText('label-date-lottery')?></div>
                                    <div class="wc-tl"><?=$MUI->getText('label-win-combination')?></div>
                                    <div class="nw-tl"><?=$MUI->getText('label-total-winners')?></div>
                                </div>
                                <ul class="ht-bk">
                                    <? foreach ($lotteries as $lottery) { ?>
                                        <li data-lotid="<?=$lottery->getId()?>" class="lot-container <?=(isset($playerPlayedLotteries[$lottery->getId()]) ? "win" : "")?>">
                                            <div class="dt"><?=$lottery->getDate('d.m.Y')?></div>
                                            <ul class="ht-ct">
                                                <? foreach ($lottery->getCombination() as $num) { ?>
                                                    <li><?=$num?></li>
                                                <? } ?>
                                            </ul>
                                            <div class="nw"><?=($lottery->getWinnersCount()+($lottery->getId()>84?1750:($lottery->getId()>76?1000:0)))?></div>
                                            <? if ($lottery->getWinnersCount() > 0 || isset($playerPlayedLotteries[$lottery->getId()])) { ?>
                                                <div class="aw-bt" data-lotid="<?=$lottery->getId()?>">
                                                    <a href="javascript:void(0)"></a>
                                                </div>
                                            <? } ?>
                                        </li>
                                    <? } ?>
                                </ul>

                                <!-- КНОПКА ЗАГРУЗИТЬ ЕЩЕ -->
                                <div class="mr-bt"><?=$MUI->getText('button-more')?></div>

                                <!-- КНОПКИ СВЕРНУТЬ И ЗАГРУЗИТЬ ЕЩЕ-->
                                <div class="mr-cl-bt-bl">
                                    <div class="cl scrollto" data-href="profile"><?=$MUI->getText('button-hide')?></div>
                                    <div class="mr"><?=$MUI->getText('button-more')?></div>
                                </div>
                            </section>

                            <section class="_section profile-bonuses">
                                <div class="pb-txt"><?=$MUI->getText('text-profile-bonus')?></div>
                                <div class="if-bk">
                                    <div class="if-tl"><nobr><?=$MUI->getText('text-invite-friend', array($bonuses->getValue('bonus_email_invite'), $player->getInvitesCount()))?></nobr></div>
                                    <div class="fm-bk">
                                        <div class="inp-bk">
                                            <input type="email" name="email" autocomplete="off" spellcheck="false" placeholder="<?=$MUI->getText('placeholder-friend-email');?>" />
                                        </div>
                                        <div class="if-bt send-invite"><?=$MUI->getText('button-invite')?></div>
                                    </div>
                                </div>
                                <!--div class="sn-bt-bk">
                                    <div class="fb"><span>пригласить</span></div>
                                    <div class="vk"><span>пригласить</span></div>
                                    <div class="gp"><span>пригласить</span></div>
                                    <div class="tw"><span>пригласить</span></div>
                                </div-->
                                <div class="rp-bk">
                                    <div class="rp-txt"><?=$MUI->getText('text-post-referal', $bonuses->getValue('bonus_social_post'))?></div>
                                    <div class="rp-sl-bk">

                                    <div class="social-likes social-likes_vertical" data-counters="no" data-url="<?php echo 'http://lotzon.com/?ref='.$player->getId(); ?>" data-title="Играл и буду играть">
                                        <div href="javascript:void(0)" class="vk vk-share"></div>
                                        <div class="facebook" title="Поделиться ссылкой на Фейсбуке"></div>
                                        <div class="twitter" data-related="Играл и буду играть" title="Поделиться ссылкой в Твиттере"></div>
                                        <!--div class="vkontakte" title="Поделиться ссылкой во Вконтакте"></div!-->
                                        <div class="odnoklassniki" title="Поделиться ссылкой в Одноклассниках"></div>
                                        <!--div class="plusone" title="Поделиться ссылкой в Гугл-плюсе"></div-->

                                    </div>

                                    <script>
                                        $('.social-likes').on('popup_closed.social-likes', function(event, service) {
                                            socialRefPost(service);
                                        });
                                    </script>

                                    </div>
                                </div>
                                <div class="rp-bk ref">
                                    <div class="rp-txt"><?=$MUI->getText('text-register-by-link', $bonuses->getValue('bonus_referal_invite'));?></div>
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
                                            <div class="txt"><?=$MUI->getText('text-link-social', $bonuses->getValue('bonus_social_profile'))?></div>
                                            <? } ?>
                                            <? $socials=array('Facebook'=>'fb','Vkontakte'=>'vk', 'Odnoklassniki'=>'ok','Google'=>'gp','Twitter'=>'tw' );
                                            foreach($socials as $key=>$class)
                                                if(array_key_exists($key, $player->getAdditionalData()) && $player->getAdditionalData()[$key]['enabled'])
                                                    echo "<div data-provider='{$key}' class='cs-int-bt {$class} int'></div>";
                                                else
                                                    echo "<a href='./auth/{$key}?method=link'><div class='cs-int-bt {$class}'></div></a>";
                                            ?>
                                        </div>
                                    </div>
                                    <div class="pi-et-bk">
                                        <div class="pi-inp-bk">
                                            <div class="ph" data-default="<?=$MUI->getText('placeholder-nickname')?>"><?=$MUI->getText('placeholder-nickname')?></div>
                                            <input autocomplete="off" spellcheck="false" type="text" name="nick" data-valid="<?=($player->getNicName() ? $player->getNicName() : 'id' . $player->getId())?>" value="<?=($player->getNicName() ? $player->getNicName() : 'id' . $player->getId())?>" />
                                        </div>
                                        <div class="pi-inp-bk">
                                            <div class="ph" data-default="<?=$MUI->getText('placeholder-surname')?>"><?=$MUI->getText('placeholder-surname')?></div>
                                            <input autocomplete="off" spellcheck="false" type="text" name="surname" data-valid="<?=$player->getSurname()?>" value="<?=$player->getSurname()?>"/>
                                        </div>
                                        <div class="pi-inp-bk">
                                            <div class="ph" data-default="<?=$MUI->getText('placeholder-name')?>"><?=$MUI->getText('placeholder-name')?></div>
                                            <input autocomplete="off" spellcheck="false" type="text" name="name" data-valid="<?=$player->getName()?>" value="<?=$player->getName()?>"/>
                                        </div>
                                        <div class="pi-inp-bk td">
                                            <div class="ph" data-default="<?=$MUI->getText('placeholder-birthday')?>"><?=$MUI->getText('placeholder-birthday')?></div>
                                            <input autocomplete="off" spellcheck="false" maxlength="10" placeholder="<?=$MUI->getText('placeholder-birthday')?>" type="text" name="bd" <?=$player->getBirthday()?'disabled':''?>  data-valid="<?=($player->getBirthday() ? $player->getBirthday('d.m.Y') : '')?>" value="<?=($player->getBirthday() ? $player->getBirthday('d.m.Y') : '')?>"/>
                                        </div>
                                        <div class="pi-inp-bk td">
                                            <div class="ph" data-default="<?=$MUI->getText('placeholder-phone')?>"><?=$MUI->getText('placeholder-phone')?></div>
                                            <input autocomplete="off" spellcheck="false" placeholder="<?=$MUI->getText('placeholder-phone')?>" type="tel" name="phone" <?=$player->getPhone()?'disabled':''?> data-valid="<?=$player->getPhone()?>" value="<?=$player->getPhone()?>"/>
                                        </div>
                                        <div class="pi-inp-bk td">
                                            <div class="ph" data-default="<?=$MUI->getText('placeholder-qiwi')?>"><?=$MUI->getText('placeholder-qiwi')?></div>
                                            <input autocomplete="off" spellcheck="false" placeholder="<?=$MUI->getText('placeholder-qiwi')?>" type="tel" name="qiwi" <?=$player->getQiwi()?'disabled':''?> data-valid="<?=$player->getQiwi()?>" value="<?=$player->getQiwi()?>"/>
                                        </div>
                                        <div class="pi-inp-bk td">
                                            <div class="ph" data-default="<?=$MUI->getText('placeholder-webmoney')?>"><?=$MUI->getText('placeholder-webmoney')?></div>
                                            <input autocomplete="off" spellcheck="false" placeholder="<?=$MUI->getText('placeholder-webmoney')?>" type="text" name="webmoney" <?=$player->getWebMoney()?'disabled':''?> data-valid="<?=$player->getWebMoney()?>" value="<?=$player->getWebMoney()?>"/>
                                        </div>
                                        <div class="pi-inp-bk td">
                                            <div class="ph" data-default="<?=$MUI->getText('placeholder-yandexmoney')?>"><?=$MUI->getText('placeholder-yandexmoney')?></div>
                                            <input autocomplete="off" spellcheck="false" placeholder="<?=$MUI->getText('placeholder-yandexmoney')?>" maxlength="15" type="text" name="yandex" <?=$player->getYandexMoney()?'disabled':''?> data-valid="<?=$player->getYandexMoney()?>" value="<?=$player->getYandexMoney()?>"/>
                                        </div>
                                        <div class="pi-inp-bk td">
                                            <div class="ph" data-default="<?=$MUI->getText('placeholder-password')?>"><?=$MUI->getText('placeholder-password')?></div>
                                            <input type="text" name="plug" data-valid="" style="display: none;"/>
                                            <input type="password" name="plug" data-valid="" style="display: none;"/>
                                            <input autocomplete="off" spellcheck="false" placeholder="<?=$MUI->getText('placeholder-password')?>" type="password" value="*********" name="password" data-valid="" />
                                        </div>
                                        <div class="fc-bk">
                                            <div class="fc-nbs-bk">
                                                <ul>
                                                    <? for ($i = 1; $i <= 49; ++$i) { ?>
                                                        <li<?=(in_array($i, $player->getFavoriteCombination()) ? ' class="dis"' : '')?>><?=$i?></li>
                                                    <? } ?>
                                                </ul>
                                            </div>
                                            <div class="fc-tl"><?=$MUI->getText('label-favourite-combination')?></div>
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
                                            <label for="rulcheck"><?=$MUI->getText('label-show-my-name')?></label>
                                        </div>

                                        <? if(is_array($langs)){?>
                                            <div class="sb-ch-td">
                                                <label for="multilanguage"><?=$MUI->getText('label-multilanguage')?></label>
                                                <select id="multilanguage" class="multilanguage">
                                                <? foreach($langs as $lang){?>
                                                    <option <?=$lang->getCode()==$player->getLang()?'selected ':''?>value="<?=($lang->getCode())?>"><?=($lang->getTitle())?></option>
                                                <?}?>
                                                </select>
                                            </div>
                                        <?}?>
                                        <div class="sb-ch-td">
                                            <div class="but" onclick="$(this).parents('form').submit(); return false;"><?=$MUI->getText('button-save')?></div>
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
        <? if($seo['pages']=='chance' OR !$seo['pages']) :?>
        <section class="chance">
        <div class="ch-br-bk">

            <? echo getBanners($banners['Games'],$player,$bannerScript); ?>

        </div>
        <div class="ch-lot-bk">
        <div class="sbk-tl-bk">
        <div class="sbk-tl"><?=$MUI->getText('title-games')?></div>
        <div class="b-cntrl-block"><span class="icon-volume-2 audio" aria-hidden="true"></span></div>

        <!-- CHANCE PREVIEW -->
        <div class="ch-bk slider" style="display:block">
            <div class="ch-txt"><?=$MUI->getText('text-chance-game')?></div>

            <div class="ch-gm-tbl slide-list">
                <div class="slide-wrap">

                <? if(isset($games) && is_array($games))
                    foreach($games as $game): ?>
                        <div class="td slide-item">
                            <div class="gm-if-bk">
                                <div class="l"><?=$quickGames[$game]->getTitle($player->getLang())?></div>
                                <div class="r"><b class="qg-bk-pr"><?=$quickGames[$game]->getOption('p')?></b><?=$MUI->getText('label-points')?></div>
                            </div>
                            <div class="gm-bt" data-quick="1" data-game="<?=$game?>"><img src="tpl/img/games/Chance<?=$game?>.png"></div>
                        </div>
                    <? endforeach ?>

                </div>
                <div class="clear"></div>
                <div name="prev" class="navy prev-slide"></div>
                <div name="next" class="navy next-slide"></div>
            </div>

                <?
                $ogames = array();
                foreach($onlineGames as $onlineGame)
                    $ogames[$onlineGame->getId()]=$onlineGame;
                $ids = array_merge($gameSettings['OnlineGame']->getGames(), array_diff($gameSettings['OnlineGame']->getGames(),array_keys($ogames)) ); ?>


            <div class="ch-gm-tbl slide-list">
                <div class="slide-wrap">

                <? foreach($ids as $id):
                        $game = $ogames[$id];
                        unset ($ogames[$id]);
                        if($game && $game->isEnabled()):?>

                        <div class="td slide-item">
                            <div class="gm-if-bk">
                                <div class="l"><?=$game->getTitle($player->getLang());?></div>
                                <div class="r"></div>
                            </div>
                            <div class="ngm-bt" data-game="<?=$game->getKey();?>"><img src="tpl/img/games/<?=$game->getKey();?>.png"></div>
                        </div>

                <?      endif;
                    endforeach; ?>

                </div>

                <div class="clear"></div>
                <div name="prev" class="navy prev-slide"></div>
                <div name="next" class="navy next-slide"></div>

            </div>

            <script> $('.ch-gm-tbl.slide-list').each(function( index ) { if($('.slide-item',$(this)).length<=3) $('.navy',$(this)).hide(); })</script>
    </div>

    <!-- CHANCE GAME -->
        <div class="game-bk quickgame"  id="ChanceGame-holder" style="display:none">
            <div class="l-bk">
                <div class="rw-t">
                    <div class="bk-bt"><spn><?=$MUI->getText('button-back-to-games')?></spn></div>
                </div>
                <div class="gm-if-bk">
                    <div class="tb">
                        <!-- FIX HERE -->
                        <div class="l qg-bk-tl"></div>
                        <div class="r"><b></b><?=$MUI->getText('label-points')?></div>
                    </div>
                </div>
                <div style="display:none" id="game-rules">
                <? if(isset($games) && is_array($games))
                    foreach($games as $game): ?>
                    <div data-game="<?=$game?>">
                        <?=$quickGames[$game]->getDescription($player->getLang())?>
                    </div>
                    <? endforeach ?>
                </div>

                <div style="display:none" id="game-prizes">
                <? if(isset($games) && is_array($games))
                        foreach($games as $game): ?>
                            <div data-game="<?=$game?>">
                                <? foreach($quickGames[$game]->loadPrizes()->getPrizes() as $prize):
                                    if($prize['v'])
                                    switch ($prize['t']){
                                        case 'item': ?>
                                <div class="<?=$prize['t']?>-holder prize-holder"><img src="/filestorage/shop/<?=$prize['s']?>"></div>
                                <?          break;
                                        default: ?>
                                <div class="<?=$prize['t']?>-holder prize-holder"><span><?=
                                        ($prize['v']
                                            ? ($prize['t'] =='money' ? $prize['v'] * $currency['coefficient'] : str_replace(["[*]", "\/"], ["x", "÷"],$prize['v']))
                                            : 0).
                                        ($prize['t'] =='money'
                                            ? '<small> '.$currency['iso'].'</small>'
                                            : '');?></span></div>
                                <?          break;
                                    }
                                endforeach; ?>

                            </div>
                        <? endforeach; ?>
                </div>
                <div class="l-bk-txt qg-txt"></div>
                <div class="l-bk-txt qg-prz"></div>

            </div>
            <div class="gm-tb-bk quickgame">

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
                        <div class="bt"><?=$MUI->getText('button-get')?></div>
                    </div>
                </div>

                <!-- Кнопка "Играть" -->
                <div class="msg-tb play">
                    <div class="td">
                        <div class="bt"><?=$MUI->getText('button-play')?></div>
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

                <div class="qg-bk-pg">
                <div class="qg-msg">
                    <div class="td">
                        <div class="txt">Поздравляем, выигрыш зачислен на Ваш баланс</div>
                        <div class="bt">Играть еще раз за 30 баллов</div>
                        <div class="preloader"></div>
                    </div>
                </div>
                <div class="ul-hld"></div>

                <? if(isset($games) && is_array($games))
                    foreach($games as $game): ?>
                <!-- CHANCE<?=$game?> -->
                <ul class="gm-tb chance<?=$game?>" data-game="<?=$game?>" data-price="<?=$quickGames[$game]->getOption('p')?>" style="display:none">
<?                  for($y = 1; $y <=$quickGames[$game]->getOption('y'); ++$y) {
                        for($x = 1; $x <=$quickGames[$game]->getOption('x'); ++$x){ ?>
                    <li data-cell="<?=$x?>x<?=$y?>"></li>
<?                      }
                    } ?>
                </ul>
                <? endforeach ?>

            </div>
            </div>
        </div>


        <!-- NEW GAME CODE -->
        <div class="ngm-bk">
            <!-- правила -->
            <div class="ngm-rls">
                <div class="ngm-rls-bk">

                    <div class="rls-l">
                        <div class="rw-t">
                            <div class="bk-bt"><spn><?=$MUI->getText('button-back-to-games')?></spn></div>
                        </div>

                        <div class="gm-if-bk">
                            <div class="l"></div>
                        </div>
                        <div style="clear: both;"></div>

                        <div class="rls-bl">
                            <div class="rls-bt-bk">
                                <div id="newgame-fields" style="display:none">

                                    <div data-game="WhoMore">
                                        <ul class="mx WhoMore">
                                            <? for($i=1;$i<=$onlineGames['WhoMore']->getOption('y');$i++)
                                                for($j=1;$j<=$onlineGames['WhoMore']->getOption('x');$j++)
                                                    echo "<li data-cell='{$j}x{$i}'></li>";
                                            ?>
                                        </ul>
                                    </div>

                                    <div data-game="Mines">
                                        <ul class="mx Mines">
                                            <? for($i=1;$i<=$onlineGames['Mines']->getOption('y');$i++)
                                                for($j=1;$j<=$onlineGames['Mines']->getOption('x');$j++)
                                                    echo "<li data-cell='{$j}x{$i}'></li>";
                                            ?>
                                        </ul>
                                    </div>

                                    <div data-game="FiveLine">
                                        <ul class="mx FiveLine">
                                            <? for($i=1;$i<=$onlineGames['FiveLine']->getOption('y');$i++)
                                                for($j=1;$j<=$onlineGames['FiveLine']->getOption('x');$j++)
                                                    echo "<li data-cell='{$j}x{$i}'></li>";
                                            ?>
                                        </ul>
                                    </div>

                                    <div data-game="SeaBattle">
                                        <ul class="mx SeaBattle m">
                                            <? for($i=1;$i<=$onlineGames['SeaBattle']->getOption('y');$i++)
                                                for($j=1;$j<=$onlineGames['SeaBattle']->getOption('x');$j++)
                                                    echo "<li data-coor='{$j}x{$i}'></li>";
                                            ?>
                                        </ul>
                                        <div class="place">Расставьте корабли в необходимом порядке.<br><br>Что бы изменить ориентацию корабля, кликните по нему дважды.
                                            <div class="sb-random but">случайно</div>
                                            <div class="sb-ready but">готово</div>
                                            <div class="sb-wait">ожидаем соперника</div>
                                        </div>
                                        <ul class="mx SeaBattle o">
                                            <? for($i=1;$i<=$onlineGames['SeaBattle']->getOption('y');$i++)
                                                for($j=1;$j<=$onlineGames['SeaBattle']->getOption('x');$j++)
                                                    echo "<li data-coor='{$j}x{$i}'></li>";
                                            ?>
                                        </ul>
                                    </div>

                                    <div data-game="Durak">
                                        <div class="mx Durak">
                                            <div class="players"></div>
                                            <div class="deck"></div>
                                            <div class="table"></div>
                                            <div class="off"></div>
                                        </div>
                                    </div>

                                    <div data-game="DurakRevert">
                                        <div class="mx Durak">
                                            <div class="players"></div>
                                            <div class="deck"></div>
                                            <div class="table"></div>
                                            <div class="off"></div>
                                        </div>
                                    </div>

                                </div>

                                <div style="display:none" id="newgame-rules">
                                    <? foreach($onlineGames as $onlineGame):
                                        if($onlineGame->isEnabled()):?>
                                    <div data-game="<?=$onlineGame->getKey();?>">
                                        <?=$onlineGame->getDescription($player->getLang());?>
                                    </div>
                                    <?  endif;
                                    endforeach; ?>
                                </div>

                                <div class="prz-fnd">
                                    <div class="prz-fnd-ttl"><span class="icon-trophy" aria-hidden="true"></span> <?=$MUI->getText('label-prize-fund')?></div>
                                    <div><b class="prz-fnd-mon">...</b><i><?=$currency['many']?></i></div>
                                    <div><b class="prz-fnd-pnt">...</b><i><?=$MUI->getText('label-points')?></i></div>
                                </div>
                            </div>
                            <div class="rls-txt-bk">

                            </div>
                        </div>
                    </div>

                    <div class="rls-r">

                        <div class="rls-r-t">
                            <div class="rls-r-t-avatar" style="background-image: url(
                                <? if ($player->getAvatar()) {?>
                                    '/filestorage/avatars/<?=ceil($player->getId() / 100)?>/<?=$player->getAvatar()?>'
                                <? } else { ?>
                                    '/tpl/img/default.jpg'
                                <? } ?>
                                )">
                            </div>
                            <div class="rls-r-t-rating">
                                <div class="icon-star"></div>
                                <div class="rls-r-t-rating-points"></div>
                            </div>
                            <div class="rls-r-t-balance">
                                <div class="icon-wallet wallet"></div>
                                <div><span class="plMoneyHolder">...</span> <?=$currency['iso']?></div>
                                <div><span class="plPointHolder">...</span> <?=$MUI->getText('label-points')?></div>
                            </div>
                        </div>

                        <div class="rls-r-ts"><div class="rls-r-search"><div class="loader"></div><b><?=$MUI->getText('label-search')?></b></div><div class="ngm-cncl"><?=$MUI->getText('button-cancel')?></div></div>

                        <div class="rls-mn-bk">
                            <div class="cell">
                                <div class="bt ngm-games" data-block="now"><?=$MUI->getText('button-online-games')?></div>
                            </div>
                            <div class="cell">
                                <div class="bt ngm-create" data-block="new"><?=$MUI->getText('button-create-game')?></div>
                            </div>
                            <div class="cell">
                                <div class="bt ngm-rating" data-block="top"><?=$MUI->getText('button-rating')?></div>
                            </div>
                        </div>

                        <div class="blocks">
                        <div class="new-bl">
                            <div class="prc-but-cover"></div>
                            <div class="prc-but-bk">

                                <div class="prc-txt-bk">
                                    <span class="icon-wallet" aria-hidden="true"></span> <?=$MUI->getText('text-choose-bet')?>
                                </div>
                                <div class="prc-bt"><?=$MUI->getText('button-points')?></div>
                                <div class="prc-sel" data-currency="POINT">
                                    <div class="prc-tl"><?=$MUI->getText('button-points')?></div>
                                </div>

                                <div class="prc-bt"><?=$MUI->getText('button-money')?></div>
                                <div class="prc-sel" data-currency="MONEY">
                                    <div class="prc-tl"><?=$MUI->getText('button-money')?></div>
                                </div>

                                <div class="prc-bt"><?=$MUI->getText('button-free')?></div>
                                <div class="prc-sel" data-currency="FREE"><div data-price='POINT-0'><?=$MUI->getText('button-free')?></div></div>

                                <div class="plr-bt">
                                    <div class="prc-txt-bk">
                                        <span class="icon-equalizer" aria-hidden="true"></span> <?=$MUI->getText('text-choose-options')?>
                                    </div>

                                    <div class="plr-sel">
                                        <div class="plr-tl"><?=$MUI->getText('button-players')?></div>
                                    </div>
                                </div>

                                <!--div class="ngm-cncl">отмена</div-->
                                <div class="ngm-go"><?=$MUI->getText('button-create')?></div>
                            </div>
                        </div>

                        <div class="top-bl">
                            <!--div class="rls-r-ws">
                                <b><?=$MUI->getText('label-best-players')?></b>
                                <span><?=$MUI->getText('text-rating')?> <b>|</b> <?=$MUI->getText('label-games-played')?> <b>|</b> <?=$MUI->getText('label-wins')?></span>
                            </div-->
                            <div class="prc-txt-bk"><?=$MUI->getText('button-money')?></div>
                            <div class="prc-txt-bk"><?=$MUI->getText('button-points')?></div>
                            <ul class="rls-r-prs top-mon"></ul>
                            <ul class="rls-r-prs top-pnt"></ul>
                        </div>

                        <div class="now-bl">
                            <ul class="filter">
                                <li class="bt-all sel"><a href="javascript:void(0)"><?=$MUI->getText('button-all')?> <span></span></a></li>
                                <li class="bt-om"><a href="javascript:void(0)"><?=$MUI->getText('button-only-free')?> <span></span></a></li>
                            </ul>
                            <div class="gn-now-create"><?=$MUI->getText('text-no-online-create-own')?></div>
                            <div class="list-games"></div>
                        </div>

                    </div>
                    </div>

                    <div class="b-cl-block"></div>
                </div>
            </div>

            <div class="ngm-gm">
                <div class="tm" id="tm">00:55</div>

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
                    <div class="pr-pr">
                        <b>0</b>
                        <span>ваша<br/>ставка</span>
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
                                <div class="button re">Повторить</div>
                                <div class="button ch-ot">другой соперник</div>
                                <div class="button exit">Выйти</div>
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

        <!--=========================================================================
                                    NOTIFICATIONS POPUP CODE
        ==========================================================================-->
        <div class="notifications">
            <div class="badge parent">
                <div class="bl-pp_td">
                    <section class="badge-block pop-box">
                        <div class="cs"></div>
                        <div class="title"></div>
                        <div class="txt"></div>
                    </section>
                </div>
            </div>

            <div class="badge" id="qgame" style="/*display:none;*/">
                <div class="bl-pp_td">
                    <section class="badge-block pop-box">
                        <div class="cs"></div>
                        <div class="title"><?=$MUI->getText('title-random')?></div>
                        <div class="txt">
                            <div class="timer">
                                <span id="text_soon"><?=$MUI->getText('text-game-will-be-able-until')?> </span>
                                <span id="timer_soon"></span>
                            </div>
                            <div class="start"><?=$MUI->getText('button-play')?></div>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        </article>
        <!--=====================================================================
                                    FOOTER BLOCK
            ======================================================================-->
        <footer>

            <!--section class="fr-br-bk">
                <img src="/tpl/img/baners/goroskop.jpg?<?=(strtotime(date("md")))?>" width="1280" height="257">
            </section-->
            <div class="fr-cnt-bk">
                <a href="javascript:void(0)" class="ts-lk" id="terms-bt"><?=$MUI->getText('text-terms')?></a>
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


        </div>
        <script src="/tpl/js/backend.min.js"></script>
        <script src="/tpl/js/template.js"></script>
        <script src="/tpl/js/main.min.js"></script>
        <script src="/tpl/js/ws.js"></script>
        <script src="/tpl/js/ads.js"></script>

    <? include('popups.php'); ?>
    <? if($debug) include('./protected/templates/admin/statictexts_frontend.php'); ?>

    <? echo getScripts($banners['BodyScripts'],$player); ?>

    <script>

        if(window.VK)
            VK.init({
            apiId: 4617228,
            scope: 'wall,photos'
        });

        filledTicketsCount = <?=($filledTicketsCount?:0);?>;
        var playerFavorite = [<?=implode(',',$player->getFavoriteCombination());?>];
        var playerPoints   = <?=$player->getPoints()?>;
        var playerMoney   = <?=$player->getMoney()?>;
        var currency =  <?=json_encode($currency); ?>;
        var playerId   = <?=$player->getId()?>;
        var ws = 0;
        var one = false;
        var texts = {
            'CHEAT_GAME'        : 'Игра не может запускаться с нескольких открытых вкладок, пожалуйста, закройте лишние вкладки c игрой',
            'TIME_NOT_YET'      : 'Время игры еще не настало!',
            'GAME_NOT_ENABLED'  : 'Игра не доступна',
            'GAME_NOT_FOUND'    : 'Игра не найдена',
            'INSUFFICIENT_FUNDS': 'На балансе недостаточно средств',
            'NICKNAME_BUSY'     : 'Ник уже занят',
            'INVALID_PHONE_FORMAT'  : 'Неверный формат',
            'INVALID_DATE_FORMAT'   : 'Неверный формат даты',
            'MONEY_ORDER_COMPLETE'  : 'Денежные средства списаны и поступят на Ваш счет в течение 7 рабочих дней.',
            'NOT_YOUR_MOVE'     : 'Сейчас не Ваша очередь ходить',
            'APPLICATION_DOESNT_EXISTS' : 'Потеря связи со стороны сервера, средства с баланса не списаны',
            'CELL_IS_PLAYED'    : 'Ячейка уже сыграла',
            'ENOUGH_MOVES'      : 'У Вас закончились ходы',
            'SHIP_TOO_CLOSE'    : 'Корабли расположены слишком близко',
            'ERROR_COORDINATES' : 'Неверные координаты',
            'CHOICE_BET'                : 'Выберите ставку',
            'PLAY_ONE_MORE_TIME'        : 'Играть еще раз за {0} баллов',
            'button-answer'             : '<?=$MUI->getText('button-answer')?>',
            'message-review-approved'   : '<?=$MUI->getText('message-review-approved')?>',
        };

        var quickGame   = {};
        var onlineGame  = {};
        var online      = 1;
        var page        = <?=($seo['pages']?1:0)?>;
        var appId       = 0;
        var appName     = '';
        var appMode     = 0;
        <? /* foreach ($onlineGames as $game){
    if(is_array($game->getModes()))
        foreach ($game->getModes() as $cur=>$m)
            foreach ($m as $v=>$p)
                $modes[$game->getKey()][$cur][] = $v;

    if(is_array($game->getAudio()))
        foreach ($game->getAudio() as $k=>$f)
            if($f)
                $audio[$game->getKey()][$k] = $f;
        <?json_encode($modes, JSON_PRETTY_PRINT); ?>;
        <?json_encode($audio, JSON_PRETTY_PRINT); ?>;
    } */ ?>
        var appModes = {}
        var appAudio = {}
        var unreadNotices = <?=$notices?>;
        var bannerTicketLastNum = (5-Math.ceil(Math.random() * (5-<?=($filledTicketsCount?:1);?>)));
        var url = 'ws://<?=$_SERVER['SERVER_NAME'];?>:<?=\Config::instance()->wsPort?>';

        updateNotices(unreadNotices);
        getTpl.init(<?=json_encode($templates)?>);

        <? if($chanceGame):?>
        $('.gm-bt[data-game="<?=$chanceGame;?>"]').trigger('click');
        $('.game-bk .play .bt').trigger('click');
        window.setTimeout(function(){
            $("html, body").animate({scrollTop: $('.chance').offset().top-60}, 500, 'easeInOutQuint');
            $('.ngm-bk .msg').hide();
        }, 300);
        <? endif;?>

        <? if($quickGame['current']) : ?>
        $('#qgame .start').click();
        <? endif; ?>
        $('#qgame').hide();
        setTimeout(function(){$('#qgame').fadeIn(200)},1200);
        $("#timer_soon").countdown({until: (<?=($quickGame['timer']>0?$quickGame['timer']:1);?>) ,layout: "{mnn}:{snn}",
            onExpiry: showQuickGameStart
        });

        $("#timer_soon").countdown('resume');
        $("#timer_soon").countdown('option', {until: (<?=($quickGame['timer']>0?$quickGame['timer']:1);?>)});

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

        <? if(!$metrikaDisabled) :?>
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
    <script type="text/javascript" src="//yastatic.net/share/share.js" charset="utf-8"></script>
    <?=$bannerScript;?>
    </body>

</html>