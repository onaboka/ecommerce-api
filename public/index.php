<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Database\DatabaseConnection;
use Dotenv\Dotenv;


$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

header('Content-Type: application/json');

try {
	$database = new DatabaseConnection();

	$connection = $database->connect();

	$statement = $connection->query(
		'SELECT current_database(), current_user, version()'
	);

	$result = $statement->fetch();

	echo json_encode([
		'message' => 'Database connection successful!',
		'database' => $result['current_database'],
		'user' => $result['current_user'],
		'postgresql_version' => $result['version'],
	], JSON_PRETTY_PRINT);

} catch (PDOException $exception) {
	error_log('Database error: ' . $exception->getMessage());

	throw $exception;
}
