<?php

declare(strict_types=1);
namespace oneX\Models;
use oneX\Core\Database;
use PDO;

class Player
{
	private static function db(): PDO
	{
		return Database::getConnection('player');
	}

	public static function countPlayers(): int
	{
		$db = self::db();
		$stmt = $db->query("SELECT COUNT(*) FROM player WHERE name NOT LIKE '[%]%'");
		return (int)$stmt->fetchColumn();
	}

	public static function topPlayers(int $limit = 10): array
	{
		$db = self::db();
		$limit = max(1, min($limit, 100));

		$sql = "SELECT p.name, p.level, p.exp, p.playtime, pi.empire
		 FROM player p LEFT JOIN player.player_index pi ON p.id = pi.id WHERE p.name NOT LIKE '[%]%' ORDER BY p.level DESC, p.exp DESC LIMIT :lim";
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public static function topPlayersPage(int $page, int $perPage = 20): array
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

		$sql = "SELECT p.name, p.level, p.exp, p.playtime, pi.empire FROM player p LEFT JOIN player.player_index pi ON p.id = pi.id
		 WHERE p.name NOT LIKE '[%]%' ORDER BY p.level DESC, p.exp DESC LIMIT :limit OFFSET :offset";
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':limit',  $remaining, PDO::PARAM_INT);
		$stmt->bindValue(':offset', $offset,    PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public static function totalForRanking(): int
	{
		$db = self::db();
		$stmt = $db->query("SELECT COUNT(*) FROM player WHERE name NOT LIKE '[%]%'");
		$count = (int)$stmt->fetchColumn();
		return min($count, 100);
	}

	public static function countOnlineLastMinutes(int $minutes = 5): int
	{
		$db = self::db();
		$sql = "SELECT COUNT(*) FROM player WHERE last_play > (NOW() - INTERVAL :min MINUTE)";
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':min', $minutes, PDO::PARAM_INT);
		$stmt->execute();
		return (int)$stmt->fetchColumn();
	}

	public static function countOnlineLast24h(): int
	{
		$db = self::db();
		$sql = "SELECT COUNT(*) FROM player WHERE last_play > (NOW() - INTERVAL 24 HOUR)";
		$stmt = $db->prepare($sql);
		$stmt->execute();
		return (int)$stmt->fetchColumn();
	}
}
