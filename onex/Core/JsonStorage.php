<?php

declare(strict_types=1);
namespace oneX\Core;
class JsonStorage
{
	private static function path(string $file): string
	{
		return dirname(__DIR__, 2) . '/storage/' . $file;
	}

	public static function load(string $file): array
	{
		$path = self::path($file);
		if (!file_exists($path)) {
			return [];
		}
		$json = file_get_contents($path);
		$data = json_decode($json, true);
		return is_array($data) ? $data : [];
	}

	public static function save(string $file, array $data): void
	{
		$path = self::path($file);
		$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

		$fp = fopen($path, 'c+');
		if ($fp === false) {
			throw new \RuntimeException("Cannot write {$path}");
		}

		flock($fp, LOCK_EX);
		ftruncate($fp, 0);
		fwrite($fp, $json);
		fflush($fp);
		flock($fp, LOCK_UN);
		fclose($fp);
	}
}
