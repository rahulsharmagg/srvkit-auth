<?php

use SrvKit\Auth\Config\Auth;
use SrvKit\Auth\Config\Version;

$title = 'SrvKit Authentication';

$flashIcon = session()->getFlashdata('icon');
$flashData = session()->getFlashdata('message');
[$type, $content] = $flashData ? explode(':', $flashData, 2) : ['info', ''];
$message = (object) ['type' => $type, 'content' => $content];

if ($action == 'message-block' && empty($message->content)) {
	$flashIcon = 'info';
	$message->content = 'The previous operation has been <i>successfully</i> executed and is now fully completed, ensuring all tasks are finalized.';
}


 ?>

<?= $this->extend(config(Auth::class)->views['layout']) ?>
<?php $this->section('form-footer'); ?>
<div class="text-center p-4">
	<span class="block text-sm font-sans font-semibold"><?= Version::VERSION ?></span>
</div>
<?php $this->endSection(); ?>

<?php $this->section('icon'); ?>
	<?php if ($flashIcon == 'success'): ?>
		<div class="flex justify-center my-6">
			<svg xmlns="http://www.w3.org/2000/svg" class="text-green-400" viewBox="0 0 24 24" width="64" height="64" color="currentColor" fill="none">
				<path d="M22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12Z" stroke="currentColor" stroke-width="1.5" />
				<path d="M8 12.75C8 12.75 9.6 13.6625 10.4 15C10.4 15 12.8 9.75 16 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
			</svg>
		</div>
	<?php endif ?>
	<?php if ($flashIcon == 'error'): ?>
		<div class="flex justify-center my-6">
			<svg xmlns="http://www.w3.org/2000/svg" class="text-red-400" viewBox="0 0 24 24" width="64" height="64" color="currentColor" fill="none">
			    <path d="M22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12Z" stroke="#141B34" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
			    <path d="M14.9994 15L9 9M9.00064 15L15 9" stroke="#141B34" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
			</svg>
		</div>
	<?php endif ?>
	<?php if ($flashIcon == 'info'): ?>
		<div class="flex justify-center my-6">
			<svg xmlns="http://www.w3.org/2000/svg" class="text-blue-400" viewBox="0 0 24 24" width="64" height="64" color="currentColor" fill="none">
			    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></circle>
			    <path d="M12 16V11.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
			    <path d="M12 8.01172V8.00172" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
			</svg>
		</div>
	<?php endif ?>
	<?php if ($flashIcon == 'warning'): ?>
		<div class="flex justify-center my-6">
			<svg xmlns="http://www.w3.org/2000/svg" class="text-orange-400" viewBox="0 0 24 24" width="64" height="64" color="currentColor" fill="none">
			    <path d="M13.9248 21H10.0752C5.44476 21 3.12955 21 2.27636 19.4939C1.42317 17.9879 2.60736 15.9914 4.97574 11.9985L6.90057 8.75333C9.17559 4.91778 10.3131 3 12 3C13.6869 3 14.8244 4.91777 17.0994 8.75332L19.0243 11.9985C21.3926 15.9914 22.5768 17.9879 21.7236 19.4939C20.8704 21 18.5552 21 13.9248 21Z" stroke="#141B34" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
			    <path d="M12 17V12.5" stroke="#141B34" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
			    <path d="M12 8.99828V8.98828" stroke="#141B34" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
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
	    			<div class="flex justify-end gap-6 items-center">
	    				<a class="link" href="/auth/login">Login</a>
	    				<button type="submit" class="btn">Confirm</button>
	    			</div>
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
				<div class="message text-sm font-semibold p-2 <?= esc($message->type) ?>"><?= strip_tags($message->content, "<b><u><i>") ?></div>
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