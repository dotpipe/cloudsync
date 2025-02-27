<?php
// preview_modala.php

// Read the saved JSON content from the file
$jsonSource = file_get_contents('modala_content.json');
if ($jsonSource === false) {
    $jsonSource = '{}'; // fallback to empty JSON object
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Modala Preview</title>
  <!-- Make sure to include your modala function script -->
  <script src="modala.js"></script>
</head>
<body>
  <!-- The following script calls your modala function with the JSON data from the file. -->
  <script>
    modala(<?= $jsonSource ?>, document.body);
  </script>
</body>
</html>
