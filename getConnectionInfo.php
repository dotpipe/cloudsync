<?php
header('Content-Type: application/json');

$host = isset($_GET['host']) ? $_GET['host'] : '';
if (!$host) {
    echo json_encode(["error" => "No host specified."]);
    exit;
}

// Optionally use the mode (dev/prod) if needed:
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'dev';

// Our configuration file is named based on the host:
$configFile = __DIR__ . "/config_{$host}.json";

// If the file doesn’t exist, return an empty array
if (!file_exists($configFile)) {
    echo json_encode([]);
    exit;
}

$json = file_get_contents($configFile);
$data = json_decode($json, true);
if ($data === null) {
    $data = [];
}
echo json_encode($data);
?>
