<form action="<?php echo dashboard_url( 'users/save/' ); ?>" method="post">

	<div class="content__banner">

		<div class="container">

			<div class="grid">

				<div class="row row--inline">

					<div class="col col--100">

						<h1 class="no-margin">New User</h1>

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

						<p><i class="fas fa-reply" aria-hidden="true"></i> <a href="<?php echo dashboard_url( 'users/' ); ?>">Back to user listings</a></p>

						<fieldset>
							<label for="fullname">Full name <span class="required">*</span></label>
							<input type="text" name="fullname" id="fullname" required="required" />
						</fieldset>

						<fieldset>
							<label for="email">Email Address <span class="required">*</span></label>
							<input type="email" name="email" id="email" required="required" />
						</fieldset>

						<fieldset>
							<label for="password">Password <span class="required">*</span></label>
							<input type="password" name="password" id="password" required="required" />
							<p class="input-desc"><a class="show-hide-pass" data-show="Show Password" data-hide="Hide Password" role="link" tabindex="0">Show Password</a></p>
						</fieldset>

						<?php if ( is_admin() ) : ?>

							<fieldset>
								<label for="type">User Type</label>
								<select name="type" id="type">
									<option value="user">User</option>
									<option value="admin">Admin</option>
								</select>
								<p class="input-desc">Admins have permission to do anything. Be careful.</p>
							</fieldset>

						<?php endif; ?>

						<fieldset>
							<label for="notify"><input type="checkbox" name="notify" id="notify" /> Notify user of their new account via email.</label>
						</fieldset>

						<fieldset>
							<button type="submit" class="button button--primary">Create User</button>
						</fieldset>

					</div>

				</div>

			</div>

		</div>

	</div>

</form>

