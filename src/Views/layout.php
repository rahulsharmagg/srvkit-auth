<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $this->renderSection('title') ?></title>
	<!-- script/tailwindcss -->
	<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
	<style type="text/tailwindcss">
		@layer utility{
			.link{
				@apply underline;
				&:hover{
					@apply text-primary;
				}
			}
			.form-container{
				@apply m-auto min-w-[300px] max-w-3/12 mt-28 bg-gray-100;
				@apply border-b-4 border-primary;
			}
			.form-header{
				@apply w-full px-4 bg-primary relative;
				
				span:nth-child(2){
					@apply bg-primary-dark;
				}
			}
			.form-content{
				@apply p-4;
			}
			.form-input{
				@apply flex flex-col mb-4;
				label{
					@apply text-sm;
				}
				input{
					@apply text-sm font-sans font-semibold;
					@apply flex-1 px-2 min-h-[35px] border-1 border-gray-400;
				}
			}
			.form-radio{
				.role{
					@apply flex justify-between items-center gap-4;
					input[type="radio"]{
						@apply hidden;

						&:checked + label{
							@apply bg-primary font-semibold;
						}
					}

					label{
						@apply block bg-gray-200 text-sm flex-1 px-4 py-1 text-center;
					}

				}
			}

			.btn{
				@apply px-4 h-[35px] block bg-primary cursor-pointer;

				&:hover{
					@apply bg-primary-dark;
				}
			}

			.fade-in {
				animation: fade-in 0.5s ease-in-out forwards;
			}

			.message{
				@apply px-2 mb-4;

				&.error{
					@apply bg-red-400;
				}
				&.success{
					@apply bg-green-400;
				}
				&.info{
					@apply bg-blue-400;
				}
				&.warning{
					@apply bg-orange-400;
				}
			}

			@keyframes fade-in {
				from {
					opacity: 0;
				}
				to {
					opacity: 1;
				}
			}
		}
	</style>
	<?= view_cell(\SrvKit\Auth\Cells\Theme::class) ?>
</head>
<body>
	<main role="main">
		<?= $this->renderSection('main') ?>
	</main>
	<?= $this->renderSection('script') ?>
</body>
</html>