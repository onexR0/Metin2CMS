<?php

declare(strict_types=1);
namespace oneX\Models;
use oneX\Core\JsonStorage;

class Page
{
	private const FILE = 'pages.json';

	private static function allInternal(): array
	{
		$data = JsonStorage::load(self::FILE);
		return array_values($data);
	}

	public static function all(): array
	{
		return self::allInternal();
	}

	public static function create(string $title, string $content): void
	{
		$pages = self::allInternal();
		$nextId = 1;
		foreach ($pages as $p) {
			$nextId = max($nextId, (int)$p['id'] + 1);
		}

		$slug = preg_replace('/[^a-z0-9\-]+/', '-', strtolower(trim(iconv('UTF-8', 'ASCII//TRANSLIT', $title))));
		$slug = trim($slug, '-');

		$pages[] = [
			'id'         => $nextId,
			'slug'       => $slug,
			'title'      => $title,
			'content'    => $content,
			'created_at' => date('Y-m-d H:i:s'),
		];

		JsonStorage::save(self::FILE, $pages);
	}

	public static function findById(int $id): ?array
	{
		foreach (self::allInternal() as $p) {
			if ((int)$p['id'] === $id) {
				return $p;
			}
		}
		return null;
	}

	public static function update(int $id, string $title, string $content): void
	{
		$pages = self::allInternal();
		$found = false;

		foreach ($pages as $idx => $p) {
			if ((int)$p['id'] === $id) {
				$slug = preg_replace('/[^a-z0-9\-]+/', '-', strtolower(trim(iconv('UTF-8', 'ASCII//TRANSLIT', $title))));
				$slug = trim($slug, '-');

				$pages[$idx]['title'] = $title;
				$pages[$idx]['content'] = $content;
				$pages[$idx]['slug'] = $slug;
				$found = true;
				break;
			}
		}

		if ($found) {
			JsonStorage::save(self::FILE, $pages);
		}
	}

	public static function delete(int $id): void
	{
		$pages = self::allInternal();
		$pages = array_values(array_filter($pages, fn ($p) => (int)$p['id'] !== $id));
		JsonStorage::save(self::FILE, $pages);
	}

	public static function findBySlug(string $slug): ?array
	{
		foreach (self::allInternal() as $p) {
			if (($p['slug'] ?? '') === $slug) {
				return $p;
			}
		}
		return null;
	}
}
