<div class="container-fluid">
    <div class="row-fluid">
        <h2>Debug
            <hr/>
    </div>
    <div class="row-fluid">
        <? foreach ($list as $log) : ?>
            <div class="col-md-12">
                <div class="col-md-1" style="color: grey;">
                    <?= $log['Date']; ?>
                </div>
                <div class="col-md-1">

                    <b><?= $log['PlayerId']; ?></b>
                    <br>
                    <?= $log['Ip']; ?>
                </div>
                <div class="col-md-5" style="color:red;">
                    <? echo strip_tags($log['Log']); ?>
                </div>
                <div class="col-md-5" style="color: lightgrey;">
                    <?= $log['Agent']; ?>
                </div>
            </div>
        <? endforeach ?>

    </div>
</div>