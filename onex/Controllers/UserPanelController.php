<?php

declare(strict_types=1);
namespace oneX\Controllers;
use oneX\Core\Controller;
use oneX\Core\Mailer;
use oneX\Core\RateLimiter;
use oneX\Models\GameAccount;
use oneX\Models\VerificationCode;

class UserPanelController extends Controller
{
	public function index(): void
	{
		if (!$this->accountLoggedIn()) {
			$this->redirect('/login');
		}

		try {
			$account = GameAccount::findByLogin($_SESSION['account_login']);
			if (!$account) {
				$_SESSION['error'] = t('account_not_found');
				$this->redirect('/login');
				return;
			}

			$characters = GameAccount::getCharacters($_SESSION['account_login']);

			$this->view('user_panel', [
				'title'      => t('user_panel'),
				'account'    => $account,
				'characters' => $characters,
				'message'    => $_SESSION['panel_msg'] ?? null,
				'error'      => $_SESSION['panel_err'] ?? null,
			]);

			unset($_SESSION['panel_msg'], $_SESSION['panel_err']);
		} catch (\Throwable $e) {
			error_log("User Panel Error: " . $e->getMessage());
			$_SESSION['error'] = t('error_loading_panel');
			$this->redirect('/');
		}
	}

	public function changePassword(): void
	{
		if (!$this->accountLoggedIn()) {
			$this->redirect('/login');
			return;
		}

		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			$this->redirect('/user/panel');
			return;
		}

		if (!$this->verifyCSRF()) {
			$_SESSION['panel_err'] = t('csrf_token_invalid');
			$this->redirect('/user/panel');
			return;
		}

		if (RateLimiter::isLimited('change_password_' . $_SESSION['account_id'], 5, 600)) {
			$remaining = RateLimiter::getRemainingTime('change_password_' . $_SESSION['account_id'], 600);
			$_SESSION['panel_err'] = t('rate_limit_exceeded') . ' ' . ceil($remaining / 60) . ' ' . t('minutes');
			$this->redirect('/user/panel');
			return;
		}

		$newPassword = $_POST['new_password'] ?? '';
		$newPassword2 = $_POST['new_password2'] ?? '';
		$verificationCode = $_POST['verification_code'] ?? '';

		if (empty($verificationCode)) {
			if (empty($newPassword) || empty($newPassword2)) {
				RateLimiter::recordAttempt('change_password_' . $_SESSION['account_id']);
				$_SESSION['panel_err'] = t('all_fields_required');
				$this->redirect('/user/panel');
				return;
			}

			if ($newPassword !== $newPassword2) {
				RateLimiter::recordAttempt('change_password_' . $_SESSION['account_id']);
				$_SESSION['panel_err'] = t('passwords_do_not_match');
				$this->redirect('/user/panel');
				return;
			}

			if (strlen($newPassword) < 6) {
				RateLimiter::recordAttempt('change_password_' . $_SESSION['account_id']);
				$_SESSION['panel_err'] = t('password_min_max_length');
				$this->redirect('/user/panel');
				return;
			}

			$account = GameAccount::findByLogin($_SESSION['account_login']);
			if (empty($account['email'])) {
				$_SESSION['panel_err'] = t('no_mail');
				$this->redirect('/user/panel');
				return;
			}

			$_SESSION['pending_password'] = $newPassword;

			$code = GameAccount::generateSecureCode($_SESSION['account_login']);
			if (!$code) {
				$_SESSION['panel_err'] = t('error_generating_code');
				$this->redirect('/user/panel');
				return;
			}

			$emailSent = Mailer::sendVerificationCode($this->config, $account['email'], $code, 'password_reset');
			if (!$emailSent) {
				$_SESSION['panel_err'] = t('error_sending_email');
				$this->redirect('/user/panel');
				return;
			}

			$_SESSION['panel_msg'] = t('verify_mail_for_code');
			$_SESSION['show_password_verification'] = true;
			$this->redirect('/user/panel');
			return;
		}

