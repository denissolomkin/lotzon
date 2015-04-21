
<div class="container-fluid">

    <div class="row-fluid">
        <button onclick="document.location.href='/private/reviews?status=2&sortField=<?=$currentSort['field']?>&sortDirection=<?=$currentSort['direction']?>'" class="btn right btn-md btn-danger <?=($status==2 ? 'active' : '' )?>"><i class="glyphicon glyphicon-ban-circle"></i> Отклоненные</button>
        <button onclick="document.location.href='/private/reviews?status=1&sortField=<?=$currentSort['field']?>&sortDirection=<?=$currentSort['direction']?>'" class="btn right  btn-md btn-success <?=($status==1 ? 'active' : '')?>"><i class="glyphicon glyphicon-ok"></i> Одобренные</button>
        <button onclick="document.location.href='/private/reviews?status=0&sortField=<?=$currentSort['field']?>&sortDirection=<?=$currentSort['direction']?>'" class="btn right btn-warning btn-md <?=(!$status ? 'active' : '')?>"><i class="glyphicon glyphicon-time"></i> На рассмотрении</button>
        <h2>Отзывы
            <button type="button" data-sector="<?=$key?>" class="btn btn-success edit-trigger"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button>
        </h2>
        <hr/>
    </div>
    <? if ($pager['pages'] > 1) {?>
        <div class="row-fluid">
            <div class="btn-group">
                <? for ($i=1; $i <= $pager['pages']; ++$i) { ?>
                    <button onclick="document.location.href='/private/reviews?status=<?=$status?>&page=<?=$i?>&sortField=<?=$currentSort['field']?>&sortDirection=<?=$currentSort['direction']?>'" class="btn btn-default btn-xs <?=($i == $pager['page'] ? 'active' : '')?>"><?=$i?></button>
                <? } ?>
            </div>
        </div>
    <? } ?> 
    <div class="row-fluid">
        <table class="table table-striped users reviews">
            <thead>
                <th>Дата</th>
                <th style="min-width: 170px;">Игрок </th>
                <th style="min-width: 200px;">Информация </th>
                <th>Отзыв</th>
                <th style="width: 146px;">Options</th>
            </thead>
            <tbody>
                <? while ($reviews = array_pop($list))
                    foreach ($reviews as $review) {
                    $player = new Player;
                    $player->setId($review->getPlayerId())->fetch()->initDates()->initCounters();?>
                    <tr data-id="<?=$review->getId()?>" data-playerid="<?=$review->getPlayerId()?>" data-ispromo="<?=$review->isPromo()?>" data-reviewid="<?=$review->getReviewId()?>">
                        <td><?=$review->getDate('d.m.Y <b\r> H:m:s')?></td>
                        <td class="" style="position: relative;" >
                            <div onclick="window.open('/private/users?search[where]=Id&search[query]=<?=$player->getId();?>')" data-id="<?=$player->getId()?>" class="left pointer<?=$player->getBan()?' danger':''?>" style="width: 80%;" <? if($player->getAvatar()) : ?>data-toggle="tooltip" data-html="1" data-placement="auto" title="<img style='width:32px;' src='../filestorage/avatars/<?=(ceil($player->getId() / 100)) . '/'.$player->getAvatar()?>'>"<? endif ?>>
                                <?=$player->getNicname()?>
                                <br>
                                <?=$player->getName()?> <?=$player->getSurName()?> <?=$player->getSecondName()?>

                            </div>
                            <div style="position: relative;text-align: right;" class="pointer profile-trigger<?=$player->getBan()?' danger':''?>" data-id="<?=$player->getId()?>">
                                <?=($player->getOnlineTime()>time()-SettingsModel::instance()->getSettings('counters')->getValue('PLAYER_TIMEOUT')?'<i class="online" style="margin-top: 5px;   line-height: 0px;">•</i>':'');?>
                                <?=$player->getCountry()?>
                            </div>
                            <div class="right games-holder">

                                <? if($player->getGamesPlayed()){?>
                                    <span class="stats-trigger pointer success" data-id="<?=$player->getId()?>">
                                        <i class="fa fa-gift <?=($player->getGamesPlayed() ? '' : 'text-danger' )?>"></i><?=$player->getGamesPlayed()?>
                                    </span>
                                <?}?>

                                <? if($player->getDates('QuickGame')){?>
                                    <i class="fa fa-puzzle-piece <?=
                                    ($player->getDates('QuickGame') > strtotime('-2 day', time()) ? 'text-success' :
                                        ($player->getDates('QuickGame') > strtotime('-7 day', time()) ? 'text-warning' : 'text-danger')
                                    )?>"></i>
                                <?}?>

                                <? if($player->getDates('Moment')){?>
                                    <i class="fa fa-rocket <?=
                                    ($player->getDates('Moment') > strtotime('-2 day', time()) ? 'text-success' :
                                        ($player->getDates('Moment') > strtotime('-7 day', time()) ? 'text-warning' : 'text-danger')
                                    )?>"></i>
                                <?}?>

                                <? if($player->getDates('ChanceGame')){?>
                                    <i class="fa fa-star <?=
                                    ($player->getDates('ChanceGame') > strtotime('-2 day', time()) ? 'text-success' :
                                        ($player->getDates('ChanceGame') > strtotime('-7 day', time()) ? 'text-warning' : 'text-danger')
                                    )?>"></i>
                                <?}?>

                                <? if($player->getCounters('WhoMore')){?>
                                    <span <?=($player->getCounters('WhoMore')*100 > SettingsModel::instance()->getSettings('counters')->getValue('DANGER_MAX_WIN') || $player->getCounters('WhoMore')*100 < SettingsModel::instance()->getSettings('counters')->getValue('DANGER_MIN_WIN')? 'class="text-danger"' : '' )?>>
                                    <nobr><i class="fa fa-sort-numeric-asc"></i><?=ceil($player->getCounters('WhoMore')*100).'%'?></nobr>
                                </span>
                                <?}?>

                                <? if($player->getCounters('SeaBattle')){?>
                                    <span <?=($player->getCounters('SeaBattle')*100 > SettingsModel::instance()->getSettings('counters')->getValue('DANGER_MAX_WIN') || $player->getCounters('SeaBattle')*100 < SettingsModel::instance()->getSettings('counters')->getValue('DANGER_MIN_WIN')? 'class="text-danger"' : '' )?>>
                                    <nobr><i class="fa fa-ship"></i><?=ceil($player->getCounters('SeaBattle')*100).'%'?></nobr>
                                </span>
                                <?}?>

                            </div>
                        </td>
                        <td class="contact-information <?=$player->getValid() ? "success" : "danger"?>"><?=$player->getEmail()?>
                            <div class="social-holder">
                            <?foreach($player->getAdditionalData() as $provider=>$info)
                            {
                                echo '<a href="javascript:void(0)" class="sl-bk '.$provider.($info['enabled']==1?' active':'').'"></a>
                                <div class="hidden">';
                                if(is_array($info))
                                    foreach ($info as $key=>$value) {
                                        echo $key.' : ';
                                        if(is_array($value))
                                        {
                                            $array=array();
                                            foreach($value as $k=>$v)
                                                $array[] = $k.' - '.$v;
                                            echo implode('<br>',$array).' ; ';
                                        }
                                        else
                                            echo $value.' ; ';
                                    }
                                else echo $info;
                                echo'</div>';
                            }?>
                            </div>
                            <br>

                            <div class="left">
                                <? if($player->getCounters()['Ip']>1):?>
                                    <button class="btn btn-xs btn-danger" <?=($player->getLastIP() || $player->getIP()?"onclick=\"window.open('users?search[where]=Ip&search[query]=".$player->getIP().($player->getLastIP() && $player->getIP()?',':'').$player->getLastIP():'')?>');">
                                    <span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span><?=$player->getCounters()['Ip']?>
                                </button>
                            <? endif ?>

                                <? if ($player->getDateAdBlocked()):?>
                                    <button class="btn btn-xs btn-<?=($player->getAdBlock()?'danger':($player->getDateAdBlocked() < strtotime('-14 day', time()) ? "success" : "warning" ))?> logs-trigger" data-action="AdBlock" data-id="<?=$player->getId()?>">
                                        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><?=($player->getCounters()['AdBlock']?:'')?>
                                    </button>
                                <? endif ?>

                                <? if(($player->getCookieId() && $player->getCookieId()!=$player->getId()) || $player->getCounters()['CookieId']>1) :?>
                                    <button class="btn btn-xs btn-danger" onclick="window.open('users?search[where]=CookieId&search[query]=<?=$player->getCookieId();?>')">
                                        <span class="glyphicon glyphicon-flag" aria-hidden="true"></span><?=$player->getCounters()['CookieId']>1?$player->getCounters()['CookieId']:'';?>
                                    </button>
                                <? endif ?>

                                <? if (($orders=$player->getCounters('ShopOrder')+$player->getCounters('MoneyOrder'))>0): ?>
                                    <button class="btn btn-xs btn-success orders-trigger" data-id="<?=$player->getId()?>">
                                        <span class="glyphicon glyphicon-tag" aria-hidden="true"></span><?=($orders>1?$orders:'');?>
                                    </button>
                                <? endif ?>

                            </div >



                            <div class="right">

                                <button class="btn btn-xs btn-<?=($player->getCounters()['Note']?'danger':'warning');?> notes-trigger" data-type="Note" data-id="<?=$player->getId()?>">
                                    <span class="glyphicon glyphicon-edit" aria-hidden="true"></span><?=$player->getCounters()['Note']>1?$player->getCounters()['Note']:'';?>
                                </button>
                                <button class="btn btn-xs btn-<?=($player->getCounters()['Notice']?'success':'warning');?> notices-trigger" data-type="Message" data-id="<?=$player->getId()?>">
                                    <span class="glyphicon glyphicon-bell" aria-hidden="true"></span><?=$player->getCounters()['Notice']>1?$player->getCounters()['Notice']:''?>
                                </button>

                                <? if ($player->getCounters()['MyReferal']>0): ?>
                                    <button class="btn btn-xs btn-success" onclick="window.open('users?search[where]=ReferalId&search[query]=<?=$player->getId();?>')">
                                        <span class="glyphicon glyphicon-user" aria-hidden="true"></span><?=($player->getCounters()['MyReferal']>1?$player->getCounters()['MyReferal']:'');?>
                                    </button>
                                <? endif ?>

                                <? if ($player->getCounters()['MyInviter']>0): ?>
                                    <button class="btn btn-xs btn-success" onclick="window.open('users?search[where]=InviterId&search[query]=<?=$player->getId();?>')">
                                        <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span><?=($player->getCounters()['MyInviter']>1?$player->getCounters()['MyInviter']:'');?>
                                    </button>
                                <? endif ?>

                                <? if ($player->getCounters()['Review']>0): ?>
                                    <button class="btn btn-xs btn-success reviews-trigger" data-id="<?=$player->getId()?>">
                                        <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span><?=$player->getCounters()['Review']>1?$player->getCounters()['Review']:''?>
                                    </button>
                                <? endif ?>

                                <!--button class="btn btn-xs btn-warning transactions-trigger" data-id="<?=$player->getId()?>">T</button>
                            <button class="btn btn-xs btn-warning stats-trigger" data-id="<?=$player->getId()?>">Р</button-->
                                <? if ($player->getCounters()['Log']>0): ?>
                                    <button class="btn btn-xs btn-<?=($player->getCounters()['Log']>1?'danger':(($player->getCounters()['Log']==1 AND $player->getValid())?'success':'warning'))?> logs-trigger" data-id="<?=$player->getId()?>">
                                        <span class="glyphicon glyphicon-time" aria-hidden="true"></span><?=$player->getCounters()['Log']>1?$player->getCounters()['Log']:''?>
                                    </button>
                                <? endif ?>

                            </div>

                        </td>
                        <td class="text"><?=$review->getReviewId()?'<i class="fa fa-reply"></i> ':''?><?=$review->getText()?><?=$review->getImage()?"<br><img src='/filestorage/reviews/".$review->getImage()."'>":''?></td>
                        <td>
                            <button class="btn btn-md btn-primary edit-trigger"><i class="glyphicon glyphicon-edit"></i></button>
                            <button class="btn btn-md btn-warning status-trigger<?=($status==0 ? ' hidden' : '' )?>" data-status='0' data-id="<?=$review->getId()?>"><i class="glyphicon glyphicon-time"></i></button>
                            <button class="btn btn-md btn-success status-trigger<?=($status==1 ? ' hidden' : '' )?>" data-status='1' data-id="<?=$review->getId()?>"><i class="glyphicon glyphicon-ok"></i></button>
                            <button class="btn btn-md btn-danger status-trigger<?=($status==2 ? ' hidden' : '' )?>" data-status='2' data-id="<?=$review->getId()?>"><i class="glyphicon glyphicon-ban-circle"></i></button>
                        </td>
                    </tr>
                <? } ?>
            </tbody>
        </table>
    </div>

    <? if ($pager['pages'] > 1) {?>
        <div class="row-fluid">
            <div class="btn-group">
                <? for ($i=1; $i <= $pager['pages']; ++$i) { ?>
                    <button onclick="document.location.href='/private/reviews?status=<?=$status?>&page=<?=$i?>&sortField=<?=$currentSort['field']?>&sortDirection=<?=$currentSort['direction']?>'" class="btn btn-default btn-xs <?=($i == $pager['page'] ? 'active' : '')?>"><?=$i?></button>
                <? } ?>
            </div>
        </div>
    <? } ?> 
