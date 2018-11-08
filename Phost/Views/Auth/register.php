<form action="<?php echo auth_url( 'register/' ); ?>" method="post">

	<h1 class="h6">Register</h1>

	<fieldset>
		<label for="fullname">Full Name</label>
		<input type="text" name="fullname" id="fullname" required="required" />
	</fieldset>

	<fieldset>
		<label for="email">Email address</label>
		<input type="email" name="email" id="email" required="required" />
	</fieldset>

	<fieldset>
		<label for="password">Password</label>
		<input type="password" name="password" id="password" required="required" />
	</fieldset>

	<fieldset>
		<button type="submit" class="button button--primary">Register</button>
	</fieldset>

</form>

