<!doctype html>
<html style="overflow: auto;">
    <head>
        <meta charset="utf-8">
        <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0 minimum-scale=1, maximum-scale=1">  -->
        <meta name="mobile-web-app-capable" content="yes" />
        <meta name="theme-color" content="#ffe700" />
        
        <link rel="icon" href="/res/img/favicones/favicon.png_128x128.png?v=666" type="image/png"/>
        <meta content="/res/img/favicones/favicon.png_128x128.png?v=666" itemprop="image">
        <link href="/res/img/favicones/favicon.png.ico?v=666" rel="shortcut icon">

        <title><?php echo $seo['Title'];?></title>
        <meta name="description" content="<?php echo $seo['Description'];?>">
        <meta name="keywords" content="<?php echo $seo['Keywords'];?>" />

        <link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700,400italic,700italic&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="/res/css/style.css?<?php echo $version;?>">
        <?php //if (isset($isMobile)) {
            //if ($isMobile) { ?>
                <!-- <link rel="stylesheet" href="/res/css/mobile/style.css?<?php echo $version;?>"> -->
            <?php // } else { ?>
                <!-- <link rel="stylesheet" href="/res/css/screen/style.css?<?php echo $version;?>"> -->
            <?php // }
        //} ?>
        <!-- screen only -->
        <link rel="stylesheet" href="/res/css/screen/style.css?<?php echo $version;?>">

        <link rel="stylesheet" href="/res/css/animate.css?<?php echo $version;?>">
        <link rel="stylesheet" href="/res/css/slots.css?<?php echo $version;?>" type="text/css">
        <link rel="stylesheet" href="/res/css/social-likes_birman.css">
        <link rel="stylesheet" href="/res/css/denis.css">

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
        } ?>
        <?php $antiblock_message = imagestringbox($antiblock_message); ?>
        <?php $antiblock_layer_id = chr(98 + mt_rand(0,24)) . substr(md5(time()), 0, 3); ?><?php $antiblock_html_elements = array (  0 => 'div',  1 => 'span',  2 => 'b',  3 => 'i',  4 => 'font',  5 => 'strong',); $antiblock_html_element = $antiblock_html_elements[array_rand($antiblock_html_elements)]; ?>
        <style type="text/css">#<?php echo $antiblock_layer_id; ?>{z-index: 10000;position:fixed !important;position:absolute;top:<?php echo mt_rand(-3, 3); ?>px;top:expression((t=document.documentElement.scrollTop?document.documentElement.scrollTop:document.body.scrollTop)+"px");left:<?php echo mt_rand(-3, 3); ?>px;width:<?php echo mt_rand(98, 103); ?>%;height:<?php echo mt_rand(98, 103); ?>%;background: rgba(0,0,0,0.85);display:block;padding:5% 0}#<?php echo $antiblock_layer_id; ?> *{text-align:center;margin:0 auto;display:block;filter:none;font:15px/50px Handbook-bold;text-decoration:none}#<?php echo $antiblock_layer_id; ?> ~ *{}#<?php echo $antiblock_layer_id; ?> div{margin-top: -100px;} #<?php echo $antiblock_layer_id; ?> div a[href]{display: inline-block;text-transform: uppercase;width:220px;height:50px; }#<?php echo $antiblock_layer_id; ?> div a.please {background-color:#ffe400;color:#000;cursor:pointer;}#<?php echo $antiblock_layer_id; ?> div a.please:hover {background-color:#000!important;color:#fff;}#<?php echo $antiblock_layer_id; ?> > :first-child{background-color: white;height: 500px;width: 540px;}</style>
        <div id="<?php echo $antiblock_layer_id; ?>"><<?php echo $antiblock_html_element; ?>>Пожалуйста, включите Javascript!</<?php echo $antiblock_html_element; ?>></div>
        <script type="text/javascript">
            window.document.getElementById("<?php echo $antiblock_layer_id; ?>").parentNode.removeChild(window.document.getElementById("<?php echo $antiblock_layer_id; ?>"));(function(l,m){function n(a){a&&<?php echo $antiblock_layer_id; ?>.nextFunction()}var h=l.document,p=["i","s","u"];n.prototype={rand:function(a){return Math.floor(Math.random()*a)},getElementBy:function(a,b){return a?h.getElementById(a):h.getElementsByTagName(b)},getStyle:function(a){var b=h.defaultView;return b&&b.getComputedStyle?b.getComputedStyle(a,null):a.currentStyle},deferExecution:function(a){setTimeout(a,2E3)},insert:function(a,b){var e=h.createElement("<?php echo $antiblock_html_element; ?>"),d=h.body,c=d.childNodes.length,g=d.style,f=0,k=0;if("<?php echo $antiblock_layer_id; ?>"==b){e.setAttribute("id",b);g.margin=g.padding=0;g.height="100%";for(c=this.rand(c);f<c;f++)1==d.childNodes[f].nodeType&&(k=Math.max(k,parseFloat(this.getStyle(d.childNodes[f]).zIndex)||0));k&&(e.style.zIndex=k+1);c++}e.innerHTML=a;d.insertBefore(e,d.childNodes[c-1])},displayMessage:function(a){var b=this;a="abisuq".charAt(b.rand(5));
                b.insert("<"+a+'><img src=tpl/img/please.jpg><div><a href="/" class="skip bt please">Обновить страницу</a><a href="http://ru.wikihow.com/%D0%BE%D1%82%D0%BA%D0%BB%D1%8E%D1%87%D0%B8%D1%82%D1%8C-Adblock" target="_blank">Как отключить AdBlock</a><div>'+("</"+a+">"),"<?php echo $antiblock_layer_id; ?>");
                h.addEventListener&&b.deferExecution(function(){b.getElementBy("<?php echo $antiblock_layer_id; ?>").addEventListener("DOMNodeRemoved",function(){b.displayMessage()},!1)})},i:function(){for(var a="<?php echo implode(",", array_merge(array_rand(array_flip(array('ADS_2','ADSlideshow','AD_300','Ad-3-Slider','Ad-Top','Ad3TextAd','AdBanner_S','AdBox728','AdFrame1','AdLayer2','AdMiddle','AdSense1','AdSpotMovie','AdTop','AdZone2','Adbanner','Adcode','AdsDiv','AdsFrame','AdsLeader','AdsWrap','Ads_Special','AdvHead','Advert1','BBoxAd','BannerAds','BodyAd','BottomAd0','BottomAds','ContentAd2','LeftAd','LeftAdF1','MPUAdSpace','OAS_Top','SIDEMENUAD','TDads','TextLinkAds','TopAd0','TopAdPos','VertAdBox','WNAd41','WNAd47','WNAd63','WelcomeAd','a_ad10Sp','aboveAd','ad-120-left','ad-162','ad-2','ad-300x40-1','ad-32','ad-320','ad-37','ad-4','ad-635x40-1','ad-655','ad-7','ad-a','ad-a1','ad-abs-b-10','ad-ban','ad-block','ad-boxes','ad-column','ad-cube-sec','ad-cube-top','ad-five','ad-frame','ad-inner','ad-ldr-spot','ad-leader','ad-makeup','ad-midpage','ad-mrec2','ad-one','ad-other','ad-rbkua','ad-rian','ad-sky-atf','ad-stripe','ad-wrapper1','ad-zone-1','ad002','ad02','ad160','ad180','ad2-label','ad2CONT','ad300-title','ad300c','ad336iiatf','ad336x280','ad600','ad728Bottom','ad728X90','ad97090','adBanner2','adBanner3','adBelt','adBottom','adBreak','adCENTRAL','adClickMe','adColumn','adFtofrs','adGroup4','adLContain','adLabel','adLeader','adLeft','adMed','adMedRect','adMeld','adOne','adRight2','adSlot3','adSlug','adSpace11','adSpace12','adSpace16','adSpace17','adSpace18','adSpace21','adSpace5','adSpace7','adSquare','adTag-genre','adTag2','adTeaser','adTop','adTopModule','adTopbanner','adUnit','adWrap','ad_300','ad_300_250','ad_300a','ad_300x100','ad_300x600','ad_500x150','ad_728_90','ad_728x91','ad_990x90','ad_B','ad_B1','ad_D','ad_F','ad_M','ad_P','ad_block','ad_box02','ad_box_ad_0','ad_bs_area','ad_cont','ad_fb_circ','ad_frame','ad_grp1','ad_island2','ad_layer1','ad_left','ad_main','ad_mast','ad_message','ad_mrec','ad_new','ad_num_2','ad_post','ad_poster','ad_promoAd','ad_right','ad_sgd','ad_short','ad_sky','ad_slot','ad_small','ad_stream11','ad_stream16','ad_stream19','ad_top','ad_topmob','ad_topnav','ad_wp_base','ad_zone1','adblade_ad','adblock-big','adbody','adbottomgao','adclose','adcode3','adcolumn','add_ciao2','adjacency','adl_728x90','adlink-55','adlink-74','adnet','adplace','adposition','adposition1','ads-200','ads-300-250','ads-468','ads-F','ads-G','ads-bot','ads-h-right','ads-king','ads-middle','ads-outer','ads-right','ads-vers7','ads2','ads300','ads300x250','ads728','ads728x90','adsDiv6','adsPanel','adsSPRBlock','adsZone_1','ads_300x250','ads_bigrec3','ads_eo','ads_h','ads_inner','ads_pave','ads_player','ads_right','ads_video','ads_wide','adsbox-left','adsense04','adsenseWrap','adsensetext','adside','adsky','adslot','adslot2','adslot_m2','adspace-1','adspace_top','adspot-1x4','adsquare2','adsspace','adstext2','adstory','adtab','adtags_left','adtech_2','adtop','adtxt','adunit','adunitl','adv-google','adv-right','adv-right1','adv-strip','adv-top','adv-x34','adv130x195','adv160x600','advSkin','adv_5','adv_728','adv_96','adv_Skin','adv_r','adv_sky','advertRight','advert_1','adverthome','advertise1','advertorial','advframe','adwin','adzbanner','adzerk','alert_ads','amazon-ads','anchorAd','ap_adtext','article_ads','asinglead','ban_300x250','banner-ads','bigad','bigbox-ad','blog-ad','bnrAd','body_728_ad','bot_ads','bottom-ads','box1ad','boxAd300','boxAdvert','boxad','boxad4','browsead','c_ad_sb','catad','central-ads','chartAdWrap','charts_adv','chatad','cltAd','cmn_ad_box','cnnRR336ad','cnnTowerAd','companionAd','contentAd','contest-ads','coverADS','ctr-ad','cubead2','dAdverts','ddAdZone2','devil-ad','div-ad-1x1','div-ad-r','divDoubleAd','divTopAd','divadsensex','docmainad','download_ad','event_ads','ffsponsors','first_ad','flAdData6','footerAd','footerAdd','four_ads','g_ad','galleryad1','gamepage_ad','gameplay_ad','gasense','geoAd','gglads213A','gog_ad','google-ad','google-afc','googleAdBox','googleAds','google_ad','google_ads','googleadsrc','h_ads1','header_ad','hi5-ad-1','hiddenadAC','home-ad','home_mpu','homead','homepage-ad','houseAd','icom-ad-top','idDivAd','iframe-ad','iframeAd_2','imPopup','imgad1','index_ad','inlineAd','instoryad','introAds','iqadtile11','iqadtile4','iqadtile8','iqd_topAd','j_ad','kdz_ad2','largead','lbAdBar','lblAds','leaderAd','leftAdCol','leftAd_rdr','left_adv','leftcolAd','linkAds','localAds','lower_ad','mainAdUnit','mid_ad_div','midadd','midadvert','midbarad','midpost_ad','mini-ad','mn_ads','monsterAd','moogleAd','mpuDiv','mpu_300x250','mpuad','mpusLeftAd','narrow-ad','nationalad','nbaVid300Ad','nrcAd_Top','ns_ad1','oas_Middle','oas_Right','oas_Right2','onpageads','ovAd','p-advert','p2squaread','page_ad_top','partner-ad','pgFooterAd','picad_div','player_ads','post-ads','post_advert','premiumads','pusher-ad','r_adver','railAd','reklama','related-ads','related_ads','rh-ad','rhc_ads','richad','right-ad1','rightAdDiv1','right_ad','rightinfoad','rrAdWrapper','rtmod_ad','rxgcontent','searchAds','secondaryad','section-ad','self-ad','side-ads','sideAds','sideads','sidebar-ads','sidebar-adv','sidebarAd','sidebarAds','singleAd','skinmid-ad','sky_advert','slideshowAd','smallAd','smallad','smallads','spl_ad','spon_links','sponsorAd','sponsorAd1','sponsorSpot','sponsor_bar','sponsor_no','spr_ad_bg','sq_ads','square_ad','squaread','starad','story_ads','takeover_ad','td_adunit1','td_adunit2','textAd','textAdsTop','tilia_ad','tmn_ad_1','top-ads','top-left-ad','topAd728x90','topAdArea','topMPU','top_add','top_ads','topad728','topadbanner','topaddwide','topadsense','topadz','toprow-ad','tour728Ad','towerad','upperMpu','upper_adbox','vert-ads','vertAd2','videoAdvert','wallAd','wf_SingleAd','wgtAd','wideAdd','wide_adv','wp-topAds','wrapAdTop','y-ad-units','yahooads','ybf-ads')), 7), array("ad", "ads", "adsense"))); ?>".split(","),b=a.length,e="",d=this,c=0,g="abisuq".charAt(d.rand(5));c<b;c++)d.getElementBy(a[c])||(e+="<"+g+' id="'+a[c]+'"></'+g+">");d.insert(e);d.deferExecution(function(){for(c=0;c<b;c++)if(null==d.getElementBy(a[c]).offsetParent||"none"==d.getStyle(d.getElementBy(a[c])).display)return d.displayMessage("#"+a[c]+"("+c+")");d.nextFunction()})},s:function(){var a={'pagead2.googlesyndic':'google_ad_client','js.adscale.de/getads':'adscale_slot_id','get.mirando.de/miran':'adPlaceId'},b=this,e=b.getElementBy(0,"script"),d=e.length-1,c,g,f,k;h.write=null;for(h.writeln=null;0<=d;--d)if(c=e[d].src.substr(7,20),a[c]!==m){f=h.createElement("script");f.type="text/javascript";f.src=e[d].src;g=a[c];l[g]=m;f.onload=f.onreadystatechange=function(){k=this;l[g]!==m||k.readyState&&"loaded"!==k.readyState&&"complete"!==k.readyState||(l[g]=f.onload=f.onreadystatechange=null,e[0].parentNode.removeChild(f))};e[0].parentNode.insertBefore(f,e[0]);b.deferExecution(function(){if(l[g]===m)return b.displayMessage(f.src);b.nextFunction()});return}b.nextFunction()},u:function(){var a="/ad-callback.,/ad_images/ad,/ads-leader|,/Ads/adrp0.,/carbonads/ad,/sidelinead.,/textads.,/toprightads.,_adbg2a.,_mainad.".split(","),b=this,e=b.getElementBy(0,"img"),d,c;e[0]!==m&&e[0].src!==m&&(d=new Image,d.onload=function(){c=this;c.onload=null;c.onerror=function(){p=null;b.displayMessage(c.src)};c.src=e[0].src+"#"+a.join("")},d.src=e[0].src);b.deferExecution(function(){b.nextFunction()})},nextFunction:function(){var a=p[0];a!==m&&(p.shift(),this[a]())}};l.<?php echo $antiblock_layer_id; ?>=<?php echo $antiblock_layer_id; ?>=new n;h.addEventListener?l.addEventListener("load",n,!1):l.attachEvent("onload",n)})(window);
        </script>

        <!--style>
            header .header-logo{ background: url('/tpl/img/9may.png') no-repeat; }
            header .header-logo::before{ content: " "; }
        </style-->

    </head>

    <body>

    <div id="banner-desktop-brand"  class="t-box">
    </div>

        <div id="menu-navigation-mobile" class="menu-mobile pushy pushy-left">
        </div>
        <div class="site-overlay"></div>
        <div class="wrapper clearfix">
            <span class="js-detect"></span>

            <!-- SITE TOP -->
            <div class="site-top">
                <div id="banner-desktop-top" class="banner-3" style="height: 150px;">
                </div>
            </div>
            <!-- end of SITE TOP -->

            <!-- HEADER -->
            <header class="header clearfix">
                <div class="sticky_box">
                    <div class="container clearfix">

                        <!-- Header Left -->
                        <div class="header-left clearfix">

                            <!-- Logo -->
                            <span class="header-logo i-lotzon"></span>

                            <!-- MENU -->
                            <nav id="menu-navigation" class="menu"></nav>
                            <!-- end of MENU -->

                            <!-- MENU buttons -->
                            <div id="menu-buttons"></div>
                            <!-- end of MENU buttons -->

                        </div>
                        <!-- .header-left -->

                        <!-- Header Right -->
                        <div class="header-right">

                            <div id="menu-slider" class="inf-slider"></div>

                            <div id="menu-balance"></div>

                        </div>
                        <!-- .header-right -->

                    </div>
                    <!-- .container -->
                </div>
            </header>
            <!-- end of HEADER -->