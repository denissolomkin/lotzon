<div class="container-fluid users">
    <div class="row-fluid"">
        <h2>Отчеты | <?php echo $title?></h2>
        <hr />
    </div>

    <div class="row-fluid">
<form>
        <div class="col-my-1">
            <input type="text" name="dateFrom" value="<?php echo $dateFrom?>" placeholder="От" class="form-control datepick" />
        </div>

        <div class="col-my-1">
            <input type="text" name="dateTo" value="<?php echo $dateTo?>" placeholder="До" class="form-control datepick" />
        </div>
<?php switch($identifier){
    case 'OnlineGames':
    case 'TopOnlineGames':
    case 'BotWins': ?>
        <div class="col-my-1">
            <select name="args[GameId]" class="form-control" placeholder="Игра" />
            <?if($identifier!='TopOnlineGames'){?><option value=""></option><?}?>
            <?php foreach(\GameConstructorModel::instance()->getOnlineGames() as $key => $game):
                if(!is_numeric($key))
                    continue; ?>
                <option <?php echo  is_numeric($args['GameId']) && $game->getId()==$args['GameId']?'selected':''?> value="<?php echo $game->getId();?>"><?php echo $game->getTitle('default');?></option>
            <?php endforeach;?>
            </select>
        </div>
        <div class="col-my-1">
            <select name="args[Currency]" class="form-control" placeholder="Статус" />
            <option value=""></option>
            <?php foreach(array('MONEY','POINT') as $currency):?>
                <option <?php echo  $args['Currency'] && $args['Currency']!='' && $currency==$args['Currency']?'selected':''?> value="<?php echo $currency;?>"><?php echo $currency;?></option>
            <?php endforeach;?>
            </select>
        </div>
<?php  break;
    case 'UserRegistrations':?>
        <div class="col-my-1">
            <select name="args[GroupBy]" class="form-control" placeholder="Группировка" />
            <option value=""></option>
            <?php foreach(array('Month','Country') as $groupBy):?>
                <option <?php echo  $args['GroupBy'] && $args['GroupBy']!='' && $groupBy==$args['GroupBy']?'selected':''?> value="<?php echo $groupBy;?>">by <?php echo $groupBy;?></option>
            <?php endforeach;?>
            </select>
        </div>
<?php  break;
    case 'MoneyOrders':
    case 'ShopOrders':
    case 'UserReviews':?>
    <div class="col-my-1">
        <select name="args[Status]" class="form-control" placeholder="Статус" />
        <option value=""></option>
        <?php foreach(array('На рассмотрении','Подтвержден','Отклонен') as $id=>$title):?>
            <option <?php echo  is_numeric($args['Status']) && $id==$args['Status']?'selected':''?> value="<?php echo $id;?>"><?php echo $title;?></option>
        <?php endforeach;?>
        </select>
    </div>

        <div class="col-my-1">
            <select name="args[AdminID]" class="form-control" placeholder="Админ" />
            <option value=""></option>
            <?php foreach($admins as $admin):?>
                <option <?php echo  is_numeric($args['AdminID']) && $admin->getId()==$args['AdminID']?'selected':''?> value="<?php echo $admin->getId();?>"><?php echo $admin->getLogin();?></option>
            <?php endforeach;?>
            </select>
        </div>
<?php break;
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
<?php if(is_array($reports[0])) { ?>
    <div class="row-fluid">
        <table class="table table-striped">
            <thead>
            <?php foreach ($reports[0] as $title=>$column) { ?>
                <th><?php echo $title?></th>
            <?php } ?>

            </thead>
            <tbody>
                <?php foreach ($reports as $report) { ?>
                <tr>
                    <?php foreach ($report as $title=>$column) {
                        if(!isset($bottom[$title]))
                            $bottom[$title] = null;
                        if(is_numeric($column))
                            $bottom[$title]+=$column; ?>
                        <td><?php echo (is_numeric($column) || ($temp[$title]!=$column) || (is_array($temp) && is_array($report) && reset($temp) != reset($report))? $column:'').prev($report)?></td>
                    <?php } ?>
                </tr>
                <?php $temp=$report;
                } ?>

                <tr>
                    <?php foreach ($bottom as $title=>$column) {?>
                        <th><?php echo $column?:''?></th>
                        <?php } ?>
                </tr>
            </tbody>
        </table>
    </div>
<?php } ?>

</div>