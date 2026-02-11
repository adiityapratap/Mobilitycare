<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search  = $_POST['search'] ?? '';
    $replace = $_POST['replace'] ?? '';
    $action  = $_POST['action'] ?? ''; // check which button was pressed
    $path    = __DIR__; // current folder (portal or opencart root)

    if ($search !== '') {
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        foreach ($rii as $file) {
            if ($file->isDir()) continue;

            // Skip this script itself
            if ($file->getFilename() === basename(__FILE__)) continue;

            $contents = @file_get_contents($file->getPathname());
            if ($contents !== false && stripos($contents, $search) !== false) {
                
                if ($action === 'search') {
                    // Just show where it's found
                    echo "🔍 Found in: " . $file->getPathname() . "<br>";
                }

                if ($action === 'replace') {
                    // Replace and overwrite
                    $newContents = str_replace($search, $replace, $contents);
                    if ($newContents !== $contents) {
                        file_put_contents($file->getPathname(), $newContents);
                        echo "✅ Replaced in: " . $file->getPathname() . "<br>";
                    }
                }
            }
        }
    } else {
        echo "⚠️ Please enter a search string.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search & Replace Tool</title>
</head>
<body>
    <h2>Search & Replace Tool</h2>
    <form method="post">
        <label>Find:</label><br>
        <input type="text" name="search" required><br><br>
        <label>Replace with:</label><br>
        <input type="text" name="replace"><br><br>
        
        <button type="submit" name="action" value="search">🔍 Search Only</button>
        <!--<button type="submit" name="action" value="replace">♻️ Search & Replace</button>-->
    </form>
</body>
</html>
