<?php

declare(strict_types=1);
namespace oneX\Controllers;
use oneX\Core\Controller;
use oneX\Core\Captcha;
use oneX\Core\CSRF;
use oneX\Core\RateLimiter;
use oneX\Models\GameAccount;
use oneX\Models\Setting;

class AuthController extends Controller
{
	public function showLogin(): void
	{
		if ($this->accountLoggedIn()) {
			$this->redirect('/');
			return;
		}

		$this->view('login', [
			'title' => t('login'),
		]);
	}

	public function login(): void
	{
		if ($this->accountLoggedIn()) {
			$this->redirect('/');
			return;
		}

		if (!$this->verifyCSRF()) {
			$this->view('login', [
				'title' => t('login'),
				'error' => t('csrf_token_invalid'),
			]);
			return;
		}

		if (RateLimiter::isLimited('login', 5, 300)) {
			$remaining = RateLimiter::getRemainingTime('login', 300);
			$this->view('login', [
				'title' => t('login'),
				'error' => t('rate_limit_exceeded') . ' ' . ceil($remaining / 60) . ' ' . t('minutes'),
			]);
			return;
		}

		$username = trim($_POST['username'] ?? '');
		$password = trim($_POST['password'] ?? '');

		$captchaResponse = $_POST['g-recaptcha-response'] ?? null;
		if (!Captcha::verify($captchaResponse, $this->config)) {
			RateLimiter::recordAttempt('login');
			$this->view('login', [
				'title' => t('login'),
				'error' => t('invalide_captcha'),
			]);
			return;
		}

		if ($username === '' || $password === '') {
			RateLimiter::recordAttempt('login');
			$this->view('login', [
				'title' => t('login'),
				'error' => t('complate_username_password'),
			]);
			return;
		}

		$account = GameAccount::verifyCredentials($username, $password);
		if (!$account) {
			RateLimiter::recordAttempt('login');
			$this->view('login', [
				'title' => t('login'),
				'error' => t('invalid_credentials'),
			]);
			return;
		}

		if (isset($account['banned']) && $account['banned'] === true) {
			RateLimiter::recordAttempt('login');
			$this->view('login', [
				'title' => t('login'),
				'error' => t('account_banned'),
			]);
			return;
		}

		GameAccount::clearSecureCode($username);

		session_regenerate_id(true);

		$_SESSION['account_id']    = (int)$account['id'];
		$_SESSION['account_login'] = $account['login'];
		$_SESSION['account_web_admin'] = (int)($account['web_admin'] ?? 0);

		RateLimiter::reset('login');

		$this->redirect('/');
	}

	public function logout(): void
	{
		session_regenerate_id(true);

		unset(
			$_SESSION['account_id'],
			$_SESSION['account_login'],
			$_SESSION['account_web_admin'],
			$_SESSION['show_password_verification'],
			$_SESSION['show_email_verification'],
			$_SESSION['pending_password'],
			$_SESSION['pending_email']
		);
		$this->redirect('/');
	}

	public function showRegister(): void
	{
		if ($this->accountLoggedIn()) {
			$this->redirect('/');
			return;
		}

		if (Setting::get('register_enabled', '1') !== '1') {
			$this->view('register', [
				'title'          => t('register'),
				'warning'        => t('register_closed'),
				'registerClosed' => true,
			]);
			return;
		}

		$this->view('register', [
			'title'          => t('register'),
			'registerClosed' => false,
		]);
	}

