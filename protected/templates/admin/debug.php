<div class="container-fluid">
    <div class="row-fluid">
        <h2>Debug
            <a class="btn btn-primary" href="?mode=1">Все</a>
            <a class="btn btn-primary" href="?mode=2">По логам</a>
            <hr/>
    </div>
        <? foreach ($list as $log) : ?>
    <div class="row-fluid">
            <div class="col-md-12">
                <? if ($log['Date']):?>
                <div class="col-md-1" style="color: grey;">
                    <?= $log['Date']?:''; ?>
                </div>
                <div class="col-md-1">
                    <? if ($log['PlayerId']){?>
                    <b><?= $log['PlayerId']?:''; ?></b>
                    <br>
                    <?= $log['Ip']?:''; ?>
                    <? } ?>

                </div>
                <? endif; ?>
                <div class="col-md-5" style="color:red;">

                    <? echo strip_tags($log['Log']); ?>

                    <? if($log['Count']) { ?>
                        <span class="label label-warning right"><? echo $log['Count']; ?></span>
                    <? } ?>
                    <i style="color: #02008c; font-size: 12px;">
                    <br>
                    <? echo $log['Url']?'<span style="">Url:</span> '.$log['Url']:''; ?>
                    <? echo isset($log['Line'])?' <span >Line: '.$log['Line'].'</span>':'' ?>
                    </i>
                </div>
                <div class="col-md-5" style="color: lightgrey;">
                    <?= $log['Agent']; ?>
                </div>
            </div>
    </div>
        <? endforeach ?>

</div>