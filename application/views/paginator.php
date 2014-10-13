<?php if(sizeof($this->paginator) > 0) {?>
<!-- Блок пагинации -->
<div class="pagination">
	<ul>
	<?php if(isset($this->paginator['prevpage'])) {?>
	    <li><a href="/index/index/page/<?php echo $this->paginator['prevpage'];?>">Назад</a></li>
    <? } ?>
	
	<?php if(isset($this->paginator['firstpage'])) {?>
	    <? 
		$i = 1;
		foreach($this->paginator['firstpage'] as $fp) {
		?>
			<li><a href="/index/index/page/<?php echo $fp;?>"><? echo $fp;?></a></li>
			<? if(sizeof($this->paginator['firstpage']) == $i) { ?>
			<li><a href="#">...</a></li> 
			<? }?>
		<? 
		$i++;
		} ?>
    <? } ?>
	
	<? foreach($this->paginator['pages'] as $p => $active) { ?>
		<? if ($active) { ?>
			<li class="active"><a href="#"><?php echo $p; ?></a></li>
		<? } else { ?>
			<li><a href="/index/index/page/<? echo $p; ?>"><?php echo $p;?></a></li>
		<? } ?>
	<? } ?>
	
	<?php if(isset($this->paginator['lastpage'])) {?>
	    <? 
		$i = 1;
		foreach($this->paginator['lastpage'] as $lp) {
		?>
			<? if($i == 1) { ?>
			<li><a href="#">...</a></li> 
			<? }?>
			<li><a href="/index/index/page/<?php echo $lp;?>"><? echo $lp;?></a></li>
		<? 
		$i++;
		} ?>
    <? } ?>

	<?php if(isset($this->paginator['nextpage'])) {?>
	    <li><a href="/index/index/page/<?php echo $this->paginator['nextpage'];?>">Вперед</a></li>
    <? } ?>
    </ul>
</div>
<!-- конец блока пагинации -->
<? } ?>