<td class="" style="position: relative;">
    <div onclick="window.open('/private/users?search[where]=Id&search[query]=<?= $player->getId(); ?>')"
         data-id="<?= $player->getId() ?>" class="left pointer<?= $player->getBan() ? ' danger' : '' ?>"
         style="width: 80%;" <? if ($player->getAvatar()) : ?>data-toggle="tooltip" data-html="1" data-placement="auto"
         title="<img style='width:32px;' src='../filestorage/avatars/<?= (ceil($player->getId() / 100)) . '/' . $player->getAvatar() ?>'>"<? endif ?>>
        <?= $player->getNicname() ?>
        <br>
        <?= $player->getName() ?> <?= $player->getSurname() ?> <?= $player->getSecondName() ?>

    </div>
    <div style="position: relative;text-align: right;"
         class="pointer profile-trigger<?= $player->getBan() ? ' danger' : '' ?>" data-id="<?= $player->getId() ?>">
        <?= ($player->getDates('Ping') > time() - SettingsModel::instance()->getSettings('counters')->getValue('PLAYER_TIMEOUT') ? '<i class="online" style="margin-top: 5px;   line-height: 0px;">•</i>' : ''); ?>
        <?= $player->getCountry(); ?>
    </div>
    <div class="right games-holder">

        <? if ($player->getGamesPlayed()) { ?>
            <span class="stats-trigger pointer success" data-id="<?= $player->getId() ?>">
                                        <i class="fa fa-gift <?= ($player->getGamesPlayed() ? '' : 'text-danger') ?>"></i><?= $player->getGamesPlayed() ?>
                                    </span>
        <? } ?>

        <? if ($player->getDates('QuickGame')) { ?>
            <i class="fa fa-puzzle-piece <?=
            ($player->getDates('QuickGame') > strtotime('-2 day', time()) ? 'text-success' :
                ($player->getDates('QuickGame') > strtotime('-7 day', time()) ? 'text-warning' : 'text-danger')
            ) ?>"></i>
        <? } ?>

        <? if ($player->getDates('Moment')) { ?>
            <i class="fa fa-rocket <?=
            ($player->getDates('Moment') > strtotime('-2 day', time()) ? 'text-success' :
                ($player->getDates('Moment') > strtotime('-7 day', time()) ? 'text-warning' : 'text-danger')
            ) ?>"></i>
        <? } ?>

        <? if ($player->getDates('ChanceGame')) { ?>
            <i class="fa fa-star <?=
            ($player->getDates('ChanceGame') > strtotime('-2 day', time()) ? 'text-success' :
                ($player->getDates('ChanceGame') > strtotime('-7 day', time()) ? 'text-warning' : 'text-danger')
            ) ?>"></i>
        <? } ?>

        <? if ($player->getStats('WhoMore')) { ?>
            <span <?= ($player->getStats('WhoMore') * 100 > SettingsModel::instance()->getSettings('counters')->getValue('DANGER_MAX_WIN') || $player->getStats('WhoMore') * 100 < SettingsModel::instance()->getSettings('counters')->getValue('DANGER_MIN_WIN') ? 'class="text-danger"' : '') ?>>
                                    <nobr><i
                                            class="fa fa-sort-numeric-asc"></i><?= ceil($player->getStats('WhoMore') * 100) . '%' ?>
                                    </nobr>
                                </span>
        <? } ?>

        <? if ($player->getStats('SeaBattle')) { ?>
            <span <?= ($player->getStats('SeaBattle') * 100 > SettingsModel::instance()->getSettings('counters')->getValue('DANGER_MAX_WIN') || $player->getStats('SeaBattle') * 100 < SettingsModel::instance()->getSettings('counters')->getValue('DANGER_MIN_WIN') ? 'class="text-danger"' : '') ?>>
                                    <nobr><i
                                            class="fa fa-ship"></i><?= ceil($player->getStats('SeaBattle') * 100) . '%' ?>
                                    </nobr>
                                </span>
        <? } ?>

    </div>
