<?php
use oneX\Core\Router;
use oneX\Core\SecurityHeaders;
use oneX\Core\Session;
use oneX\Core\DatabaseMigration;
use oneX\Controllers\HomeController;
use oneX\Controllers\AuthController;
use oneX\Controllers\DownloadController;
use oneX\Controllers\RankingController;
use oneX\Controllers\UserPanelController;
use oneX\Controllers\PageController;
use oneX\Controllers\Admin\DashboardController;
use oneX\Controllers\Admin\NewsController;
use oneX\Controllers\Admin\SettingsController;
use oneX\Controllers\Admin\DownloadsController;
use oneX\Controllers\Admin\SocialController;
use oneX\Controllers\Admin\PagesController;
use oneX\Controllers\Admin\AccountsController;
use oneX\Controllers\Admin\CoinsController;

ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/php_errors.log');

if (!isset($_SERVER['SERVER_NAME']) || $_SERVER['SERVER_NAME'] !== 'localhost') {
	error_reporting(E_ALL);
	ini_set('display_errors', '0');
} else {
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
}

try {
	if (file_exists(__DIR__ . '/.env')) {
		$lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		foreach ($lines as $line) {
			if (strpos(trim($line), '#') === 0) continue;
			list($name, $value) = explode('=', $line, 2);
			$name = trim($name);
			$value = trim($value);
			putenv("$name=$value");
		}
	}

	spl_autoload_register(function (string $class): void {
		$prefix  = 'oneX\\';
		$baseDir = __DIR__ . '/onex/';

		$len = strlen($prefix);
		if (strncmp($prefix, $class, $len) !== 0) {
			return;
		}

		$relative = substr($class, $len);
		$file = $baseDir . str_replace('\\', '/', $relative) . '.php';

		if (file_exists($file)) {
			require $file;
		}
	});

	$config = require __DIR__ . '/config.php';
	Session::init($config);

	SecurityHeaders::set();

	\oneX\Core\Lang::init($config);
	function t(string $key): string {
		return \oneX\Core\Lang::get($key);
	}

	try {
		$migration = new DatabaseMigration();
		if ($migration->needsMigration()) {
			error_log("[System] Running database migration...");
			$migration->run();
		}
	} catch (\Exception $e) {
		error_log("[System] Migration check failed: " . $e->getMessage());
	}

	$router = new Router();
	$router->get('/', [HomeController::class, 'index']);
	$router->get('/login', [AuthController::class, 'showLogin']);
	$router->post('/login', [AuthController::class, 'login']);
	$router->get('/logout', [AuthController::class, 'logout']);
	$router->get('/forgot-password', [AuthController::class, 'showForgotPassword']);
	$router->post('/forgot-password', [AuthController::class, 'forgotPassword']);
	$router->get('/register', [AuthController::class, 'showRegister']);
	$router->post('/register', [AuthController::class, 'register']);
	$router->get('/download', [DownloadController::class, 'index']);
	$router->get('/ranking-players', [RankingController::class, 'players']);
	$router->get('/ranking-guilds', [RankingController::class, 'guilds']);
	$router->get('/user/panel', [UserPanelController::class, 'index']);
	$router->post('/user/panel/change-password', [UserPanelController::class, 'changePassword']);
	$router->post('/user/panel/change-email', [UserPanelController::class, 'changeEmail']);
	$router->get('/user/panel/cancel-verification', [UserPanelController::class, 'cancelVerification']);
	$router->post('/user/panel/send-social-id', [UserPanelController::class, 'sendSocialId']);
	$router->post('/user/panel/send-warehouse-password', [UserPanelController::class, 'sendWarehousePassword']);
	$router->get('/page', [PageController::class, 'show']);
	$router->get('/admin', [DashboardController::class, 'index']);
	$router->get('/admin/news', [NewsController::class, 'index']);
	$router->post('/admin/news/create', [NewsController::class, 'create']);
	$router->get('/admin/news/edit', [NewsController::class, 'edit']);
	$router->post('/admin/news/update', [NewsController::class, 'update']);
	$router->post('/admin/news/delete', [NewsController::class, 'delete']);
	$router->get('/admin/settings', [SettingsController::class, 'index']);
	$router->post('/admin/settings', [SettingsController::class, 'save']);
	$router->get('/admin/downloads', [DownloadsController::class, 'index']);
	$router->post('/admin/downloads/create', [DownloadsController::class, 'create']);
	$router->post('/admin/downloads/delete', [DownloadsController::class, 'delete']);
	$router->get('/admin/social', [SocialController::class, 'index']);
	$router->post('/admin/social', [SocialController::class, 'save']);
	$router->get('/admin/pages', [PagesController::class, 'index']);
	$router->post('/admin/pages/create', [PagesController::class, 'create']);
	$router->get('/admin/pages/edit', [PagesController::class, 'edit']);
	$router->post('/admin/pages/update', [PagesController::class, 'update']);
	$router->post('/admin/pages/delete', [PagesController::class, 'delete']);
	$router->get('/admin/accounts', [AccountsController::class, 'index']);
	$router->post('/admin/accounts/ban', [AccountsController::class, 'ban']);
	$router->post('/admin/accounts/unban', [AccountsController::class, 'unban']);
	$router->get('/admin/coins', [CoinsController::class, 'index']);
	$router->post('/admin/coins/update', [CoinsController::class, 'update']);
	$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

} catch (\Throwable $e) {
	error_log("Fatal error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
	error_log("Stack trace: " . $e->getTraceAsString());

	if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] === 'localhost') {
		echo "<h1>Error</h1>";
		echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
		echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
		echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
		echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
	} else {
		http_response_code(500);
		echo "An error occurred. Please check php_errors.log for details.";
	}
}
?>
