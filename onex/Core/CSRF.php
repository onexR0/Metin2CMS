<?php

declare(strict_types=1);
namespace oneX\Core;

class CSRF
{
	private const TOKEN_NAME = 'csrf_token';
	private const TOKEN_TIME_NAME = 'csrf_token_time';
	private const TOKEN_LIFETIME = 3600;

	public static function generateToken(): string
	{
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		$token = bin2hex(random_bytes(32));
		$_SESSION[self::TOKEN_NAME] = $token;
		$_SESSION[self::TOKEN_TIME_NAME] = time();

		return $token;
	}

	public static function getToken(): string
	{
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		if (
			!empty($_SESSION[self::TOKEN_NAME]) &&
			!empty($_SESSION[self::TOKEN_TIME_NAME]) &&
			(time() - $_SESSION[self::TOKEN_TIME_NAME]) < self::TOKEN_LIFETIME
		) {
			return $_SESSION[self::TOKEN_NAME];
		}

		return self::generateToken();
	}

	public static function verifyToken(?string $token): bool
	{
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		if (empty($token) || empty($_SESSION[self::TOKEN_NAME])) {
			return false;
		}

		if (
			empty($_SESSION[self::TOKEN_TIME_NAME]) ||
			(time() - $_SESSION[self::TOKEN_TIME_NAME]) >= self::TOKEN_LIFETIME
		) {
			self::clearToken();
			return false;
		}

		return hash_equals($_SESSION[self::TOKEN_NAME], $token);
	}

	public static function clearToken(): void
	{
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		unset($_SESSION[self::TOKEN_NAME], $_SESSION[self::TOKEN_TIME_NAME]);
	}

	public static function inputField(): string
	{
		$token = self::getToken();
		return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
	}
}
