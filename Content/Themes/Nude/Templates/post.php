<article class="post post-<?php echo $post->ID; ?>" id="post-<?php echo $post->ID; ?>">

	<h1 class="post__heading"><?php echo $post->post_title; ?></h1>

	<p class="post__author">By <?php echo $author->user_fullname; ?></p>

	<time class="post__timestamp">Published on <?php echo date( 'jS F Y', strtotime( $post->published_at ) ); ?></time>

	<div class="post__content">

		<?php echo markify_content( $post ); ?>

	</div>

</article>
