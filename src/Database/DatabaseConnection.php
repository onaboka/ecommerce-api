<?php

namespace App\Database;

use PDO;
use PDOException;

class DatabaseConnection
{
	public function connect(): PDO
	{
		$host = $_ENV['DB_HOST'];
		$port = $_ENV['DB_PORT'];
		$database = $_ENV['DB_DATABASE'];
		$username = $_ENV['DB_USERNAME'];
		$password = $_ENV['DB_PASSWORD'];

		$dsn = "pgsql:host={$host};port={$port};dbname={$database}";

		try {
			return new PDO($dsn, $username, $password, [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES => false,
			]);
		} catch (PDOException $exception) {
			throw new PDOException(
				'Database connection failed.',
				(int) $exception->getCode(),
				$exception
			);
		}
	}
}
