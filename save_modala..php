<?php
// save_modala.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get content from POST (from the modala-editor text area)
    $content = isset($_POST['modala_editor']) ? $_POST['modala_editor'] : '';
    
    // Optional: Validate that the content is valid JSON
    json_decode($content);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo "Invalid JSON provided.";
        exit;
    }
    
    // Save the JSON content into a file (you can change the file path as needed)
    $result = file_put_contents('modala_content.json', $content);
    if ($result === false) {
        http_response_code(500);
        echo "Failed to save content.";
    } else {
        echo "Content saved successfully.";
    }
}
?>
