<?php
require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/StoryImage.php';

/**
 * Coordonează poveștile atașate adopțiilor: creează, actualizează,
 * atașează imagini și verifică integritatea cu adopțiile/animalele.
 */
class Story extends Model {

    // 🔹 Obține povestea unui animal
    public function getByAnimalId(int $animalId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM stories WHERE animal_id = ?");
        $stmt->execute([$animalId]);
        $row = $stmt->fetch() ?: null;
        if ($row) {
            $si = new StoryImage();
            $imgs = $si->getByStory((int)$row['id']);
            $filenames = array_map(function($r){ return $r['filename']; }, $imgs);
            $row['images'] = $filenames;
            $row['image'] = !empty($filenames) ? json_encode($filenames) : null;
        }
        return $row;
    }

    // 🔹 Creează o poveste nouă pentru un animal
    // Title is optional (nullable) — some stories may not have a separate title
    // Optional: $userId = author, $adoptionId = link to specific adoption (nullable)
    public function create(int $animalId, ?string $title, string $content, string $image = null, ?int $userId = null, ?int $adoptionId = null): int {
        // If adoption_id provided, verify it exists and matches the animal_id
        if ($adoptionId !== null) {
            $sv = $this->db->prepare('SELECT id, user_id, animal_id FROM adoptions WHERE id = ? LIMIT 1');
            $sv->execute([$adoptionId]);
            $ar = $sv->fetch();
            if (!$ar) throw new Exception('Invalid adoption_id provided');
            if ((int)$ar['animal_id'] !== $animalId) throw new Exception('adoption.animal_id does not match story.animal_id');
            // Note: we do not force adoption.user_id == userId here to allow admin-authored stories
        }

        $stmt = $this->db->prepare("
    INSERT INTO stories (animal_id, title, content, user_id, adoption_id, created_at)
    VALUES (?, ?, ?, ?, ?, NOW())
");

        $stmt->execute([$animalId, $title, $content, $userId, $adoptionId]);
        $sid = (int)$this->db->lastInsertId();
        if (!empty($image)) {
            $si = new StoryImage();
            $decoded = json_decode($image, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                foreach ($decoded as $f) {
                    if ($f !== '') $si->create($sid, $f);
                }
            } else {
                $si->create($sid, $image);
            }
        }
        return $sid;
    }

    // 🔹 Actualizează povestea unui animal existent
    // Title can be nullable
    // Optional: pass $userId or $adoptionId to update those fields as well (nullable)
    public function updateStory(int $animalId, ?string $title, string $content, string $image = null, ?int $userId = null, ?int $adoptionId = null): void {
        // If adoption_id provided, verify it exists and matches the animal_id
        if ($adoptionId !== null) {
            $sv = $this->db->prepare('SELECT id, user_id, animal_id FROM adoptions WHERE id = ? LIMIT 1');
            $sv->execute([$adoptionId]);
            $ar = $sv->fetch();
            if (!$ar) throw new Exception('Invalid adoption_id provided');
            if ((int)$ar['animal_id'] !== $animalId) throw new Exception('adoption.animal_id does not match story.animal_id');
        }

        // Build dynamic SET clause so we don't overwrite user_id/adoption_id unintentionally
        $set = ['title = ?', 'content = ?'];
        $params = [$title, $content];
        if ($userId !== null) { $set[] = 'user_id = ?'; $params[] = $userId; }
        if ($adoptionId !== null) { $set[] = 'adoption_id = ?'; $params[] = $adoptionId; }
        $params[] = $animalId;

        $sql = "UPDATE stories SET " . implode(', ', $set) . " WHERE animal_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        // If image param provided, update story_images rows to match
        $s = $this->db->prepare('SELECT id FROM stories WHERE animal_id = ? LIMIT 1');
        $s->execute([$animalId]);
        $row = $s->fetch();
        if ($row && $image !== null) {
            $sid = (int)$row['id'];
            $si = new StoryImage();
            $decoded = json_decode($image, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $keep = array_values(array_filter($decoded, function($v){ return $v !== ''; }));
                // get existing filenames
                $existingRows = $si->getByStory($sid);
                $existing = array_map(function($r){ return $r['filename']; }, $existingRows);
                // compute files to delete from disk
                $toDelete = array_values(array_diff($existing, $keep));
                $uploadDir = __DIR__ . '/../../public/uploads/';
                foreach ($toDelete as $fdel) {
                    $path = $uploadDir . $fdel;
                    if (is_file($path)) @unlink($path);
                }
                // delete DB rows not in keep
                $si->deleteByStoryExceptFilenames($sid, $keep);
                // insert any filenames that do not already exist
                $remaining = array_map(function($r){ return $r['filename']; }, $si->getByStory($sid));
                foreach ($keep as $f) {
                    if (!in_array($f, $remaining, true)) {
                        $si->create($sid, $f);
                    }
                }
            } else {
                // single filename: delete all and insert single
                $existingRows = $si->getByStory($sid);
                $existing = array_map(function($r){ return $r['filename']; }, $existingRows);
                $uploadDir = __DIR__ . '/../../public/uploads/';
                foreach ($existing as $fdel) { if (is_file($uploadDir.$fdel)) @unlink($uploadDir.$fdel); }
                $si->deleteByStoryExceptFilenames($sid, []);
                if ($image !== '') $si->create($sid, $image);
            }
        }
    }

    // 🔹 Șterge povestea unui animal
    public function deleteByAnimalId(int $animalId): void {
        $stmt = $this->db->prepare("DELETE FROM stories WHERE animal_id = ?");
        $stmt->execute([$animalId]);
    }

    /**
     * Returnează toate poveștile asociate cu animale adoptate, împreună cu numele animalului și al adoptatorului
     */
    public function getAllWithAnimalAndAdopter(): array {
        $sql = "
            SELECT s.*, an.name AS animal_name, an.image AS animal_image, u.name AS adopter_name
            FROM stories s
            JOIN animals an ON s.animal_id = an.id
            JOIN adoptions ad ON ad.animal_id = an.id AND ad.status = 'approved'
            JOIN users u ON u.id = ad.user_id
            ORDER BY s.id DESC
        ";
        $stmt = $this->db->query($sql);
        $rows = $stmt->fetchAll();
        $si = new StoryImage();
        foreach ($rows as &$r) {
            $imgs = $si->getByStory((int)$r['id']);
            $filenames = array_map(function($x){ return $x['filename']; }, $imgs);
            $r['images'] = $filenames;
            $r['image'] = !empty($filenames) ? json_encode($filenames) : null;
        }
        return $rows;
    }

    /**
     * Returnează toate adopțiile aprobate (animal + adoptator) și, dacă există, povestea asociată.
     * Folosit pentru pagina "Povești fericite" astfel încât animalele adoptate să apară chiar dacă nu au poveste.
     */
    public function getAllAdoptedWithOptionalStories(): array {
        $sql = "
         SELECT ad.id AS adoption_id, ad.user_id AS adopter_id, an.id AS animal_id, an.name AS animal_name, an.image AS animal_image,
             u.name AS adopter_name,
             s.id AS story_id, s.title AS story_title, s.content AS story_content, s.created_at AS story_created_at
            FROM adoptions ad
            JOIN animals an ON ad.animal_id = an.id
            JOIN users u ON u.id = ad.user_id
            LEFT JOIN stories s ON s.animal_id = an.id
            WHERE ad.status = 'approved'
            ORDER BY ad.adoption_date DESC
        ";
        $stmt = $this->db->query($sql);
        $rows = $stmt->fetchAll();
        $si = new StoryImage();
        foreach ($rows as &$r) {
            if (!empty($r['story_id'])) {
                $imgs = $si->getByStory((int)$r['story_id']);
                $filenames = array_map(function($x){ return $x['filename']; }, $imgs);
                $r['story_images'] = $filenames;
                $r['story_image'] = !empty($filenames) ? json_encode($filenames) : $r['story_image'];
            } else {
                $r['story_images'] = [];
            }
        }
        return $rows;
    }
}
