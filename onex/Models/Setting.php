<?php

declare(strict_types=1);
namespace oneX\Models;
use oneX\Core\JsonStorage;

class Setting
{
	private const FILE = 'settings.json';
	private static ?array $cache = null;

	private static function allInternal(): array
	{
		if (self::$cache !== null) return self::$cache;
		self::$cache = JsonStorage::load(self::FILE);
		return self::$cache;
	}

	public static function all(): array
	{
		return self::allInternal();
	}

	public static function get(string $name, mixed $default = null): mixed
	{
		$all = self::allInternal();
		return $all[$name] ?? $default;
	}

	public static function set(string $name, string $value): void
	{
		$all = self::allInternal();
		$all[$name] = $value;
		JsonStorage::save(self::FILE, $all);
		self::$cache = $all;
	}
}
