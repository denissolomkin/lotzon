<?php echo "<!-- {$this->getTitle()} -->".
    str_replace('document.write', "$('#{{$this->getGroup()}}').append", $this->getDiv()) .
    str_replace('document.write', "$('#{{$this->getGroup()}}').append", $this->getScript()) ;
?>