<div class="content__banner">

	<div class="container">

		<div class="grid">

			<div class="row row--inline">

				<div class="col col--50 col-mob--100">

					<h1 class="no-margin">Menus</h1>

				</div>

				<div class="col col--50 col-mob--100 text--right text-mob--left">

					<a href="<?php echo dashboard_url( 'menus/new/' ); ?>" class="button button--primary">Create Menu</a>

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

			<?php if ( empty( $menus ) ) : ?>

				<div class="row">

					<div class="col col--100">

						<h2 class="h4">No menus found</h2>

						<p>There aren't any menus to show you right now.</p>

					</div>

				</div>

			<?php else : ?>

				<div class="row">

					<div class="col col--100">

						<table class="user-list">

							<thead>

								<tr>

									<th>ID</th>
									<th>Name</th>
									<th>Location</th>
									<th>Links</th>
									<th>Created</th>
									<th>Updated</th>

								</tr>

							</thead>

							<tbody>

								<?php foreach ( $menus as $menu ) : ?>

									<tr>

										<td><?php echo $menu->ID; ?></td>
										<td><a href="<?php echo dashboard_url( 'menus/edit/' . $menu->ID . '/' ); ?>"><?php echo $menu->menu_name; ?></a></td>
										<td><code><?php echo $menu->menu_location; ?></code></td>
										<td><?php echo count( $menu->menu_list ); ?></td>
										<td><abbr title="<?php echo date( 'jS F Y, H:i:s', strtotime( $menu->created_at ) ); ?>"><?php echo date( 'Y-m-d', strtotime( $menu->created_at ) ); ?></abbr></td>
										<td><abbr title="<?php echo date( 'jS F Y, H:i:s', strtotime( $menu->updated_at ) ); ?>"><?php echo date( 'Y-m-d', strtotime( $menu->updated_at ) ); ?></abbr></td>

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

