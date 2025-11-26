<?php
require_once __DIR__ . '/Model.php';

/**
 * Persistă potrivirile generate de chestionar și oferă interogări pentru
 * utilizatori sau animale specifice.
 */
class MatchModel extends Model {

  /**
   * Salvează o potrivire în baza de date
   */
  public function create(int $userId, int $animalId, int $compatibility): int {
    $stmt = $this->db->prepare("
      INSERT INTO matches (user_id, animal_id, compatibility, created_at)
      VALUES (?, ?, ?, NOW())
    ");
    $stmt->execute([$userId, $animalId, $compatibility]);
    return (int)$this->db->lastInsertId();
  }

  /**
   * Obține toate potrivirile unui utilizator
   */
  public function getByUserId(int $userId): array {
    $stmt = $this->db->prepare("
      SELECT m.*, a.name AS animal_name, a.image AS animal_image, s.name AS species
      FROM matches m
      JOIN animals a ON a.id = m.animal_id
      JOIN species s ON s.id = a.species_id
      WHERE m.user_id = ?
      ORDER BY m.created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
  }

  /**
   * Obține potrivirile pentru un animal specific
   */
  public function getByAnimalId(int $animalId): array {
    $stmt = $this->db->prepare("
      SELECT m.*, u.name AS user_name
      FROM matches m
      JOIN users u ON u.id = m.user_id
      WHERE m.animal_id = ?
      ORDER BY m.compatibility DESC, m.created_at DESC
    ");
    $stmt->execute([$animalId]);
    return $stmt->fetchAll();
  }
}

