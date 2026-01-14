<div class="box">
	<form method="post" action="<?= $baseUrl; ?>/admin/social" class="form form--narrow">
		<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
		
		<div class="form-group">
			<label class="form-label">Discord URL</label>
			<input type="text" name="discord" class="form-input" placeholder="https://discord.gg/..." value="<?= htmlspecialchars($links['discord'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
		</div>

		<div class="form-group">
			<label class="form-label">TikTok URL</label>
			<input type="text" name="tiktok" class="form-input" placeholder="https://tiktok.com/@..." value="<?= htmlspecialchars($links['tiktok'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
		</div>
		<div class="form-group">
			<label class="form-label">Ishop URL</label>
			<input type="text" name="ishop" class="form-input" placeholder="https://site.ro/ishop..." value="<?= htmlspecialchars($links['ishop'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
		</div>
		<div class="form-actions">
			<button type="submit" class="btn btn--small"><?= t('save_button'); ?></button>
		</div>
	</form>
</div>
