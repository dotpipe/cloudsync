<?php
// Function to create a zipped file of current draft files (excluding images/movies)
function saveDraft() {
    $app = $_GET['appName'];
    $draftDirectory = "/$app/dev";
    $prodDirectory = "/$app/prod";
    $timestamp = time();
    $draftFileName = $draftDirectory . '/tms_' . $timestamp . '.zip';

    $zip = new ZipArchive();
    if ($zip->open($draftFileName, ZipArchive::CREATE) !== TRUE) {
        exit("Cannot open <$draftFileName>\n");
    }

    // Add files (excluding images and movies)
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($prodDirectory),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($iterator as $fileinfo) {
        if (!$fileinfo->isDir()) {
            // Skip images and movies
            if (!in_array(pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION), ['jpg', 'jpeg', 'webp', 'avi', 'mp3', 'png', 'gif', 'mp4', 'mov'])) {
                $zip->addFile($fileinfo->getRealPath(), $fileinfo->getFilename());
            }
        }
    }

    $zip->close();
    echo "Draft saved successfully: $draftFileName";
}

// Call the function to save the draft
saveDraft();
?>
