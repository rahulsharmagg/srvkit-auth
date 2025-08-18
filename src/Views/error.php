<?php

use SrvKit\Auth\Config\Auth;
?>


<?= $this->extend(config(Auth::class)->views['layout']) ?>
<?= view_cell(\SrvKit\Auth\Cells\Theme::class) ?>
<?php $this->section('title'); ?>Authorization Error<?php $this->endSection(); ?>
<?php $this->section('main') ?>
<div class="bg-gray-100 container max-w-xl m-auto mt-20 p-4">
	<h3 class="text-center text-xl font-semibold">Authorization Error</h3>
	<pre><?= PHP_EOL. json_encode($error, JSON_PRETTY_PRINT) ?></pre>
</div>
<?php $this->endSection() ?>