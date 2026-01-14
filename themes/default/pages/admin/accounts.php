<div id="alertBanner"></div>
<?php if (isset($success)): ?>
	<div class="alert alert-success" data-message="<?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>" data-type="success"></div>
<?php endif; ?>
<?php if (isset($error)): ?>
	<div class="alert alert-error" data-message="<?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>" data-type="error"></div>
<?php endif; ?>

<div class="box">
	<form method="get" action="<?= $baseUrl; ?>/admin/accounts" class="form form--narrow">
		<div class="form-group">
			<label class="form-label"><?= t('search_user_or_mail'); ?></label>
			<input type="text"  name="search"  class="form-input"  value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
		</div>

		<div class="form-actions">
			<button type="submit" class="btn btn--small"><?= t('search_button'); ?></button>
			<?php if (!empty($search)): ?>
				<a href="<?= $baseUrl; ?>/admin/accounts" class="btn btn--small"><?= t('reset_button'); ?></a>
			<?php endif; ?>
		</div>
	</form>

	<?php if (!empty($search)): ?>
		<h2 class="section-title"><?= t('search_resutls'); ?></h2>

		<?php if (!empty($accounts)): ?>
			<div class="table-wrapper">
				<table class="download-table">
					<thead>
						<tr>
							<th>ID</th>
							<th><?= t('acc_src.account'); ?></th>
							<th><?= t('acc_src.email'); ?></th>
							<th><?= t('acc_src.state'); ?></th>
							<th><?= t('acc_src.action'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($accounts as $acc): ?>
							<tr>
								<td><?= (int)$acc['id']; ?></td>
								<td><?= htmlspecialchars($acc['login'], ENT_QUOTES, 'UTF-8'); ?></td>
								<td><?= htmlspecialchars($acc['email'], ENT_QUOTES, 'UTF-8'); ?></td>
								<td>
									<?php 
									$status = strtoupper($acc['status'] ?? 'OK');
									$statusColor = ($status === 'OK') ? 'var(--clr-accent-soft-text)' : '#ff6b6b';
									?>
									<span style="color: <?= $statusColor; ?>; font-weight: bold;">
										<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>
									</span>
								</td>
								<td>
									<div class="form-actions" style="margin-top: 0; justify-content: center;">
										<?php if (strtoupper($acc['status'] ?? 'OK') === 'OK'): ?>
											<form method="post"  action="<?= $baseUrl; ?>/admin/accounts/ban" style="margin: 0;">
												<!-- Added CSRF token for security -->
												<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
												<input type="hidden" name="login" value="<?= htmlspecialchars($acc['login'], ENT_QUOTES, 'UTF-8'); ?>">
												<input type="hidden" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
												<button type="submit" class="btn btn--small" style="background: var(--grad-btn-matte); color: #ff6b6b;">
													Ban
												</button>
											</form>
										<?php else: ?>
											<form method="post"  action="<?= $baseUrl; ?>/admin/accounts/unban" style="margin: 0;">
												<!-- Added CSRF token for security -->
												<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
												<input type="hidden" name="login" value="<?= htmlspecialchars($acc['login'], ENT_QUOTES, 'UTF-8'); ?>">
												<input type="hidden" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
												<button type="submit" class="btn btn--small" style="background: var(--grad-btn-matte); color: var(--clr-accent-soft-text);">
													Unban
												</button>
											</form>
										<?php endif; ?>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php else: ?>
			<p class="no-news-message"><?= t('nothing_found'); ?></p>
		<?php endif; ?>
	<?php endif; ?>
</div>
