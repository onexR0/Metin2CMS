<div id="alertBanner"></div>
<?php if (!empty($error)): ?>
	<div class="alert alert-error" style="display: none;" data-message="<?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>" data-type="error"></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
	<div class="alert alert-success" style="display: none;" data-message="<?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>" data-type="success"></div>
<?php endif; ?>

<div class="auth-wrapper">
	<form method="post" action="<?= $baseUrl; ?>/login" class="form">
		<!-- Added CSRF token for security -->
		<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
		
		<div class="form-group">
			<label class="form-label"><?= t('user_name'); ?></label>
			<input class="form-input" type="text" name="username" required>
		</div>

		<div class="form-group">
			<label class="form-label"><?= t('password'); ?></label>
			<input class="form-input" type="password" name="password" required>
		</div>

		<?php if ($recaptchaEnabled && $recaptchaSiteKey): ?>
			<div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($recaptchaSiteKey); ?>"></div>
			<script src="https://www.google.com/recaptcha/api.js" async defer></script>
		<?php endif; ?>

		<button type="submit" class="btn"><?= t('login'); ?></button>
	</form>
</div>
