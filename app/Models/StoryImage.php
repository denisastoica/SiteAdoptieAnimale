<?php
require_once __DIR__ . '/Model.php';

/**
 * Layer dedicat pentru imaginile asociate unei povești – CRUD + operații bulk.
 */
class StoryImage extends Model {
    /** Insert a new image row for a story */
    public function create(int $storyId, string $filename): int {
        $stmt = $this->db->prepare('INSERT INTO story_images (story_id, filename, created_at) VALUES (?, ?, NOW())');
        $stmt->execute([$storyId, $filename]);
        return (int)$this->db->lastInsertId();
    }

    /** Get all images for a story */
    public function getByStory(int $storyId): array {
        $stmt = $this->db->prepare('SELECT id, filename FROM story_images WHERE story_id = ? ORDER BY id ASC');
        $stmt->execute([$storyId]);
        return $stmt->fetchAll();
    }

    /** Delete images for a story by filename (used during replace) */
    public function deleteByStoryExceptFilenames(int $storyId, array $keepFilenames): int {
        if (empty($keepFilenames)) {
            $stmt = $this->db->prepare('DELETE FROM story_images WHERE story_id = ?');
            $stmt->execute([$storyId]);
            return $stmt->rowCount();
        }
        // Build placeholders
        $placeholders = implode(',', array_fill(0, count($keepFilenames), '?'));
        $sql = "DELETE FROM story_images WHERE story_id = ? AND filename NOT IN ($placeholders)";
        $params = array_merge([$storyId], $keepFilenames);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /** Delete images for a story by filename list (exact match) */
    public function deleteByFilenames(int $storyId, array $filenames): int {
        if (empty($filenames)) return 0;
        $placeholders = implode(',', array_fill(0, count($filenames), '?'));
        $sql = "DELETE FROM story_images WHERE story_id = ? AND filename IN ($placeholders)";
        $params = array_merge([$storyId], $filenames);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
}
