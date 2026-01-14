<div id="alertBanner"></div>
<?php if (!empty($error)): ?>
	<div class="alert alert-error" style="display: none;" data-message="<?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>" data-type="error"></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
	<div class="alert alert-success" style="display: none;" data-message="<?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>" data-type="success"></div>
<?php endif; ?>

<div class="box">
	<form method="get" action="<?= $baseUrl; ?>/admin/coins" class="form form--narrow">
		<div class="form-group">
			<label class="form-label"><?= t('search_user_or_mail'); ?></label>
			<input type="text" name="search" class="form-input" value="<?= htmlspecialchars($search); ?>" required>
		</div>
		
		<div class="form-actions">
			<button type="submit" class="btn btn--small"><?= t('search_button'); ?></button>
			<?php if (!empty($search)): ?>
				<a href="<?= $baseUrl; ?>/admin/coins" class="btn btn--small"><?= t('reset_button'); ?></a>
			<?php endif; ?>
		</div>
	</form>

	<?php if ($search !== '' && $account === null): ?>
		<p class="form-error"><?= t('nothing_found'); ?></p>
	<?php endif; ?>

	<?php if ($account !== null): ?>
		<h2 class="section-title"><?= t('account_details'); ?></h2>
		
		<div class="table-wrapper">
			<table class="download-table">
				<tbody>
					<tr>
						<td>Id:</td>
						<td><?= htmlspecialchars($account['id']); ?></td>
					</tr>
					<tr>
						<td><?= t('acc_src.account'); ?>:</td>
						<td><?= htmlspecialchars($account['login']); ?></td>
					</tr>
					<tr>
						<td><?= t('acc_src.email'); ?>:</td>
						<td><?= htmlspecialchars($account['email']); ?></td>
					</tr>
					<tr>
						<td>Md:</td>
						<td><?= number_format((int)($account['coins'] ?? 0)); ?></td>
					</tr>
					<tr>
						<td>Jd:</td>
						<td><?= number_format((int)($account['jcoins'] ?? 0)); ?></td>
					</tr>
				</tbody>
			</table>
		</div>

		<h2 class="section-title"><?= t('update_button'); ?></h2>

		<form method="post" action="<?= $baseUrl; ?>/admin/coins/update" class="form form--narrow">
			<!-- Added CSRF token for security -->
			<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
			<input type="hidden" name="login" value="<?= htmlspecialchars($account['login']); ?>">
			
			<div class="form-group">
				<label for="coins" class="form-label">MD (coins)</label>
				<input type="number" id="coins" name="coins" class="form-input" value="0" required>
			</div>

			<div class="form-group">
				<label for="jcoins" class="form-label">JD (jcoins)</label>
				<input type="number" id="jcoins" name="jcoins" class="form-input" value="0" required
				>
			</div>

			<div class="form-actions">
				<button type="submit" class="btn btn--small"><?= t('update_button'); ?></button>
			</div>
		</form>
	<?php endif; ?>
</div>
