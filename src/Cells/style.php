<style>
	:root{
	<?php foreach($this->themes[$theme] as $key => $value){ ?>
	<?php
		$prefix = '--srvkit-';
	 ?>
	<?= ($prefix.$key.': '.$value[0].';').PHP_EOL ?>
	<?= empty($value[1]) ? '' : ($prefix.$key.'-content: '.$value[1].';').PHP_EOL ?>
	<?php } ?>
	}
</style>
