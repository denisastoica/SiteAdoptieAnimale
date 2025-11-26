<?php
require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/IModel.php';

/**
 * Reprezintă cererile de adopție: salvează relația utilizator-animal și
 * asigură actualizarea statusului plus interogări dedicate.
 */
class Adoption extends Model implements IModel {

  public int $id;
  public int $user_id;
  public int $animal_id;
  public string $status = 'pending';
  public ?string $adoption_date = null;

  public function create(): int {
    $stmt = $this->db->prepare("INSERT INTO adoptions (user_id, animal_id, status) VALUES (?, ?, ?)");
    $stmt->execute([$this->user_id, $this->animal_id, $this->status]);
    return (int)$this->db->lastInsertId();
  }

  public function read($id): ?array {
    $stmt = $this->db->prepare("
      SELECT a.*, u.name AS user_name, an.name AS animal_name 
      FROM adoptions a
      JOIN users u ON u.id = a.user_id
      JOIN animals an ON an.id = a.animal_id
      WHERE a.id = ?
    ");
    $stmt->execute([$id]);
    $result = $stmt->fetch();
    return $result ?: null;
  }

  public function update($id, $data): void {
    $newStatus = $data['status'] ?? $this->status;
    if ($newStatus === 'approved') {
      $stmt = $this->db->prepare("UPDATE adoptions SET status = ?, adoption_date = NOW() WHERE id = ?");
      $stmt->execute([$newStatus, $id]);
    } else {
      $stmt = $this->db->prepare("UPDATE adoptions SET status = ? WHERE id = ?");
      $stmt->execute([$newStatus, $id]);
    }
  }

  public function delete($id): void {
    $stmt = $this->db->prepare("DELETE FROM adoptions WHERE id = ?");
    $stmt->execute([$id]);
  }

  public function createAdoption($user_id, $animal_id): int {
    $stmt = $this->db->prepare("INSERT INTO adoptions (user_id, animal_id, status) VALUES (?, ?, 'pending')");
    $stmt->execute([$user_id, $animal_id]);
    return (int)$this->db->lastInsertId();
  }

  public function getUserAdoptions($user_id): array {
    $stmt = $this->db->prepare("
      SELECT a.*, an.name AS animal_name, an.image
      FROM adoptions a
      JOIN animals an ON a.animal_id = an.id
      WHERE a.user_id = ?
      ORDER BY a.adoption_date DESC
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
  }

  public function getAllPending(): array {
    $stmt = $this->db->query("
      SELECT a.id, u.name AS user_name, an.name AS animal_name, a.status
      FROM adoptions a
      JOIN users u ON u.id = a.user_id
      JOIN animals an ON an.id = a.animal_id
      WHERE a.status = 'pending'
    ");
    return $stmt->fetchAll();
  }

  public function getAll(): array {
    $stmt = $this->db->query("
      SELECT a.id, u.name AS user_name, an.name AS animal_name, a.status
      FROM adoptions a
      JOIN users u ON u.id = a.user_id
      JOIN animals an ON an.id = a.animal_id
      ORDER BY FIELD(a.status, 'pending','approved','rejected'), a.id DESC
    ");
    return $stmt->fetchAll();
  }

  /**
   * Verifică dacă un utilizator este adoptatorul aprobat pentru un anumit animal
   */
  public function isUserAdopter(int $user_id, int $animal_id): bool {
    $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM adoptions WHERE user_id = ? AND animal_id = ? AND status = 'approved'");
    $stmt->execute([$user_id, $animal_id]);
    $row = $stmt->fetch();
    return ($row && (int)$row['cnt'] > 0);
  }
}
