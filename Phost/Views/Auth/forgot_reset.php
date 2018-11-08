<form action="<?php echo auth_url( 'forgot/update/' ); ?>" method="post">
<input type="hidden" name="id" id="id" value="<?php echo $user->ID; ?>" />
<input type="hidden" name="token" id="token" value="<?php echo $user->token_reset; ?>" />

	<h1 class="h6">Forgot Password</h1>

	<fieldset>
		<label for="email">Email address</label>
		<input type="email" name="email" id="email" value="<?php echo $user->user_email; ?>" disabled="disabled" />
	</fieldset>

	<fieldset>
		<label for="password">New Password <span class="required">*</span></label>
		<input type="password" name="password" id="password" required="required" />
	</fieldset>

	<fieldset>
		<label for="confirm_password">Confirm Password <span class="required">*</span></label>
		<input type="password" name="confirm_password" id="confirm_password" required="required" />
	</fieldset>

	<fieldset>
		<button type="submit" class="button button--primary">Reset</button>
	</fieldset>

</form>

