<div class="content__banner">

	<div class="container">

		<div class="grid">

			<div class="row row--inline">

				<div class="col col--100">

					<h1 class="no-margin">System</h1>

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

			<div class="row">

				<div class="col col--100">

					<h2 class="h6">Updates</h2>

					<p>Last checked for core updates at <strong><?php echo date( 'jS F Y, H:i:s', strtotime( update_check() ) ); ?></strong>.</p>

					<?php if ( update_available() ) : ?>

						<p>Your version of Phost is out of date. <strong>Please update now to version <?php echo blog_setting( 'update_available' ); ?>.</strong></p>

						<p><a href="<?php echo dashboard_url( 'system/update-core/' ); ?>" class="button button--primary">Update Phost</a></p>

					<?php else : ?>

						<p><a href="<?php echo dashboard_url( 'system/check-updates/' ); ?>" class="button button--secondary">Check for updates</a></p>

					<?php endif; ?>

					<ul>

						<li>Your currently running Phost version <?php echo blog_version(); ?>.</li>

						<li>Your server is running PHP version <?php echo PHP_VERSION; ?>.</li>

						<li>Automatic updates checks are currently <?php if ( auto_updates() ) : ?>enabled<?php else : ?>disabled<?php endif; ?>.</li>

					</ul>

				</div>

			</div>

		</div>

	</div>

</div>

