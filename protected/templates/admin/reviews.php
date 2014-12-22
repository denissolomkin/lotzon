
<div class="container-fluid">

    <div class="row-fluid">
        <button onclick="document.location.href='/private/reviews?status=1&sortField=<?=$currentSort['field']?>&sortDirection=<?=$currentSort['direction']?>'" class="btn right btn-default btn-md <?=($status ? 'active' : '')?>">Одобренные</button></h2>
        <button onclick="document.location.href='/private/reviews?status=0&sortField=<?=$currentSort['field']?>&sortDirection=<?=$currentSort['direction']?>'" class="btn right btn-default btn-md <?=(!$status ? 'active' : '')?>">На рассмотрении</button>
        <h2>Отзывы
        <hr/>
    </div>
    <? if ($pager['pages'] > 1) {?>
        <div class="row-fluid">
            <div class="btn-group">
                <? for ($i=1; $i <= $pager['pages']; ++$i) { ?>
                    <button onclick="document.location.href='/private/reviews?status=<?=$status?>&page=<?=$i?>&sortField=<?=$currentSort['field']?>&sortDirection=<?=$currentSort['direction']?>'" class="btn btn-default btn-md <?=($i == $pager['page'] ? 'active' : '')?>"><?=$i?></button>
                <? } ?>
            </div>
        </div>
    <? } ?> 
    <div class="row-fluid">
        <table class="table table-striped users">
            <thead>
                <th>#ID</th>
                <th>Никнейм </th>
                <th>Дата</th>
                <th>Отзыв</th>
                <th style="width: 110px;">Options</th>
            </thead>
            <tbody>
                <? foreach ($list as $review) { ?>
                    <tr>
                        <td><?=$review->getId()?></td>
                        <td><?=$review->getPlayerName()?></td>
                        <td><?=$review->getDate('d.m.Y H:i')?></td>
                        <td><?=$review->getText()?><?=$review->getImage()?"<br><img src='/filestorage/reviews/".$review->getImage()."'>":''?></td>
                        <td>
                            <button class="btn btn-md btn-warning status-trigger" data-status='0' data-id="<?=$review->getId()?>"><i class="glyphicon glyphicon-lock"></i></button>
                            <button class="btn btn-md btn-success status-trigger" data-status='1' data-id="<?=$review->getId()?>"><i class="glyphicon glyphicon-ok"></i></button>
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
                    <button onclick="document.location.href='/private/reviews?status=<?=$status?>&page=<?=$i?>&sortField=<?=$currentSort['field']?>&sortDirection=<?=$currentSort['direction']?>'" class="btn btn-default btn-md <?=($i == $pager['page'] ? 'active' : '')?>"><?=$i?></button>
                <? } ?>
            </div>
        </div>
    <? } ?> 
</div>


<script>

    $('.status-trigger').on('click', function() {
        location.href = '/private/reviews/status/' + $(this).data('id') + '?status=<?=$status?>&setstatus=' + $(this).data('status');
    });

</script>
<?php

    function sortIcon($currentField, $currentSort, $pager)
    {
        $icon = '<a href="/private/users?page=1&sortField=%s&sortDirection=%s"><i class="glyphicon glyphicon-chevron-%s"></i></a>';
        if ($currentField == $currentSort['field']) {
            $icon = vsprintf($icon, array(
                $currentField,
                $currentSort['direction'] == 'desc' ? 'asc' : 'desc',
                $currentSort['direction'] == 'desc' ? 'down' : 'up',
            ));
        } else {
            $icon = vsprintf($icon, array(
                $currentField,
                'desc',
                'up',
            ));
        }

        return $icon;
    }
?>