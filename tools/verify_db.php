<?php
// verify_db.php - quick DB + filesystem integrity checks for story_images
// Run: php tools/verify_db.php

$config = require __DIR__ . '/../config/database.php';
try {
    $pdo = new PDO($config['dsn'], $config['user'], $config['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    echo "DB connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

function hasColumn(PDO $pdo, $table, $column) {
    $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE :col");
    $stmt->execute([':col' => $column]);
    return (bool)$stmt->fetch();
}

echo "== DB integrity checks (" . date('c') . ") ==\n";
// Basic counts
$tables = ['stories','story_images','adoptions','users'];
foreach ($tables as $t) {
    try {
        $c = $pdo->query("SELECT COUNT(*) AS cnt FROM `$t`")->fetchColumn();
        echo "Table $t: $c rows\n";
    } catch (Exception $e) {
        echo "Table $t: ERROR (" . $e->getMessage() . ")\n";
    }
}

// story_images distinct story count
try {
    $c = $pdo->query("SELECT COUNT(DISTINCT story_id) FROM story_images")->fetchColumn();
    echo "story_images distinct story_id count: $c\n";
} catch (Exception $e) {
    echo "story_images distinct story_id count: ERROR (" . $e->getMessage() . ")\n";
}

// orphan story_images (no parent story)
try {
    $orphanCount = $pdo->query("SELECT COUNT(*) FROM story_images si LEFT JOIN stories s ON si.story_id = s.id WHERE s.id IS NULL")->fetchColumn();
    echo "Orphan story_images (no story): $orphanCount\n";
} catch (Exception $e) {
    echo "Orphan story_images check: ERROR (" . $e->getMessage() . ")\n";
}

// stories that have old image-like columns non-empty
$imageCols = ['image','image_deprecated','old_image'];
$presentCols = [];
foreach ($imageCols as $col) {
    if (hasColumn($pdo,'stories',$col)) {
        $presentCols[] = $col;
        $stmt = $pdo->query("SELECT COUNT(*) FROM stories WHERE `$col` IS NOT NULL AND `$col` <> ''");
        $val = $stmt->fetchColumn();
        echo "stories.$col non-empty count: $val\n";
    }
}
if (empty($presentCols)) echo "No legacy image columns found in stories (image/image_deprecated/old_image)\n";

// stories without any story_images
try {
    $noImgCount = $pdo->query("SELECT COUNT(*) FROM stories WHERE id NOT IN (SELECT DISTINCT story_id FROM story_images)")->fetchColumn();
    echo "Stories without story_images: $noImgCount\n";
} catch (Exception $e) {
    echo "Stories without story_images: ERROR (" . $e->getMessage() . ")\n";
}

// stories referencing adoption/user that don't exist
try {
    if (hasColumn($pdo,'stories','adoption_id')) {
        $q = "SELECT COUNT(*) FROM stories s LEFT JOIN adoptions a ON s.adoption_id = a.id WHERE s.adoption_id IS NOT NULL AND a.id IS NULL";
        $bad = $pdo->query($q)->fetchColumn();
        echo "Stories with non-existent adoption_id: $bad\n";
    }
    if (hasColumn($pdo,'stories','user_id')) {
        $q = "SELECT COUNT(*) FROM stories s LEFT JOIN users u ON s.user_id = u.id WHERE s.user_id IS NOT NULL AND u.id IS NULL";
        $bad = $pdo->query($q)->fetchColumn();
        echo "Stories with non-existent user_id: $bad\n";
    }
} catch (Exception $e) {
    echo "FK sanity checks: ERROR (" . $e->getMessage() . ")\n";
}

// Files on disk vs DB
$uploadDir = __DIR__ . '/../public/uploads/';
$filesOnDisk = [];
if (is_dir($uploadDir)) {
    $it = new DirectoryIterator($uploadDir);
    foreach ($it as $f) {
        if ($f->isFile()) $filesOnDisk[] = $f->getFilename();
    }
    echo "Files on disk in public/uploads: " . count($filesOnDisk) . "\n";
} else {
    echo "Upload directory not found: $uploadDir\n";
}

// filenames in DB
try {
    $stmt = $pdo->query('SELECT filename FROM story_images');
    $dbFiles = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Filenames referenced in story_images: " . count($dbFiles) . "\n";

    // missing files referenced
    $missing = array_filter($dbFiles, function($fn) use ($uploadDir){ return !is_file($uploadDir . $fn); });
    echo "Referenced but missing on disk: " . count($missing) . "\n";
    if (count($missing) > 0) {
        echo "Sample missing files:\n";
        foreach (array_slice($missing,0,20) as $m) echo " - $m\n";
    }

    // files on disk not referenced
    $unreferenced = array_values(array_diff($filesOnDisk, $dbFiles));
    echo "Files on disk not referenced in DB: " . count($unreferenced) . "\n";
    if (count($unreferenced) > 0) {
        echo "Sample unreferenced files:\n";
        foreach (array_slice($unreferenced,0,20) as $m) echo " - $m\n";
    }
} catch (Exception $e) {
    echo "File vs DB check: ERROR (" . $e->getMessage() . ")\n";
}

// report a few sample inconsistent stories
try {
    $samples = $pdo->query('SELECT id, animal_id, adoption_id, user_id FROM stories ORDER BY id DESC LIMIT 10')->fetchAll();
    echo "\nRecent stories sample (id,animal_id,adoption_id,user_id):\n";
    foreach ($samples as $s) echo " - {$s['id']},{$s['animal_id']},{$s['adoption_id']},{$s['user_id']}\n";
} catch (Exception $e) {}

echo "\nDone.\n";
