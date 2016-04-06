<?php $id=time();
$timer = is_numeric($this->getTitle()) ? $this->getTitle() : 15; ?>
<div>
    <div class="timer-wrapper">
        <div class="timer" id="timer_videobanner<?php echo $id;?>"> загрузка...</div>
    </div>
<?php echo "<!-- {$this->getTitle()} -->".
    str_replace('document.write', "$('#banner-{$this->getDevice()}-{$this->getLocation()}-{$this->getPage()}').append", $this->getDiv()) .
    str_replace('document.write', "$('#banner-{$this->getDevice()}-{$this->getLocation()}-{$this->getPage()}').append", $this->getScript()) ;?>
    <script>
        $("#timer_videobanner<?php echo $id;?>").countdown({until: <?php echo $timer;?>,layout: 'Реклама {snn}'});
        setTimeout( function(){ document.getElementById('banner-<?php echo "{$this->getDevice()}-{$this->getLocation()}-{$this->getPage()}";?>').innerHTML = '';}, (<?php echo $timer;?>+1)*1000);
    </script>
</div>