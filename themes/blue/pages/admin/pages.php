<div class="box">
	<form method="post" action="<?= $baseUrl; ?>/admin/pages/create" class="form">
		<!-- Added CSRF token for security -->
		<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

		<div class="form-group">
			<label class="form-label"><?= t('title'); ?></label>
			<input type="text" name="title" class="form-input" required>
		</div>

		<div class="form-group">
			<label class="form-label"><?= t('content'); ?></label>
			<textarea name="content" class="rich-editor" rows="8"></textarea>
		</div>

		<div class="form-actions">
			<button type="submit" class="btn"><?= t('save_button'); ?></button>
		</div>
	</form>

	<h2 class="section-title"><?= t('pages'); ?></h2>
	<?php if (!empty($pages)): ?>
		<?php foreach ($pages as $p): ?>
			<article class="news-item" style="margin-bottom: 20px;">
				<div class="news-title-wrapper">
					<h2><?= htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
				</div>

				<div class="news-content" style="max-height: 120px; overflow: hidden;">
					<?php
						if (mb_strlen($p['content']) > 200) {
							echo mb_substr($p['content'], 0, 200) . '...';
						} else {
							echo $p['content'];
						}
					?>
				</div>

				<div class="news-footer">
					<span class="news-date">
						<?= htmlspecialchars($p['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
					</span>
				</div>

				<div class="form-actions">
					<a href="<?= $baseUrl; ?>/page?slug=<?= htmlspecialchars($p['slug'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="btn btn--admin">
						<?= t('view_button'); ?>
					</a>

					<a href="<?= $baseUrl; ?>/admin/pages/edit?id=<?= (int)$p['id']; ?>"
					   class="btn btn--admin">
						<?= t('edit_button'); ?>
					</a>

					<form method="post" action="<?= $baseUrl; ?>/admin/pages/delete" onsubmit="return confirm('Ștergi pagina?');">
						<!-- Added CSRF token for security -->
						<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
						<input type="hidden" name="id" value="<?= (int)$p['id']; ?>">
						<button type="submit" class="btn btn--admin">
							<?= t('delete_button'); ?>
						</button>
					</form>
				</div>
			</article>
		<?php endforeach; ?>
	<?php endif; ?>
</div>

<?php require __DIR__ . '/../../includes/ckeditor.php'; ?>
