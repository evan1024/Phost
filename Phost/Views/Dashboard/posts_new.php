<form action="<?php echo dashboard_url( 'posts/save/' ); ?>" method="post">

	<div class="content__banner">

		<div class="container">

			<div class="grid">

				<div class="row row--inline">

					<div class="col col--50">

						<h1 class="no-margin">New Post</h1>

					</div>

					<div class="col col--50 text--right">

						<button type="submit" class="button button--primary">Create Post</button>

					</div>

				</div>

			</div>

		</div>

	</div>

	<div class="editor">

		<div class="editor__container">

			<?php echo do_notices(); ?>

			<div class="editor__toolbar">

				<ul>

					<li><a class="toolbar-item toolbar-heading" id="heading" data-syntax="# "><i class="fas fa-heading"></i></a></li>
					<li><a class="toolbar-item toolbar-bold" id="bold" data-syntax="****"><i class="fas fa-bold"></i></a></li>
					<li><a class="toolbar-item toolbar-italic" id="italic" data-syntax="**"><i class="fas fa-italic"></i></a></li>
					<li><a class="toolbar-item toolbar-image" id="image" data-syntax="![]()"><i class="fas fa-image"></i></a></li>
					<li><a class="toolbar-item toolbar-quote" id="quote" data-syntax="> "><i class="fas fa-quote-left"></i></a></li>
					<li><a class="toolbar-item toolbar-list" id="list" data-syntax="- "><i class="fas fa-list"></i></a></li>
					<li><a class="toolbar-item toolbar-link" id="link" data-syntax="[]()"><i class="fas fa-link"></i></a></li>
					<li><a class="toolbar-item toolbar-code" id="code" data-syntax="``"><i class="fas fa-code"></i></a></li>

				</ul>

			</div>

			<div class="editor__content">

				<div class="editor__inputs">

					<input type="text" name="title" id="title" class="editor__title" placeholder="Your post title..." />

					<textarea name="content" id="editor__textarea" class="editor__textarea" placeholder="Start writing your story..."></textarea>

				</div>

				<div class="editor__settings">

					<fieldset>
						<label for="path">Path</label>
						<input type="text" name="path" id="path" />
						<p class="input-desc">Example: <code>hello-world</code></p>
					</fieldset>

					<fieldset>
						<label for="published_at">Publish Date</label>
						<input type="datetime-local" name="published_at" id="published_at" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}" />
					</fieldset>

					<fieldset>
						<label for="status">Status</label>
						<select name="status" id="status">
							<option value="publish">Published</option>
							<option value="draft" selected="selected">Draft</option>
						</select>
					</fieldset>

					<fieldset>
						<label for="tags">Tags</label>
						<input type="text" name="tags" id="tags" />
						<p class="input-desc">Separate tags with a comma.</p>
					</fieldset>

					<fieldset>
						<label for="author_id">Author</label>
						<select name="author_id" id="author_id">
							<?php foreach ( $users as $user ) : ?>
								<option value="<?php echo $user->ID; ?>"><?php echo $user->user_fullname; ?></option>
							<?php endforeach; ?>
						</select>
					</fieldset>

					<fieldset>
						<label for="type">Type</label>
						<select name="type" id="type">
							<?php foreach ( $post_types as $post_type ) : ?>
								<option value="<?php echo $post_type[ 'id' ]; ?>"><?php echo $post_type[ 'labels' ][ 'singular' ]; ?></option>
							<?php endforeach; ?>
						</select>
					</fieldset>

					<hr />

					<p>Characters: <span id="editor__charcount">0</span></p>

				</div>

			</div>

		</div>

	</div>

</form>

