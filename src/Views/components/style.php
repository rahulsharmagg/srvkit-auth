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
