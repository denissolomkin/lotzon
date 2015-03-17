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
        <!--meta property="og:url" content="http://www.lotzon.com/" /-->
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
        <style>
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
                width:  <?=(480-($onlineGames['Mines']->getOption('x')*1)) / $onlineGames['Mines']->getOption('x')?>px;
                height: <?=(480-($onlineGames['Mines']->getOption('y')*1)) / $onlineGames['Mines']->getOption('y')?>px;
                margin:0 1px 1px 0;float:left;cursor:pointer;text-align:center;color:#4c4c4c;letter-spacing:-2px;
            }

            .ngm-bk .ngm-gm .gm-mx ul.Mines > li img {margin: 10%;width: 80%;height: 80%;}
            /*.ngm-bk .ngm-gm .gm-mx ul.Mines > li.m {background:url("tpl/img/games/bomb.png") #d8e7ea no-repeat 0 0/ 100% 100%;}*/

            ul.SeaBattle.mx > li {
                width:  <?=(220-($onlineGames['SeaBattle']->getOption('x')*1)) / $onlineGames['SeaBattle']->getOption('x')?>px;
                height: <?=(440-($onlineGames['SeaBattle']->getOption('y')*1)) / $onlineGames['SeaBattle']->getOption('y')?>px;
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
                (490-($onlineGames['FiveLine']->getOption('y')*1)) / $onlineGames['FiveLine']->getOption('y')); ?>px;
                margin: <?=(480-($onlineGames['FiveLine']->getOption('y')*1)) / $onlineGames['FiveLine']->getOption('y') * 0.1?>px <?=(480-(($onlineGames['FiveLine']->getOption('x')-1)*1)) / $onlineGames['FiveLine']->getOption('x') * 0.1?>px;
                width: <?=(480-(($onlineGames['FiveLine']->getOption('x')-1)*1)) / $onlineGames['FiveLine']->getOption('x') * 0.8?>px;
                height: <?=(480-($onlineGames['FiveLine']->getOption('y')*1)) / $onlineGames['FiveLine']->getOption('y') * 0.8?>px;
            }
            .ngm-bk .ngm-gm .gm-mx ul.FiveLine > li {
                background-color: #d8e7e9;
                font: <?=(480-($onlineGames['FiveLine']->getOption('y')*1)) / 1.6 / $onlineGames['FiveLine']->getOption('y')?>px/<?=(480-($onlineGames['FiveLine']->getOption('y')*1)) / $onlineGames['FiveLine']->getOption('y')?>px Handbook-bold;
                width: <?=(480-(($onlineGames['FiveLine']->getOption('x')-1)*1)) / $onlineGames['FiveLine']->getOption('x')?>px;
                height: <?=(480-($onlineGames['FiveLine']->getOption('y')*1)) / $onlineGames['FiveLine']->getOption('y')?>px;
                margin:0 1px 1px 0;float:left;cursor:pointer;text-align:center;color:#4c4c4c;letter-spacing:-2px;
            }
        </style>

    </head>
    <body>
    <?php $antiblock_layer_id = chr(98 + mt_rand(0,24)) . substr(md5(time()), 0, 3); $antiblock_html_elements = array (  0 => 'div',  1 => 'span',  2 => 'b',  3 => 'i',  4 => 'font',  5 => 'strong',  6 => 'center',); $antiblock_html_element = $antiblock_html_elements[array_rand($antiblock_html_elements)]; ?>
    <style>#<?php echo $antiblock_layer_id; ?>{z-index: 10000;position:fixed !important;position:absolute;top:<?php echo mt_rand(-3, 3); ?>px;top:expression((t=document.documentElement.scrollTop?document.documentElement.scrollTop:document.body.scrollTop)+"px");left:<?php echo mt_rand(-3, 3); ?>px;width:<?php echo mt_rand(98, 103); ?>%;height:<?php echo mt_rand(98, 103); ?>%;background: rgba(0,0,0,0.85);display:block;padding:5% 0}#<?php echo $antiblock_layer_id; ?> *{text-align:center;margin:0 auto;display:block;filter:none;font:15px/50px Handbook-bold;text-decoration:none}#<?php echo $antiblock_layer_id; ?> ~ *{/*display:none*/}#<?php echo $antiblock_layer_id; ?> div{margin-top: -100px;} #<?php echo $antiblock_layer_id; ?> div a[href]{display: inline-block;text-transform: uppercase;width:220px;height:50px; }#<?php echo $antiblock_layer_id; ?> div a.please {background-color:#ffe400;color:#000;cursor:pointer;}#<?php echo $antiblock_layer_id; ?> div a.please:hover {background-color:#000!important;color:#fff;}#<?php echo $antiblock_layer_id; ?> > :first-child{background-color: white;height: 500px;width: 540px;}</style>
    <div id="<?php echo $antiblock_layer_id; ?>"><<?php echo $antiblock_html_element; ?>>Пожалуйста, включите Javascript!</<?php echo $antiblock_html_element; ?>></div>
    <script>window.document.getElementById("<?php echo $antiblock_layer_id; ?>").parentNode.removeChild(window.document.getElementById("<?php echo $antiblock_layer_id; ?>"));(function(l,m){function n(a){a&&<?php echo $antiblock_layer_id; ?>.nextFunction()}var h=l.document,p=["i","s","u"];n.prototype={rand:function(a){return Math.floor(Math.random()*a)},getElementBy:function(a,b){return a?h.getElementById(a):h.getElementsByTagName(b)},getStyle:function(a){var b=h.defaultView;return b&&b.getComputedStyle?b.getComputedStyle(a,null):a.currentStyle},deferExecution:function(a){setTimeout(a,2E3)},insert:function(a,b){var e=h.createElement("<?php echo $antiblock_html_element; ?>"),d=h.body,c=d.childNodes.length,g=d.style,f=0,k=0;if("<?php echo $antiblock_layer_id; ?>"==b){e.setAttribute("id",b);g.margin=g.padding=0;g.height="100%";for(c=this.rand(c);f<c;f++)1==d.childNodes[f].nodeType&&(k=Math.max(k,parseFloat(this.getStyle(d.childNodes[f]).zIndex)||0));k&&(e.style.zIndex=k+1);c++}e.innerHTML=a;d.insertBefore(e,d.childNodes[c-1])},displayMessage:function(a){var b=this;a="abisuq".charAt(b.rand(5));b.insert("<"+a+'><img src=tpl/img/please.jpg><div><a href="/" class="bt please">Обновить страницу</a><a href="http://ru.wikihow.com/%D0%BE%D1%82%D0%BA%D0%BB%D1%8E%D1%87%D0%B8%D1%82%D1%8C-Adblock" target="_blank">Как отключить AdBlock</a><div>'+("</"+a+">"),"<?php echo $antiblock_layer_id; ?>");h.addEventListener&&b.deferExecution(function(){b.getElementBy("<?php echo $antiblock_layer_id; ?>").addEventListener("DOMNodeRemoved",function(){b.displayMessage()},!1)})},i:function(){for(var a="<?php echo implode(",", array_merge(array_rand(array_flip(array('AdMiddle','adsense-new','adsense1','mainAdUnit','ad','adsense','AD_gallery','Ad3Right','Ad3TextAd','Ad728x90','AdBar','AdPopUp','AdRectangle','AdSenseDiv','AdServer','AdSquare02','Ad_Block','Ad_Right1','Ad_Top','Adbanner','Ads_BA_SKY','AdvHeader','AdvertPanel','BigBoxAd','BodyAd','GoogleAd1','GoogleAd3','HEADERAD','HomeAd1','Home_AdSpan','JuxtapozAds','LeftAdF1','LeftAdF2','LftAd','MPUAdSpace','OpenXAds','RgtAd1','SkyAd','SpecialAds','SponsoredAd','TopAdBox','ad-300x250','ad-300x60-1','ad-728','ad-ads','ad-banner','ad-banner-1','ad-box2','ad-boxes','ad-bs','ad-campaign','ad-center','ad-halfpage','ad-lrec','ad-mpu','ad-mpu2','ad-north','ad-one','ad-row','ad-section','ad-side','ad-sidebar','ad-sky','ad-sky-btf','ad-space-2','ad-splash','ad-squares','ad-top-wrap','ad-two','ad-typ1','ad-wrapper1','ad-zone-2','ad02','ad125BL','ad125TR','ad125x125','ad160x600','ad1Sp','ad300_x_250','ad300b','ad300x600','ad336','ad728Top','ad728x90_1','adBadges','adBanner10','adBanner9','adBannerTop','adBlocks','adBox16','adBox350','adCol','adColumn3','adFiller','adLB','adLabel','adLink300','adMPU','adMedRect','adMeld','adMpuBottom','adPlacer','adPosOne','adRight3','adSidebar','adSidebarSq','adSlot01','adSlot2','adSpace','adSpace0','adSpace1','adSpace11','adSpace13','adSpace16','adSpace2','adSpace21','adSpace23','adSpace25','adSpace5','adSquare','adStaticA','adStrip','adSuperAd','adTag1','adTile','adTop','adTop2','adTower1','adUnit','adValue','adZoneTop','ad_300','ad_300_250','ad_300c','ad_300x250','ad_300x90','ad_500','ad_940','ad_984','ad_A','ad_C','ad_G','ad_H','ad_I','ad_K','ad_O','ad_block_1','ad_block_2','ad_bottom','ad_box','ad_branding','ad_bs_area','ad_buttons','ad_feature','ad_h3','ad_img','ad_in_arti','ad_label','ad_lastpost','ad_layer2','ad_left','ad_lnk','ad_message','ad_mpuav','ad_place','ad_play_300','ad_post','ad_post_300','ad_promoAd','ad_rect','ad_rect2','ad_sec_div','ad_sgd','ad_sidebar','ad_sidebar1','ad_sidebar2','ad_sky','ad_ss','ad_wide_box','adbForum','adbig','adblade_ad','adbnr','adbox1','adbutton','adcell','adclose','adcode2','adcode4','adhead_g','adheader','adhomepage','adl_250x250','adl_300x100','adl_300x250','adlabel','adlayerad','adlrec','adposition1','adposition2','adposition4','adright2','adrighthome','ads-468','ads-block','ads-dell','ads-header','ads-king','ads-rhs','ads-vers7','ads125','ads160left','ads300','ads300x250','ads315','adsDiv5','ads_01','ads_300','ads_banner','ads_button','ads_catDiv','ads_center','ads_header','ads_lb','ads_space','ads_text','ads_top','ads_wrapper','ads_zone27','adsense-top','adsense05','adsense728','adsenseWrap','adsense_box','adserv','adshowtop','adskinright','adsleft1','adspaceBox','adspot-2','adspot-a','adtab','adtag5','adtag8','adtagfooter','adtech_0','adtech_1','adtech_2','adtopHeader','adtophp','adv-300','adv-left','adv-middle','adv-preroll','adv-x36','adv300top','advWrapper','adv_728','adv_mpu1','adver3','adver4','adver6','advert-1','advert-text','advert-top','advert_1','advertbox2','advertbox3','advertise1','advertisers','advertorial','advheader','advtext','adwin_rec','adwith','adxBigAd','adxSponLink','adxtop2','adzerk2','anchorAd','ap_adframe','apolload','area1ads','article-ad','asideads','babAdTop','backad','bannerAdTop','bbccom_mpu','bigadbox','bigadframe','bigadspot','blog-ad','body_728_ad','botad','bottomAd','bottom_ad','box1ad','boxAd','boxad','boxad2','boxad4','boxtube-ad','bpAd','browsead','btnAds','buttonad','c_ad_sky','catad','centerads','cmn_ad_box','cnnTopAd','cnnVPAd','colRightAd','companionad','content_ad','contentads','contextad','coverads','ctl00_topAd','ctr-ad','dAdverts','divFooterAd','divLeftAd12','divadfloat','dlads','dp_ads1','ds-mpu','dvAd2Center','editorsmpu','elite-ads','flAdData6','floatingAd','floatingAds','footad','footerAdDiv','four_ads','ft-ad','ft-ad-1','ft-ads','g_ad','g_adsense','gallery-ad','google-ads','googleAd','googleAds','grid_ad','gtopadvts','halfPageAd','hd-ads','hdr-ad','head-ads','headAd','head_advert','header_ads','headerads','headline_ad','hiddenadAC','hideads','homeMPU','houseAd','hp-mpu','iframeTopAd','inadspace','iqadtile9','js_adsense','layerad','leaderad','left-ad-1','left-ad-2','left-ad-col','leftAds','live-ad','lower_ad','lowerads','main-advert','main-tj-ad','mastAdvert','medRecAd','medrectad','menu-ads','midadvert','middle_mpu','midrect_ad','midstrip_ad','monsterAd','mpu-cont','mpuAd','mpu_banner','mpu_holder','multi_ad','name-advert','nba300Ad','nbaVid300Ad','ng_rtcol_ad','northad','ns_ad1','oanda_ads','onespot-ads','ovadsense','page-top-ad','pageAds','pageAdvert','pinball_ad','player_ad','player_ads','post_ad','print_ads','qm-dvdad','rail_ad','rail_ad2','rectangleAd','rhapsodyAd','rhsadvert','right-ad1','right-ads-3','rightAd_rdr','rightAdsDiv','rightColAd','right_ad','rightad','rightads','rightinfoad','rtmod_ad','sAdsBox','sb_ad_links','sb_advert','search_ad','sec_adspace','sew-ad1','shortads','show-ad','showAd','side-ad','sideBarAd','side_ad','sidead','sideadzone','sidebar-ad','sidebarAd','single-ad-2','single-mpu','singlead','site_top_ad','sitead','sky_advert','smallerAd','some-ads','speeds_ads','spl_ad','sponlink','sponsAds','sponsored1','spotlightad','squareAd','square_ad','story-ad-a','story-ad-b','storyAd','storyAdWrap','swfAd5','synch-ad','tblAd','tcwAd','text-ads','textAds','text_ads','thefooterad','tileAds','top-ads','top728ad','topAdsG','top_ad','top_ad_area','top_ad_zone','top_mpu','topad2','topad_left','topad_right','topadbar','topaddwide','topadsense','topadwrap','topadzone','topbannerad','topcustomad','toprightad','toptextad','tour728Ad','twogamesAd','vertical_ad','view_ads','wall_advert','wrapAdTop','y-ad-units','y708-ad-ysm','yahoo-ads','yahooad-tbl','yatadsky','tads.c')), 7), array("ad", "ads", "adsense")));?>".split(","),b=a.length,e="",d=this,c=0,g="abisuq".charAt(d.rand(5));c<b;c++)d.getElementBy(a[c])||(e+="<"+g+' id="'+a[c]+'"></'+g+">");d.insert(e);d.deferExecution(function(){for(c=0;c<b;c++)if(null==d.getElementBy(a[c]).offsetParent||"none"==d.getStyle(d.getElementBy(a[c])).display)return d.displayMessage("#"+a[c]+"("+c+")");d.nextFunction()})},s:function(){var a={'pagead2.googlesyndic':'google_ad_client','js.adscale.de/getads':'adscale_slot_id','get.mirando.de/miran':'adPlaceId'},b=this,e=b.getElementBy(0,"script"),d=e.length-1,c,g,f,k;h.write=null;for(h.writeln=null;0<=d;--d)if(c=e[d].src.substr(7,20),a[c]!==m){f=h.createElement("script");f.type="text/javascript";f.src=e[d].src;g=a[c];l[g]=m;f.onload=f.onreadystatechange=function(){k=this;l[g]!==m||k.readyState&&"loaded"!==k.readyState&&"complete"!==k.readyState||(l[g]=f.onload=f.onreadystatechange=null,e[0].parentNode.removeChild(f))};e[0].parentNode.insertBefore(f,e[0]);b.deferExecution(function(){if(l[g]===m)return b.displayMessage(f.src);b.nextFunction()});return}b.nextFunction()},u:function(){var a="/adcreative.,/adify_,/ads2.,/ads_sidebar.,/boomad.,/js2.ad/size=,/plugins_ads_,ad=dartad_,_centre_ad.,/468x60v1_".split(","),b=this,e=b.getElementBy(0,"img"),d,c;e[0]!==m&&e[0].src!==m&&(d=new Image,d.onload=function(){c=this;c.onload=null;c.onerror=function(){p=null;b.displayMessage(c.src)};c.src=e[0].src+"#"+a.join("")},d.src=e[0].src);b.deferExecution(function(){b.nextFunction()})},nextFunction:function(){var a=p[0];a!==m&&(p.shift(),this[a]())}};l.<?php echo $antiblock_layer_id; ?>=<?php echo $antiblock_layer_id; ?>=new n;h.addEventListener?l.addEventListener("load",n,!1):l.attachEvent("onload",n)})(window);</script>
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
                                            <div class="tb-t"><?=Common::viewNumberFormat($gameInfo['lotteryWins'][$i]['sum'])?> <?=($gameInfo['lotteryWins'][$i]['currency'] == LotterySettings::CURRENCY_POINT ? 'баллов' : $currency)?></div>
                                        </li>
                                    <? } ?>
                                </ul>
                                <div class="b-cl-block"></div>
                            </div>
                            <ul class="faq">
                                <?=$staticTexts['main-faq'][$lang]->getText()?>
                            </ul>
                            <div class="r-add-but more">ЧИТАТЬ ДАЛЬШЕ</div>
                            <div class="r-add-but less scrollto" data-href="rules" style="display:none;">спрятать</div>
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
                                        <!--a href="javascript:void(0)"
                                            onclick="
                                                window.open(
                                                'http://twitter.com/share?url=<?php echo 'http://lotzon.com/?ref='.$player->getId(); ?>',
                                                'twitter-share-dialog',
                                                'width=500,height=436');
                                                return false;"
                                             class="tw"></a-->
                                        <!--a href="javascript:void(0)"
                                            onclick="
                                                window.open(
                                                'https://plus.google.com/share?url=<?php echo 'http://lotzon.com/?ref='.$player->getId(); ?>',
                                                'googleplus-share-dialog',
                                                'width=500,height=436');
                                                return false;"
                                                class="gp"></a-->
                                        <!--div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="twitter,gplus"></div-->
                                        <!--a  href="javascript:void(0)"
                                            onclick="
                                                window.open(
                                                'https://www.facebook.com/sharer/sharer.php?u=<?php echo 'http://lotzon.com/?ref='.$player->getId(); ?>',
                                                'facebook-share-dialog',
                                                'width=626,height=436');
                                                return false;"
                                            class="fb fb-share">
                                        </a-->
                                        <a href="javascript:void(0)" class="vk vk-share"></a>
                                    </div>
                                </div>
                                <div class="rp-bk ref">
                                    <div class="rp-txt">Регистрация по вашей ссылке +<?=Player::REFERAL_INVITE_COST?> баллов<br><span style="font-size: 15px;"><span style="color:red">Внимание!</span> Приглашать участников через CAP системы (буксы) категорически запрещено! Баллы за такие приглашения не начисляются.</span></div>
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
                                            <? $socials=array('Facebook'=>'fb','Vkontakte'=>'vk', 'Odnoklassniki'=>'ok','Google'=>'gp','Twitter'=>'tw' );
                                            foreach($socials as $key=>$class)
                                                if(array_key_exists($key, $player->getAdditionalData()) && $player->getAdditionalData()[$key]['enabled'])
                                                    echo "<div data-provider='{$key}' class='cs-int-bt {$class} int'></div>";
                                                else
                                                    echo "<a href=''./auth/{$key}?method=link'><div class='cs-int-bt {$class}'></div></a>";
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
                        <div class="l"><?=$onlineGames['WhoMore']->getTitle($player->getLang());?></div>
                        <div class="r"></div>
                    </div>
                    <div class="ngm-bt" data-game="WhoMore"><img src="tpl/img/games/WhoMore.png"></div>
                </div>
                <div class="td c">
                    <div class="gm-if-bk">
                        <div class="l"><?=$onlineGames['SeaBattle']->getTitle($player->getLang());?></div>
                        <div class="r"></div>
                    </div>
                    <div class="ngm-bt" data-game="SeaBattle"><img src="tpl/img/games/SeaBattle.png"></div>
                </div>
                <div class="td r">

                    <!--div class="gm-if-bk">
                        <div class="l">Скоро</div>
                        <div class="r"></div>
                    </div>
                    <img src="tpl/img/game-dots.png"-->

                <div class="gm-if-bk">
                    <div class="l"><?=$onlineGames['FiveLine']->getTitle($player->getLang());?></div>
                    <div class="r"></div>
                </div>
                <div class="ngm-bt" data-game="FiveLine"><img src="tpl/img/games/FiveLine.png"></div>
            </div>
        </div>
            <? /*
            <div class="ch-gm-tbl">
                <div class="td l">
                    <div class="gm-if-bk">
                        <div class="l"><?=$onlineGames['Mines']->getTitle($player->getLang());?></div>
                        <div class="r"></div>
                    </div>
                    <div class="ngm-bt" data-game="Mines"><img src="tpl/img/games/Mines.png"></div>
                </div>
            </div>
            */ ?>
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
                                <div class="all">Выберите тип и размер ставки</div>
                            </div>
                            <div class="prc-but-cover"></div>
                            <div class="prc-but-bk">

                                <div class="prc-bt">баллы</div>
                                <div class="prc-sel" data-currency="POINT">
                                    <div class="prc-tl">баллы</div>
                                </div>

                                <div class="prc-bt">деньги</div>
                                <div class="prc-sel" data-currency="MONEY">
                                    <div class="prc-tl">деньги</div>
                                </div>

                                <div class="prc-bt">бесплатно</div>
                                <div class="prc-sel" data-currency="FREE"><div data-price='POINT-0'>бесплатно</div></div>

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
                                </div>

                                <div style="display:none" id="newgame-rules">
                                    <div data-game="WhoMore">
                                        Выберите ставку и нажмите кнопку «Играть»<br/><br/>
                                        <?=$onlineGames['WhoMore']->getDescription($player->getLang());?>
                                    </div>
                                    <div data-game="FiveLine">
                                        Выберите ставку и нажмите кнопку «Играть»<br/><br/>
                                        <?=$onlineGames['FiveLine']->getDescription($player->getLang());?>
                                    </div>
                                    <div data-game="SeaBattle">
                                        Выберите ставку и нажмите кнопку «Играть»<br/><br/>
                                        <?=$onlineGames['SeaBattle']->getDescription($player->getLang());?>
                                    </div>
                                    <div data-game="Mines">
                                        Выберите ставку и нажмите кнопку «Играть»<br/><br/>
                                        <?=$onlineGames['SeaBattle']->getDescription($player->getLang());?>
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

                        <div class="rls-r-t">ВЫ <b>|</b> 0 <b>|</b> 0</div>
                        <div class="rls-r-ts"><div class="rls-r-search"><div class="loader"></div><b>Поиск</b></div><div class="ngm-cncl">отмена</div></div>


                        <div class="rls-r-ws">
                            <b>победители</b>
                            <span>рейтиHг <b>|</b> сыграHо игр <b>|</b> побед</span>
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
                        <div class="title"><?=$quickGame['title'];?></div>
                        <div class="txt">
                            <div class="timer">
                                <span id="text_soon">Игра будет доступна через </span>
                                <span id="timer_soon"></span>
                            </div>
                            <div class="start">Играть!</div>
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
            <img id="ad" src="/tpl/img/baners/ad.gif" />
            <section class="fr-br-bk">
                <img src="/tpl/img/baners/goroskop.jpg?<?=(strtotime(date("md")))?>" width="1280" height="257">
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
        </div>
        <script src="/tpl/js/lib/jquery.damnUploader.min.js"></script>
        <script src="/tpl/js/backend.js"></script>
        <script src="/tpl/js/main.js"></script>
        <script src="/tpl/js/ws.js"></script>
        <script src="/tpl/js/ads.js"></script>

        <? include('popups.php') ?>

    <? /*echo $_SERVER['HTTP_USER_AGENT'];
    $browser = get_browser(null, true);
    print_r($browser);*/
    echo getScripts($banners['BodyScripts'],$player); ?>

    <script>

        VK.init({
            apiId: 4617228,
            scope: 'wall,photos'
        });

        filledTicketsCount = <?=($filledTicketsCount?:0);?>;
        var playerFavorite = [<?=implode(',',$player->getFavoriteCombination());?>];
        var playerPoints   = <?=$player->getPoints()?>;
        var playerMoney   = <?=$player->getMoney()?>;
        var playerCurrency = '<?=$player->getCountry() == 'UA' ? 'гривен' : 'рублей'?>';
        var playerCurrencyISO = '<?=$currency;?>';
        var playerId   = <?=$player->getId()?>;
        var coefficient   = <?=$gameInfo['coefficient']?>;
        var ws = 0;
        var texts = {
            'TIME_NOT_YET'      : 'Время игры еще не настало!',
            'GAME_NOT_ENABLED'  : 'Игра не доступна',
            'GAME_NOT_FOUND'    : 'Игра не найдена',
            'INSUFFICIENT_FUNDS'    : 'На балансе недостаточно средств',
            'NICKNAME_BUSY'     : 'Ник уже занят',
            'INVALID_PHONE_FORMAT'     : 'Неверный формат',
            'INVALID_DATE_FORMAT'     : 'Неверный формат даты',
            'MONEY_ORDER_COMPLETE'  : 'Денежные средства списаны и поступят на Ваш счет в течение 7 рабочих дней.',
            'NOT_YOUR_MOVE' : 'Сейчас не Ваша очередь ходить',
            'APPLICATION_DOESNT_EXISTS' : 'Потеря связи со стороны сервера, средства с баланса не списаны',
            'CELL_IS_PLAYED' : 'Ячейка уже сыграла',
            'ENOUGH_MOVES' : 'У Вас закончились ходы',
            'SHIP_TOO_CLOSE' : 'Корабли расположены слишком близко',
            'ERROR_COORDINATES' : 'Неверные координаты',
            'CHOICE_BET' : 'Выберите ставку',
        };
        var quickGame = {};
        var online = 1;
        var page = <?=($page?1:0)?>;
        var appId   = 0;
        var appName   = '';
        var appMode   = 0;
        <? foreach ($onlineGames as $game){
            if(is_array($game->getModes()))
                foreach ($game->getModes() as $cur=>$m)
                    foreach ($m as $v=>$p)
                        $modes[$game->getKey()][$cur][] = $v;

            if(is_array($game->getAudio()))
                foreach ($game->getAudio() as $k=>$f)
                    if($f)
                        $audio[$game->getKey()][$k] = $f;
            } ?>
        var appModes = <?=json_encode($modes, JSON_PRETTY_PRINT); ?>;
        var appAudio = <?=json_encode($audio, JSON_PRETTY_PRINT); ?>;
        var unreadNotices = <?=$notices?>;
        var bannerTicketLastNum = (5-Math.ceil(Math.random() * (5-<?=($filledTicketsCount?:1);?>)));
        var url = 'ws://<?=$_SERVER['SERVER_NAME'];?>:<?=\Config::instance()->wsPort?>';
        updateNotices(unreadNotices);
        <? if($quickGame['current']) : ?>$('#qgame .start').click();<? endif; ?>
        $('#qgame').hide();
        setTimeout(function(){$('#qgame').fadeIn(200)},1800);
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