<div <?php if ($this->getChance() AND !rand(0, $this->getChance() - 1) AND $this->getEnabled()) { echo " class='teaser";}?>>
<?php echo "<!-- {$this->getTitle()} -->".
    str_replace('document.write', "$('#banner-{$this->getDevice()}-{$this->getLocation()}').append", $this->getDiv()) .
    str_replace('document.write', "$('#banner-{$this->getDevice()}-{$this->getLocation()}').append", $this->getScript()) ;?>
</div>