</td>
<td class="contact-information <?= $player->getValid() ? "success" : "danger" ?>">
    <? if ($player->getStats('Mult') > 1) : ?>
    <div class="mults-trigger left pointer" data-id="<?= $player->getId() ?>">
        <div class="label label-danger label-mult"><?= $player->getStats('Mult') ?></div>
        <? endif ?><?= $player->getEmail() ?><? if ($player->getStats('Mult') > 1) : ?></div><? endif ?>
    <div class="social-holder">
        <? foreach ($player->getAdditionalData() as $provider => $info) {
            echo '<a href="javascript:void(0)" class="sl-bk ' . $provider . ($info['enabled'] == 1 ? ' active' : '') . '"></a>
                                <div class="hidden">';
            if (is_array($info))
                foreach ($info as $key => $value) {
                    echo $key . ' : ';
                    if (is_array($value)) {
                        $array = array();
                        foreach ($value as $k => $v)
                            $array[] = $k . ' - ' . $v;
                        echo implode('<br>', $array) . ' ; ';
                    } else
                        echo $value . ' ; ';
                }
            else echo $info;
            echo '</div>';
        } ?>
    </div>
    <br>

    <div class="left">
        <? if ($player->getStats()['Ip'] > 1): ?>
            <button
                class="btn btn-xs btn-danger" <?= ($player->getLastIp() || $player->getIp() ? "onclick=\"window.open('users?search[where]=Ip&search[query]=" . $player->getId() : '') ?>');">
            <span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span><?= $player->getStats()['Ip'] ?>
            </button>
        <? endif ?>

        <? if ($player->getDates('AdBlocked')): ?>
            <button
                class="btn btn-xs btn-<?= ($player->getDates('AdBlockLast') ? 'danger' : ($player->getDates('AdBlocked') < strtotime('-14 day', time()) ? "success" : "warning")) ?> logs-trigger"
                data-action="AdBlock" data-id="<?= $player->getId() ?>">
                <span class="glyphicon glyphicon-exclamation-sign"
                      aria-hidden="true"></span><?= ($player->getStats()['AdBlock'] ?: '') ?>
            </button>
        <? endif ?>

        <? if (($player->getCookieId() && $player->getCookieId() != $player->getId()) || $player->getStats()['CookieId'] > 1) : ?>
            <button class="btn btn-xs btn-danger"
                    onclick="window.open('users?search[where]=CookieId&search[query]=<?= $player->getId(); ?>')">
                <span class="glyphicon glyphicon-flag"
                      aria-hidden="true"></span><?= $player->getStats()['CookieId'] > 1 ? $player->getStats()['CookieId'] : ''; ?>
            </button>
        <? endif ?>

        <? if (($orders = $player->getStats('ShopOrder') + $player->getStats('MoneyOrder')) > 0): ?>
            <button class="btn btn-xs btn-success orders-trigger" data-id="<?= $player->getId() ?>">
                <span class="glyphicon glyphicon-tag" aria-hidden="true"></span><?= ($orders > 1 ? $orders : ''); ?>
            </button>
        <? endif ?>

    </div>


    <div class="right">

        <button class="btn btn-xs btn-<?= ($player->getStats()['Note'] ? 'danger' : 'warning'); ?> notes-trigger"
                data-type="Note" data-id="<?= $player->getId() ?>">
            <span class="glyphicon glyphicon-edit"
                  aria-hidden="true"></span><?= $player->getStats()['Note'] > 1 ? $player->getStats()['Note'] : ''; ?>
        </button>
        <button class="btn btn-xs btn-<?= ($player->getStats()['Notice'] ? 'success' : 'warning'); ?> notices-trigger"
                data-type="Message" data-id="<?= $player->getId() ?>">
            <span class="glyphicon glyphicon-bell"
                  aria-hidden="true"></span><?= $player->getStats()['Notice'] > 1 ? $player->getStats()['Notice'] : '' ?>
        </button>

        <? if ($player->getStats()['MyReferal'] > 0): ?>
            <button class="btn btn-xs btn-success"
                    onclick="window.open('users?search[where]=ReferalId&search[query]=<?= $player->getId(); ?>')">
                <span class="glyphicon glyphicon-user"
                      aria-hidden="true"></span><?= ($player->getStats()['MyReferal'] > 1 ? $player->getStats()['MyReferal'] : ''); ?>
            </button>
        <? endif ?>

        <? if ($player->getStats()['MyInviter'] > 0): ?>
            <button class="btn btn-xs btn-success"
                    onclick="window.open('users?search[where]=InviterId&search[query]=<?= $player->getId(); ?>')">
                <span class="glyphicon glyphicon-envelope"
                      aria-hidden="true"></span><?= ($player->getStats()['MyInviter'] > 1 ? $player->getStats()['MyInviter'] : ''); ?>
            </button>
        <? endif ?>

        <? if ($player->getStats()['Review'] > 0): ?>
            <button class="btn btn-xs btn-success reviews-trigger" data-id="<?= $player->getId() ?>">
                <span class="glyphicon glyphicon-thumbs-up"
                      aria-hidden="true"></span><?= $player->getStats()['Review'] > 1 ? $player->getStats()['Review'] : '' ?>
            </button>
        <? endif ?>

        <? if ($player->getStats('Message') > 0): ?>
            <button class="btn btn-xs btn-success messages-trigger" data-id="<?= $player->getId() ?>">
                <span class="fa fa-inbox"
                      aria-hidden="true"></span><?= $player->getStats('Message') > 1 ? $player->getStats('Message') : '' ?>
            </button>
        <? endif ?>

        <? if ($player->getStats()['Log'] > 0): ?>
            <button
                class="btn btn-xs btn-<?= ($player->getStats()['Log'] > 1 ? 'danger' : (($player->getStats()['Log'] == 1 AND $player->getValid()) ? 'success' : 'warning')) ?> logs-trigger"
                data-id="<?= $player->getId() ?>">
                <span class="glyphicon glyphicon-time"
                      aria-hidden="true"></span><?= $player->getStats()['Log'] > 1 ? $player->getStats()['Log'] : '' ?>
            </button>
        <? endif ?>

    </div>

</td>