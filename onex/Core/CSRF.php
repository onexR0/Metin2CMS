<?php

declare(strict_types=1);
namespace oneX\Core;

class CSRF
{
	private static ?array $config = null;

	private static function loadConfig(): array
	{
		if (self::$config === null) {
			$config = require(__DIR__ . '/../../config.php');
			self::$config = $config['csrf'] ?? [];
		}
		return self::$config;
	}

	private static function isEnabled(): bool
	{
		$config = self::loadConfig();
		return (bool)($config['enabled'] ?? true);
	}

	private static function getTokenName(): string
	{
		$config = self::loadConfig();
		return $config['token_name'] ?? 'csrf_token';
	}

	private static function getTokenTimeName(): string
	{
		$config = self::loadConfig();
		return $config['token_time_name'] ?? 'csrf_token_time';
	}

	private static function getTokenLifetime(): int
	{
		$config = self::loadConfig();
		return (int)($config['token_lifetime'] ?? 3600);
	}

	public static function generateToken(): string
	{
		if (!self::isEnabled()) {
			return '';
		}

		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		$token = bin2hex(random_bytes(32));
		$_SESSION[self::getTokenName()] = $token;
		$_SESSION[self::getTokenTimeName()] = time();

		return $token;
	}

	public static function getToken(): string
	{
		if (!self::isEnabled()) {
			return '';
		}

		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		$tokenName = self::getTokenName();
		$tokenTimeName = self::getTokenTimeName();
		$lifetime = self::getTokenLifetime();

		if (
			!empty($_SESSION[$tokenName]) &&
			!empty($_SESSION[$tokenTimeName]) &&
			(time() - $_SESSION[$tokenTimeName]) < $lifetime
		) {
			return $_SESSION[$tokenName];
		}

		return self::generateToken();
	}

	public static function verifyToken(?string $token): bool
	{
		if (!self::isEnabled()) {
			return true;
		}

		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		$tokenName = self::getTokenName();
		$tokenTimeName = self::getTokenTimeName();
		$lifetime = self::getTokenLifetime();

		if (empty($token) || empty($_SESSION[$tokenName])) {
			return false;
		}

		if (
			empty($_SESSION[$tokenTimeName]) ||
			(time() - $_SESSION[$tokenTimeName]) >= $lifetime
		) {
			self::clearToken();
			return false;
		}

		return hash_equals($_SESSION[$tokenName], $token);
	}

	public static function clearToken(): void
	{
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		$tokenName = self::getTokenName();
		$tokenTimeName = self::getTokenTimeName();

		unset($_SESSION[$tokenName], $_SESSION[$tokenTimeName]);
	}

	public static function inputField(): string
	{
		if (!self::isEnabled()) {
			return '';
		}

		$token = self::getToken();
		return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
	}
}

