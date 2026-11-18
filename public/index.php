<?php

require_once "../vendor/autoload.php";

use App\Services\ProductService;

header('Content-Type: application/json');

$productService = new ProductService();

echo json_encode([
	'message' => $productService->getMessage(),
	'php_version' => PHP_VERSION,
]);
