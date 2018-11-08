<form action="<?php echo auth_url( 'forgot/send/' ); ?>" method="post">

	<h1 class="h6">Forgot Password</h1>

	<fieldset>
		<label for="email">Email address</label>
		<input type="email" name="email" id="email" />
	</fieldset>

	<fieldset>
		<button type="submit" class="button button--primary">Reset</button>
	</fieldset>

</form>

