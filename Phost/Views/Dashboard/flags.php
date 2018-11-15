<form action="<?php echo dashboard_url( 'flags/save/' ); ?>" method="post">

	<div class="content__banner">

		<div class="container">

			<div class="grid">

				<div class="row row--inline">

					<div class="col col--50">

						<h1 class="no-margin">Flags</h1>

					</div>

					<div class="col col--50 text--right">

						<button type="submit" class="button button--primary">Save Changes</button>

					</div>

				</div>

			</div>

		</div>

	</div>

	<div class="content__main">

		<div class="container">

			<div class="grid">

				<div class="row">

					<div class="col col--100">

						<?php echo do_notices(); ?>

					</div>

				</div>

				<div class="row row--centered">

					<div class="col col--50 col-tab--75 col-tab--100">

						<fieldset>
							<label for="flag_pass_hash">Hashing algorithm</label>
							<?php $flags->fetch( 'flag_pass_hash', 'setting_key' ); ?>
							<select name="flag_pass_hash" id="flag_pass_hash">
								<option value="bcrypt">Bcrypt (default)</option>
								<option value="argon2"<?php if ( 'argon2' == $flags->setting_value ) : ?> selected="selected"<?php endif; ?>>Argon2</option>
							</select>
							<p class="input-desc">The default algorithm for password hashing. Requires the Argon2 package to be installed with the servers version of PHP.</p>
						</fieldset>

					</div>

				</div>

			</div>

		</div>

	</div>

</form>

