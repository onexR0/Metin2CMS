<?php

declare(strict_types=1);
namespace oneX\Core;

class Session
{
	public static function init(array $config): void
	{
		if (session_status() === PHP_SESSION_ACTIVE) {
			return;
		}

		$sessionName = $config['app']['session_name'] ?? 'onix2_session';
		$isSecure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
		session_set_cookie_params([
			'lifetime' => 0,
			'path' => '/',
			'domain' => '',
			'secure' => $isSecure,
			'httponly' => true,
			'samesite' => 'Strict'
		]);
		
		session_name($sessionName);
		session_start();
		
		if (!isset($_SESSION['initiated'])) {
			session_regenerate_id(true);
			$_SESSION['initiated'] = true;
			$_SESSION['created_at'] = time();
		}
		
		if (isset($_SESSION['created_at']) && (time() - $_SESSION['created_at'] > 1800)) {
			session_regenerate_id(true);
			$_SESSION['created_at'] = time();
		}
	}
}
