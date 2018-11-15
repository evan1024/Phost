<form action="<?php echo dashboard_url( 'settings/save/' ); ?>" method="post">

	<div class="content__banner">

		<div class="container">

			<div class="grid">

				<div class="row row--inline">

					<div class="col col--50">

						<h1 class="no-margin">Settings</h1>

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
							<label for="name">Blog name <span class="required">*</span></label>
							<?php $settings->fetch( 'name', 'setting_key' ); ?>
							<input type="text" name="name" id="name" value="<?php echo $settings->setting_value; ?>" />
						</fieldset>

						<fieldset>
							<label for="domain">Blog domain <span class="required">*</span></label>
							<?php $settings->fetch( 'domain', 'setting_key' ); ?>
							<input type="text" name="domain" id="domain" value="<?php echo $settings->setting_value; ?>" />
							<p class="input-desc">Example: <code>example.com</code></p>
						</fieldset>

						<?php if ( $themes ) : ?>

							<fieldset>
								<label for="theme">Theme <span class="required">*</span></label>
								<?php $settings->fetch( 'theme', 'setting_key' ); ?>
								<select name="theme" id="theme">
									<?php foreach ( $themes as $theme ) : ?>
										<option value="<?php echo $theme[ 'domain' ]; ?>"<?php if ( theme_domain() == $theme[ 'domain' ] ) : ?> selected="selected"<?php endif; ?>><?php echo $theme[ 'name' ]; ?> by <?php echo $theme[ 'author_name' ]; ?></option>
									<?php endforeach; ?>
								</select>
							</fieldset>

						<?php endif; ?>

						<fieldset>
							<label for="email">Email address <span class="required">*</span></label>
							<?php $settings->fetch( 'email', 'setting_key' ); ?>
							<input type="email" name="email" id="email" value="<?php echo $settings->setting_value; ?>" />
							<p class="input-desc">The <em>from</em> email address for all blog emails.</p>
						</fieldset>

						<fieldset>
							<label for="per_page">Per page <span class="required">*</span></label>
							<?php $settings->fetch( 'per_page', 'setting_key' ); ?>
							<input type="number" name="per_page" id="per_page" value="<?php echo $settings->setting_value; ?>" min="1" steps="1" />
							<p class="input-desc">The number of posts, users and other items per page.</p>
						</fieldset>

						<fieldset>
							<?php $settings->fetch( 'register', 'setting_key' ); ?>
							<label for="register"><input type="checkbox" name="register" id="register"<?php if ( 'on' == $settings->setting_value ) : ?> checked="checked"<?php endif; ?> /> Let anyone register a new account.</label>
						</fieldset>

						<fieldset>
							<?php $settings->fetch( 'https', 'setting_key' ); ?>
							<label for="https"><input type="checkbox" name="https" id="https"<?php if ( 'on' == $settings->setting_value ) : ?> checked="checked"<?php endif; ?> /> Access blog over a HTTPS (secure) connection.</label>
						</fieldset>

						<fieldset>
							<?php $settings->fetch( 'hsts', 'setting_key' ); ?>
							<label for="hsts"><input type="checkbox" name="hsts" id="hsts"<?php if ( 'on' == $settings->setting_value ) : ?> checked="checked"<?php endif; ?> /> Force HTTPS connections with extreme prejudice.</label>
						</fieldset>

						<fieldset>
							<?php $settings->fetch( 'auto_check', 'setting_key' ); ?>
							<label for="updates"><input type="checkbox" name="updates" id="updates"<?php if ( 'on' == $settings->setting_value ) : ?> checked="checked"<?php endif; ?> /> Automatically check for system updates each day.</label>
						</fieldset>

						<fieldset>
							<?php $settings->fetch( 'debug', 'setting_key' ); ?>
							<label for="debug"><input type="checkbox" name="debug" id="debug"<?php if ( 'on' == $settings->setting_value ) : ?> checked="checked"<?php endif; ?> /> Enable system debugging (<strong>not recommended</strong>).</label>
						</fieldset>

						<fieldset>
							<label for="language">Language</label>
							<?php $settings->fetch( 'language', 'setting_key' ); ?>
							<select name="language" id="language">
								<option value="en_gb">British English</option>
							</select>
						</fieldset>

						<fieldset>
							<label for="timezone">Timezone</label>
							<?php $settings->fetch( 'timezone', 'setting_key' ); ?>
							<select name="timezone" id="timezone">
								<?php foreach ( $timezones as $timezone ) : ?>
									<option value="<?php echo $timezone; ?>"<?php if ( $timezone == $settings->setting_value ) : ?> selected="selected"<?php endif; ?>><?php echo $timezone; ?></option>
								<?php endforeach; ?>
							</select>
						</fieldset>

					</div>

				</div>

			</div>

		</div>

	</div>

</form>

