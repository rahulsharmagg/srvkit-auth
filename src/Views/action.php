<?php

use SrvKit\Auth\Config\Auth;
use SrvKit\Auth\Config\Version;

$title = 'SrvKit Authentication';

$flashIcon = session()->getFlashdata('icon');
$flashData = session()->getFlashdata('message');
[$type, $content] = $flashData ? explode(':', $flashData, 2) : ['info', ''];
$message = (object) ['type' => $type, 'content' => $content];

 ?>

<?= $this->extend(config(Auth::class)->views['layout']) ?>
<?php $this->section('form-footer'); ?>
<div class="text-center p-4">
	<span class="block text-sm font-sans font-semibold"><?= Version::VERSION ?></span>
</div>
<?php $this->endSection(); ?>

<?php $this->section('icon'); ?>
	<?php if ($flashIcon == 'success'): ?>
		<div class="flex justify-center my-10">
			<svg xmlns="http://www.w3.org/2000/svg" class="text-green-400" viewBox="0 0 24 24" width="64" height="64" color="currentColor" fill="none">
				<path d="M22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12Z" stroke="currentColor" stroke-width="1.5" />
				<path d="M8 12.75C8 12.75 9.6 13.6625 10.4 15C10.4 15 12.8 9.75 16 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
			</svg>
		</div>
	<?php endif ?>
<?php $this->endSection(); ?>

<?php $this->section('main') ?>
	<?php switch ($action):
	case 'forgot-password' ?>
		<?php $this->section('title'); ?>Forgot Password<?php $this->endSection(); ?>
	    <div class="form-container">
	    	<div class="form-header">
	    		<div class="flex flex-col gap-0 font-sans">
	    			<span class="font-bold py-1">SrvKit Authentication</span>
	    			<span class="text-sm font-semibold -mx-4 px-4">Forgot Password</span>
	    		</div>
	    	</div>
	    	<div class="form-content">
	    		<div class="message fade-in <?= $message->type ?>"><?= $message->content ?></div>
	    		<form action="<?= '/auth/action/reset-password' ?>" method="POST">
	    			<div class="form-input">
	    				<label for="username">Username</label>
	    				<input type="text" name="username" value="<?= old('username') ?>" id="username">
	    			</div>
	    			<div class="form-input">
	    				<label for="email">Email</label>
	    				<input type="text" name="email" value="<?= old('email') ?>" id="email">
	    			</div>
	    			<p class="text-sm font-semibold">Note: This will send you an email for restting password</p>
	    			<button type="submit" class="btn mt-6 relative left-full -translate-x-full">Confirm</button>
	    		</form>
	    	</div>
	    	<?= $this->renderSection('form-footer') ?>
	    </div>
	<?php break;
	case 'reset-password': ?>
		<?php $this->section('title'); ?>Reset Password<?php $this->endSection(); ?>
		<div class="form-container">
			<div class="form-header">
				<div class="flex flex-col gap-0 font-sans">
					<span class="font-bold py-1">SrvKit Authentication</span>
					<span class="text-sm font-semibold -mx-4 px-4">Reset Password</span>
				</div>
			</div>
			<div class="form-content">
				<div class="message <?= esc($message->type) ?>"><?= esc($message->content) ?></div>
				<form action="<?= '/auth/action/update-password' ?>" method="POST">
					<input type="hidden" name="_method" value="PUT">
					<input type="hidden" name="token" value="<?= old('token', esc($_GET['token'] ?? '')) ?>">
					<div class="form-input">
						<label for="password">New Password</label>
						<input type="password" name="password" id="password">
					</div>
					<div class="form-input">
						<label for="cpassword">Confirm New Password</label>
						<input type="password" name="passconf" id="cpassword">
					</div>
					<button type="submit" class="btn mt-6 relative left-full -translate-x-full">Confirm</button>
				</form>
			</div>
			<?= $this->renderSection('form-footer') ?>
		</div>
	<?php break;
	case 'message-block': ?>
		<?php
			$actionName = str_replace('-', ' ', ucwords($name, '-'));
			if(empty($message->content)){
				$message->content = 'The previous operation has been successfully executed and is now fully completed, ensuring all tasks are finalized.';
			}
		 ?>
		<?php $this->section('title'); ?><?= $actionName ?><?php $this->endSection(); ?>
		<div class="form-container">
			<div class="form-header">
				<div class="flex flex-col gap-0 font-sans">
					<span class="font-bold py-1">SrvKit Authentication</span>
					<span class="text-sm font-semibold -mx-4 px-4"><?= $actionName ?></span>
				</div>
			</div>
			<div class="form-content">
				<?= $this->renderSection('icon') ?>
				<div class="message <?= esc($message->type) ?>"><?= esc($message->content) ?></div>
				<div class="text-center">
					<a class="link" href="/auth/login">Back to login</a>
				</div>
			</div>
			<?= $this->renderSection('form-footer') ?>
		</div>
	<?php break;
	default: ?>
	    <div>Unable to render view for this action.</div>
	<?php break;
	endswitch; ?>
<?php $this->endSection() ?>