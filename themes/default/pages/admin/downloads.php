<div>
	<form method="post" action="<?= $baseUrl; ?>/admin/downloads/create" class="form">
		<!-- Added CSRF token for security -->
		<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
		
		<div class="form-row">
			<div class="form-group">
				<label class="form-label"><?= t('name'); ?></label>
				<input type="text" name="label" class="form-input" required>
			</div>

			<div class="form-group">
				<label class="form-label">URL</label>
				<input type="text" name="url" class="form-input" required>
			</div>

			<div class="form-group">
				<button type="submit" class="btn">
					<?= t('add_button'); ?>
				</button>
			</div>
		</div>
	</form>

<h2 class="section-title"><?= t('existing_links'); ?></h2>
	<?php if (!empty($links)): ?>
		<?php foreach ($links as $l): ?>
			<div class="panel" style="margin-bottom: 10px;">
				<div class="panel__body">
					<div class="flex-between">
						<div>
							<div style="font-weight:600; color: var(--clr-accent); font-size: 14px;">
								<?= htmlspecialchars($l['label'], ENT_QUOTES, 'UTF-8'); ?>
							</div>
							<div style="margin-top: 5px; font-size: 12px;">
								<a href="<?= htmlspecialchars($l['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank">
									<?= htmlspecialchars($l['url'], ENT_QUOTES, 'UTF-8'); ?>
								</a>
							</div>
						</div>

						<form method="post"
							  action="<?= $baseUrl; ?>/admin/downloads/delete">
							<!-- Added CSRF token for security -->
							<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
							<input type="hidden" name="id" value="<?= (int)$l['id']; ?>">
							<button type="submit" class="btn btn--small">
								<?= t('delete_button'); ?>
							</button>
						</form>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