</div>



<div class="modal fade" id="edit-review" role="dialog" aria-labelledby="confirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmLabel">Редактирование отзыва</h4>
            </div>
            <div class="modal-body">
                <div class="row-fluid" id="errorForm" style="display:none">
                    <div class="alert alert-danger" role="alert">
                        <span class="error-container"></span>
                    </div>
                </div>

                <form class="form">
                    <input type="hidden" name="edit[Id]" id="edit-id" value="" />
                    <input type="hidden" name="edit[ReviewId]" id="edit-reviewid" value="" />
                    <input type="hidden" name="add[ReviewId]" id="add-reviewid" value="" />
                    <input type="hidden" name="add[Status]" value="1" />

                    <div class="form-group">

                        <div id="edit-sector">
                        <div class="row-fluid">

                            <div class="col-my-4">
                                <label>Id пользователя</label>
                                <input type="input" class="form-control" name="edit[PlayerId]" id="edit-playerid" value="" />
                            </div>

                            <div class="col-my-4">
                                <label>Опубликовать в промо</label>
                                <select name="edit[IsPromo]" id="edit-ispromo" class="form-control"/>
                                <option value="0">Нет</option>
                                <option value="1">Да</option>
                                </select>
                            </div>

                            <div class="col-my-4">
                                <label>Статус</label>
                                <select name="edit[Status]" id="edit-status" class="form-control"/>
                                    <option value="1">Одобрен</option>
                                    <option value="0">На рассмотрении</option>
                                    <option value="2">Отклонен</option>
                                </select>
                            </div>

                        </div>

                    <div class="row-fluid">
                    <div class="form-group">
                        <label class="control-label">Текст отзыва</label>
                        <textarea name="edit[Text]" class="form-control" id="edit-text"></textarea>
                    </div>
                    </div>
                    <div style="clear: both;"></div>
                    <hr/>
                        </div>

                    <div class="row-fluid">
                        <div class="col-my-4">
                            <label>Id пользователя</label>
                            <input type="input" class="form-control" name="add[PlayerId]" id="add-playerid" value="<?=\SettingsModel::instance()->getSettings('counters')->getValue('USER_REVIEW_DEFAULT');?>"/>
                        </div>
                        <div class="col-my-4">
                            <label>Опубликовать в промо</label>
                            <select name="add[IsPromo]" class="form-control"/>
                            <option value="0">Нет</option>
                            <option value="1">Да</option>
                            </select>
                        </div>
                    </div>

                        <div style="clear: both;"></div>

                    <div class="row-fluid">
                        <div class="form-group">
                            <label class="control-label">Текст ответа</label>
                            <textarea name="add[Text]" class="form-control" id="add-text"></textarea>
                        </div>
                    </div>

                    </div>
                </form>

                <div class="row-fluid">
                    <button class="btn btn-md btn-success save pull-right">Сохранить</button>
                    <button class="btn btn-danger cls">Отмена</button>
                </div>
            </div>
        </div>
    </div>
