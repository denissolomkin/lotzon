<div class="container-fluid users">
    <div class="row-fluid" id="items">
        <h2>Запросы на вывод товаров (<span id="shopCount"><?=$shopCount?></span>) <button class="btn btn-info" onclick="location.href='#money'"><i class="glyphicon glyphicon-hand-down"></i></button>

            <button onclick="document.location.href='/private/monetisation?moneyPage=<?=$moneyPager['page']?>&moneyStatus=<?=$moneyStatus?>&moneyType=<?=$moneyType?>&shopStatus=2&amp;sortField=Id&amp;sortDirection=desc#shop'" class="btn right btn-md btn-danger <?=($shopStatus==2 ? 'active' : '' )?>"><i class="glyphicon glyphicon-ban-circle"></i></button>
            <button onclick="document.location.href='/private/monetisation?moneyPage=<?=$moneyPager['page']?>&moneyStatus=<?=$moneyStatus?>&moneyType=<?=$moneyType?>&shopStatus=1&amp;sortField=Id&amp;sortDirection=desc#shop'" class="btn right btn-md btn-success <?=($shopStatus==1 ? 'active' : '' )?>"><i class="glyphicon glyphicon-ok"></i></button>
            <button onclick="document.location.href='/private/monetisation?moneyPage=<?=$moneyPager['page']?>&moneyStatus=<?=$moneyStatus?>&moneyType=<?=$moneyType?>&shopStatus=0&amp;sortField=Id&amp;sortDirection=desc#shop'" class="btn right btn-warning btn-md <?=(!$shopStatus ? 'active' : '' )?>"><i class="glyphicon glyphicon-time"></i></button>

  </h2>
        <hr />
    </div>

    <? if ($shopPager['pages'] > 1) {?>
        <div class="row-fluid">
            <div class="btn-group">
                <? for ($i=1; $i <= $shopPager['pages']; ++$i) { ?>
                    <button onclick="document.location.href='/private/monetisation?shopPage=<?=$i?>&moneyPage=<?=$moneyPager['page']?>&moneyStatus=<?=$moneyStatus?>&moneyType=<?=$moneyType?>&shopStatus=<?=$shopStatus?>&sortField=<?=$currentSort['field']?>&sortDirection=<?=$currentSort['direction'].($search['query']?'&search[where]='.$search['where'].'&search[query]='.$search['query']:'').'#items'?>'" class="btn btn-default btn-xs <?=($i == $shopPager['page'] ? 'active' : '')?>"><?=$i?></button>
                <? } ?>
            </div>
        </div>
    <? } ?>

    <div class="row-fluid">
        <table class="table table-striped">
            <thead>
                <!--th>#ID</th-->
                <th width="50">Дата</th>
                <th width="10%" style="min-width: 170px;">Игрок</th>
                <th width="20%">Информация</th>
                <th width="50">Баланс</th>
                <th>Данные</th>
                <th>Товар</th>
                <th width="50">Options</th>
            </thead>
            <tbody>
                <? foreach ($shopOrders as $order) { ?>
                    <tr id="shop<?=$order->getId()?>">

                        <?$player=$order->getPlayer();?>
                        <td<?=$player->getBan()?' class="danger"':''?>><?=date('d.m.Y <b\r> H:i:s', $order->getDateOrdered())?></td>

                        <?php include('user_template.php');?>

                        <td class="nobr pointer transactions-trigger" data-id="<?=$player->getId()?>"><?=($player->getPoints()<0?'<b class="red">'.$player->getPoints().'</b>':$player->getPoints())?> <br><?=($player->getMoney()<0?'<b class="red">':'').$player->getMoney()?> <?=\CountriesModel::instance()->getCountry($player->getCountry())->loadCurrency()->getTitle('iso');?></td>
                        <td><?=$order->getItem()->getTitle()?></br><?=($order->getChanceGameId() ? 'Выиграл в шанс' : $order->getItem()->getPrice().' баллов')?></td>


                        <?if($order->getCount()>0):?>
                        <td class="pointer orders-trigger" data-number="<?=$order->getNumber()?>">
                            <span class="label label-danger" ><?=$order->getCount()+1?></span>
                            <? else : ?> <td> <? endif ?>
                            ФИО: <?=$order->getSurname()?> <?=$order->getName()?> <?=$order->getSecondName()?> <br />
                            Телефон: <?=$order->getPhone()?> <br />
                            Адрес: <?=($order->getRegion() ? $order->getRegion() . ' обл.,' : '')?> г. <?=$order->getCity()?>, <?=$order->getAddress()?>

                        </td>
                        <td class="nobr">
                            <button class="btn btn-md btn-warning status <?=($shopStatus==0 ? ' hidden' : '' )?>" data-status='0' data-id="<?=$order->getId()?>"><i class="glyphicon glyphicon-time"></i></button>
                            <button class="btn btn-md btn-success status <?=($shopStatus==1 ? ' hidden' : '' )?>" data-status='1' data-id="<?=$order->getId()?>"><i class="glyphicon glyphicon-ok"></i></button>
                            <button class="btn btn-md btn-danger status <?=($shopStatus==2 ? ' hidden' : '' )?>" data-status='2' data-id="<?=$order->getId()?>"><i class="glyphicon glyphicon-ban-circle"></i></button>
                            <button class="btn btn-md btn-danger status <?=($shopStatus==2 ? ' hidden' : ' hidden' )?>" data-status='3' data-id="<?=$order->getId()?>"><i class="glyphicon glyphicon-remove"></i></button>
                        </td>
                    </tr>   
                <? } ?>
            </tbody>
        </table>
    </div>
    <div class="row-fluid" id="money">
        <h2>Запросы на вывод денег (<span id="moneyCount"><?=$moneyCount?></span>)
            <button class="btn btn-info" onclick="location.href='#items'" style="margin-right: 10%;"><i class="glyphicon glyphicon-hand-up"></i></button>
            <div class="types">
                <button onclick="document.location.href='/private/monetisation?shopPage=<?=$shopPager['page']?>&shopStatus=<?=$shopStatus?>&moneyStatus=<?=$moneyStatus?>&sortField=Id&sortDirection=desc#money'" class="btn btn-default btn-md <?=(!$moneyType? 'active' : '' )?>" style="padding: 6px;"><div style="width: 24px;height: 24px"></div></button>
                <? foreach(array('webmoney','yandex','private24','qiwi','phone','item') as $type) : ?>
                <button onclick="document.location.href='/private/monetisation?shopPage=<?=$shopPager['page']?>&shopStatus=<?=$shopStatus?>&moneyType=<?=$type?>&moneyStatus=<?=$moneyStatus?>&sortField=Id&sortDirection=desc#money'" class="btn btn-default btn-md <?=$type?> <?=($moneyType===$type ? 'active' : '' )?>" style="padding: 6px;"><img src="../tpl/img/<?=$type?>.png">
                <? if(isset($moneyCountByTypes[$type]) && $moneyCountByTypes[$type]>0) {?><span class="label label-danger"><?=$moneyCountByTypes[$type]?></span></button><? } ?>
                <? endforeach ?>
            </div>
            <button onclick="document.location.href='/private/monetisation?shopPage=<?=$shopPager['page']?>&shopStatus=<?=$shopStatus?>&moneyType=<?=$moneyType?>&moneyStatus=2&sortField=Id&sortDirection=desc#money'" class="btn right btn-md btn-danger <?=($moneyStatus==2 ? 'active' : '' )?>"><i class="glyphicon glyphicon-ban-circle"></i></button>
            <button onclick="document.location.href='/private/monetisation?shopPage=<?=$shopPager['page']?>&shopStatus=<?=$shopStatus?>&moneyType=<?=$moneyType?>&moneyStatus=1&sortField=Id&sortDirection=desc#money'" class="btn right btn-md btn-success <?=($moneyStatus==1 ? 'active' : '' )?>"><i class="glyphicon glyphicon-ok"></i></button>
            <button onclick="document.location.href='/private/monetisation?shopPage=<?=$shopPager['page']?>&shopStatus=<?=$shopStatus?>&moneyType=<?=$moneyType?>&moneyStatus=0&sortField=Id&sortDirection=desc#money'" class="btn right btn-warning btn-md <?=(!$moneyStatus ? 'active' : '' )?>"><i class="glyphicon glyphicon-time"></i></button>
        </h2>
        <hr />
    </div>


    <? if ($moneyPager['pages'] > 1) {?>
        <div class="row-fluid">
            <div class="btn-group">
                <? for ($i=1; $i <= $moneyPager['pages']; ++$i) { ?>
                    <button onclick="document.location.href='/private/monetisation?moneyPage=<?=$i?>&shopPage=<?=$shopPager['page']?>&moneyType=<?=$moneyType?>&shopStatus=<?=$shopStatus?>&moneyStatus=<?=$moneyStatus?>&sortField=<?=$currentSort['field']?>&sortDirection=<?=$currentSort['direction'].($search['query']?'&search[where]='.$search['where'].'&search[query]='.$search['query']:'').'#money'?>'" class="btn btn-default btn-xs <?=($i == $moneyPager['page'] ? 'active' : '')?>"><?=$i?></button>
                <? } ?>
            </div>
        </div>
    <? } ?>

    <div class="row-fluid">
        <table class="table table-striped">
            <thead>
                <!--th>#ID</th-->
                <th width="50">Дата</th>
                <th width="10%" style="min-width: 170px;">Игрок</th>
                <th width="20%">Информация</th>
                <th width="100">Баланс</th>
                <th>Номер</th>
                <th>Данные</th>
                <th>Options</th>
            </thead>
            <tbody>
                <? foreach ($moneyOrders as $order) { ?>
                    <tr id="money<?=$order->getId()?>">
                        <?$player=$order->getPlayer();?>
                        <td<?=$player->getBan()?' class="danger"':''?>><?=date('d.m.Y <b\r> H:i:s', $order->getDateOrdered())?></td>

                        <?php include('user_template.php');?>

                        <td class="nobr pointer transactions-trigger" data-id="<?=$player->getId()?>"><?=($player->getPoints()<0?'<b class="red">'.$player->getPoints().'</b>':$player->getPoints())?> <br><?=($player->getMoney()<0?'<b class="red">':'').$player->getMoney()?> <? ($currency=\CountriesModel::instance()->getCountry($player->getCountry())->loadCurrency()->getTitle('iso')); echo $currency;?></td>
                        <?if($order->getCount()>0):?>
                        <td class="pointer orders-trigger" data-number="<?=$order->getNumber()?>">
                        <span class="label label-danger" ><?=$order->getCount()+1?></span>
                        <? else : ?> <td> <? endif ?>
                        <img class="right" src="../tpl/img/<?=$order->getType()?>.png"><?=(($order->getType()=='webmoney')?($order->getData()['card-number'] ? $order->getData()['card-number']['value'][0] : $order->getData()['webmoney']['value'][0]):(in_array($order->getType(),array('phone','qiwi'))?'+':''))?><?=($order->getNumber()?:'')?></td>
                        <td>
                            <? if (is_array($order->getData())) foreach ($order->getData() as $key => $data) { ?>
                                <?=$data['title']?>: <?=$data['value']?> <?=($key=='summ' ? $currency :'');?><br />
                            <? } ?>

                        </td>
                        <td class="nobr" width="50">

                            <button class="btn btn-md btn-warning status money <?=($moneyStatus==0 ? ' hidden' : '' )?>" data-status='0' data-type="<?=$order->getType()?>" data-id="<?=$order->getId()?>"><i class="glyphicon glyphicon-time"></i></button>
                            <button class="btn btn-md btn-success status money <?=($moneyStatus==1 ? ' hidden' : '' )?>" data-status='1' data-type="<?=$order->getType()?>" data-id="<?=$order->getId()?>"><i class="glyphicon glyphicon-ok"></i></button>
                            <button class="btn btn-md btn-danger status money<?=($moneyStatus==2 ? ' hidden' : '' )?>" data-status='2' data-type="<?=$order->getType()?>" data-id="<?=$order->getId()?>"><i class="glyphicon glyphicon-ban-circle"></i></button>
                            <button class="btn btn-md btn-danger status money<?=($moneyStatus==2 ? ' hidden' : ' hidden' )?>" data-status='3' data-id="<?=$order->getId()?>"><i class="glyphicon glyphicon-remove"></i></button>

                        </td>
                    </tr>   
                <? } ?>
            </tbody>
        </table>
    </div>
