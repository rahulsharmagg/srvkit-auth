<style type="text/tailwindcss">
	@layer components{
		.input{
			@apply !text-sm/0 px-6 py-4 font-sans font-semibold;
			@apply bg-transparent border-1 border-gray-400;
			@apply focus:border-primary;
		}

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
			@apply w-full px-4 bg-primary text-complementry relative;
			
			span:nth-child(2){
				@apply bg-primary-dark;
			}
		}
		.form-content{
			@apply p-4;
		}
		.form-input{
			@apply input flex flex-col mb-4;
			label{
				@apply text-sm;
			}
			input{
				@apply flex-1;
				@apply input;
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
			@apply px-4 block bg-primary text-complementry cursor-pointer;

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

	@layer utilities{
		.input{
			@apply py-4;
		}
		.button{
			@apply border-none bg-blue-4 px-6 py-4 text-sm/0;
			@apply hover:bg-blue-5;
		}
	}
</style>