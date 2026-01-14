<?php

declare(strict_types=1);
namespace oneX\Core;

class Lang
{
	private static array $translations = [];
	private static string $lang = 'ro';
	private static bool $initialized = false;

	public static function init(array $config): void
	{
		if (self::$initialized) return;

		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		$default   = $config['lang']['default'] ?? 'ro';
		$available = $config['lang']['available'] ?? ['ro'];

		if (!empty($_GET['lang']) && in_array($_GET['lang'], $available, true)) {
			$_SESSION['lang'] = $_GET['lang'];
		}

		self::$lang = $_SESSION['lang'] ?? $default;

		$file = dirname(__DIR__, 2) . '/lang/translations.json';
		if (file_exists($file)) {
			$json = file_get_contents($file);
			self::$translations = json_decode($json, true) ?: [];
		}

		self::$initialized = true;
	}

	public static function get(string $key, ?string $lang = null): string
	{
		$lang = $lang ?? self::$lang;
		$parts = explode('.', $key);
		$node = self::$translations;

		foreach ($parts as $part) {
			if (!isset($node[$part])) {
				return $key;
			}
			$node = $node[$part];
		}

		if (is_array($node) && isset($node[$lang])) {
			return $node[$lang];
		}

		return $key;
	}

	public static function currentLang(): string
	{
		return self::$lang;
	}
}
