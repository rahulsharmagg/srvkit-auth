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
				<input type="text" name="username" id="username" value="<?= old('username') ?>" autocomplete="username webauthn">
			</div>
			<div class="form-input relative">
				<label for="password">Password</label>
				<a class="link link-primary absolute right-0" href="/auth/action/forgot-password">Forgot Password?</a>
				<input type="password" name="password" id="password" autocomplete="current-password">
			</div>
			<div class="form-checkbox">
				<input type="checkbox" id="password-view" onchange="document.getElementById('password').type=(this.checked? 'text': 'password');">
				<label for="password-view">Show Password</label>
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
		<form action="/auth/signup?step=1" method="post">
			<div class="form-input">
				<label for="name">Name</label>
				<input type="text" name="name" id="name" value="<?= old('name') ?>">
			</div>
			<div class="form-input relative">
				<label for="username">Username</label>
				<input type="text" name="username" id="username" value="<?= old('username') ?>">
				<div id="username-status" class="absolute right-0 flex justify-end items-center space-x-2 h-6"></div>
			</div>
			<div class="form-input">
				<label for="password">Password</label>
				<input type="password" name="password" id="password" autocomplete="new-password">
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
		<form id="signup-2" action="/auth/signup?step=2" method="post">
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
	<?php $this->section('script'); ?>
	<script>
		document.getElementById('username').addEventListener('input', debounce(checkUsername, 500));
		async function checkUsername() {
			const input = document.getElementById('username');
			const status = document.getElementById('username-status');
			const username = input.value.trim();

			// Clear and show loader
			if (username === '') {
				status.innerHTML = '';
				return;
			}

			status.innerHTML = `
				<svg class="animate-spin h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
				  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
				  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
				</svg>
				<span class="text-gray-500">Checking...</span>
			`;

			try {
				const response = await fetch('/auth/check-username?username=' + encodeURIComponent(username));
				const data = await response.json();
				if(data.type === 'error') throw new Error(data.error.message);

				if (data.available) {
				  	status.innerHTML = `<span class="text-green-400 font-medium">Username is available</span>`;
				} else {
				  	status.innerHTML = `<span class="text-red-400 font-medium">Username is taken</span>`;
				}
			} catch (err) {
				status.innerHTML = `<span class="text-orange-500">${err.message}</span>`;
			}
		}

		// Debounce to reduce API calls
		function debounce(func, delay) {
			let timer;
			return function (...args) {
				clearTimeout(timer);
				timer = setTimeout(() => func.apply(this, args), delay);
			};
		}
	</script>
	<?php $this->endSection(); ?>
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
