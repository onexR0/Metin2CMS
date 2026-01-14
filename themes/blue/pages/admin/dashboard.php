<nav class="admin-nav">
	<a href="<?= $baseUrl; ?>/admin/news"		class="btn btn--small"><?= t('news'); ?></a>
	<a href="<?= $baseUrl; ?>/admin/downloads"	class="btn btn--small"><?= t('download'); ?></a>
	<a href="<?= $baseUrl; ?>/admin/settings"	class="btn btn--small"><?= t('settings'); ?></a>
	<a href="<?= $baseUrl; ?>/admin/pages"		class="btn btn--small"><?= t('pages'); ?></a>
	<a href="<?= $baseUrl; ?>/admin/social"		class="btn btn--small"><?= t('links'); ?></a>
	<a href="<?= $baseUrl; ?>/admin/accounts"	class="btn btn--small"><?= t('accounts'); ?></a>
	<a href="<?= $baseUrl; ?>/admin/coins"		class="btn btn--small">MD/JD</a>
</nav>

<div class="admin-stats-grid">
	<div class="admin-stat-card">
		<div class="admin-stat-card__value">
			<?= number_format((int)($stats['accounts_total'] ?? 0)); ?>
		</div>
		<div class="admin-stat-card__label">
			<?= t('stats.accounts_created'); ?>
		</div>
	</div>

	<div class="admin-stat-card">
		<div class="admin-stat-card__value">
			<?= number_format((int)($stats['characters_total'] ?? 0)); ?>
		</div>
		<div class="admin-stat-card__label">
			<?= t('stats.characters_created'); ?>
		</div>
	</div>

	<div class="admin-stat-card">
		<div class="admin-stat-card__value admin-stat-card__value--online">
			<?= number_format((int)($stats['players_online'] ?? 0)); ?>
		</div>
		<div class="admin-stat-card__label">
			<?= t('stats.players_online'); ?>
		</div>
	</div>

	<div class="admin-stat-card">
		<div class="admin-stat-card__value">
			<?= number_format((int)($stats['guilds_total'] ?? 0)); ?>
		</div>
		<div class="admin-stat-card__label">
			<?= t('stats.guilds_created'); ?>
		</div>
	</div>
</div>
