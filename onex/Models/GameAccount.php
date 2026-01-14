<?php

declare(strict_types=1);
namespace oneX\Models;
use oneX\Core\Database;
use PDO;

class GameAccount
{
	private static ?array $config = null;

	private static function getConfig(): array
	{
		if (self::$config === null) {
			self::$config = require dirname(__DIR__, 2) . '/config.php';
		}

		return self::$config;
	}
	private static function db(): PDO
	{
		return Database::getConnection('account');
	}

	private static function playerDb(): PDO
	{
		return Database::getConnection('player');
	}

	private static function encodePassword(string $plain): string
	{
		$config = self::getConfig();
		$type   = $config['app']['password_hash'] ?? 'sha1';

		return match ($type) {
			'md5'  => '*' . strtoupper(sha1(sha1($plain, true))),
			'sha1' => sha1($plain),
			default => $plain,
		};
	}

	public static function findByLogin(string $login): ?array
	{
		$db = self::db();
		$stmt = $db->prepare("SELECT * FROM account WHERE login = :l LIMIT 1");
		$stmt->execute([':l' => $login]);
		$row = $stmt->fetch();
		return $row ?: null;
	}

	public static function loginExists(string $login): bool
	{
		return self::findByLogin($login) !== null;
	}

	public static function create(string $login, string $password, string $email): bool
	{
		$db = self::db();
		$pass = self::encodePassword($password);

		$socialId = (string)random_int(1000000, 9999999);

		$stmt = $db->prepare("INSERT INTO account (login, password, email, social_id, create_time) VALUES (:l, :p, :e, :s, NOW())");

		try {
			return $stmt->execute([
				':l' => $login,
				':p' => $pass,
				':e' => $email,
				':s' => $socialId,
			]);
		} catch (\PDOException) {
			return false;
		}
	}

	public static function verifyCredentials(string $login, string $password): ?array
	{
		$account = self::findByLogin($login);
		if (!$account) return null;

		if (isset($account['status']) && strtoupper($account['status']) !== 'OK') {
			return ['banned' => true];
		}

		$input = self::encodePassword($password);
		if (!hash_equals($account['password'], $input)) {
			return null;
		}

		return $account;
	}

	public static function generateSecureCode(string $login): ?string
	{
		$code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
		
		$db = self::db();
		$stmt = $db->prepare("UPDATE account SET secure_code = :code WHERE login = :l");
		
		try {
			$success = $stmt->execute([
				':code' => $code,
				':l' => $login,
			]);
			return $success ? $code : null;
		} catch (\PDOException) {
			return null;
		}
	}

	public static function verifySecureCode(string $login, string $code): bool
	{
		$account = self::findByLogin($login);
		if (!$account || empty($account['secure_code'])) {
			return false;
		}

		return $account['secure_code'] === $code;
	}

	public static function clearSecureCode(string $login): bool
	{
		$db = self::db();
		$stmt = $db->prepare("UPDATE account SET secure_code = NULL WHERE login = :l");
		
		try {
			return $stmt->execute([':l' => $login]);
		} catch (\PDOException) {
			return false;
		}
	}

	public static function updatePassword(string $login, string $newPassword): bool
	{
		$db = self::db();
		$pass = self::encodePassword($newPassword);

		$stmt = $db->prepare("UPDATE account SET password = :p, secure_code = NULL WHERE login = :l");
		
		try {
			return $stmt->execute([
				':p' => $pass,
				':l' => $login,
			]);
		} catch (\PDOException) {
			return false;
		}
	}

	public static function updateEmail(string $login, string $newEmail): bool
	{
		$db = self::db();

		$stmt = $db->prepare("UPDATE account SET email = :e, secure_code = NULL WHERE login = :l");
		
		try {
			return $stmt->execute([
				':e' => $newEmail,
				':l' => $login,
			]);
		} catch (\PDOException) {
			return false;
		}
	}

