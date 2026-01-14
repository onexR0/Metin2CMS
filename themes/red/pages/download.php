<?php if (!empty($links)): ?>
	<div class="download-table-wrapper">
		<table class="download-table">
			<thead>
				<tr>
					<th>#</th>
					<th><?= t('server'); ?></th>
					<th><?= t('download'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($links as $index => $l): ?>
					<tr>
						<td class="download-number"><?= $index + 1 ?></td>
						<td class="download-server"><?= htmlspecialchars($l['label'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
						<td class="download-action">
							<a class="btn btn--small"href="<?= htmlspecialchars($l['url'] ?? '#', ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener"> <?= t('download'); ?></a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php else: ?>
	<div id="alertBanner">
		<div class="alert-banner alert-warning" data-permanent="true">
			<?= t('no_download_links'); ?>
			<button type="button"class="alert-banner-close"onclick="closeAlert(this)">&times;</button>
		</div>
	</div>
<?php endif; ?>
