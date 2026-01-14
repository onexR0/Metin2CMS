<?php

declare(strict_types=1);
namespace oneX\Core;
use PDO;
use PDOException;

class DatabaseMigration
{
	private PDO $db;
	private string $migrationFile;

	public function __construct()
	{
		$this->db = Database::getConnection('account');
		$this->migrationFile = dirname(__DIR__, 2) . '/.migration_done';
	}

	public function needsMigration(): bool
	{
		return !file_exists($this->migrationFile);
	}

	public function run(): bool
	{
		try {
			$this->migrateAccountTable();
			file_put_contents($this->migrationFile, date('Y-m-d H:i:s'));
			error_log("[Migration] Database migration completed successfully");
			return true;
		} catch (\Exception $e) {
			error_log("[Migration] Error during migration: " . $e->getMessage());
			return false;
		}
	}

	private function migrateAccountTable(): void
	{
		$requiredColumns = [
			'web_admin' => [
				'type' => 'INT(11)',
				'default' => '0',
				'null' => false
			],
			'coins' => [
				'type' => 'INT(20)',
				'default' => '0',
				'null' => false
			],
			'jcoins' => [
				'type' => 'INT(20)',
				'default' => '0',
				'null' => false
			],
			'secure_code' => [
				'type' => 'VARCHAR(6)',
				'default' => null,
				'null' => true
			]
		];

		foreach ($requiredColumns as $columnName => $columnSpec) {
			if (!$this->columnExists('account', $columnName)) {
				$this->addColumn('account', $columnName, $columnSpec);
				error_log("[Migration] Added column '{$columnName}' to account table");
			} else {
				error_log("[Migration] Column '{$columnName}' already exists in account table");
			}
		}
	}

	private function columnExists(string $table, string $column): bool
	{
		try {
			$stmt = $this->db->prepare("
				SELECT COUNT(*) 
				FROM INFORMATION_SCHEMA.COLUMNS 
				WHERE TABLE_SCHEMA = DATABASE() 
				AND TABLE_NAME = :table 
				AND COLUMN_NAME = :column
			");
			$stmt->execute([
				'table' => $table,
				'column' => $column
			]);
			return (int)$stmt->fetchColumn() > 0;
		} catch (PDOException $e) {
			error_log("[Migration] Error checking column existence: " . $e->getMessage());
			return false;
		}
	}

	private function addColumn(string $table, string $column, array $spec): void
	{
		$sql = "ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$spec['type']}";
		
		if (!$spec['null']) {
			$sql .= " NOT NULL";
		} else {
			$sql .= " NULL";
		}
		
		if ($spec['default'] !== null) {
			$sql .= " DEFAULT '{$spec['default']}'";
		}
		
		try {
			$this->db->exec($sql);
		} catch (PDOException $e) {
			throw new \RuntimeException("Failed to add column '{$column}' to table '{$table}': " . $e->getMessage());
		}
	}
}
