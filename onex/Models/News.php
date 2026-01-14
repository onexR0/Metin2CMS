<?php

declare(strict_types=1);
namespace oneX\Models;
use oneX\Core\JsonStorage;

class News
{
	private const FILE = 'news.json';

	private static function allInternal(): array
	{
		$items = JsonStorage::load(self::FILE);
		$items = array_values($items);

		usort($items, function (array $a, array $b): int {
			return strcmp($b['created_at'] ?? '', $a['created_at'] ?? '');
		});

		return $items;
	}

	public static function all(): array
	{
		return self::allInternal();
	}

	public static function latest(int $limit = 5): array
	{
		$items = self::allInternal();
		return array_slice($items, 0, $limit);
	}

	public static function find(int $id): ?array
	{
		foreach (self::allInternal() as $n) {
			if ((int)($n['id'] ?? 0) === $id) {
				return $n;
			}
		}
		return null;
	}

	public static function create(string $title, string $content): void
	{
		$items = self::allInternal();

		$nextId = 1;
		foreach ($items as $n) {
			$nextId = max($nextId, (int)($n['id'] ?? 0) + 1);
		}

		$items[] = [
			'id'         => $nextId,
			'title'      => $title,
			'content'    => $content,
			'created_at' => date('Y-m-d H:i:s'),
		];

		JsonStorage::save(self::FILE, $items);
	}

	public static function update(int $id, string $title, string $content): void
	{
		$items = self::allInternal();

		foreach ($items as &$n) {
			if ((int)($n['id'] ?? 0) === $id) {
				$n['title']   = $title;
				$n['content'] = $content;
				break;
			}
		}
		unset($n);

		JsonStorage::save(self::FILE, $items);
	}

	public static function delete(int $id): void
	{
		$items = self::allInternal();

		$items = array_values(array_filter(
			$items,
			fn(array $n) => (int)($n['id'] ?? 0) !== $id
		));

		JsonStorage::save(self::FILE, $items);
	}
}
