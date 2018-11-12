<!DOCTYPE html>
<html lang="<?php echo blog_lang(); ?>">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2" />
<title><?php echo load_title( $title ); ?></title>
<link rel="stylesheet" type="text/css" href="<?php echo assets_url( 'Css/style.css' ); ?>" />
</head>
<body id="phost" class="phost dashboard">

	<main class="dashboard">

		<a class="header-toggle"><i class="fas fa-bars"></i> Menu</a>

		<menu class="header">

			<div class="header__gutter">

				<a href="<?php echo home_url(); ?>" class="header__logo">
					<span><?php echo blog_name(); ?></span>
				</a>

				<div class="header__search">

					<form action="<?php echo dashboard_url( 'search/' ); ?>" method="get">

						<label for="query" class="screen-reader-only">Search</label>
						<input type="search" name="query" id="query" placeholder="Search for posts..." <?php if ( get_search_query() ) : ?>value="<?php echo get_search_query(); ?>" <?php endif; ?>/>
						<button type="submit" id="submit" aria-label="Search posts"><i class="fas fa-search" aria-hidden="true"></i></button>

					</form>

				</div>

				<div class="header__menu">

					<ul>

						<li><a href="<?php echo dashboard_url(); ?>"><i class="fas fa-tachometer-alt" aria-hidden="true"></i> Dashboard</a></li>
						<li class="spacer"></li>
						<li><a href="<?php echo dashboard_url( 'posts/new/' ); ?>"><i class="fas fa-plus" aria-hidden="true"></i> New Post</a></li>
						<li><a href="<?php echo dashboard_url( 'posts/' ); ?>"><i class="fas fa-pencil-alt" aria-hidden="true"></i> Posts</a></li>
						<li><a href="<?php echo dashboard_url( 'media/' ); ?>"><i class="fas fa-image" aria-hidden="true"></i> Media</a></li>
						<li class="spacer"></li>
						<li><a href="<?php echo dashboard_url( 'users/edit/' . my_id() . '/' ); ?>"><i class="fas fa-smile" aria-hidden="true"></i> Profile</a></li>
						<?php if ( is_admin() ) : ?>
							<li><a href="<?php echo dashboard_url( 'users/' ); ?>"><i class="fas fa-user-friends" aria-hidden="true"></i> Users</a></li>
							<li><a href="<?php echo dashboard_url( 'menus/' ); ?>"><i class="fas fa-list-ol" aria-hidden="true"></i> Menus</a></li>
							<li><a href="<?php echo dashboard_url( 'settings/' ); ?>"><i class="fas fa-wrench" aria-hidden="true"></i> Settings</a></li>
							<li class="spacer"></li>
							<li><a href="<?php echo dashboard_url( 'system/' ); ?>"><i class="fas fa-university" aria-hidden="true"></i> System</a></li>
						<?php endif; ?>

					</ul>

				</div>

				<div class="header__callout">

					<a href="<?php echo auth_url( 'logout/' ); ?>" class="button"><i class="fas fa-door-open" aria-hidden="true"></i> Log Out</a>

				</div>

			</div>

		</menu>

		<section class="content">

			<div class="content__wrapper">

				<?php Controller::view( $view, $args ); ?>

			</div>

			<div class="content__footer">

				<div class="container">

					<div class="grid">

						<div class="row">

							<div class="col col--100">

								<p><a href="https://phost.app/" target="_blank">Powered by Phost</a> &mdash; <a href="<?php echo dashboard_url( 'about/' ); ?>">Version <?php echo blog_version(); ?></a></p>

							</div>

						</div>

					</div>

				</div>

			</div>

		</section>

	</main>

<script type="text/javascript" src="<?php echo assets_url( 'Js/Vendor/jquery.min.js' ); ?>"></script>
<script type="text/javascript" src="<?php echo assets_url( 'Js/editor.min.js' ); ?>"></script>
<script type="text/javascript" src="<?php echo assets_url( 'Js/scripts.min.js' ); ?>"></script>
</body>
</html>