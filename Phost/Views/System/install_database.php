<form action="/system/install/database-setup/" method="post">

	<h1 class="h4">Database Details</h1>

	<p>Enter your database details below. You may need to talk to your hosting provider if you're unsure what these might be. All fields are required to progress forward with the installation.</p>

	<fieldset>
		<label for="host">Host Name</label>
		<input type="text" name="host" id="host" required="required" />
	</fieldset>

	<fieldset>
		<label for="port">Port Number</label>
		<input type="text" name="port" id="port" required="required" />
	</fieldset>

	<fieldset>
		<label for="name">Database Name</label>
		<input type="text" name="name" id="name" required="required" />
	</fieldset>

	<fieldset>
		<label for="username">Username</label>
		<input type="text" name="username" id="username" required="required" />
		<p class="input-desc">The username of the database user.</p>
	</fieldset>

	<fieldset>
		<label for="password">Password</label>
		<input type="password" name="password" id="password" required="required" />
		<p class="input-desc">The password of the database user. <a class="show-hide-pass" data-show="Show Password" data-hide="Hide Password">Show Password</a></p>
	</fieldset>

	<fieldset>
		<label for="prefix">Prefix</label>
		<input type="text" name="prefix" id="prefix" value="ph_" required="required" />
	</fieldset>

	<fieldset>
		<button type="submit" class="button button--primary">Submit</button>
	</fieldset>

</form>

