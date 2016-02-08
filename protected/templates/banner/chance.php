<?php echo '<!-- ' . $this->getTitle() . ' -->' .
    str_replace('document.write', "$('#games-{$this->getKey()} .ad').append", $this->getDiv()) .
    str_replace('document.write', "$('#games-{$this->getKey()} .ad').append", $this->getScript()).
"<script>
    $('#games-{$this->getKey()} .ad')
        .css('position','relative')
        .append(
    \"<div style='z-index: 5;bottom: 0px;width: 100%;text-align: center;position: absolute;'><div class='timer' id='timer_chance".($id=time())."'> загрузка...</div></div>\"
    ).prev().hide();
    $('#timer_chance{$id}').countdown({
        until: ".($timer=is_numeric($this->getTitle())?$this->getTitle():10).",
        layout: 'осталось {snn} сек'
    });
    setTimeout(function(){ $('#games-{$this->getKey()} .ad').hide().prev().show();}, ({$timer}+1)*1000);
</script>";
