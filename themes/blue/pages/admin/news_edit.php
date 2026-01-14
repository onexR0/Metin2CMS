<div class="box">
	<form method="post" action="<?= $baseUrl; ?>/admin/news/update" class="form">
		<!-- Added CSRF token for security -->
		<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
		<input type="hidden" name="id" value="<?= (int)$newsItem['id']; ?>">

		<div class="form-group">
			<label class="form-label"><?= t('title'); ?></label>
			<input type="text" name="title" class="form-input" required value="<?= htmlspecialchars($newsItem['title'], ENT_QUOTES, 'UTF-8'); ?>">
		</div>

		<div class="form-group">
			<label class="form-label"><?= t('content'); ?></label>
			<textarea name="content" class="rich-editor" rows="12"><?= htmlspecialchars($newsItem['content'], ENT_QUOTES, 'UTF-8'); ?></textarea>
		</div>

		<div class="form-actions">
			<button type="submit" class="btn btn--small"><?= t('save_button'); ?></button>
			<a href="<?= $baseUrl; ?>/admin/news" class="btn btn--small"><?= t('cancel_button'); ?></a>
		</div>
	</form>
</div>

<?php require __DIR__ . '/../../includes/ckeditor.php'; ?>
