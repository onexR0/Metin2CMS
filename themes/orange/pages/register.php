<?php
$registerClosed = !empty($registerClosed);
?>
<div id="alertBanner"></div>
<?php if (!empty($error)): ?>
	<div class="alert alert-error" style="display: none;" data-message="<?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>" data-type="error"></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
	<div class="alert alert-success" style="display: none;" data-message="<?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>" data-type="success"></div>
<?php endif; ?>

<?php if (!empty($warning)): ?>
	<div class="alert alert-warning" style="display: none;" data-message="<?= htmlspecialchars($warning, ENT_QUOTES, 'UTF-8'); ?>" data-type="warning" data-permanent="true"></div>
<?php endif; ?>
<div class="auth-wrapper">
	<?php if (!$registerClosed): ?>
		<form method="post" action="<?= $baseUrl; ?>/register" class="form auth-form" id="passwordCheck">
			<!-- Added CSRF token for security -->
			<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
			
			<div class="form-group">
				<label class="form-label">
					<?= t('user_name'); ?>
				</label>
				<input class="form-input" type="text" name="username" minlength="5" maxlength="16" pattern="[a-zA-Z0-9]+" required>
			</div>

			<div class="form-group">
				<label class="form-label">
					<?= t('password'); ?>
				</label>
				<input class="form-input" type="password" name="password" id="password" minlength="5" maxlength="16" required>
			</div>

			<div class="form-group">
				<label class="form-label">
					<?= t('confirm_password'); ?>
				</label>
				<input class="form-input" type="password" name="password2" id="password2" minlength="5" maxlength="16" required>
				<small id="password-error" class="field-error"></small>
			</div>

			<div class="form-group">
				<label class="form-label">
					<?= t('email'); ?>
				</label>
				<input class="form-input" type="email" name="email" required>
			</div>

			<label class="terms-label">
				<input type="checkbox" name="accept_terms" value="1" required>
				<?= t('accept_tcs'); ?>
			</label>

			<?php if ($recaptchaEnabled && $recaptchaSiteKey): ?>
				<div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($recaptchaSiteKey); ?>"></div>
				<script src="https://www.google.com/recaptcha/api.js" async defer></script>
			<?php endif; ?>

			<button type="submit" class="btn">
				<?= t('register_btn'); ?>
			</button>
		</form>
	<?php endif; ?>
</div>
