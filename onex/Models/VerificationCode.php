<?php

declare(strict_types=1);
namespace oneX\Models;
use oneX\Core\Database;
use PDO;

class VerificationCode
{
	private const CODE_EXPIRY_MINUTES = 30;

	private static function db(): PDO
	{
		return Database::getConnection('account');
	}

	private static function generateCode(): string
	{
		return str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
	}

	public static function create(int $accountId, string $type, ?string $newEmail = null): ?string
	{
		$db = self::db();
		$stmt = $db->prepare("DELETE FROM verification_codes WHERE account_id = :aid AND type = :type AND used = 0");
		$stmt->execute([':aid' => $accountId, ':type' => $type]);
		$code = self::generateCode();
		$expiresAt = date('Y-m-d H:i:s', time() + (self::CODE_EXPIRY_MINUTES * 60));
		$stmt = $db->prepare("INSERT INTO verification_codes (account_id, code, type, new_email, expires_at, created_at)VALUES (:aid, :code, :type, :email, :expires, NOW())");

		try {
			$success = $stmt->execute([
				':aid'     => $accountId,
				':code'    => $code,
				':type'    => $type,
				':email'   => $newEmail,
				':expires' => $expiresAt,
			]);

			return $success ? $code : null;
		} catch (\PDOException) {
			return null;
		}
	}

	public static function verify(int $accountId, string $code, string $type): ?array
	{
		$db = self::db();
		$stmt = $db->prepare("SELECT * FROM verification_codes WHERE account_id = :aid AND code = :code AND type = :type AND used = 0 AND expires_at > NOW() LIMIT 1");
		$stmt->execute([
			':aid'  => $accountId,
			':code' => $code,
			':type' => $type,
		]);
		$row = $stmt->fetch();
		return $row ?: null;
	}

	public static function markAsUsed(int $id): bool
	{
		$db = self::db();
		$stmt = $db->prepare("UPDATE verification_codes SET used = 1 WHERE id = :id");
		try {
			return $stmt->execute([':id' => $id]);
		} catch (\PDOException) {
			return false;
		}
	}

	public static function cleanupExpired(): void
	{
		$db = self::db();
		$stmt = $db->prepare("DELETE FROM verification_codes WHERE expires_at < NOW()");
		$stmt->execute();
	}

	public static function verifyByLogin(string $login, string $code, string $type): ?array
	{
		$account = GameAccount::findByLogin($login);
		if (!$account) {
			return null;
		}

		return self::verify((int)$account['id'], $code, $type);
	}
}
