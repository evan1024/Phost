<div class="content__banner">

	<div class="container">

		<div class="grid">

			<div class="row row--inline">

				<div class="col col--100">

					<h1 class="no-margin">Extension Manager</h1>

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

					<p><i class="fas fa-reply" aria-hidden="true"></i> <a href="<?php echo dashboard_url( 'extensions/' ); ?>">Back to extension listings</a></p>

				</div>

			</div>

			<div class="row">

				<div class="col col--100">

					<h2 class="h6"><?php echo $extension[ 'name' ]; ?></h2>

					<p><?php echo $extension[ 'description' ]; ?></p>

					<hr />

					<ul>

						<li><strong>Version:</strong> <?php echo $extension[ 'version' ]; ?></li>

						<li><strong>Author:</strong> <?php echo $extension[ 'author_name' ]; ?></li>

						<li><strong>Website:</strong> <a href="<?php echo $extension[ 'author_url' ]; ?>"><?php echo $extension[ 'author_url' ]; ?></a></li>

						<li><strong>Licence:</strong> <a href="<?php echo $extension[ 'licence_url' ]; ?>"><?php echo $extension[ 'licence_name' ]; ?></a></li>

					</ul>

					<form action="<?php echo dashboard_url( 'extensions/save/' ); ?>" method="post">

						<input type="hidden" name="domain" id="domain" value="<?php echo $extension[ 'domain' ]; ?>" />

						<?php if ( is_extension_installed( $extension[ 'domain' ] ) ) : ?>

							<button type="submit" class="button button--warning">Uninstall Extension</button>

						<?php else : ?>

							<button type="submit" class="button button--primary">Install Extension</button>

						<?php endif; ?>

					</form>

				</div>

			</div>

		</div>

	</div>

</div>

