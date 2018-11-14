<article class="page page-<?php echo $page->ID; ?>" id="page-<?php echo $page->ID; ?>">

	<div class="page__heading">

		<h1 class="heading heading--emphasis"><?php echo $page->post_title; ?></h1>

	</div>

	<div class="page__content">

		<?php echo markify_content( $page ); ?>

	</div>

</article>
