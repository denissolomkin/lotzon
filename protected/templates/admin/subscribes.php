<div class="container-fluid">
    <div class="row-fluid">
        <h2>Заявки на регистрацию (<?=$count?>)</h2>
        <hr />
    </div>
    <div class="row-fluid">
        <table class="table table-striped">
            <thead>
                <th>#ID</th>
                <th>Email</th>
                <th>Дата заявки</th>
            </thead>
            <tbody>
                <? foreach ($list as $subscr) { ?>
                    <tr>
                        <td><?=$subscr['Id']?></td>
                        <td><?=$subscr['Email']?></td>
                        <td><?=date('d.m.Y', $subscr['DateSubscribed']);?></td>
                    </tr>
                <? } ?>
            </tbody>
        </table>
    </div>
</div>