<?php

declare(strict_types=1);
namespace oneX\Core;
use PDO;
use PDOException;

class Database
{
	private static array $connections = [];

	public static function getConnection(string $name = 'site'): PDO
	{
		if (isset(self::$connections[$name])) {
			return self::$connections[$name];
		}

		$config = require dirname(__DIR__, 2) . '/config.php';
		$dbConf = $config['db'];

		if (empty($dbConf['databases'][$name])) {
			throw new \RuntimeException("Database '{$name}' not defined in config.");
		}

		$host    = $dbConf['host'];
		$port    = (int)$dbConf['port'];
		$user    = $dbConf['user'];
		$pass    = $dbConf['pass'];
		$dbname  = $dbConf['databases'][$name];
		$charset = $dbConf['charset'] ?? 'utf8mb4';

		$dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s',
			$host, $port, $dbname, $charset
		);

		try {
			$pdo = new PDO(
				$dsn,
				$user,
				$pass,
				[
					PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					PDO::ATTR_EMULATE_PREPARES   => false,
				]
			);
		} catch (PDOException $e) {
			die('Database connection error: ' . $e->getMessage());
		}

		self::$connections[$name] = $pdo;
		return $pdo;
	}
}
