<div class="ranking-table-wrapper">
	<table class="ranking-table">
		<thead>
			<tr>
				<th>#</th>
				<th><?= t('name'); ?></th>
				<th><?= t('leader'); ?></th>
				<th><?= t('level'); ?></th>
				<th><?= t('points'); ?></th>
				<th><?= t('kingdom'); ?></th>
			</tr>
		</thead>

		<tbody>
		<?php if (!empty($guilds)): ?>
			<?php foreach ($guilds as $index => $g): ?>
				<?php
					$empire = isset($g['kingdom']) ? (int)$g['kingdom'] : 1;
					if ($empire < 1 || $empire > 3) {
						$empire = 1;
					}

					$empireImage = $assetsUrl . '/empire/' . $empire . '.jpg';
				?>
				<tr>
					<td><?= ($page - 1) * 20 + $index + 1; ?></td>

					<td class="player-name-cell">
						<?= htmlspecialchars($g['name'], ENT_QUOTES, 'UTF-8'); ?>
					</td>

					<td>
						<?= htmlspecialchars($g['leader_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
					</td>

					<td class="level-cell">
						<?= (int)$g['level']; ?>
					</td>

					<td class="playtime-cell">
						<?= (int)$g['ladder_point']; ?>
					</td>

					<td class="kingdom-cell">
						<img src="<?= $empireImage; ?>"
							 alt="Empire <?= $empire; ?>"
							>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php else: ?>
			<tr>
				<td colspan="6" class="no-news-message">
					<?= t('no_guilds_found'); ?>
				</td>
			</tr>
		<?php endif; ?>
		</tbody>
	</table>
</div>

<?php if ($maxPage > 1): ?>
	<div class="pagination">
		<?php if ($page > 1): ?>
			<a href="<?= $baseUrl; ?>/ranking-guilds?page=<?= $page - 1; ?>" class="pagination-btn">&#8678;</a>
		<?php endif; ?>

		<?php for ($i = 1; $i <= $maxPage; $i++): ?>
			<?php if ($i == $page): ?>
				<span class="pagination-current"><?= $i; ?></span>
			<?php else: ?>
				<a href="<?= $baseUrl; ?>/ranking-guilds?page=<?= $i; ?>" class="pagination-btn"><?= $i; ?></a>
			<?php endif; ?>
		<?php endfor; ?>

		<?php if ($page < $maxPage): ?>
			<a href="<?= $baseUrl; ?>/ranking-guilds?page=<?= $page + 1; ?>" class="pagination-btn">&#8680;</a>
		<?php endif; ?>
	</div>
<?php endif; ?>
