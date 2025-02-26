<?php
header('Content-Type: application/json');

$baseDir = './CloudSync/'; // Base directory for all hosts
$host = $_GET['host'] ?? '';

if (!$host) {
    echo json_encode(["error" => "No host specified"]);
    exit;
}

$hostPath = $baseDir . $host;

// Check if the host directory exists
if (!is_dir($hostPath)) {
    echo json_encode(["error" => "Host directory does not exist"]);
    exit;
}

// Get directories (apps) inside the host folder
$apps = [];
foreach (scandir($hostPath) as $entry) {
    if ($entry !== '.' && $entry !== '..' && is_dir($hostPath . '/' . $entry)) {
        $apps[] = $entry;
    }
}

// Return the list of apps as JSON
echo json_encode($apps);
?>