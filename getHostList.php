<?php
header('Content-Type: application/json');

$baseDir = __DIR__ . "/CloudSync/";
$hosts = [];

if (is_dir($baseDir)) {
    $directories = array_filter(glob($baseDir . '*'), 'is_dir');
    foreach ($directories as $dir) {
        $hosts[] = basename($dir); // Keep proper capitalization
    }
}

echo json_encode($hosts);
