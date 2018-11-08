<h1 class="heading">Tag: <?php echo $tag->tag_name; ?></h1>

<?php if ( ! empty( $posts ) ) : ?>

	<div class="post-list">

		<ul>

			<?php foreach ( $posts as $post ) : ?>

				<li><a href="<?php echo post_url( $post ); ?>"><?php echo $post->post_title; ?></a> on the <time><?php echo date( 'jS F Y', strtotime( $post->published_at ) ); ?></time></li>

			<?php endforeach; ?>

		</ul>

	</div>

	<?php if ( 1 < get_total_pages() ) : ?>

		<?php if ( get_pagination_link( home_url( 'tag/' . $tag->tag_name . '/' ), 'previous' ) ) : ?>

			<a href="<?php echo get_pagination_link( home_url( 'tag/' . $tag->tag_name . '/' ), 'previous' ); ?>" class="button">&laquo; Previous</a>

		<?php endif; ?>

		<?php if ( get_pagination_link( home_url( 'tag/' . $tag->tag_name . '/' ), 'next' ) ) : ?>

			<a href="<?php echo get_pagination_link( home_url( 'tag/' . $tag->tag_name . '/' ), 'next' ); ?>" class="button">Next &raquo;</a>

		<?php endif; ?>

	<?php endif; ?>

<?php else : ?>

	<h2 class="h5">No posts found</h2>

	<p>There aren't any posts to show you right now, sorry.</p>

<?php endif; ?>