	public function register(): void
	{
		if ($this->accountLoggedIn()) {
			$this->redirect('/');
			return;
		}

		if (Setting::get('register_enabled', '1') !== '1') {
			$this->view('register', [
				'title'          => t('register'),
				'warning'        => t('register_closed'),
				'registerClosed' => true,
			]);
			return;
		}

		if (!$this->verifyCSRF()) {
			$this->view('register', [
				'title' => t('register'),
				'error' => t('csrf_token_invalid'),
			]);
			return;
		}

		if (RateLimiter::isLimited('register', 3, 600)) {
			$remaining = RateLimiter::getRemainingTime('register', 600);
			$this->view('register', [
				'title' => t('register'),
				'error' => t('rate_limit_exceeded') . ' ' . ceil($remaining / 60) . ' ' . t('minutes'),
			]);
			return;
		}

		$username  = trim($_POST['username'] ?? '');
		$password  = trim($_POST['password'] ?? '');
		$password2 = trim($_POST['password2'] ?? '');
		$email     = trim($_POST['email'] ?? '');

		$captchaResponse = $_POST['g-recaptcha-response'] ?? null;
		if (!Captcha::verify($captchaResponse, $this->config)) {
			RateLimiter::recordAttempt('register');
			$this->view('register', [
				'title' => t('register'),
				'error' => t('invalide_captcha'),
			]);
			return;
		}

		if (!preg_match('/^[\x21-\x7E]{5,16}$/', $password)) {
			RateLimiter::recordAttempt('register');
			$this->view('register', [
				'title' => t('register'),
				'error' => t('user_min_max_length'),
			]);
			return;
		}

		if (!preg_match('/^[\x21-\x7E]{5,16}$/', $password)) {
			RateLimiter::recordAttempt('register');
			$this->view('register', [
				'title' => t('register'),
				'error' => t('password_min_max_length'),
			]);
			return;
		}

		if ($password !== $password2) {
			RateLimiter::recordAttempt('register');
			$this->view('register', [
				'title' => t('register'),
				'error' => t('passwords_do_not_match'),
			]);
			return;
		}

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			RateLimiter::recordAttempt('register');
			$this->view('register', [
				'title' => t('register'),
				'error' => t('invalid_email'),
			]);
			return;
		}

		if (GameAccount::loginExists($username)) {
			RateLimiter::recordAttempt('register');
			$this->view('register', [
				'title' => t('register'),
				'error' => t('username_exists'),
			]);
			return;
		}

		if (!GameAccount::create($username, $password, $email)) {
			RateLimiter::recordAttempt('register');
			$this->view('register', [
				'title' => t('register'),
				'error' => t('account_creation_failed'),
			]);
			return;
		}

		RateLimiter::reset('register');

