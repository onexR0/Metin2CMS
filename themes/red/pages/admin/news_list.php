<div class="box">
		<form method="post" action="<?= $baseUrl; ?>/admin/news/create" class="form">
			<!-- Added CSRF token for security -->
			<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
			
			<div class="form-group">
				<label class="form-label"><?= t('title'); ?></label>
				<input type="text" name="title" class="form-input" required>
			</div>

			<div class="form-group">
				<label class="form-label"><?= t('content'); ?></label>
				<textarea name="content" class="rich-editor" rows="6"></textarea>
			</div>

			<div class="form-actions">
				<button type="submit" class="btn btn--small">
					<?= t('add_button'); ?>
				</button>
			</div>
		</form>

	<h2 class="section-title"><?= t('news_list'); ?></h2>
	<?php if (!empty($news)): ?>
		<?php foreach ($news as $n): ?>
			<article class="news-item" style="margin-bottom: 20px;">
				<div class="news-title-wrapper">
					<h2><?= htmlspecialchars($n['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
				</div>

				<div class="news-content" style="max-height: 150px; overflow: hidden;">
					<?php
						$contentPreview = strip_tags($n['content']);
						if (mb_strlen($contentPreview) > 200) {
							echo htmlspecialchars(mb_substr($contentPreview, 0, 200), ENT_QUOTES, 'UTF-8') . '...';
						} else {
							echo htmlspecialchars($contentPreview, ENT_QUOTES, 'UTF-8');
						}
					?>
				</div>

				<div class="news-footer">
					<span class="news-date">
						<?= htmlspecialchars($n['created_at'], ENT_QUOTES, 'UTF-8'); ?>
					</span>
				</div>

				<div class="form-actions">
					<form method="get" action="<?= $baseUrl; ?>/admin/news/edit">
						<input type="hidden" name="id" value="<?= (int)$n['id']; ?>">
						<button type="submit" class="btn btn--small">
							<?= t('edit_button'); ?>
						</button>
					</form>

					<form method="post" action="<?= $baseUrl; ?>/admin/news/delete">
						<!-- Added CSRF token for security -->
						<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
						<input type="hidden" name="id" value="<?= (int)$n['id']; ?>">
						<button type="submit" class="btn btn--small">
							<?= t('delete_button'); ?>
						</button>
					</form>
				</div>
			</article>
		<?php endforeach; ?>
	<?php endif; ?>
</div>

<?php require __DIR__ . '/../../includes/ckeditor.php'; ?>
