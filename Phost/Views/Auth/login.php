<form action="<?php echo auth_url( 'login/' ); ?>" method="post">

	<h1 class="h6">Log in</h1>

	<fieldset>
		<label for="email">Email address</label>
		<input type="email" name="email" id="email" />
	</fieldset>

	<fieldset>
		<label for="password">Password</label>
		<input type="password" name="password" id="password" />
	</fieldset>

	<fieldset>
		<label for="remember"><input type="checkbox" name="remember" id="remember" /> Remember me</label>
	</fieldset>

	<fieldset>
		<a href="<?php echo auth_url( 'forgot/' ); ?>">Forgot your password?</a>
	</fieldset>

	<fieldset>
		<button type="submit" class="button button--primary">Log in</button>
	</fieldset>

</form>

