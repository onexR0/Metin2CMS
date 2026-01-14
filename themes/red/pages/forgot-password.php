<?php
$step    = $step ?? '1';
$email   = $email ?? '';
$account = $account ?? '';
$csrfToken = $csrfToken ?? ''; // Assuming $csrfToken is defined somewhere in the script
?>
<div id="alertBanner"></div>
<?php if (!empty($error)): ?>
	<div class="alert alert-error" style="display: none;" data-message="<?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>" data-type="error"></div>
<?php endif; ?>
<?php if (!empty($success)): ?>
	<div class="alert alert-success" style="display: none;" data-message="<?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>" data-type="success"></div>
<?php endif; ?>

<div class="auth-wrapper">
	<?php if ($step === '1'): ?>
		<form method="post" action="<?= $baseUrl; ?>/forgot-password" class="form auth-form">
			<input type="hidden" name="step" value="1">
			<!-- Added CSRF token for security -->
			<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
			
			<div class="form-group">
				<label class="form-label">
					<?= t('user_name'); ?>
				</label>
				<input class="form-input" type="text" name="account" value="<?= htmlspecialchars($account, ENT_QUOTES, 'UTF-8'); ?>" required>
			</div>

			<div class="form-group">
				<label class="form-label">
					<?= t('email'); ?>
				</label>
				<input class="form-input" type="email" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required>
			</div>

			<?php if ($recaptchaEnabled && $recaptchaSiteKey): ?>
				<div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($recaptchaSiteKey); ?>"></div>
				<script src="https://www.google.com/recaptcha/api.js" async defer></script>
			<?php endif; ?>

			<button type="submit" class="btn">
				<?= t('send_code'); ?>
			</button>
		</form>

	<?php else: ?>
		<form method="post" action="<?= $baseUrl; ?>/forgot-password" class="form auth-form" id="passwordCheck">
			
			<input type="hidden" name="step" value="2">
			<input type="hidden" name="account" value="<?= htmlspecialchars($account, ENT_QUOTES, 'UTF-8'); ?>">
			<input type="hidden" name="email"   value="<?= htmlspecialchars($email,   ENT_QUOTES, 'UTF-8'); ?>">
			<!-- Added CSRF token for security -->
			<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

			<div class="form-group">
				<label class="form-label">
					<?= t('secure_code'); ?>
				</label>
				<input class="form-input" type="text" name="secure_code" maxlength="6" required placeholder="******">
			</div>

			<div class="form-group">
				<label class="form-label">
					<?= t('password'); ?>
				</label>
				<input class="form-input" type="password" name="new_password" id="password" required>
			</div>

			<div class="form-group">
				<label class="form-label">
					<?= t('confirm_password'); ?>
				</label>
				<input class="form-input" type="password" name="new_password2" id="password2" required>
				<small id="password-error" class="field-error"></small>
			</div>

			<button type="submit" class="btn">
				<?= t('reset_password'); ?>
			</button>
		</form>
	<?php endif; ?>
</div>