		$this->view('login', [
			'title'   => t('login'),
			'success' => t('account_created_successfully'),
		]);
	}

	public function showForgotPassword(): void
	{
		if ($this->accountLoggedIn()) {
			$this->redirect('/');
			return;
		}

		$this->view('forgot-password', [
			'title' => t('forgot_password'),
		]);
	}

	public function forgotPassword(): void
	{
		if ($this->accountLoggedIn()) {
			$this->redirect('/');
			return;
		}

		if (!$this->verifyCSRF()) {
			$this->view('forgot-password', [
				'title' => t('forgot_password'),
				'error' => t('csrf_token_invalid'),
			]);
			return;
		}

		if (RateLimiter::isLimited('forgot_password', 3, 600)) {
			$remaining = RateLimiter::getRemainingTime('forgot_password', 600);
			$this->view('forgot-password', [
				'title' => t('forgot_password'),
				'error' => t('rate_limit_exceeded') . ' ' . ceil($remaining / 60) . ' ' . t('minutes'),
			]);
			return;
		}

		$account = trim($_POST['account'] ?? '');
		$email = trim($_POST['email'] ?? '');
		$secureCode = trim($_POST['secure_code'] ?? '');
		$newPassword = trim($_POST['new_password'] ?? '');
		$newPassword2 = trim($_POST['new_password2'] ?? '');
		$step = $_POST['step'] ?? '1';

		if ($step === '1') {
			$captchaResponse = $_POST['g-recaptcha-response'] ?? null;
			if (!Captcha::verify($captchaResponse, $this->config)) {
				RateLimiter::recordAttempt('forgot_password');
				$this->view('forgot-password', [
					'title' => t('forgot_password'),
					'error' => t('invalide_captcha'),
					'account' => $account,
					'email' => $email,
				]);
				return;
			}

			if ($account === '' || $email === '') {
				RateLimiter::recordAttempt('forgot_password');
				$this->view('forgot-password', [
					'title' => t('forgot_password'),
					'error' => t('account_email_required'),
					'account' => $account,
					'email' => $email,
				]);
				return;
			}

			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				RateLimiter::recordAttempt('forgot_password');
				$this->view('forgot-password', [
					'title' => t('forgot_password'),
					'error' => t('invalid_email'),
					'account' => $account,
					'email' => $email,
				]);
				return;
			}

			$accountData = GameAccount::findByLogin($account);
			if (!$accountData) {
				RateLimiter::recordAttempt('forgot_password');
				$this->view('forgot-password', [
					'title' => t('forgot_password'),
					'error' => t('account_not_found'),
					'account' => $account,
					'email' => $email,
				]);
				return;
			}

			if (strtolower($accountData['email']) !== strtolower($email)) {
				RateLimiter::recordAttempt('forgot_password');
				$this->view('forgot-password', [
					'title' => t('forgot_password'),
					'error' => t('email_not_match_account'),
					'account' => $account,
					'email' => $email,
				]);
				return;
			}

			$code = GameAccount::generateSecureCode($accountData['login']);
			if (!$code) {
				RateLimiter::recordAttempt('forgot_password');
				$this->view('forgot-password', [
					'title' => t('forgot_password'),
					'error' => t('error_generating_code'),
					'account' => $account,
					'email' => $email,
				]);
				return;
			}

			$sent = \oneX\Core\Mailer::sendVerificationCode(
				$this->config,
				$email,
				$code,
				'password_reset'
			);

			if (!$sent) {
				RateLimiter::recordAttempt('forgot_password');
				$this->view('forgot-password', [
					'title' => t('forgot_password'),
					'error' => t('error_sending_email'),
					'account' => $account,
					'email' => $email,
				]);
				return;
			}

			$this->view('forgot-password', [
				'title' => t('forgot_password'),
				'success' => t('code_sent_to_email'),
				'account' => $account,
				'email' => $email,
				'step' => '2',
			]);
			return;
		}

		if ($step === '2') {
			if ($account === '' || $email === '' || $secureCode === '' || $newPassword === '' || $newPassword2 === '') {
				RateLimiter::recordAttempt('forgot_password');
				$this->view('forgot-password', [
					'title' => t('forgot_password'),
					'error' => t('all_fields_required'),
					'account' => $account,
					'email' => $email,
					'step' => '2',
				]);
				return;
			}

			$accountData = GameAccount::findByLogin($account);
			if (!$accountData) {
				RateLimiter::recordAttempt('forgot_password');
				$this->view('forgot-password', [
					'title' => t('forgot_password'),
					'error' => t('account_not_found'),
					'step' => '1',
				]);
				return;
			}

			if (strtolower($accountData['email']) !== strtolower($email)) {
				RateLimiter::recordAttempt('forgot_password');
				$this->view('forgot-password', [
					'title' => t('forgot_password'),
					'error' => t('email_not_match_account'),
					'step' => '1',
				]);
				return;
			}

			if (!GameAccount::verifySecureCode($accountData['login'], $secureCode)) {
				RateLimiter::recordAttempt('forgot_password');
				$this->view('forgot-password', [
					'title' => t('forgot_password'),
					'error' => t('invalid_verification_code'),
					'account' => $account,
					'email' => $email,
					'step' => '2',
				]);
				return;
			}

			if (!preg_match('/^[\x21-\x7E]{5,16}$/', $newPassword)) {
				RateLimiter::recordAttempt('forgot_password');
				$this->view('forgot-password', [
					'title' => t('forgot_password'),
					'error' => t('password_min_max_length'),
					'account' => $account,
					'email' => $email,
					'step' => '2',
				]);
				return;
			}

			if ($newPassword !== $newPassword2) {
				RateLimiter::recordAttempt('forgot_password');
				$this->view('forgot-password', [
					'title' => t('forgot_password'),
					'error' => t('passwords_do_not_match'),
					'account' => $account,
					'email' => $email,
					'step' => '2',
				]);
				return;
			}

			if (!GameAccount::updatePassword($accountData['login'], $newPassword)) {
				RateLimiter::recordAttempt('forgot_password');
				$this->view('forgot-password', [
					'title' => t('forgot_password'),
					'error' => t('error_updating_password'),
					'account' => $account,
					'email' => $email,
					'step' => '2',
				]);
				return;
			}

			RateLimiter::reset('forgot_password');

			$this->view('login', [
				'title' => t('login'),
				'success' => t('password_reset_success'),
			]);
			return;
		}
	}

	function accountLoggedIn(): bool
	{
		return isset($_SESSION['account_id']);
	}
}
