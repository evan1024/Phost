<!DOCTYPE html>
<html lang="<?php echo blog_lang(); ?>">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2" />
<title><?php echo load_title( $title ); ?></title>
<link rel="stylesheet" href="<?php echo theme_url( 'Assets/Css/style.css' ); ?>" />
</head>
<body id="phost">

	<div class="page">

		<header class="header">

			<div class="container">

				<div class="header__inner">

					<a href="<?php echo home_url(); ?>" class="header__logo">

						<?php echo blog_name(); ?>

					</a>

					<?php $menu = get_menu_links( 'header' ); ?>
					<?php if ( ! empty( $menu ) ) : ?>

						<a href="#" class="header__toggle">Menu</a>

						<div class="header__menu">

							<ul>

								<?php foreach ( $menu as $link ) : ?>

									<li><a href="<?php echo $link[ 'href' ]; ?>"><?php echo $link[ 'name' ]; ?></a></li>

								<?php endforeach; ?>

							</ul>

						</div>

					<?php endif; ?>

				</div>

			</div>

		</header>

		<section class="content">

			<div class="container">

				<?php Controller::view( $view, $args ); ?>

			</div>

		</section>

		<footer class="footer">

			<div class="container">

				<div class="footer__inner">

					<p>&copy; <?php echo date( 'Y' ); ?>, <?php echo blog_name(); ?></p>

					<?php $menu = get_menu_links( 'footer' ); ?>

					<nav class="footer__menu">

						<ul>

							<?php if ( ! empty( $menu ) ) : ?>

								<?php foreach ( $menu as $link ) : ?>

									<li><a href="<?php echo $link[ 'href' ]; ?>"><?php echo $link[ 'name' ]; ?></a></li>

								<?php endforeach; ?>

							<?php endif; ?>

							<?php if ( is_logged_in() ) : ?>
								<li><a href="<?php echo dashboard_url(); ?>">Dashboard</a></li>
								<li><a href="<?php echo auth_url( 'logout/' ); ?>">Log out</a></li>
							<?php else : ?>
								<li><a href="<?php echo auth_url( 'login/' ); ?>">Log in</a></li>
							<?php endif; ?>

						</ul>

					</nav>

					<p class="vanity-link"><a href="https://phost.app">Powered by Phost</a></p>

				</div>

			</div>

		</footer>

	</div>

<script src="<?php echo theme_url( 'Assets/Js/jquery.min.js' ); ?>"></script>
<script src="<?php echo theme_url( 'Assets/Js/scripts.min.js' ); ?>"></script>
</body>
</html>