<div class="content__banner">

	<div class="container">

		<div class="grid">

			<div class="row row--inline">

				<div class="col col--100">

					<h1 class="no-margin">Media</h1>

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

					<form action="<?php echo dashboard_url( 'media/upload/' ); ?>" method="post" enctype="multipart/form-data">

						<fieldset class="inline">

							<label for="upload">
								<input type="file" name="upload[]" id="upload" multiple />
							</label>

						</fieldset>

						<fieldset class="inline">

							<button type="submit" class="button button--primary">Upload Files</button>

						</fieldset>

					</form>

					<hr />

					<?php if ( $media ) : ?>

						<ul class="media__gallery">

							<?php foreach ( $media as $file ) : ?>

								<li class="media__item">

									<a href="<?php echo dashboard_url( 'media/details/' . $file->ID . '/' ); ?>" style="background-image: url('<?php echo $file->get_url(); ?>');">

										<span><?php echo $file->get_filename(); ?></span>
										
										<br />
										
										<span><?php echo $file->get_size(); ?></span>

									</a>

								</li>

							<?php endforeach; ?>

						</ul>

					<?php else : ?>

						<h2 class="h4">No files found</h2>

						<p>There aren't any files to show you right now.</p>

					<?php endif; ?>

				</div>

			</div>

			<?php if ( 1 < get_total_pages() ) : ?>

				<div class="row">

					<div class="col col--100">

						<hr />

					</div>

					<div class="col col--50">

						<?php if ( get_pagination_link( dashboard_url( 'media/' ), 'previous' ) ) : ?>

							<a href="<?php echo get_pagination_link( dashboard_url( 'media/' ), 'previous' ); ?>" class="button button--tertiary button--small">&laquo; Previous</a>

						<?php endif; ?>

					</div>

					<div class="col col--50 text--right">

						<?php if ( get_pagination_link( dashboard_url( 'media/' ), 'next' ) ) : ?>

							<a href="<?php echo get_pagination_link( dashboard_url( 'media/' ), 'next' ); ?>" class="button button--tertiary button--small">Next &raquo;</a>

						<?php endif; ?>

					</div>

				</div>

			<?php endif; ?>

		</div>

	</div>

</div>

