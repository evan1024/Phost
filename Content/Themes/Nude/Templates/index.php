<h1 class="heading">Blog</h1>

<?php if ( ! empty( $posts ) ) : ?>

	<div class="post-list">

		<ul>

			<?php foreach ( $posts as $post ) : ?>

				<li>
					<article class="post post-<?php echo $post->ID; ?>" id="post-<?php echo $post->ID; ?>">
						<h2 class="h3 post__heading"><a href="<?php echo post_url( $post ); ?>"><?php echo $post->post_title; ?></a></h2>
						<time class="post__timestamp"><?php echo date( 'jS F Y', strtotime( $post->published_at ) ); ?></time>
						<div class="post__content">
							<p><?php echo content_excerpt( $post, 200 ); ?></p>
						</div>
					</article>
				</li>

			<?php endforeach; ?>

		</ul>

	</div>

	<?php if ( 1 < get_total_pages() ) : ?>

		<ul class="pagination pagination--below">

			<?php if ( get_pagination_link( home_url(), 'previous' ) ) : ?>

				<li class="pagination__item">
					<a href="<?php echo get_pagination_link( home_url(), 'previous' ); ?>" class="button">&laquo; Previous</a>
				</li>

			<?php endif; ?>

			<?php if ( get_pagination_link( home_url(), 'next' ) ) : ?>

				<li class="pagination__item">
					<a href="<?php echo get_pagination_link( home_url(), 'next' ); ?>" class="button">Next &raquo;</a>
				</li>

			<?php endif; ?>

		</ul>

	<?php endif; ?>

<?php else : ?>

	<h2 class="h5">No posts found</h2>

	<p>There aren't any posts to show you right now, sorry.</p>

<?php endif; ?>
