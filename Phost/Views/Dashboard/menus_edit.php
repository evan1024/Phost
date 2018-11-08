<form action="<?php echo dashboard_url( 'menus/save/' ); ?>" method="post">
<input type="hidden" name="id" id="id" value="<?php echo $menu->ID; ?>" />

	<div class="content__banner">

		<div class="container">

			<div class="grid">

				<div class="row row--inline">

					<div class="col<?php if ( is_admin() ) : ?> col--50<?php else : ?> col--100<?php endif; ?>">

						<h1 class="no-margin">Edit Menu</h1>

					</div>

					<?php if ( is_admin() ) : ?>

						<div class="col col--50 text--right">

							<a href="<?php echo csrfify_url( dashboard_url( 'menus/delete/' . $menu->ID . '/' ) ); ?>" class="button button--warning js-delete-warn">Delete</a>

						</div>

					<?php endif; ?>

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

						<p><i class="fas fa-reply"></i> <a href="<?php echo dashboard_url( 'menus/' ); ?>">Back to menu listings</a></p>

						<fieldset>
							<label for="name">Menu name <span class="required">*</span></label>
							<input type="text" name="name" id="name" value="<?php echo $menu->menu_name; ?>" required="required" />
						</fieldset>

						<fieldset>
							<label for="location">Menu location <span class="required">*</span></label>
							<input type="text" name="location" id="location" value="<?php echo $menu->menu_location; ?>" required="required" />
							<p class="input-desc">The location identifier defined by your chosen theme.</p>
						</fieldset>

						<fieldset>
							<label>Menu Items <span class="required">*</span></label>
							<?php if ( ! empty( $menu->menu_list ) ) : ?>
								<ul class="menu-input-list" data-row-index="<?php echo count( $menu->menu_list ); ?>">
									<?php $menu_index = 0; ?>
									<?php foreach ( $menu->menu_list as $item ) : ?>
										<li>
											<div class="inputs">
												<input type="text" name="item[<?php echo $menu_index; ?>][name]" id="item" value="<?php echo $item[ 'name' ]; ?>" placeholder="Text" required="required" />
												<input type="url" name="item[<?php echo $menu_index; ?>][href]" id="item" value="<?php echo $item[ 'href' ]; ?>" placeholder="https://" required="required" />
											</div>
											<div class="remove">
												<a class="remove-menu-row" role="link" tabindex="0"><i class="fas fa-trash-alt"></i></a>
											</div>
										</li>
										<?php $menu_index++; ?>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
							<p class="input-desc">Both fields are required. <a class="add-menu-row" role="link" tabindex="0">Add new row</a>.</p>
						</fieldset>

						<fieldset>
							<button type="submit" class="button button--primary">Save Menu</button>
						</fieldset>

					</div>

				</div>

			</div>

		</div>

	</div>

</form>