		if (!GameAccount::verifySecureCode($_SESSION['account_login'], $verificationCode)) {
			RateLimiter::recordAttempt('change_password_' . $_SESSION['account_id']);
			$_SESSION['panel_err'] = t('invalid_verification_code');
			$this->redirect('/user/panel');
			return;
		}

		$newPassword = $_SESSION['pending_password'] ?? '';
		if (empty($newPassword)) {
			$_SESSION['panel_err'] = t('session_expire');
			unset($_SESSION['show_password_verification']);
			$this->redirect('/user/panel');
			return;
		}

		$success = GameAccount::updatePassword($_SESSION['account_login'], $newPassword);
		if ($success) {
			unset($_SESSION['pending_password'], $_SESSION['show_password_verification']);
			RateLimiter::reset('change_password_' . $_SESSION['account_id']);
			$_SESSION['panel_msg'] = t('password_changed_successfully');
		} else {
			$_SESSION['panel_err'] = t('error_changing_password');
		}

		$this->redirect('/user/panel');
	}

	public function changeEmail(): void
	{
		if (!$this->accountLoggedIn()) {
			$this->redirect('/login');
			return;
		}

		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			$this->redirect('/user/panel');
			return;
		}

		if (!$this->verifyCSRF()) {
			$_SESSION['panel_err'] = t('csrf_token_invalid');
			$this->redirect('/user/panel');
			return;
		}

		if (RateLimiter::isLimited('change_email_' . $_SESSION['account_id'], 5, 600)) {
			$remaining = RateLimiter::getRemainingTime('change_email_' . $_SESSION['account_id'], 600);
			$_SESSION['panel_err'] = t('rate_limit_exceeded') . ' ' . ceil($remaining / 60) . ' ' . t('minutes');
			$this->redirect('/user/panel');
			return;
		}

		$newEmail = $_POST['new_email'] ?? '';
		$verificationCode = $_POST['verification_code'] ?? '';

		if (empty($verificationCode)) {
			if (empty($newEmail)) {
				RateLimiter::recordAttempt('change_email_' . $_SESSION['account_id']);
				$_SESSION['panel_err'] = t('email_required');
				$this->redirect('/user/panel');
				return;
			}

			if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
				RateLimiter::recordAttempt('change_email_' . $_SESSION['account_id']);
				$_SESSION['panel_err'] = t('invalid_email');
				$this->redirect('/user/panel');
				return;
			}

			$account = GameAccount::findByLogin($_SESSION['account_login']);
			if (empty($account['email'])) {
				$_SESSION['panel_err'] = t('no_mail');
				$this->redirect('/user/panel');
				return;
			}

			$_SESSION['pending_email'] = $newEmail;

			$code = GameAccount::generateSecureCode($_SESSION['account_login']);
			if (!$code) {
				$_SESSION['panel_err'] = t('error_generating_code');
				$this->redirect('/user/panel');
				return;
			}

			$emailSent = Mailer::sendVerificationCode($this->config, $account['email'], $code, 'email_change');
			if (!$emailSent) {
				$_SESSION['panel_err'] = t('error_sending_email');
				$this->redirect('/user/panel');
				return;
			}

			$_SESSION['panel_msg'] = t('code_sent_to_email');
			$_SESSION['show_email_verification'] = true;
			$this->redirect('/user/panel');
			return;
		}

		if (!GameAccount::verifySecureCode($_SESSION['account_login'], $verificationCode)) {
			RateLimiter::recordAttempt('change_email_' . $_SESSION['account_id']);
			$_SESSION['panel_err'] = t('invalid_verification_code');
			$this->redirect('/user/panel');
			return;
		}

		$newEmail = $_SESSION['pending_email'] ?? '';
		if (empty($newEmail)) {
			$_SESSION['panel_err'] = t('error_processing_request');
			unset($_SESSION['show_email_verification']);
			$this->redirect('/user/panel');
			return;
		}

		$success = GameAccount::updateEmail($_SESSION['account_login'], $newEmail);
		if ($success) {
			unset($_SESSION['pending_email'], $_SESSION['show_email_verification']);
			RateLimiter::reset('change_email_' . $_SESSION['account_id']);
			$_SESSION['panel_msg'] = t('email_changed_successfully');
		} else {
			$_SESSION['panel_err'] = t('error_processing_request');
		}

		$this->redirect('/user/panel');
	}

	public function cancelVerification(): void
	{
		if (!$this->accountLoggedIn()) {
			$this->redirect('/login');
			return;
		}

		unset(
			$_SESSION['show_password_verification'],
			$_SESSION['show_email_verification'],
			$_SESSION['pending_password'],
			$_SESSION['pending_email']
		);

		$_SESSION['panel_msg'] = t('verification_process_cancelled');
		$this->redirect('/user/panel');
	}

	public function sendSocialId(): void
	{
		if (!$this->accountLoggedIn()) {
			$this->redirect('/login');
			return;
		}

		if (!$this->verifyCSRF()) {
			$_SESSION['panel_err'] = t('csrf_token_invalid');
			$this->redirect('/user/panel');
			return;
		}

		if (RateLimiter::isLimited('send_social_id_' . $_SESSION['account_id'], 3, 600)) {
			$remaining = RateLimiter::getRemainingTime('send_social_id_' . $_SESSION['account_id'], 600);
			$_SESSION['panel_err'] = t('rate_limit_exceeded') . ' ' . ceil($remaining / 60) . ' ' . t('minutes');
			$this->redirect('/user/panel');
			return;
		}

		$account = GameAccount::findByLogin($_SESSION['account_login']);
		if (empty($account['email'])) {
			$_SESSION['panel_err'] = t('no_mail');
			$this->redirect('/user/panel');
			return;
		}

		$socialId = GameAccount::getSocialId($_SESSION['account_login']);
		$emailSent = Mailer::sendSocialId($this->config, $account['email'], $socialId, $account['login']);
		if (!$emailSent) {
			RateLimiter::recordAttempt('send_social_id_' . $_SESSION['account_id']);
			$_SESSION['panel_err'] = t('error_sending_email');
			$this->redirect('/user/panel');
			return;
		}

		RateLimiter::reset('send_social_id_' . $_SESSION['account_id']);
		$_SESSION['panel_msg'] = t('delete_character_code_sended');
		$this->redirect('/user/panel');
	}

	public function sendWarehousePassword(): void
	{
		if (!$this->accountLoggedIn()) {
			$this->redirect('/login');
			return;
		}

		if (!$this->verifyCSRF()) {
			$_SESSION['panel_err'] = t('csrf_token_invalid');
			$this->redirect('/user/panel');
			return;
		}

		if (RateLimiter::isLimited('send_warehouse_' . $_SESSION['account_id'], 3, 600)) {
			$remaining = RateLimiter::getRemainingTime('send_warehouse_' . $_SESSION['account_id'], 600);
			$_SESSION['panel_err'] = t('rate_limit_exceeded') . ' ' . ceil($remaining / 60) . ' ' . t('minutes');
			$this->redirect('/user/panel');
			return;
		}

		$account = GameAccount::findByLogin($_SESSION['account_login']);
		if (empty($account['email'])) {
			$_SESSION['panel_err'] = t('no_mail');
			$this->redirect('/user/panel');
			return;
		}

		$warehousePassword = GameAccount::getWarehousePassword($_SESSION['account_login']);
		
		if ($warehousePassword === null) {
			$_SESSION['panel_err'] = t('warehouse_password_not_found');
			$this->redirect('/user/panel');
			return;
		}
		
		$emailSent = Mailer::sendWarehousePassword($this->config, $account['email'], $warehousePassword, $account['login']);
		if (!$emailSent) {
			RateLimiter::recordAttempt('send_warehouse_' . $_SESSION['account_id']);
			$_SESSION['panel_err'] = t('error_sending_email');
			$this->redirect('/user/panel');
			return;
		}

		RateLimiter::reset('send_warehouse_' . $_SESSION['account_id']);
		$_SESSION['panel_msg'] = t('warehouse_password_sent');
		$this->redirect('/user/panel');
	}
}
