<form action="<?php echo dashboard_url( 'menus/save/' ); ?>" method="post">

	<div class="content__banner">

		<div class="container">

			<div class="grid">

				<div class="row row--inline">

					<div class="col col--100">

						<h1 class="no-margin">New Menu</h1>

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

						<p><i class="fas fa-reply" aria-hidden="true"></i> <a href="<?php echo dashboard_url( 'menus/' ); ?>">Back to menu listings</a></p>

						<fieldset>
							<label for="name">Menu name <span class="required">*</span></label>
							<input type="text" name="name" id="name" required="required" />
						</fieldset>

						<fieldset>
							<label for="location">Menu location <span class="required">*</span></label>
							<input type="text" name="location" id="location" value="default" required="required" />
							<p class="input-desc">A location identifier defined within the theme.</p>
						</fieldset>

						<fieldset>
							<label>Menu Items <span class="required">*</span></label>
							<ul class="menu-input-list" data-row-index="0">
								<li>
									<div class="inputs">
										<input type="text" name="item[0][name]" id="item" placeholder="Item name" required="required" aria-label="Menu item text" />
										<input type="url" name="item[0][href]" id="item" placeholder="https://" required="required" aria-label="Menu item link" />
									</div>
									<div class="remove">
										<a class="remove-menu-row" role="link" tabindex="0" aria-label="Delete menu item"><i class="fas fa-trash-alt" aria-hidden="true"></i></a>
									</div>
								</li>
							</ul>
							<p class="input-desc">Both fields are required. <a class="add-menu-row" role="link" tabindex="0">Add new row</a>.</p>
						</fieldset>

						<fieldset>
							<button type="submit" class="button button--primary">Create Menu</button>
						</fieldset>

					</div>

				</div>

			</div>

		</div>

	</div>

</form>

