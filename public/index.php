<?php

header('Content-Type: application/json');

echo json_encode([
	'message' => 'E-commerce API is running!',
	'php_version' => PHP_VERSION,
]);
