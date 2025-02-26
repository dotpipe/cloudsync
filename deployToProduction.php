<?php
function deployToProduction($draftFile) {
    $prodDirectory = '/var/www/html/<app>/production';
    $draftDirectory = '/var/www/html/<app>/drafts';
    $filePath = $draftDirectory . '/' . $draftFile;

    // Extract zip file and overwrite production files
    $zip = new ZipArchive;
    if ($zip->open($filePath) === TRUE) {
        $zip->extractTo($prodDirectory);
        $zip->close();
        echo "Deployment successful to production!";
    } else {
        echo "Failed to deploy draft to production.";
    }
}

// Example usage: deploy draft to production
deployToProduction('tms_20250224_1415.zip');
?>
