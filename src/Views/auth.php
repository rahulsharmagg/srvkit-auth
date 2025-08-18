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


<?php $this->section('login'); ?>
<div class="form-container">
	<div class="form-header">
		<div class="flex flex-col gap-0 font-sans">
			<span class="font-bold py-1"><?= $title ?></span>
			<span class="text-sm font-semibold -mx-4 px-4">Login</span>
		</div>
	</div>
	<div class="form-content">
		<div class="message fade-in <?= $message->type ?>"><?= $message->content ?></div>
		<form action="/auth/login" method="post">
			<div class="form-input">
				<label for="username">Username</label>
				<input type="text" name="username" id="username" value="<?= old('username') ?>">
			</div>
			<div class="form-input relative">
				<label for="password">Password</label>
				<a class="link link-primary absolute right-0" href="/auth/action/forgot-password">Forgot Password?</a>
				<input type="password" name="password" id="password" value="<?= old('password') ?>">
			</div>
			<div class="flex justify-end items-center">
				<div class="flex-1">
					<a class="link link-sm link-primary" href="/auth/signup/1">Create an account</a>
				</div>
				<button type="submit" class="btn btn-primary btn-sm">Login</button>
			</div>
			
		</form>
	</div>
	<?= $this->include('\SrvKit\Auth\Views\_form-footer') ?>
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
				<div class="flex-1 text-sm theme-dark:text-gray-50">Already have an account? <a class="link link-sm link-primary" href="/auth/login">Login</a></div>
				<button class="btn btn-sm btn-primary" type="submit">Create</button>
			</div>
		</form>
	</div>
	<?= $this->include('\SrvKit\Auth\Views\_form-footer') ?>
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
				<div class="group">
					<div class="radio">
						<input type="radio" name="role" value="member" id="member" <?= (old("role") == 'member') && 'checked' ?>>
						<label for="member">Member</label>
					</div>
					<div class="radio">
						<input type="radio" name="role" value="author" id="author" <?= (old("role") == 'author') && 'checked' ?>>
						<label for="author">Auther</label>
					</div>
					<div class="radio">
						<input type="radio" name="role" value="admin" id="admin" <?= (old("role") == 'admin') && 'checked' ?>>
						<label for="admin">Admin</label>
					</div>
				</div>
			</div>
			<p class="text-xs font-semibold mb-10 theme-dark:text-gray-50">
				Note: Role for auther & admin requires owner verification.
			</p>
		</form>
		<form id="signup-cancel" action="/auth/signup/cancel" method="post">
			<input type="hidden" name="username" value="<?= $cookie_username ?>">
		</form>
		<div class="flex gap-4 justify-end items-center">
			<button class="btn btn-sm btn-secondry" type="submit" form="signup-cancel">Cancel</button>
			<button class="btn btn-sm btn-primary" type="submit" form="signup-2">Finish</button>
		</div>
	</div>
	<?= $this->include('\SrvKit\Auth\Views\_form-footer') ?>
</div>
<?php $this->endSection(); ?>



<?= $this->extend(config(Auth::class)->views['layout']) ?>
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
