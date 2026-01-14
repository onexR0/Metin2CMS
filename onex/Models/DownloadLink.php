<?php

declare(strict_types=1);
namespace oneX\Models;
use oneX\Core\JsonStorage;

class DownloadLink
{
	private const FILE = 'downloads.json';

	private static function allInternal(): array
	{
		$data = JsonStorage::load(self::FILE);
		return array_values($data);
	}

	public static function all(): array
	{
		return self::allInternal();
	}

	public static function create(string $label, string $url): void
	{
		$items = self::allInternal();
		$nextId = 1;
		foreach ($items as $i) {
			$nextId = max($nextId, (int)$i['id'] + 1);
		}

		$items[] = [
			'id'    => $nextId,
			'label' => $label,
			'url'   => $url,
		];

		JsonStorage::save(self::FILE, $items);
	}

	public static function delete(int $id): void
	{
		$items = self::allInternal();
		$items = array_values(array_filter($items, fn ($i) => (int)$i['id'] !== $id));
		JsonStorage::save(self::FILE, $items);
	}
}
