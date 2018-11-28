<div class="content__banner">

	<div class="container">

		<div class="grid">

			<div class="row row--inline">

				<div class="col col--100">

					<h1 class="no-margin">Dashboard</h1>

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

			<div class="row row--centered">

				<div class="col col--50 col-tab--75 col-tab--100">

					<div class="dashboard__card">

						<?php if ( '' == $me->user_fullname ) : ?>

							<p>Welcome mysterious person 👋.</p>

						<?php else : ?>

							<p>Welcome back, <strong><?php echo $me->user_fullname; ?></strong> 👋.</p>

						<?php endif; ?>

						<p>You last logged in on the <abbr title="<?php echo $me->auth_at; ?>"><?php echo date( 'jS F Y, H:i', strtotime( $me->auth_at ) ); ?></abbr>.</p>

						<?php if ( is_author() ) : ?>

							<p>Are you ready to <a href="<?php echo dashboard_url( 'posts/new/' ); ?>">start writing your next story</a>?</p>

							<hr />

							<h2 class="h6">Statistics</h2>

							<ul>

								<li><strong><?php echo count( $posts ); ?></strong> posts have been published.</li>

								<li><strong><?php echo count( $media ); ?></strong> files have been uploaded.</li>

								<?php if ( is_admin() ) : ?>

									<li><strong><?php echo count( $users ); ?></strong> users have been created.</li>

								<?php endif; ?>

							</ul>

						<?php endif; ?>

					</div>

				</div>

			</div>

		</div>

	</div>

</div>

