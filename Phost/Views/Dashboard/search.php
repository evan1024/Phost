<div class="content__banner">

	<div class="container">

		<div class="grid">

			<div class="row row--inline">

				<div class="col col--100">

					<h1 class="no-margin">Search</h1>

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

			<?php if ( empty( $posts ) ) : ?>

				<div class="row">

					<div class="col col--100">

						<h2 class="h4">No posts found</h2>

						<p>Your search query didn't return any results.</p>

					</div>

				</div>

			<?php else : ?>

				<div class="row">

					<div class="col col--100">

						<?php if ( get_search_query() ) : ?>

							<p>Showing search results for: <em><?php echo get_search_query(); ?></em></p>

						<?php else : ?>

							<p>You didn't enter a search query.</p>

						<?php endif; ?>

					</div>

				</div>

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

						<div class="col col--50">

							<?php if ( get_pagination_link( dashboard_url( 'search/' ), 'previous' ) ) : ?>

								<a href="<?php echo get_pagination_link( dashboard_url( 'search/' ), 'previous' ); ?>" class="button button--tertiary button--small">&laquo; Previous</a>

							<?php endif; ?>

						</div>

						<div class="col col--50 text--right">

							<?php if ( get_pagination_link( dashboard_url( 'search/' ), 'next' ) ) : ?>

								<a href="<?php echo get_pagination_link( dashboard_url( 'search/' ), 'next' ); ?>" class="button button--tertiary button--small">Next &raquo;</a>

							<?php endif; ?>

						</div>

					</div>

				<?php endif; ?>

			<?php endif; ?>

		</div>

	</div>

</div>

