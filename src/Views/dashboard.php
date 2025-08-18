<?php

use SrvKit\Auth\Config\Auth;
use SrvKit\Auth\Entities\User;
use SrvKit\Auth\Helpers\AvatarHelper;

/** @var User $user */

$router     = service('router');
$controller = class_basename($router->controllerName());
$method     = $router->methodName();

?>

<?php $this->section('header') ?>
<?php $this->endSection() ?>


<?= $this->extend(config(Auth::class)->views['layout']) ?>

<?php $this->section('dashboard-header') ?>
<?php $this->endSection() ?>

<?php $this->section('main') ?>
<div>
	<?php switch ($method):
	case 'profile' ?>
		<div class="container max-w-xl bg-gray-100 m-auto mt-20 p-4">
			<h3 class="text-xl font-semibold">Profile</h3>
		</div>
	<?php break;
	case 'settings': ?>
		<?= $this->include('\SrvKit\Auth\Views\_dashboard-setting') ?>
	<?php break;
	default: ?>
	    <div class="container max-w-xl bg-gray-100 m-auto mt-20 p-4">
	    	<h3 class="text-center font-semibold text-xl">Dashboard</h3>
	    	<div class="flex gap-4 items-start mb-10">
	    		<img class="block" src="<?= AvatarHelper::toUrl($user->avatar) ?>" alt="avatar" width="48px">
	    		<div class="flex-1 flex flex-col gap-0">
	    			<span class="font-semibold"><?= $user->name ?></span>
	    			<span class="text-gray-400 underline italic text-sm cursor-default"><?= $user->username ?></span>
	    		</div>
	    	</div>
	    	<form action="/auth/action/logout" method="post">
	    		<input type="hidden" name="username" value="<?= $user->username ?>">
	    		<button class="btn" type="submit">Logout</button>
	    	</form>
	    </div>
	<?php break;
	endswitch; ?>	
</div>
<?php $this->endSection() ?>
