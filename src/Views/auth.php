<?php

use SrvKit\Auth\Config\Services;
use SrvKit\Auth\Config\Version;

$title = 'SrvKit Authentication';
$mode = null;

$flashData = session()->getFlashdata('message');
[$type, $message] = $flashData ? explode(':', $flashData, 2) : ['info', ''];
$message_type = $type;

if($path == 'auth/login'){
	$title = 'Login — '.$title;
	$mode = 'login';
}

if($path == 'auth/signup/1'){
	$title = 'Create new user — '.$title;
	$mode = 'signup-1';
}

if($path == 'auth/signup/2'){
	$title = 'Create new user — '.$title;
	$mode = 'signup-2';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $title ?></title>
	<?= view_cell(\SrvKit\Auth\Cells\Theme::class) ?>
	<style>
		body{
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
		}

		.form-container{
			box-sizing: border-box;
			background-color: var(--srvkit-base-100);
			color: var(--srvkit-base-content);
			max-width: 320px;
			padding: 20px;
			border: 1px solid var(--srvkit-base-300);
			margin: auto;
			margin-top: 10%;
			position: relative;
			padding-top: 60px;
		}

		.form-container .form-title{
			background: var(--srvkit-primary);
			position: absolute;
			padding: 8px 20px;
			top: 0;
			right: 0;
			color: var(--srvkit-primary-content);
			width: 100%;
			box-sizing: border-box;
		}

		.form-container .form-title div{
			display: flex;
			flex-direction: column;
			line-height: 1;
		}

		.form-container .form-title div .title{
			font-size: 14px;
			font-weight: bold;
		}

		.form-container .form-title div .subtitle{
			font-size: small;
			font-weight: 500;
			text-transform: capitalize;
		}

		.form-container form > div {
			display: flex;
			flex-direction: column;
			gap: 6px;
			margin-bottom: 12px;
		}

		.form-container form > div > input {
			flex: 1;
			background: transparent;
			padding: 10px 20px;
			color: var(--srvkit-base-content) !important;
			border: 1px solid var(--srvkit-base-content);
		}

		.role{
			flex: 1;
			display: flex;
			justify-content: space-between;
		}

		.role > input[type="radio"]{
			display: none !important;
		}

		.role > input[type="radio"]:checked + label {
			background-color: var(--srvkit-secondary);
		}

		.role label{
			display: block;
			padding: 4px 6px;
			text-transform: uppercase;
			background-color: var(--srvkit-base-300);
			color: var(--srvkit-base-content);
		}

		.form-container .group-1{
			display: flex;
			align-items: center;
			justify-content: space-between;
			flex-direction: unset;
			margin-top: 24px;
		}

		.form-container a{
			color: var(--srvkit-base-content);
		}

		.form-container a:hover{
			color: var(--srvkit-accent);
		}

		.form-container button{
			border: none;
			padding: 10px 20px;
			background-color: var(--srvkit-accent);
			color: var(--srvkit-accent-content);
			cursor: pointer;
		}

		.form-container button:hover{
			background-color: var(--srvkit-secondary);
			color: var(--srvkit-secondary-content);
		}

		.form-container .version-number{
			margin-top: 20px;
			text-align: center;
			font-size: small;
			color: var(--srvkit-base-content);
		}

		.form-container .icon-link{
			position: absolute;
			top: 0;
			right: 0;
			z-index: 2;
			display: flex;
			aspect-ratio: 1;
			height: 100%;
			align-items: center;
			justify-content: center;
			color: var(--srvkit-secondary-content);
			background: var(--srvkit-secondary);
		}

		.form-container .icon-link:hover{
			background-color: var(--srvkit-neutral);
		}

		.form-response{
			font-size: small;
			padding: 6px 10px;
			color: var(--srvkit-base-content);
			margin-bottom: 20px;
			font-weight: 500;
		}

		.form-response:empty{
			display: none;
		}

		.info{
			color: var(--srvkit-info-content);
			background-color: var(--srvkit-info);
		}

		.warning{
			color: var(--srvkit-warning-content);
			background-color: var(--srvkit-warning);
		}

		.success{
			color: var(--srvkit-success-content);
			background-color: var(--srvkit-success);
		}

		.error{
			color: var(--srvkit-error-content);
			background-color: var(--srvkit-error);
		}
	</style>
</head>
<body>
	<div class="form-container">
		<div class="form-title">
			<div>
				<span class="title">SrvKit Authentication</span>
				<span class="subtitle"><?= preg_replace('/[\W\d]/', '', $mode) ?></span>
			</div>
			<?php if ($mode !== 'signup'): ?>
			<a class="icon-link" href="/auth/signup/1">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none">
					<path d="M13.5 16.0001V14.0623C15.2808 12.6685 16.5 11 16.5 7.41681C16.5 5.09719 16.0769 3 13.5385 3C13.5385 3 12.6433 2 10.4923 2C7.45474 2 5.5 3.82696 5.5 7.41681C5.5 11 6.71916 12.6686 8.5 14.0623V16.0001L4.78401 17.1179C3.39659 17.5424 2.36593 18.6554 2.02375 20.0101C1.88845 20.5457 2.35107 21.0001 2.90639 21.0001H13.0936" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
					<path d="M18.5 22L18.5 15M15 18.5H22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</a>
			<?php endif ?>
		</div>
		<div class="form-response <?= $message_type ?>"><?= $message ?></div>
		<?php if ($mode == 'login'): ?>
		<form action="/auth/login" method="post">
			<div>
				<label for="username">Username</label>
				<input type="text" name="username" id="username" value="testuser1">
			</div>
			<div>
				<label for="password">Password</label>
				<input type="password" name="password" id="password" value="secret123">
			</div>
			<div class="group-1">
				<a href="/auth/reset-password">Forgot Password?</a>
				<button type="submit">Login</button>
			</div>
		</form>
		<?php elseif($mode == 'signup-1'): ?>
		<form action="/auth/signup/1" method="post">
			<div>
				<label for="name">Name</label>
				<input type="text" name="name" id="name" value="<?= old('name') ?>">
			</div>
			<div>
				<label for="username">Username</label>
				<input type="text" name="username" id="username" value="<?= old('username') ?>">
			</div>
			<div>
				<label for="password">Password</label>
				<input type="password" name="password" id="password">
			</div>
			<div class="group-1">
				<a href="/auth/login">Login</a>
				<button type="submit">Create</button>
			</div>
		</form>
		<?php elseif($mode == 'signup-2'): ?>
		<form id="cancel-form" name="cancel-form" method="/auth/signup/cancel" method="post">
			<input type="hidden" name="username" value="<?= $username ?? '' ?>">
		</form>
		<form id="signup-2" action="/auth/signup/2" method="post">
			<div>
				<label for="email">Email</label>
				<input type="text" name="email" id="email" value="<?= old("email") ?>">
			</div>
			<div>
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
			<div class="group-1">
				<button type="submit">Finish</button>
			</div>
		</form>
		<button type="submit" form="cancel-form">Cancel</button>
		<?php else: ?>
		<div class="form-response error">Url mismatched. Unable to render the page content.</div>
		<?php endif ?>
		<div class="version-number"><?= \SrvKit\Auth\Config\Version::VERSION ?></div>
	</div>

	<script>
		function checkForValidUserName(e) {

		}
	</script>
</body>
</html>