<!DOCTYPE html>
<html lang="en" data-theme="<?= config('Auth')->theme ?>">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $this->renderSection('title') ?></title>
	<?php if (ENVIRONMENT === 'development'): ?>
	  <script type="module" src="http://localhost:5341/@vite/client"></script>
	  <link rel="stylesheet" href="http://localhost:5341/main.tailwind.css">
	<?php endif; ?>
</head>
<body>
	<main role="main">
		<?= $this->renderSection('main') ?>
	</main>
	<?= $this->renderSection('script') ?>
</body>
</html>