<form action="<?php echo dashboard_url( 'users/save/' ); ?>" method="post">
<input type="hidden" name="id" id="id" value="<?php echo $user->ID; ?>" />

	<div class="content__banner">

		<div class="container">

			<div class="grid">

				<div class="row row--inline">

					<div class="col<?php if ( ! is_me( $user->ID ) && is_admin() ) : ?> col--50<?php else : ?> col--100<?php endif; ?>">

						<h1 class="no-margin">Edit User</h1>

					</div>

					<?php if ( ! is_me( $user->ID ) && is_admin() ) : ?>

						<div class="col col--50 text--right">

							<a href="<?php echo csrfify_url( dashboard_url( 'users/delete/' . $user->ID . '/' ) ); ?>" class="button button--warning js-delete-warn">Delete</a>

						</div>

					<?php endif; ?>

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
							<input type="text" name="fullname" id="fullname" required="required" value="<?php echo $user->user_fullname; ?>" />
						</fieldset>

						<fieldset>
							<label for="email">Email Address <span class="required">*</span></label>
							<input type="email" name="email" id="email" required="required" value="<?php echo $user->user_email; ?>" />
						</fieldset>

						<fieldset>
							<label for="password">Password</label>
							<input type="password" name="password" id="password" />
							<p class="input-desc">Leave blank to remain unchanged. <a class="show-hide-pass" data-show="Show Password" data-hide="Hide Password" role="link" tabindex="0">Show Password</a></p>
						</fieldset>

						<?php if ( ! is_me( $user->ID ) && is_admin() ) : ?>

							<fieldset>
								<label for="type">User Type</label>
								<select name="type" id="type">
									<option value="user"<?php if ( 'admin' != $user->user_type ) : ?> selected="selected"<?php endif; ?>>User</option>
									<option value="admin"<?php if ( 'admin' == $user->user_type ) : ?> selected="selected"<?php endif; ?>>Admin</option>
								</select>
								<p class="input-desc">Admins have permission to do anything. Be careful.</p>
							</fieldset>

						<?php endif; ?>

						<fieldset>
							<button type="submit" class="button button--primary">Save User</button>
						</fieldset>

					</div>

				</div>

			</div>

		</div>

	</div>

</form>

