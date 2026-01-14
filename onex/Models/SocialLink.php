<?php

declare(strict_types=1);
namespace oneX\Models;
use oneX\Core\JsonStorage;

class SocialLink
{
	private const FILE = 'social_links.json';

	public static function all(): array
	{
		return JsonStorage::load(self::FILE);
	}

	public static function setAll(array $links): void
	{
		JsonStorage::save(self::FILE, $links);
	}
}
