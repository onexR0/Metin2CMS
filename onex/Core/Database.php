<?php

declare(strict_types=1);
namespace oneX\Core;
use PDO;
use PDOException;

class Database
{
	private static array $connections = [];
	private static ?array $config = null;

	public static function getConnection(string $name = 'site'): PDO
	{
		if (isset(self::$connections[$name])) {
			return self::$connections[$name];
		}

		$config = self::getConfig();
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

	public static function getDatabaseName(string $name): string
	{
		$config = self::getConfig();
		
		if (empty($config['db']['databases'][$name])) {
			throw new \RuntimeException("Database '{$name}' not defined in config.");
		}
		
		return $config['db']['databases'][$name];
	}

	private static function getConfig(): array
	{
		if (self::$config === null) {
			self::$config = require dirname(__DIR__, 2) . '/config.php';
		}
		return self::$config;
	}
}
