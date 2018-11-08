<div class="content__banner">

	<div class="container">

		<div class="grid">

			<div class="row row--inline">

				<div class="col col--50 col-mob--100">

					<h1 class="no-margin">Users</h1>

				</div>

				<div class="col col--50 col-mob--100 text--right text-mob--left">

					<a href="<?php echo dashboard_url( 'users/new/' ); ?>" class="button button--primary">Create User</a>

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

					<form action="<?php echo dashboard_url( 'users/' ); ?>" method="get">

						<fieldset class="inline">

							<select name="type" id="type">
								<option value>&mdash; Type &mdash;</option>
								<option value="user"<?php if ( 'user' == $type_filter ) : ?> selected="selected"<?php endif; ?>>Users</option>
								<option value="admin"<?php if ( 'admin' == $type_filter ) : ?> selected="selected"<?php endif; ?>>Admins</option>
							</select>

						</fieldset>

						<fieldset class="inline">
							
							<button type="submit" id="submit" class="button button--secondary">Filter</button>

						</fieldset>

					</form>

				</div>

			</div>

			<?php if ( empty( $users ) ) : ?>

				<div class="row">

					<div class="col col--100">

						<h2 class="h4">No users found</h2>

						<p>There aren't any users to show you right now.</p>

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
									<th>Email Address</th>
									<th>Type</th>
									<th>Last Logged In</th>
									<th>Registered</th>

								</tr>

							</thead>

							<tbody>

								<?php foreach ( $users as $user ) : ?>

									<tr>

										<td><?php echo $user->ID; ?></td>
										<td><a href="<?php echo dashboard_url( 'users/edit/' . $user->ID . '/' ); ?>"><?php echo $user->user_fullname; ?></a></td>
										<td><a href="mailto:<?php echo $user->user_email; ?>"><?php echo $user->user_email; ?></a></td>
										<td>
											<?php if ( 'admin' == $user->user_type ) : ?>
												Admin
											<?php else : ?>
												User
											<?php endif; ?>
										</td>
										<td><abbr title="<?php echo date( 'jS F Y, H:i:s', strtotime( $user->auth_at ) ); ?>"><?php echo date( 'Y-m-d', strtotime( $user->auth_at ) ); ?></abbr></td>
										<td><abbr title="<?php echo date( 'jS F Y, H:i:s', strtotime( $user->created_at ) ); ?>"><?php echo date( 'Y-m-d', strtotime( $user->created_at ) ); ?></abbr></td>

									</tr>

								<?php endforeach; ?>

							</tbody>

						</table>

					</div>

				</div>

				<?php if ( 1 < get_total_pages() ) : ?>

					<div class="row">

						<div class="col col--100">

							<hr />

						</div>

						<div class="col col--50">

							<?php if ( get_pagination_link( dashboard_url( 'users/' ), 'previous' ) ) : ?>

								<a href="<?php echo get_pagination_link( dashboard_url( 'users/' ), 'previous' ); ?>" class="button button--tertiary button--small">&laquo; Previous</a>

							<?php endif; ?>

						</div>

						<div class="col col--50 text--right">

							<?php if ( get_pagination_link( dashboard_url( 'users/' ), 'next' ) ) : ?>

								<a href="<?php echo get_pagination_link( dashboard_url( 'users/' ), 'next' ); ?>" class="button button--tertiary button--small">Next &raquo;</a>

							<?php endif; ?>

						</div>

					</div>

				<?php endif; ?>

			<?php endif; ?>

		</div>

	</div>

</div>

