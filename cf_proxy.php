
<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-API-Key, Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$configPath = __DIR__ . '/data/config.json';
if (!file_exists($configPath)) {
    http_response_code(500);
    echo json_encode(['error' => 'Missing config.json']);
    exit;
}

$config = json_decode(file_get_contents($configPath), true);
$apiUrl = rtrim($config['url'] ?? '', '/');
$token = $config['token'] ?? '';
$lang = $config['lang'] ?? 'en';

if (!$apiUrl || !$token) {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid configuration']);
    exit;
}

$targetPath = $_GET['path'] ?? '';
$query = $_SERVER['QUERY_STRING'] ?? '';
$query = preg_replace('/(^|&)path=[^&]*/', '', $query);
$targetUrl = $apiUrl . '/' . ltrim($targetPath, '/') . ($query ? "?$query" : '');

$method = $_SERVER['REQUEST_METHOD'];
$headers = [
    "X-API-Key: $token",
    "X-App-Lang: $lang",
    "Accept: application/json"
];
$body = file_get_contents('php://input');

$ch = curl_init($targetUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
}
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);

http_response_code($httpCode);
header("Content-Type: $contentType");
echo $response;
