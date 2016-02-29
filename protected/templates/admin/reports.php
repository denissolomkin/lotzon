<div class="container-fluid users">
    <div class="row-fluid"">
        <h2>Отчеты | <?=$title?></h2>
        <hr />
    </div>

    <div class="row-fluid">
<form>
        <div class="col-my-1">
            <input type="text" name="dateFrom" value="<?=$dateFrom?>" placeholder="От" class="form-control datepick" />
        </div>

        <div class="col-my-1">
            <input type="text" name="dateTo" value="<?=$dateTo?>" placeholder="До" class="form-control datepick" />
        </div>
<? switch($identifier){
    case 'OnlineGames':
    case 'TopOnlineGames':
    case 'BotWins': ?>
        <div class="col-my-1">
            <select name="args[GameId]" class="form-control" placeholder="Игра" />
            <?if($identifier!='TopOnlineGames'){?><option value=""></option><?}?>
            <? foreach(\GameConstructorModel::instance()->getOnlineGames() as $key => $game):
                if(!is_numeric($key))
                    continue; ?>
                <option <?= is_numeric($args['GameId']) && $game->getId()==$args['GameId']?'selected':''?> value="<?=$game->getId();?>"><?=$game->getTitle('default');?></option>
            <? endforeach;?>
            </select>
        </div>
        <div class="col-my-1">
            <select name="args[Currency]" class="form-control" placeholder="Статус" />
            <option value=""></option>
            <? foreach(array('MONEY','POINT') as $currency):?>
                <option <?= $args['Currency'] && $args['Currency']!='' && $currency==$args['Currency']?'selected':''?> value="<?=$currency;?>"><?=$currency;?></option>
            <? endforeach;?>
            </select>
        </div>
<?  break;
    case 'UserRegistrations':?>
        <div class="col-my-1">
            <select name="args[GroupBy]" class="form-control" placeholder="Группировка" />
            <option value=""></option>
            <? foreach(array('Month','Country') as $groupBy):?>
                <option <?= $args['GroupBy'] && $args['GroupBy']!='' && $groupBy==$args['GroupBy']?'selected':''?> value="<?=$groupBy;?>">by <?=$groupBy;?></option>
            <? endforeach;?>
            </select>
        </div>
<?  break;
    case 'MoneyOrders':
    case 'ShopOrders':
    case 'UserReviews':?>
    <div class="col-my-1">
        <select name="args[Status]" class="form-control" placeholder="Статус" />
        <option value=""></option>
        <? foreach(array('На рассмотрении','Подтвержден','Отклонен') as $id=>$title):?>
            <option <?= is_numeric($args['Status']) && $id==$args['Status']?'selected':''?> value="<?=$id;?>"><?=$title;?></option>
        <? endforeach;?>
        </select>
    </div>
<? break;
}
?>
    <input class="btn btn-md btn-info" value="Сформировать" type="submit">
</form>

        <script type="text/javascript">
            $(".datepick").datepicker({format: 'yyyy-mm-dd',
                showTimePicker: false,
                autoclose: true,
                pickTime: false});

            $("select").on('input',function(){$("form").submit()});
        </script>
    </div>
<? if(is_array($reports[0])) { ?>
    <div class="row-fluid">
        <table class="table table-striped">
            <thead>
            <? foreach ($reports[0] as $title=>$column) { ?>
                <th><?=$title?></th>
            <? } ?>

            </thead>
            <tbody>
                <? foreach ($reports as $report) { ?>
                <tr>
                    <? foreach ($report as $title=>$column) {
                        if(!isset($bottom[$title]))
                            $bottom[$title] = null;
                        if(is_numeric($column))
                            $bottom[$title]+=$column; ?>
                        <td><?=(is_numeric($column) || ($temp[$title]!=$column) || (is_array($temp) && is_array($report) && reset($temp) != reset($report))? $column:'').prev($report)?></td>
                    <? } ?>
                </tr>
                <? $temp=$report;
                } ?>

                <tr>
                    <? foreach ($bottom as $title=>$column) {?>
                        <th><?=$column?:''?></th>
                        <? } ?>
                </tr>
            </tbody>
        </table>
    </div>
<? } ?>

</div>