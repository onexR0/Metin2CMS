<div class="ranking-table-wrapper">
	<table class="ranking-table">
		<thead>
			<tr>
				<th>#</th>
				<th><?= t('name'); ?></th>
				<th><?= t('level'); ?></th>
				<th><?= t('play_time'); ?></th>
				<th><?= t('kingdom'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if (!empty($players)): ?>
				<?php foreach ($players as $index => $p): ?>
					<?php
						$rank = ($page - 1) * 20 + $index + 1;

						$playtimeMinutes = isset($p['playtime'])
							? floor((int)$p['playtime'] / 60)
							: 0;

						$empire = isset($p['empire']) ? (int)$p['empire'] : 1;
						if ($empire < 1 || $empire > 3) {
							$empire = 1;
						}

						$empireImage = $assetsUrl . '/empire/' . $empire . '.jpg';
					?>
					<tr>
						<td><?= $rank; ?></td>

						<td class="player-name-cell">
							<?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?>
						</td>

						<td class="level-cell">
							<?= (int)$p['level']; ?>
						</td>

						<td class="playtime-cell">
							<?= $playtimeMinutes; ?>
						</td>

						<td class="kingdom-cell">
							<img src="<?= $empireImage; ?>"
								 alt="Empire <?= $empire; ?>"
								 class="kingdom-map-icon">
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan="5" class="no-news-message">
						<?= t('no_players_found'); ?>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>

<?php if ($maxPage > 1): ?>
	<div class="pagination">
		<?php if ($page > 1): ?>
			<a href="<?= $baseUrl; ?>/ranking-players?page=<?= $page - 1; ?>" class="pagination-btn">&#8678;</a>
		<?php endif; ?>

		<?php for ($i = 1; $i <= $maxPage; $i++): ?>
			<?php if ($i == $page): ?>
				<span class="pagination-current"><?= $i; ?></span>
			<?php else: ?>
				<a href="<?= $baseUrl; ?>/ranking-players?page=<?= $i; ?>" class="pagination-btn"><?= $i; ?></a>
			<?php endif; ?>
		<?php endfor; ?>

		<?php if ($page < $maxPage): ?>
			<a href="<?= $baseUrl; ?>/ranking-players?page=<?= $page + 1; ?>" class="pagination-btn">&#8680;</a>
		<?php endif; ?>
	</div>
<?php endif; ?>