</div>


<script>


    $('.status-trigger').on('click', function() {
        location.href = '/private/reviews/status/' + $(this).data('id') + '?status=<?=$status?>&setstatus=' + $(this).data('status');
    });

    $('.edit-trigger').on('click', function() {

        var button = $(this);
        var tr = button.parents('tr').first();

        $("#edit-review").modal();
        $("#edit-review").find('.cls').off('click').on('click', function () {
            $("#edit-review").modal('hide');
        });


        $('#add-reviewid').val(tr.data('reviewid') ? tr.data('reviewid') : (tr.data('id') ? tr.data('id') : null));
        $('#add-text').val('');

        if (!tr.length) {
            $('#edit-sector').hide();
            $('#edit-text').val('');
        }else{
            var name = ($('td div:eq(0)',tr).html()).split("<br>");
            $('#add-text').val(name[0].trim()+', ');
            $('#edit-sector').show();
            $('#edit-id').val(tr.data('id'));
            $('#edit-text').val(tr.find('.text').text());
            $('#edit-playerid').val(tr.data('playerid'));
            $('#edit-ispromo').val(tr.data('ispromo') ? tr.data('ispromo') : 0).change();
        }

        $("#edit-review").find('.save').off('click').on('click', function() {
            var button =$(this);
            button.find('.fa').hide().parent().prepend($('<i class="fa fa-spinner fa-pulse"></i>'));
            var form =  $("#edit-review").find('form');

            $.ajax({
                url: "/private/reviews",
                method: 'POST',
                data: form.serialize(),
                async: true,
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        button.find('.fa').first().remove();
                        button.removeClass('btn-danger btn-success').addClass('btn-default').prepend($('<i class="fa fa-check"></i>'));
                        button.find('.fa').fadeOut(500);
                        window.setTimeout(function () {
                            button.find('.fa').show().filter(':not(.fa-save)').remove();
                        }, 500);

                        $('[name="Id"]', form).val(data.data.Id);
                        form.removeClass('label-danger');
                        location.href = '/private/reviews/?status=<?=$status?>';

                    } else {
                        button.find('.fa').first().remove();
                        button.removeClass('btn-success').addClass('btn-danger');
                        button.find('.fa').show().filter(':not(.fa-save)').remove();
                        alert(data.message);
                    }
                },
                error: function (data) {
                    button.find('.fa').first().remove();
                    button.removeClass('btn-success').addClass('btn-danger');
                    button.find('.fa').show().filter(':not(.fa-save)').remove();
                    alert('Unexpected server error');
                    console.log(data.responseText);
                }
            });
        });

    });

    $('.delete-trigger').on('click', function() {
        location.href = '/private/reviews/delete/' + $(this).data('id');
    });

    $('.status-trigger').on('click', function() {
        location.href = '/private/reviews/status/' + $(this).data('id') + '?status=<?=$status?>&setstatus=' + $(this).data('status');
    });

</script>

<? if($frontend) require_once($frontend.'_frontend.php') ?>