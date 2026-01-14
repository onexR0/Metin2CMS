<?php

declare(strict_types=1);
namespace oneX\Models;
use oneX\Core\Database;
use PDO;

class Guild
{
	private static function db(): PDO
	{
		return Database::getConnection('player');
	}

	public static function countGuilds(): int
	{
		$db = self::db();
		$stmt = $db->query("SELECT COUNT(*) FROM guild");
		return (int)$stmt->fetchColumn();
	}

	public static function topGuilds(int $limit = 10): array
	{
		$db = self::db();
		$limit = max(1, min($limit, 100));

		$playerDb = Database::getDatabaseName('player');
		$sql = "SELECT  g.name,  g.ladder_point,  g.level, p.name as leader_name, pi.empire as kingdom
		 FROM guild g LEFT JOIN player p ON g.master = p.id LEFT JOIN {$playerDb}.player_index pi ON g.master = pi.id ORDER BY g.ladder_point DESC LIMIT :lim";
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public static function topGuildsPage(int $page, int $perPage = 20): array
	{
		$db = self::db();
		$perPage = max(1, min($perPage, 20));
		$page    = max(1, $page);
		$offset  = ($page - 1) * $perPage;

		$maxRows = 100;
		if ($offset >= $maxRows) {
			return [];
		}
		$remaining = min($perPage, $maxRows - $offset);

		$playerDb = Database::getDatabaseName('player');
		$sql = "SELECT  g.name,  g.ladder_point,  g.level, p.name as leader_name, pi.empire as kingdom
		 FROM guild g LEFT JOIN player p ON g.master = p.id LEFT JOIN {$playerDb}.player_index pi ON g.master = pi.id ORDER BY g.ladder_point DESC LIMIT :limit OFFSET :offset";
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':limit',  $remaining, PDO::PARAM_INT);
		$stmt->bindValue(':offset', $offset,    PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public static function totalForRanking(): int
	{
		$db = self::db();
		$stmt = $db->query("SELECT COUNT(*) FROM guild");
		$count = (int)$stmt->fetchColumn();
		return min($count, 100);
	}
}