	public static function getCharacters(string $login): array
	{
		$account = self::findByLogin($login);
		if (!$account) {
			return [];
		}

		$accountId = $account['id'];
		$playerDb = self::playerDb();

		$sql = "SELECT p.id, p.name, p.level, p.exp, p.job, pi.empire FROM player p LEFT JOIN player_index pi ON p.id = pi.id WHERE p.account_id = :account_id AND p.name NOT LIKE '[%]%' ORDER BY p.level DESC, p.exp DESC";

		try {
			$stmt = $playerDb->prepare($sql);
			$stmt->execute([':account_id' => $accountId]);
			$characters = $stmt->fetchAll();

			foreach ($characters as &$char) {
				$char['position'] = self::getCharacterRanking($char['id']);
			}

			return $characters;
		} catch (\PDOException) {
			return [];
		}
	}

	private static function getCharacterRanking(int $characterId): string
	{
		$playerDb = self::playerDb();

		$sql = "SELECT COUNT(*) + 1 as position FROM player p1 INNER JOIN player p2 ON p2.id = :char_id WHERE p1.name NOT LIKE '[%]%' AND (p1.level > p2.level OR (p1.level = p2.level AND p1.exp > p2.exp))";
		try {
			$stmt = $playerDb->prepare($sql);
			$stmt->execute([':char_id' => $characterId]);
			$position = $stmt->fetchColumn();
			
			return (string)$position;
		} catch (\PDOException) {
			return "?";
		}
	}

	public static function getSocialId(string $login): ?string
	{
		$account = self::findByLogin($login);
		if (!$account) {
			return null;
		}

		return $account['social_id'] ?? null;
	}

	public static function getWarehousePassword(string $login): ?string
	{
		$account = self::findByLogin($login);
		if (!$account) {
			return null;
		}

		$accountId = $account['id'];
		$playerDb = self::playerDb();

		try {
			$stmt = $playerDb->prepare("SELECT password FROM safebox WHERE account_id = :account_id LIMIT 1");
			$stmt->execute([':account_id' => $accountId]);
			$password = $stmt->fetchColumn();
			
			return $password ?: null;
		} catch (\PDOException) {
			return null;
		}
	}

	public static function findByEmail(string $email): ?array
	{
		$db = self::db();
		$stmt = $db->prepare("SELECT * FROM account WHERE email = :e LIMIT 1");
		$stmt->execute([':e' => $email]);
		$row = $stmt->fetch();
		return $row ?: null;
	}

	public static function search(string $query): array
	{
		$db = self::db();

		$stmt = $db->prepare("SELECT id, login, email, status, create_time, web_admin, coins, jcoins FROM account WHERE login = :exact OR email = :exact2 LIMIT 1");
		$stmt->execute([':exact' => $query, ':exact2' => $query]);
		$result = $stmt->fetchAll();
		
		if (!empty($result)) {
			return $result;
		}
		
		$stmt = $db->prepare("SELECT id, login, email, status, create_time, web_admin, coins, jcoins FROM account WHERE login LIKE :q1 OR email LIKE :q2 ORDER BY id DESC LIMIT 100");
		$searchParam = "%{$query}%";
		$stmt->execute([':q1' => $searchParam, ':q2' => $searchParam]);
		
		return $stmt->fetchAll();
	}

	public static function updateStatus(string $login, string $status): bool
	{
		$db = self::db();
		$stmt = $db->prepare("UPDATE account SET status = :s WHERE login = :l");
		
		try {
			return $stmt->execute([
				':s' => $status,
				':l' => $login,
			]);
		} catch (\PDOException) {
			return false;
		}
	}

	public static function updateCoins(string $login, int $coins, int $jcoins): bool
	{
		$db = self::db();
		
		try {
			$stmt = $db->prepare("
				UPDATE account SET coins = coins + :coins, jcoins = jcoins + :jcoins WHERE login = :l");
			
			return $stmt->execute([
				':coins' => $coins,
				':jcoins' => $jcoins,
				':l' => $login,
			]);
		} catch (\PDOException) {
			return false;
		}
	}
}
