<?php

declare(strict_types=1);
namespace oneX\Core;
use oneX\Models\Player;
use oneX\Models\Guild;
use oneX\Models\GameStats;
use oneX\Models\SocialLink;
use oneX\Models\Page;
use oneX\Core\Lang;

class Controller
{
	protected array $config;
	protected Theme $theme;

	public function __construct()
	{
		$this->config = require dirname(__DIR__, 2) . '/config.php';

		if (session_status() === PHP_SESSION_NONE) {
			session_name($this->config['app']['session_name']);
			session_start();
		}

		Lang::init($this->config);

		$baseUrl = $this->config['app']['base_url'];
		$siteName = $this->config['app']['site_name'];
		$this->theme = new Theme($this->config['app']['theme'], $baseUrl);
	}

	protected function view(string $view, array $data = []): void
	{
		$data = array_merge($this->getLayoutData(), $data);
		
		extract($data);

		$baseUrl      = $this->config['app']['base_url'];
		$siteName     = $this->config['app']['site_name'];
		$assetsUrl    = $this->theme->assetsUrl();
		$pagePath     = $this->theme->getPagePath($view);
		$layoutPath   = $this->theme->getLayoutPath();
		$recaptchaEnabled = !empty($this->config['recaptcha']['enabled']);
		$recaptchaSiteKey = $this->config['recaptcha']['site_key'] ?? '';
		
		$currentRoute = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		
		$csrfToken = CSRF::getToken();

		require $layoutPath;
	}

	protected function getLayoutData(): array
	{
		$languages  = $this->config['lang']['available'] ?? ['ro', 'en'];
		
		return [
			'stats'       => GameStats::get(),
			'topPlayers'  => Player::topPlayers(5),
			'topGuilds'   => Guild::topGuilds(5),
			'socialLinks' => SocialLink::all(),
			'pages'       => Page::all(),
			'currentLang' => Lang::currentLang(),
			'languages'   => $languages,
		];
	}

	protected function redirect(string $path): void
	{
		$baseUrl = rtrim($this->config['app']['base_url'], '/');
		header('Location: ' . $baseUrl . '/' . ltrim($path, '/'));
		exit;
	}

	protected function accountLoggedIn(): bool
	{
		return !empty($_SESSION['account_id']);
	}

	protected function currentAccount(): ?array
	{
		if (!$this->accountLoggedIn()) return null;
		return [
			'id'    => (int)$_SESSION['account_id'],
			'login' => $_SESSION['account_login'] ?? '',
		];
	}

	protected function isAdmin(): bool
	{
		return !empty($_SESSION['account_id']) && (int)($_SESSION['account_web_admin'] ?? 0) >= 1;
	}

	protected function requireAdmin(): void
	{
		if (!$this->isAdmin()) {
			$this->redirect('/login');
		}
	}

	protected function verifyCSRF(): bool
	{
		$token = $_POST['csrf_token'] ?? null;
		return CSRF::verifyToken($token);
	}
}
