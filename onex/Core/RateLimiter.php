<?php

declare(strict_types=1);
namespace oneX\Core;

class RateLimiter
{
	private const SESSION_PREFIX = 'rate_limit_';
	public static function isLimited(string $action, int $maxAttempts = 5, int $timeWindow = 300): bool
	{
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		$key = self::SESSION_PREFIX . $action;
		$now = time();

		if (!isset($_SESSION[$key])) {
			$_SESSION[$key] = [
				'attempts' => 0,
				'first_attempt' => $now,
			];
		}

		$data = $_SESSION[$key];

		if ($now - $data['first_attempt'] > $timeWindow) {
			$_SESSION[$key] = [
				'attempts' => 0,
				'first_attempt' => $now,
			];
			return false;
		}

		return $data['attempts'] >= $maxAttempts;
	}

	public static function recordAttempt(string $action): void
	{
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		$key = self::SESSION_PREFIX . $action;
		$now = time();

		if (!isset($_SESSION[$key])) {
			$_SESSION[$key] = [
				'attempts' => 1,
				'first_attempt' => $now,
			];
		} else {
			$_SESSION[$key]['attempts']++;
		}
	}

	public static function reset(string $action): void
	{
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		$key = self::SESSION_PREFIX . $action;
		unset($_SESSION[$key]);
	}

	public static function getRemainingTime(string $action, int $timeWindow = 300): int
	{
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		$key = self::SESSION_PREFIX . $action;

		if (!isset($_SESSION[$key])) {
			return 0;
		}

		$elapsed = time() - $_SESSION[$key]['first_attempt'];
		$remaining = $timeWindow - $elapsed;

		return max(0, $remaining);
	}
}
