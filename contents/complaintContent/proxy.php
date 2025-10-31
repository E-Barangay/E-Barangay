<?php
header('Content-Type: application/json');

if (!isset($_GET['lat']) || !isset($_GET['lon'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing coordinates"]);
    exit;
}

$lat = $_GET['lat'];
$lon = $_GET['lon'];
$url = "https://nominatim.openstreetmap.org/reverse?lat=$lat&lon=$lon&format=json";

// Add User-Agent and disable SSL verify for localhost
$opts = [
    "http" => [
        "header" => "User-Agent: E-Barangay/1.0 (contact@example.com)\r\n"
    ],
    "ssl" => [
        "verify_peer" => false,
        "verify_peer_name" => false
    ]
];

$context = stream_context_create($opts);
$response = @file_get_contents($url, false, $context);

if ($response === false) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to contact Nominatim API"]);
    exit;
}

echo $response;
?>
