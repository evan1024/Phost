<form action="/system/install/account-setup/" method="post">

	<h1 class="h4">Account &amp; Blog Setup</h1>

	<p>Enter the last few details below to finish the installation. Your account will be an admin with full access rights so pick a strong password.</p>

	<fieldset>
		<label for="name">Blog Name</label>
		<input type="text" name="name" id="name" required="required" />
	</fieldset>

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
		<p class="input-desc"><a class="show-hide-pass" data-show="Show Password" data-hide="Hide Password">Show Password</a></p>
	</fieldset>

	<fieldset>
		<button type="submit" class="button button--primary">Submit</button>
	</fieldset>

</form>

