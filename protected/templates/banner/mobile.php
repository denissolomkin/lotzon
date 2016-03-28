<?php echo "<!-- {$this->getTitle()} -->".
    str_replace('document.write', "$('#banner-{$this->getDevice()}-{$this->getLocation()}').append", $this->getDiv()) .
    str_replace('document.write', "$('#banner-{$this->getDevice()}-{$this->getLocation()}').append", $this->getScript()) ;?>
?>