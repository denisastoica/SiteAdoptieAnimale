<?php
require_once __DIR__ . '/Model.php';

/**
 * Gestionează cererile de voluntariat, inclusiv fallback-uri pentru situații
 * când schema tabelei diferă și acoperă întreg ciclul de aprobare.
 */
class Volunteer extends Model {

  public function create(array $data): int {
    // Try to store status if the column exists, otherwise fallback to original insert
    try {
      $stmt = $this->db->prepare('INSERT INTO volunteers (user_id, message, status, created_at) VALUES (?, ?, ?, NOW())');
      $stmt->execute([
        $data['user_id'] ?? null,
        $data['message'] ?? '',
        $data['status'] ?? 'pending'
      ]);
    } catch (PDOException $e) {
      // Fallback: table might not have status column
      $stmt = $this->db->prepare('INSERT INTO volunteers (user_id, message, created_at) VALUES (?, ?, NOW())');
      $stmt->execute([
        $data['user_id'] ?? null,
        $data['message'] ?? ''
      ]);
    }
    return (int)$this->db->lastInsertId();
  }

  public function createWithContact(array $data): int {
    // Alternate signature: store name/email/phone/message in the message column as JSON
    $payload = json_encode([
      'name' => $data['name'] ?? null,
      'email' => $data['email'] ?? null,
      'phone' => $data['phone'] ?? null,
      'notes' => $data['message'] ?? null
    ], JSON_UNESCAPED_UNICODE);

    // Try to include a status column if available, fallback otherwise
    try {
      $stmt = $this->db->prepare('INSERT INTO volunteers (user_id, message, status, created_at) VALUES (?, ?, ?, NOW())');
      $stmt->execute([
        $data['user_id'] ?? null,
        $payload,
        $data['status'] ?? 'pending'
      ]);
    } catch (PDOException $e) {
      $stmt = $this->db->prepare('INSERT INTO volunteers (user_id, message, created_at) VALUES (?, ?, NOW())');
      $stmt->execute([
        $data['user_id'] ?? null,
        $payload
      ]);
    }
    return (int)$this->db->lastInsertId();
  }

  public function all(): array {
    // Select all volunteer requests; any additional columns (status, approved_at) will be included if present
    $stmt = $this->db->query('SELECT v.*, u.name as user_name, u.email as user_email FROM volunteers v LEFT JOIN users u ON u.id = v.user_id ORDER BY v.created_at DESC');
    return $stmt->fetchAll();
  }

  /**
   * Set status for a volunteer request. If the volunteers table lacks the status column,
   * attempt to add it (plus approved_by/approved_at) and retry the update.
   */
  public function setStatus(int $id, string $status, ?int $adminId = null): bool {
    try {
      $stmt = $this->db->prepare('UPDATE volunteers SET status = ?, approved_by = ?, approved_at = NOW() WHERE id = ?');
      $stmt->execute([$status, $adminId, $id]);
      return true;
    } catch (PDOException $e) {
      // If column does not exist, try to alter the table to add columns then retry once
      $msg = $e->getMessage();
      if (stripos($msg, 'unknown column') !== false || stripos($msg, 'column') !== false) {
        try {
          $this->db->exec("ALTER TABLE volunteers ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'pending', ADD COLUMN IF NOT EXISTS approved_by INT DEFAULT NULL, ADD COLUMN IF NOT EXISTS approved_at DATETIME DEFAULT NULL");
        } catch (PDOException $ex) {
          // Some MySQL versions don't support IF NOT EXISTS in ADD COLUMN; attempt safe individual adds
          try { $this->db->exec("ALTER TABLE volunteers ADD COLUMN status VARCHAR(20) DEFAULT 'pending'"); } catch (Exception $_) {}
          try { $this->db->exec("ALTER TABLE volunteers ADD COLUMN approved_by INT DEFAULT NULL"); } catch (Exception $_) {}
          try { $this->db->exec("ALTER TABLE volunteers ADD COLUMN approved_at DATETIME DEFAULT NULL"); } catch (Exception $_) {}
        }
        // retry update
        try {
          $stmt = $this->db->prepare('UPDATE volunteers SET status = ?, approved_by = ?, approved_at = NOW() WHERE id = ?');
          $stmt->execute([$status, $adminId, $id]);
          return true;
        } catch (PDOException $_) {
          return false;
        }
      }
      return false;
    }
  }

  public function approve(int $id, ?int $adminId = null): bool {
    return $this->setStatus($id, 'approved', $adminId);
  }

  public function reject(int $id, ?int $adminId = null): bool {
    return $this->setStatus($id, 'rejected', $adminId);
  }

  public function find(int $id): ?array {
    $stmt = $this->db->prepare('SELECT * FROM volunteers WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
  }

  public function delete(int $id): void {
    $stmt = $this->db->prepare('DELETE FROM volunteers WHERE id = ?');
    $stmt->execute([$id]);
  }
}
