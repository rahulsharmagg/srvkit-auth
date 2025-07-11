<?php

use SrvKit\Auth\Config\Auth;
use SrvKit\Auth\Config\Services;
use SrvKit\Auth\Config\Version;

$title = 'SrvKit Authentication';
$mode = null;

$flashData = session()->getFlashdata('message');
[$type, $content] = $flashData ? explode(':', $flashData, 2) : ['info', ''];
$message = (object) ['type' => $type, 'content' => $content];

$cookie_username = '';
if(isset($cookie->username)) {
	$cookie_username = $cookie->username;
}

?>

<?php $this->section('form_footer'); ?>
<div class="text-center p-4">
	<span class="block text-sm font-sans font-semibold"><?= Version::VERSION ?></span>
</div>
<?php $this->endSection(); ?>

<?php $this->section('login'); ?>
<div class="form-container">
	<div class="form-header">
		<div class="flex flex-col gap-0 font-sans">
			<span class="font-bold py-1"><?= $title ?></span>
			<span class="text-sm font-semibold -mx-4 px-4">Login</span>
		</div>
		<a class="absolute h-full aspect-square flex right-0 top-0 justify-center items-center bg-primary text-white hover:bg-primary-dark" href="/auth/signup/1">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none">
				<path d="M13.5 16.0001V14.0623C15.2808 12.6685 16.5 11 16.5 7.41681C16.5 5.09719 16.0769 3 13.5385 3C13.5385 3 12.6433 2 10.4923 2C7.45474 2 5.5 3.82696 5.5 7.41681C5.5 11 6.71916 12.6686 8.5 14.0623V16.0001L4.78401 17.1179C3.39659 17.5424 2.36593 18.6554 2.02375 20.0101C1.88845 20.5457 2.35107 21.0001 2.90639 21.0001H13.0936" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
				<path d="M18.5 22L18.5 15M15 18.5H22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
			</svg>
		</a>
	</div>
	<div class="form-content">
		<div class="message fade-in <?= $message->type ?>"><?= $message->content ?></div>
		<form action="/auth/login" method="post">
			<div class="form-input">
				<label for="username">Username</label>
				<input type="text" name="username" id="username" value="<?= old('username') ?>">
			</div>
			<div class="form-input">
				<label for="password">Password</label>
				<input type="password" name="password" id="password" value="<?= old('password') ?>">
			</div>
			<div class="flex justify-between items-center">
				<a class="link" href="/auth?action=forgot-password">Forgot Password?</a>
				<button type="submit" class="btn">Login</button>
			</div>
		</form>
	</div>
	<?= $this->renderSection('form_footer') ?>
</div>
<?php $this->endSection(); ?>
<?php $this->section('signup_one'); ?>
<div class="form-container">
	<div class="form-header">
		<div class="flex flex-col gap-0 font-sans">
			<span class="font-bold py-1"><?= $title ?></span>
			<span class="text-sm font-semibold -mx-4 px-4">SignUp 1/2</span>
		</div>
	</div>
	<div class="form-content">
		<div class="message fade-in <?= $message->type ?>"><?= $message->content ?></div>
		<form action="/auth/signup/1" method="post">
			<div class="form-input">
				<label for="name">Name</label>
				<input type="text" name="name" id="name" value="<?= old('name') ?>">
			</div>
			<div class="form-input">
				<label for="username">Username</label>
				<input type="text" name="username" id="username" value="<?= old('username') ?>">
			</div>
			<div class="form-input">
				<label for="password">Password</label>
				<input type="password" name="password" id="password">
			</div>
			<div class="flex justify-end items-center gap-4">
				<a class="link" href="/auth/login">Login</a>
				<button class="btn" type="submit">Create</button>
			</div>
		</form>
	</div>
	<div class="text-center p-4">
		<span class="block text-sm font-sans font-semibold"><?= Version::VERSION ?></span>
	</div>
</div>
<?php $this->endSection(); ?>
<?php $this->section('signup_two'); ?>
<div class="form-container">
	<div class="form-header">
		<div class="flex flex-col gap-0 font-sans">
			<span class="font-bold py-1"><?= $title ?></span>
			<span class="text-sm font-semibold -mx-4 px-4">SignUp 2/2</span>
		</div>
	</div>
	<div class="form-content">
		<div class="message fade-in <?= $message->type ?>"><?= $message->content ?></div>
		<form id="signup-2" action="/auth/signup/2" method="post">
			<div class="form-input">
				<label for="email">Email</label>
				<input type="text" name="email" id="email" value="<?= old("email") ?>">
			</div>
			<div class="form-radio">
				<label>Choose Role</label>
				<div class="role">
					<input type="radio" name="role" value="member" id="member" <?= (old("role") == 'member') && 'checked' ?>>
					<label for="member">Member</label>
					<input type="radio" name="role" value="author" id="author" <?= (old("role") == 'author') && 'checked' ?>>
					<label for="author">Auther</label>
					<input type="radio" name="role" value="admin" id="admin" <?= (old("role") == 'admin') && 'checked' ?>>
					<label for="admin">Admin</label>
				</div>
			</div>
			<div style="font-size: small; color:var(--srvkit-base-content);margin: 10px 0;">
				Note: Role for auther & admin requires owner verification.
			</div>
		</form>
		<form id="signup-cancel" action="/auth/signup/cancel" method="post">
			<input type="hidden" name="username" value="<?= $cookie_username ?>">
		</form>
		<div class="flex gap-4 justify-end items-center">
			<button class="btn btn-cancel" type="submit" form="signup-cancel">Cancel</button>
			<button class="btn" type="submit" form="signup-2">Finish</button>
		</div>
	</div>
	<?= $this->renderSection('form_footer') ?>
</div>
<?php $this->endSection(); ?>



<?= $this->extend(config(Auth::class)->views['layout']) ?>
<?= view_cell(\SrvKit\Auth\Cells\Theme::class) ?>
<?php $this->section('main') ?>
<?php if ($path == 'auth/login'): ?>
	<?php $this->section('title'); ?>Login<?php $this->endSection(); ?>
	<?= $this->renderSection('login') ?>
<?php elseif($path == 'auth/signup/1'): ?>
	<?php $this->section('title'); ?>SignUp<?php $this->endSection(); ?>
	<?= $this->renderSection('signup_one') ?>
<?php elseif($path == 'auth/signup/2'): ?>
	<?php $this->section('title'); ?>SignUp<?php $this->endSection(); ?>
	<?= $this->renderSection('signup_two') ?>
<?php else: ?>
	<div class="form-container">
		<div class="form-content">
			Unable to render this page
		</div>
	</div>
<?php endif; ?>
<?php $this->endSection() ?>

</body>
</html>