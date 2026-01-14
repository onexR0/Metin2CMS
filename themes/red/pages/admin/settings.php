<div class="box">
	<form method="post" action="<?= $baseUrl; ?>/admin/settings" class="form form--narrow">
		<!-- Added CSRF token for security -->
		<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
		
		<p class="form-section-title"><?= t('statistically'); ?></p>

		<label class="terms-label">
			<input type="checkbox" name="show_players_online" <?= (!empty($settings['show_players_online']) && $settings['show_players_online'] === '1') ? 'checked' : ''; ?>>
			<span><?= t('stats.players_online'); ?></span>
		</label>

		<label class="terms-label">
			<input type="checkbox" name="show_players_24h" <?= (!empty($settings['show_players_24h']) && $settings['show_players_24h'] === '1') ? 'checked' : ''; ?>>
			<span><?= t('stats.players_24h'); ?></span>
		</label>

		<label class="terms-label">
			<input type="checkbox" name="show_accounts_total" <?= (!empty($settings['show_accounts_total']) && $settings['show_accounts_total'] === '1') ? 'checked' : ''; ?>>
			<span><?= t('stats.accounts_created'); ?></span>
		</label>

		<label class="terms-label">
			<input type="checkbox" name="show_characters_total" <?= (!empty($settings['show_characters_total']) && $settings['show_characters_total'] === '1') ? 'checked' : ''; ?>>
			<span><?= t('stats.characters_created'); ?></span>
		</label>

		<label class="terms-label">
			<input type="checkbox" name="show_guilds_total" <?= (!empty($settings['show_guilds_total']) && $settings['show_guilds_total'] === '1') ? 'checked' : ''; ?>>
			<span><?= t('stats.guilds_created'); ?></span>
		</label>

		<p class="form-section-title"><?= t('general_settings'); ?></p>

		<label class="terms-label">
			<input type="checkbox" name="register_enabled" <?= (!empty($settings['register_enabled']) && $settings['register_enabled'] === '1') ? 'checked' : ''; ?>>
			<span><?= t('register_enabled'); ?></span>
		</label>

		<div class="form-actions">
			<button type="submit" class="btn btn--small">Salvează</button>
		</div>
	</form>
</div>