</div>

<script>

    $('.status').on('click', function() {
        id= $(this).data('id');
        type = $(this).data('type');
        status = $(this).data('status');
        money = ($(this).hasClass('money') ? 1 : 0);
        $.ajax({
            url: "/private/monetisation/status/"+id+"?status="+status+"&money=" + money,
            method: 'GET',
            async: true,
            data: null,
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    if(money){
                        if(($("table tr#money"+id).parent().children().length)==1 && parseInt($("#shopCount").text())>1)
                            location.reload()
                        $("table tr#money"+id).remove();
                        $("#moneyCount").text(parseInt($("#moneyCount").text())-1);
                        $("#money .btn."+type+" span").text(parseInt($("#money .btn."+type+" span").text())-1);

                    } else {
                        if(($("table tr#shop"+id).parent().children().length)==1 && parseInt($("#shopCount").text())>1)
                            location.reload()
                        $("table tr#shop"+id).remove();
                        $("#shopCount").text(parseInt($("#shopCount").text())-1);
                    }
                } else {
                    alert(data.message);
                }
            },
            error: function() {
                alert('Unexpected server error');
            }
        });

    });

    /*
    $('.approve').on('click', function() {
        location.href = '/private/monetisation/approve/' + $(this).data('id') + '?moneyStatus=<?=$moneyStatus?>&shopStatus=<?=$shopStatus?>&money=' + ($(this).hasClass('money') ? 1 : 0);
    });
    $('.decline').on('click', function() {
        location.href = '/private/monetisation/decline/' + $(this).data('id') + '?moneyStatus=<?=$moneyStatus?>&shopStatus=<?=$shopStatus?>&money=' + ($(this).hasClass('money') ? 1 : 0);
    });
    $('.process').on('click', function() {
        location.href = '/private/monetisation/process/' + $(this).data('id') + '?moneyStatus=<?=$moneyStatus?>&shopStatus=<?=$shopStatus?>&money=' + ($(this).hasClass('money') ? 1 : 0);
    });
    $('.delete').on('click', function() {
        location.href = '/private/monetisation/delete/' + $(this).data('id') + '?moneyStatus=<?=$moneyStatus?>&shopStatus=<?=$shopStatus?>&money=' + ($(this).hasClass('money') ? 1 : 0);
    });
    */
</script>

<? if($frontend) require_once($frontend.'_frontend.php') ?>