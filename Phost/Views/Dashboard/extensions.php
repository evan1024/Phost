<div class="content__banner">

	<div class="container">

		<div class="grid">

			<div class="row row--inline">

				<div class="col col--100">

					<h1 class="no-margin">Extensions</h1>

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

			<?php if ( empty( $extensions ) ) : ?>

				<div class="row">

					<div class="col col--100">

						<h2 class="h4">No extensions found</h2>

						<p>There aren't any extensions to show you right now.</p>

					</div>

				</div>

			<?php else : ?>

				<div class="row">

					<div class="col col--100">

						<table class="user-list">

							<thead>

								<tr>

									<th>Name</th>
									<th>Description</th>
									<th>Author</th>
									<th>Status</th>
									<th>Options</th>

								</tr>

							</thead>

							<tbody>

								<?php foreach ( $extensions as $extension ) : ?>

									<tr>

										<td><?php echo $extension[ 'name' ]; ?></td>
										
										<td><?php echo $extension[ 'description' ]; ?></td>
										
										<td><?php echo $extension[ 'author_name' ]; ?></td>
										
										<td>
											
											<?php if ( is_extension_installed( $extension[ 'domain' ] ) ) : ?>

												Active

											<?php else : ?>

												Inactive

											<?php endif; ?>

										</td>
										
										<td><a href="<?php echo dashboard_url( 'extensions/manage/' . $extension[ 'domain' ] . '/' ); ?>" class="button button--secondary button--small">Manage</a></td>

									</tr>

								<?php endforeach; ?>

							</tbody>

						</table>

					</div>

				</div>

			<?php endif; ?>

		</div>

	</div>

</div>

