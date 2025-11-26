<?php
require_once __DIR__ . '/Model.php';

/**
 * Persistă mesajele trimise prin formularul de contact și oferă operații
 * de administrare (listare, schimbare status, ștergere).
 */
class Contact extends Model {

  public function create(array $data): int {
    $stmt = $this->db->prepare(
      'INSERT INTO contacts (name, email, phone, message, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
      $data['name'] ?? null,
      $data['email'] ?? null,
      $data['phone'] ?? null,
      $data['message'] ?? '',
      $data['ip_address'] ?? null,
      $data['user_agent'] ?? null
    ]);
    return (int)$this->db->lastInsertId();
  }

  public function all(): array {
    $stmt = $this->db->query('SELECT * FROM contacts ORDER BY created_at DESC');
    return $stmt->fetchAll();
  }

  public function find(int $id): ?array {
    $stmt = $this->db->prepare('SELECT * FROM contacts WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
  }

  public function updateStatus(int $id, string $status): void {
    $stmt = $this->db->prepare('UPDATE contacts SET status = ? WHERE id = ?');
    $stmt->execute([$status, $id]);
  }

  public function delete(int $id): void {
    $stmt = $this->db->prepare('DELETE FROM contacts WHERE id = ?');
    $stmt->execute([$id]);
  }
}
