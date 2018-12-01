<form action="<?php echo dashboard_url( 'flags/save/' ); ?>" method="post">

	<div class="content__banner">

		<div class="container">

			<div class="grid">

				<div class="row row--inline">

					<div class="col col--100">

						<h1 class="no-margin">Flags</h1>

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
							<label for="flag_dev_branch">Release branch</label>
							<?php $flags->fetch( 'flag_dev_branch', 'setting_key' ); ?>
							<select name="flag_dev_branch" id="flag_dev_branch">
								<option value="stable">Stable (default)</option>
								<option value="dev"<?php if ( 'dev' == $flags->setting_value ) : ?> selected="selected"<?php endif; ?>>Developer</option>
							</select>
							<p class="input-desc">You can change the release branch your blog is on if wish to update to the very latest (<em>and sometimes unstable</em>) version of the software.</p>
						</fieldset>

						<fieldset>
							<label for="flag_pass_hash">Hashing algorithm</label>
							<?php $flags->fetch( 'flag_pass_hash', 'setting_key' ); ?>
							<select name="flag_pass_hash" id="flag_pass_hash">
								<option value="bcrypt">Bcrypt (default)</option>
								<option value="argon2"<?php if ( 'argon2' == $flags->setting_value ) : ?> selected="selected"<?php endif; ?>>Argon2</option>
							</select>
							<p class="input-desc">The default algorithm for password hashing. Requires the Argon2 package to be installed with the servers version of PHP.</p>
						</fieldset>

						<fieldset>
							<label for="flag_ext_safe">Extension safe mode</label>
							<?php $flags->fetch( 'flag_ext_safe', 'setting_key' ); ?>
							<select name="flag_ext_safe" id="flag_ext_safe">
								<option value="off">Off (default)</option>
								<option value="on"<?php if ( 'on' == $flags->setting_value ) : ?> selected="selected"<?php endif; ?>>On</option>
							</select>
							<p class="input-desc">Extension safe mode lets the system load without any extensions being activated in the process.</p>
						</fieldset>

						<fieldset>
							<button type="submit" class="button button--primary">Save Changes</button>
						</fieldset>

					</div>

				</div>

			</div>

		</div>

	</div>

</form>

