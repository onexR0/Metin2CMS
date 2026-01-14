<?php

declare(strict_types=1);
namespace oneX\Models;
use oneX\Core\Database;
use oneX\Core\JsonStorage;

class GameStats
{
	private const FILE = 'stats_cache.json';
	private const TTL  = 300;
	public static function get(): array
	{
		$data = JsonStorage::load(self::FILE);
		$now = time();
		$last = isset($data['updated_at']) ? (int)$data['updated_at'] : 0;

		if ($now - $last >= self::TTL) {
			$data = self::refresh();
		}

		return $data;
	}

	private static function refresh(): array
	{
		$dbAccount = Database::getConnection('account');
		$dbPlayer  = Database::getConnection('player');

		$accountsTotal   = (int)$dbAccount->query("SELECT COUNT(*) FROM account")->fetchColumn();
		$charactersTotal = (int)$dbPlayer->query("SELECT COUNT(*) FROM player WHERE name NOT LIKE '[%]%'")->fetchColumn();
		$guildsTotal     = (int)$dbPlayer->query("SELECT COUNT(*) FROM guild")->fetchColumn();

		$playersOnline = Player::countOnlineLastMinutes(5);
		$players24h    = Player::countOnlineLast24h();

		$data = [
			'accounts_total'   => $accountsTotal,
			'characters_total' => $charactersTotal,
			'guilds_total'     => $guildsTotal,
			'players_online'   => $playersOnline,
			'players_24h'      => $players24h,
			'updated_at'       => time(),
		];

		JsonStorage::save(self::FILE, $data);
		return $data;
	}
}
