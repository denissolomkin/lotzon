<?php

namespace controllers\production;
use \Application, \SettingsModel, \Player, \EntityException, \CountriesModel, \TicketsModel, \LotteriesModel, \ShopModel, \NewsModel, \LotterySettings, \ModelException, \ReviewsModel, \NoticesModel, \TransactionsModel, \Common;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_APPLICATION . 'model/entities/LotteryTicket.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class ContentController extends \AjaxController
{
    public function init()
    {
        parent::init();
        $this->authorizedOnly();
    }
    public function bannerAction($sector)
    {
        $resp=array();
        $banners = SettingsModel::instance()->getSettings('banners')->getValue();
        if( is_array($banners[$sector]))
            foreach($banners[$sector] as $group) {
                if (is_array($group)) {
                    shuffle($group);
                    foreach ($group as $banner) {
                        if (is_array($banner['countries']) and !in_array($this->session->get(Player::IDENTITY)->getCountry(), $banner['countries']))
                            continue;
                        if(!is_numeric($banner['title'])) $banner['title']=15;
                        $resp['block'] =
                            "<div id='ticket_video' class='tb-slide' style='display:none'>
                                <div style='z-index: 5;margin-top: 390px;margin-left: 320px;position: absolute;'>
                                    <div class='timer' id='timer_videobanner".($id=time())."'> загрузка...</div>
                                </div>".
                            str_replace('document.write',"$('#ticket_video').append",$banner['div']).
                            str_replace('document.write',"$('#ticket_video').append",$banner['script']).
                            "</div>
                            <script>$('#ticket_video').show();";
                        //<script>setTimeout(function(){ $('#ticket_video').show();}, 600);";

                        if(!rand(0,$banner['chance']-1) AND $banner['chance'] AND $banners['settings']['enabled']) {
                            $resp['block'] .= "$('#ticket_video').children().first().css('margin-top','100px').next().css('height','100px').css('overflow','hidden');
$('.tb-loto-tl li.loto-tl_li, .ticket-random, .ticket-favorite').off().on('click',function(event){
$('#timer_videobanner{$id}').countdown({until: {$banner['title']},layout: 'осталось {snn} сек'}).parent().css('margin-top','390px').next().css('height','auto').css('overflow','hidden').css('margin-top','auto');
moment=$('#ticket_video'); a=moment.find('a[target=\"_blank\"]:eq('+(Math.ceil(Math.random() * moment.find('a[target=\"_blank\"]').length/2)+3)+')').attr('href');
setTimeout(function(){  $('#ticket_video').hide(); },100);setTimeout(function(){  $('#ticket_video').show(); },300);
if(moment.find('a[target=\"_blank\"]').length>=3) window.setTimeout(function() {var win = window.open (a,'_blank');win.blur();window.focus();return false;}, 1000);
setTimeout(function(){ $('#ticket_video').remove(); }, ({$banner['title']}+1)*1000);activateTicket();});";
                        } else {
                            $resp['block'].="$('#timer_videobanner{$id}').countdown({until: {$banner['title']},layout: 'осталось {snn} сек'});
                        setTimeout(function(){ $('#ticket_video').remove(); }, ({$banner['title']}+1)*1000);
                        activateTicket();";
                        }
                        $resp['block'].="</script>";
                        break;
                    }
                }
            }

        $this->ajaxResponse($resp);
    }


}