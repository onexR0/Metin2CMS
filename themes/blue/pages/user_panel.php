<div id="alertBanner"></div>

<?php if (!empty($message)): ?>
	<div class="alert alert-success" style="display: none;" data-message="<?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>" data-type="success"></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
	<div class="alert alert-error" style="display: none;" data-message="<?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>" data-type="error"></div>
<?php endif; ?>

<?php if (empty(!$account)): ?>
	<div class="account-info-box">
		<div class="account-info-header">
			<?= t('your_account'); ?>
		</div>

		<div class="account-info-content">
			<div class="info-row">
				<span class="info-label"><?= t('user_name'); ?>:</span>
				<span class="info-value"><?= htmlspecialchars($account['login'], ENT_QUOTES, 'UTF-8'); ?></span>
			</div>

			<div class="info-row">
				<span class="info-label"><?= t('email'); ?>:</span>
				<span class="info-value"><?= htmlspecialchars($account['email'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
			</div>

			<div class="info-row">
				<span class="info-label"><?= t('dragon_conins'); ?></span>
				<span class="info-value">0</span>
			</div>

			<div class="info-row">
				<span class="info-label"><?= t('dragon_jetons'); ?></span>
				<span class="info-value">0</span>
			</div>
		</div>
	</div>

	<?php if (!empty($_SESSION['show_email_verification']) || !empty($_SESSION['show_password_verification'])): ?>
		<div class="account-info-box account-info-box--verification">
			<div class="account-info-header">
				<?= t('secure_code'); ?>
			</div>
			<div class="account-info-content account-info-content--compact">
				<p class="panel-note">
						<?= t('verify_mail_for_code'); ?>
				</p>

				<form method="post"
					  action="<?= $baseUrl; ?>/user/panel/<?= !empty($_SESSION['show_email_verification']) ? 'change-email' : 'change-password'; ?>"
					  class="form form--compact verification-form">

					<!-- Added CSRF token for security -->
					<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

					<?php if (!empty($_SESSION['show_email_verification'])): ?>
						<input type="hidden" name="new_email" value="<?= htmlspecialchars($_SESSION['pending_email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
					<?php endif; ?>

					<?php if (!empty($_SESSION['show_password_verification'])): ?>
						<input type="hidden" name="new_password2" value="<?= htmlspecialchars($_SESSION['pending_password'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
					<?php endif; ?>

					<div class="form-group">
						<label class="form-label">
							<?= t('secure_code'); ?>
						</label>
						<input type="text" name="verification_code" required placeholder="123456" maxlength="6" pattern="[0-9]{6}" class="form-input verification-code-input">
						<small id="verification-error" class="field-error"></small>
					</div>

					<div class="form-actions">
						<button type="submit" class="btn btn--admin"><?= t('confirm'); ?></button>
						<a href="<?= $baseUrl; ?>/user/panel/cancel-verification" class="btn btn--admin"><?= t('cancel_button'); ?></a>
					</div>
				</form>
			</div>
		</div>
	<?php endif; ?>

	<div class="panel-section-wrapper">
		<div class="panel-section" onclick="toggleSection(this)">
			<div class="panel-section-header">
				<?= t('characters'); ?>
			</div>
		</div>
		<div class="panel-section-content">
			<div class="ranking-table-wrapper">
				<table class="ranking-table">
					<thead>
						<tr>
							<th><?= t('position'); ?></th>
							<th><?= t('name'); ?></th>
							<th><?= t('level'); ?></th>
							<th><?= t('race'); ?></th>
							<th><?= t('exp'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($characters)): ?>
							<tr>
								<td colspan="5" class="no-news-message">
									<?= t('no_characters_found'); ?>
								</td>
							</tr>
						<?php else: ?>
							<?php foreach ($characters as $char): ?>
								<tr>
									<td><?= htmlspecialchars($char['position'], ENT_QUOTES, 'UTF-8'); ?></td>
									<td class="player-name-cell">
										<?= htmlspecialchars($char['name'], ENT_QUOTES, 'UTF-8'); ?>
									</td>
									<td class="level-cell">
										<?= htmlspecialchars($char['level'], ENT_QUOTES, 'UTF-8'); ?>
									</td>
									<td class="kingdom-cell">
										<img src="<?= $assetsUrl; ?>/job/<?= (int)$char['job']; ?>.png" alt="Class <?= (int)$char['job']; ?>" class="job-icon">
									</td>
									<td><?= number_format($char['exp'], 0, ',', '.'); ?></td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>

		<div class="panel-section" onclick="toggleSection(this)">
			<div class="panel-section-header">
				<?= t('email_address'); ?>
			</div>
		</div>
		<div class="panel-section-content">
			<p class="panel-note">
				<?= t('change_mail_note'); ?>
			</p>

			<form method="post" action="<?= $baseUrl; ?>/user/panel/change-email" class="form">

				<!-- Added CSRF token for security -->
				<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

				<div class="form-group">
					<label class="form-label">
						<?= t('new_mail'); ?>
					</label>
					<input type="email" name="new_email" required class="form-input">
					<small id="email-error" class="field-error"></small>
				</div>

				<button type="submit" class="btn">
					<?= t('send_code'); ?>
				</button>
			</form>
		</div>

		<div class="panel-section" onclick="toggleSection(this)">
			<div class="panel-section-header">
				<?= t('password') ?>
			</div>
		</div>
		<div class="panel-section-content">
			<p class="panel-note">
				<?= t('change_password_note'); ?>
			</p>

			<form method="post" action="<?= $baseUrl; ?>/user/panel/change-password" id="passwordCheck" class="form">

				<!-- Added CSRF token for security -->
				<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

				<div class="form-group">
					<label class="form-label">
						<?= t('password'); ?>
					</label>
					<input type="password" name="new_password" id="password" required minlength="6" maxlength="16" class="form-input">
				</div>

				<div class="form-group">
					<label class="form-label">
						<?= t('confirm_password'); ?>
					</label>
					<input type="password" name="new_password2" id="password2" required minlength="6" maxlength="16" class="form-input">
					<small id="password-error" class="field-error"></small>
				</div>

				<button type="submit" class="btn">
					<?= t('send_code'); ?>
				</button>
			</form>
		</div>

		<div class="panel-section" onclick="toggleSection(this)">
			<div class="panel-section-header">
				<?= t('warehouse_password'); ?>
			</div>
		</div>
		<div class="panel-section-content">
			<p class="panel-note">
				<?= t('warehouse_password_note'); ?>
			</p>

			<form method="post" action="<?= $baseUrl; ?>/user/panel/send-warehouse-password" class="form">
				<!-- Added CSRF token for security -->
				<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

				<button type="submit" class="btn">
					<?= t('send'); ?>
				</button>
			</form>
		</div>

		<div class="panel-section" onclick="toggleSection(this)">
			<div class="panel-section-header">
				<?= t('detele_character_code'); ?>
			</div>
		</div>
		<div class="panel-section-content">
			<p class="panel-note">
				<?= t('delete_character_note'); ?>
			</p>

			<form method="post" action="<?= $baseUrl; ?>/user/panel/send-social-id" class="form">
				<!-- Added CSRF token for security -->
				<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

				<button type="submit" class="btn">
					<?= t('send'); ?>
				</button>
			</form>
		</div>

	</div>
<?php endif; ?>
