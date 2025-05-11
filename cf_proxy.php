<?php
header('Content-Type: application/json');

$targetBase = $_SERVER['HTTP_X_API_TARGET'] ?? null;
if (!$targetBase) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required headers']);
    exit;
}

if (!isset($_GET['path'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$path = ltrim($_GET['path'], '/');
$query = $_SERVER['QUERY_STRING'] ?? '';
$query = preg_replace('/^path=[^&]+&?/', '', $query);

$targetUrl = rtrim($targetBase, '/') . '/' . $path;
if (!empty($query)) {
    $targetUrl .= '?' . $query;
}

$headers = [];
foreach (getallheaders() as $key => $value) {
    if (strtolower($key) !== 'host' && strtolower($key) !== 'x-api-target') {
        $headers[] = "$key: $value";
    }
}

$body = file_get_contents('php://input');

$ch = curl_init($targetUrl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
}

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($response === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
} else {
    http_response_code($httpcode);
    echo $response;
}

curl_close($ch);
?>