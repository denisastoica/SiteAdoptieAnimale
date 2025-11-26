<?php
require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/IModel.php';

/**
 * Gestionarea orașelor/locațiilor disponibile pentru animale.
 */
class Location extends Model implements IModel {

  public function all(): array {
    $stmt = $this->db->query("SELECT * FROM locations ORDER BY name ASC");
    return $stmt->fetchAll();
  }

  public function create(): int {
    $stmt = $this->db->prepare("INSERT INTO locations (name) VALUES (?)");
    $stmt->execute([$this->name]);
    return (int)$this->db->lastInsertId();
  }

  public function read($id): ?array {
    $stmt = $this->db->prepare("SELECT * FROM locations WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();
    return $data ?: null;
  }

  public function update($id, $data): void {
    $stmt = $this->db->prepare("UPDATE locations SET name = ? WHERE id = ?");
    $stmt->execute([$data['name'], $id]);
  }

  public function delete($id): void {
    $stmt = $this->db->prepare("DELETE FROM locations WHERE id = ?");
    $stmt->execute([$id]);
  }
}
