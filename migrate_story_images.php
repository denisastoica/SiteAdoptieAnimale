<?php
// migrate_story_images.php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=pawmatch;charset=utf8mb4', 'root', ''); // adaptează dacă ai alt user/parolă

$pdo->beginTransaction();

$select = $pdo->query('SELECT id, image, animal_id FROM stories');
$insert = $pdo->prepare('INSERT INTO story_images (story_id, filename) VALUES (:story_id, :filename)');
$update = $pdo->prepare('UPDATE stories SET adoption_id = :adoption_id, user_id = :user_id WHERE id = :story_id');

while ($story = $select->fetch(PDO::FETCH_ASSOC)) {
    $sid = $story['id'];
    $img = $story['image'];
    $aid = $story['animal_id'];

    if (empty($img)) continue;

    $imgs = json_decode($img, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($imgs)) {
        $imgs = [$img]; // fallback: single image string
    }

    foreach ($imgs as $filename) {
        if (!empty($filename)) {
            $insert->execute([':story_id' => $sid, ':filename' => $filename]);
        }
    }

    // opțional: încercăm să asociem automat o adopție
    $adoptStmt = $pdo->prepare("SELECT id, user_id FROM adoptions WHERE animal_id = :aid AND status = 'approved' ORDER BY adoption_date DESC LIMIT 1");
    $adoptStmt->execute([':aid' => $aid]);
    $adopt = $adoptStmt->fetch(PDO::FETCH_ASSOC);
    if ($adopt) {
        $update->execute([
            ':adoption_id' => $adopt['id'],
            ':user_id' => $adopt['user_id'],
            ':story_id' => $sid
        ]);
    }
}

$pdo->commit();

echo "✅ Migrarea a fost finalizată.\n";
