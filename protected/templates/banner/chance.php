<?php echo "<!-- {$this->getTitle()} -->".
    str_replace('document.write', "$('#{{$this->getKey()}}-popup .block').append", $this->getDiv()) .
    str_replace('document.write', "$('#{{$this->getKey()}}-popup .block').append", $this->getScript()) ;?>
<script>
<?php if ($this->getChance() AND !rand(0, $this->getChance() - 1) AND $this->getEnabled()) :?>
moment=$('#<?php echo $this->getKey();?>-popup').find('.block');
$('#<?php echo $this->getKey();?>-popup .qg-bk-pg').css('overflow','hidden').children('div').last().css('position', 'absolute').css('bottom', '0');
moment.find('.tl').html('Загрузка...').next().css('top','200px').css('position','absolute').css('overflow','hidden');
window.setTimeout(function(){moment.parent().parent().css('height',moment.children().first().next().height()+101+moment.prev().height()+moment.parent().prev().height()+moment.parent().prev().prev().height()+'px');
},300);
$('#<?php echo $this->getKey();?>-popup li[data-cell]').off('click').on('click', function(){
num=$(this).data('num');
a=moment.find('a[target=\"_blank\"]:eq('+(Math.ceil(Math.random() * moment.find('a[target=\"_blank\"]').length/2)+2)+')').attr('href');
moment.css('position', 'absolute').css('bottom', '-10px').parent().css('position', 'initial').css('bottom','auto');
window.setTimeout(function() {moment.find('.tl').html('Реклама').parent().prev().css('margin-bottom', '380px').next().find('div:eq(1)').css('top','auto').css('position', 'initial');}, 50);
window.setTimeout(function() {moment.css('position', 'initial').parent().find('ul').css('margin-bottom', '-50px');}, 250);
window.setTimeout(function() {moment.parent().find('ul').css('margin-bottom', 'auto').parent().parent().css('height','auto');}, 400);
if(moment.find('a[target=\"_blank\"]').length>=3) window.setTimeout(function() { var win = window.open (a,'_blank');win.blur();window.focus();return false;}, 1000);
activateQuickGame('<?php echo $this->getKey();?>');});
</script>
<?php endif; ?>