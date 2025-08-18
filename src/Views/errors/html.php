<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Error - SrvKit/Auth</title>
	<?php if (ENVIRONMENT === 'development' && service('vite')->isRunning()): ?>
	  <script type="module" src="http://localhost:5341/@vite/client"></script>
	  <link rel="stylesheet" href="http://localhost:5341/main.tailwind.css">
	<?php else: ?>
	  <link rel="stylesheet" href="/assets/app-wwo2ZyUN.css">
	<?php endif; ?>
</head>
<body>
	<div class="container m-auto">
		<h2 class="text-lg text-center"><?= 'SrvKit Authentication Error' ?></h2>
		<h3 class="text-lg text-center"><?= $code ?></h3>
		<p class="text-center"><?= nl2br(esc($message)) ?></p>
	</div>
</body>
</html>