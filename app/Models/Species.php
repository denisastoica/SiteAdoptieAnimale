<?php
require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/IModel.php';

/**
 * Modelează speciile disponibile (câine, pisică etc.) și oferă CRUD simplu.
 */
class Species extends Model implements IModel {

  public function all(): array {
    $stmt = $this->db->query("SELECT * FROM species ORDER BY name ASC");
    return $stmt->fetchAll();
  }

  public function create(): int { 
    $stmt = $this->db->prepare("INSERT INTO species (name) VALUES (?)");
    $stmt->execute([$this->name]);
    return (int)$this->db->lastInsertId();
  }

  public function read($id): ?array {
    $stmt = $this->db->prepare("SELECT * FROM species WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();
    return $data ?: null;
  }

  public function update($id, $data): void {
    $stmt = $this->db->prepare("UPDATE species SET name = ? WHERE id = ?");
    $stmt->execute([$data['name'], $id]);
}


  public function delete($id): void {
    $stmt = $this->db->prepare("DELETE FROM species WHERE id = ?");
    $stmt->execute([$id]);
  }
}
