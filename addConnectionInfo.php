<?php
header('Content-Type: application/json');

$host = isset($_GET['host']) ? $_GET['host'] : '';
if (!$host) {
    echo json_encode(["error" => "No host specified."]);
    exit;
}

// Expected POST fields: provider, domain, token, fileAddress, status
$provider    = isset($_POST['provider']) ? $_POST['provider'] : '';
$domain      = isset($_POST['domain']) ? $_POST['domain'] : '';
$token       = isset($_POST['token']) ? $_POST['token'] : '';
$fileAddress = isset($_POST['fileAddress']) ? $_POST['fileAddress'] : '';
$status      = isset($_POST['status']) ? $_POST['status'] : 'Unknown';

if (!$provider || !$domain) {
    echo json_encode(["error" => "Missing required fields (provider and domain are required)."]);
    exit;
}

$configFile = __DIR__ . "/config_{$host}.json";

// Load existing data or initialize an empty array
if (file_exists($configFile)) {
    $json = file_get_contents($configFile);
    $data = json_decode($json, true);
    if ($data === null) {
        $data = [];
    }
} else {
    $data = [];
}

// Append the new row
$newRow = [
    "provider"    => $provider,
    "domain"      => $domain,
    "token"       => $token,
    "fileAddress" => $fileAddress,
    "status"      => $status
];
$data[] = $newRow;

// Save updated data
$result = file_put_contents($configFile, json_encode($data, JSON_PRETTY_PRINT));
if ($result === false) {
    echo json_encode(["error" => "Failed to write config file."]);
} else {
    echo json_encode(["success" => true, "message" => "Connection info added successfully."]);
}
?>
