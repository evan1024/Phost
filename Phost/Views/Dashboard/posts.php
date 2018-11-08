<div class="content__banner">

	<div class="container">

		<div class="grid">

			<div class="row row--inline">

				<div class="col col--50 col-mob--100">

					<h1 class="no-margin">Posts</h1>

				</div>

				<div class="col col--50 col-mob--100 text--right text-mob--left">

					<a href="<?php echo dashboard_url( 'posts/new/' ); ?>" class="button button--primary">Create Post</a>

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

					<form action="<?php echo dashboard_url( 'posts/' ); ?>" method="get">

						<fieldset class="inline">

							<select name="type" id="type">
								<option value>&mdash; Type &mdash;</option>
								<?php foreach ( $post_types as $post_type ) : ?>
									<option value="<?php echo $post_type[ 'id' ]; ?>"<?php if ( $post_type[ 'id' ] == $type_filter ) : ?> selected="selected"<?php endif; ?>><?php echo $post_type[ 'labels' ][ 'singular' ]; ?></option>
								<?php endforeach; ?>
							</select>

						</fieldset>

						<fieldset class="inline">

							<select name="status" id="status">
								<option value>&mdash; Status &mdash;</option>
								<option value="publish"<?php if ( 'publish' == $status_filter ) : ?> selected="selected"<?php endif; ?>>Published</option>
								<option value="draft"<?php if ( 'draft' == $status_filter ) : ?> selected="selected"<?php endif; ?>>Drafted</option>
							</select>

						</fieldset>

						<fieldset class="inline">
							
							<button type="submit" id="submit" class="button button--secondary">Filter</button>

						</fieldset>

					</form>

				</div>

			</div>

			<?php if ( empty( $posts ) ) : ?>

				<div class="row">

					<div class="col col--100">

						<h2 class="h4">No posts found</h2>

						<p>There aren't any posts to show you right now.</p>

					</div>

				</div>

			<?php else : ?>

				<div class="row">

					<div class="col col--100">

						<table class="user-list">

							<thead>

								<tr>

									<th>ID</th>
									<th>Title</th>
									<th>Author</th>
									<th>Type</th>
									<th>Status</th>
									<th>Publish Date</th>
									<th>View</th>

								</tr>

							</thead>

							<tbody>

								<?php foreach ( $posts as $post ) : ?>

									<tr>

										<td><?php echo $post->ID; ?></td>
										<td><a href="<?php echo dashboard_url( 'posts/edit/' . $post->ID . '/' ); ?>"><?php echo $post->post_title; ?></a></td>
										<td>
											<?php

												$user = new User;
												$user->fetch( $post->post_author_ID );

												if ( '' == $user->user_fullname ) {

													echo "&mdash;";

												} else {

													echo '<a href="' . dashboard_url( 'users/edit/' . $user->ID . '/' ) . '">' . $user->user_fullname . '</a>';

												}

											?>
										</td>
										<td>
											<?php
												$post_type = new PostTypes;
												$post_type = $post_type->get( $post->post_type );
												echo $post_type['labels']['singular'];
											?>
										</td>
										<td>
											<?php if ( 'publish' == $post->post_status ) : ?>
												Published
											<?php else : ?>
												Draft
											<?php endif; ?>
										</td>
										<td><abbr title="<?php echo date( 'jS F Y, H:i:s', strtotime( $post->published_at ) ); ?>"><?php echo date( 'Y-m-d', strtotime( $post->published_at ) ); ?></abbr></td>
										<td>
											<a href="<?php echo post_url( $post ); ?>" class="button button--secondary button--small">View <?php echo $post_type['labels']['singular']; ?></a>
										</td>

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

							<?php if ( get_pagination_link( dashboard_url( 'posts/' ), 'previous' ) ) : ?>

								<a href="<?php echo get_pagination_link( dashboard_url( 'posts/' ), 'previous' ); ?>" class="button button--tertiary button--small">&laquo; Previous</a>

							<?php endif; ?>

						</div>

						<div class="col col--50 text--right">

							<?php if ( get_pagination_link( dashboard_url( 'posts/' ), 'next' ) ) : ?>

								<a href="<?php echo get_pagination_link( dashboard_url( 'posts/' ), 'next' ); ?>" class="button button--tertiary button--small">Next &raquo;</a>

							<?php endif; ?>

						</div>

					</div>

				<?php endif; ?>

			<?php endif; ?>

		</div>

	</div>

</div>

