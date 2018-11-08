<!DOCTYPE html>
<html lang="<?php echo blog_lang(); ?>">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2" />
<title><?php echo load_title( $title ); ?></title>
<link rel="stylesheet" type="text/css" href="<?php echo assets_url( 'Css/style.css' ); ?>" />
</head>
<body id="phost" class="phost auth">

	<div class="container">

		<div class="auth">

			<a href="<?php echo home_url(); ?>" class="auth__logo">
				<span><?php echo blog_name(); ?></span>
			</a>

			<?php echo do_notices(); ?>

			<div class="auth__form">

				<?php Controller::view( $view, $args ); ?>

			</div>

		</div>

	</div>

<script type="text/javascript" src="<?php echo assets_url( 'Js/jquery.min.js' ); ?>"></script>
<script type="text/javascript" src="<?php echo assets_url( 'Js/scripts.min.js' ); ?>"></script>
</body>
</html>