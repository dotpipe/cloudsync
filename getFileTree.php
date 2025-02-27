<?php
header('Content-Type: application/json');

if (!isset($_GET['host']) || !isset($_GET['app'])) {
    echo json_encode(["error" => "Missing 'host' or 'app' parameter"]);
    exit;
}

$host = $_GET['host'];
$app = $_GET['app'];
$basePath = realpath("./CloudSync/" . trim($host, '/') . '/' . trim($app, '/'));

if (!$basePath || !is_dir($basePath)) {
    echo json_encode(["error" => "Invalid directory path"]);
    exit;
}

// Function to recursively scan directories
function scanDirectory($dir, $root) {
    $result = [
        "label" => "📂 " . basename($dir), // Folder icon
        "contents" => []
    ];

    foreach (scandir($dir) as $item) {
        if ($item === '.' || $item === '..') continue;

        $path = realpath($dir . DIRECTORY_SEPARATOR . $item);
        $relativePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);

        if (is_dir($path)) {
            // Recursively scan subdirectories with folder icon
            $result["contents"][] = scanDirectory($path, $root);
        } else {
            // File properties with file icon
            $result["contents"][] = [
                "ajax" => "./" . ltrim($relativePath, '/'), // Relative URL path
                "label" => "📄 " . $item, // File icon
                "insert" => "modala-editor",
                "tool-tip" => date("Y-m-d H:i:s", filemtime($path))
            ];
        }
    }

    return $result;
}

$structure = scanDirectory($basePath, $basePath);

echo json_encode($structure, JSON_PRETTY_PRINT);
?>
