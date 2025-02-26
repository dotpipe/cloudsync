<?php

// Load configuration from .conf file
$config = parse_ini_file('deploy.conf');

// Get selected host from request
$selected_host = isset($_GET['host']) ? $_GET['host'] : $config['default_host'];
$api_token = $config['cpanel_token'];

// Function to create a backup every 15 minutes
function create_backup() {
    global $config, $selected_host;
    $backup_dir = $config['backup_directory'] . "/$selected_host";
    $timestamp = date('Ymd_His');
    $zip_file = "$backup_dir/memory_$timestamp.zip";
    
    if (!is_dir($backup_dir)) {
        mkdir($backup_dir, 0777, true);
    }
    
    $zip = new ZipArchive();
    if ($zip->open($zip_file, ZipArchive::CREATE) === TRUE) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($config['dev_directory'] . "/$selected_host"), RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $relativePath = substr($file->getRealPath(), strlen($config['dev_directory']) + strlen($selected_host) + 2);
                $zip->addFile($file->getRealPath(), $relativePath);
            }
        }
        $zip->close();
        echo "Backup created: $zip_file\n";
    } else {
        echo "Failed to create backup\n";
    }
}

// Function to save new development changes
function save_dev_changes() {
    global $config, $selected_host;
    $dev_dir = $config['dev_directory'] . "/$selected_host";
    $timestamp = date('Ymd_His');
    $snapshot_dir = "$dev_dir/snapshots/$timestamp";
    
    if (!is_dir($snapshot_dir)) {
        mkdir($snapshot_dir, 0777, true);
    }
    
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dev_dir), RecursiveIteratorIterator::LEAVES_ONLY);
    foreach ($files as $file) {
        if (!$file->isDir()) {
            $relativePath = substr($file->getRealPath(), strlen($dev_dir) + 1);
            $destPath = "$snapshot_dir/$relativePath";
            copy($file->getRealPath(), $destPath);
        }
    }
    echo "Development snapshot saved: $snapshot_dir\n";
}

// Function to manually resolve conflicts
function resolve_conflicts($file_path) {
    $lines = file($file_path, FILE_IGNORE_NEW_LINES);
    foreach ($lines as &$line) {
        if (strpos($line, '<<<<<<<') !== false) {
            $line = '// Conflict resolved manually';
        }
    }
    file_put_contents($file_path, implode("\n", $lines));
}

// Function to upload via cPanel UAPI
function upload_to_cpanel() {
    global $config, $selected_host;
    
    $file_path = $config['deployment_directory'] . "/$selected_host";
    $upload_url = "https://" . $config['cpanel_host'] . ":2083/execute/Fileman/upload_files";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $upload_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: cPanel " . $config['cpanel_user'] . ":" . $config['cpanel_token']
    ]);
    $post_fields = [
        "dir" => "/public_html/" . $config['project_directory'] . "/$selected_host",
        "file-0" => new CURLFile($file_path)
    ];
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    echo "Deployment Response: " . $response . "\n";
}

// Deploy function
function deploy() {
    create_backup();
    save_dev_changes();
    upload_to_cpanel();
    echo "Deployment completed.\n";
}

// Display API Token in a copyable format
echo '<div style="position: fixed; top: 10px; right: 10px; background: #f8f9fa; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">';
echo 'API Token: <span id="apiToken">' . htmlspecialchars($api_token) . '</span> <button onclick="copyToken()">Copy</button>';
echo '</div>';

echo '<script>
function copyToken() {
    var tokenText = document.getElementById("apiToken").innerText;
    navigator.clipboard.writeText(tokenText).then(() => {
        alert("API Token copied to clipboard.");
    }).catch(err => {
        console.error("Failed to copy API Token", err);
    });
}
</script>';

// Open Login Button
echo '<div style="position: fixed; top: 50px; right: 10px; background: #007bff; color: #fff; padding: 10px; border-radius: 5px; cursor: pointer;" onclick="openLogin()">Open Login</div>';
echo '<script>
function openLogin() {
    window.location.href = "login.php";
}
</script>';

// Execute deployment
deploy();

?>
