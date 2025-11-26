<?php
require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/IModel.php';

/**
 * Modelează animalele listate pentru adopție: conține metode CRUD,
 * filtre pentru listare publică și actualizări de status.
 */
class Animal extends Model implements IModel {
  public int $id;
  public string $name;
  public string $breed;
  public string $sex;
  public int $age_months;
  public string $description;
  public int $species_id;
  public int $location_id;
  public string $status = 'available';
  public ?string $image = null;

  public function create(): int {
    $st = $this->db->prepare("
      INSERT INTO animals (name, breed, sex, age_months, description, species_id, location_id, status, image, created_at)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $st->execute([
      $this->name,
      $this->breed,
      $this->sex,
      $this->age_months,
      $this->description,
      $this->species_id,
      $this->location_id,
      $this->status,
      $this->image
    ]);
    return (int)$this->db->lastInsertId();
  }

  public function read($id): ?array {
    $st = $this->db->prepare("
      SELECT a.*, s.name AS species, l.name AS location
      FROM animals a
      JOIN species s ON s.id = a.species_id
      JOIN locations l ON l.id = a.location_id
      WHERE a.id = ?
    ");
    $st->execute([$id]);
    $animal = $st->fetch();
    return $animal ?: null;
  }

  public function update($id, $data): void {
    $stmt = $this->db->prepare("
      UPDATE animals 
      SET name = ?, breed = ?, sex = ?, age_months = ?, description = ?, 
          species_id = ?, location_id = ?, status = ?, image = ?
      WHERE id = ?
    ");
    $stmt->execute([
      $data['name'],
      $data['breed'],
      $data['sex'],
      $data['age_months'],
      $data['description'],
      $data['species_id'],
      $data['location_id'],
      $data['status'],
      $data['image'],
      $id
    ]);
  }

  public function delete($id): void {
    $st = $this->db->prepare("DELETE FROM animals WHERE id = ?");
    $st->execute([$id]);
  }

  public function updateStatus($id, string $status): void {
    $stmt = $this->db->prepare("UPDATE animals SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
  }

  public function getAllAvailable(): array {
    $stmt = $this->db->query("
      SELECT a.*, s.name AS species, l.name AS location
      FROM animals a
      JOIN species s ON s.id = a.species_id
      JOIN locations l ON l.id = a.location_id
      WHERE a.status = 'available'
      ORDER BY a.created_at DESC
    ");
    return $stmt->fetchAll();
  }

  public function allForListing(): array {
    $sql = "
      SELECT a.id, a.name, a.breed, a.sex, a.age_months, a.description,
             s.name AS species, l.name AS location, a.image
      FROM animals a
      JOIN species s ON s.id = a.species_id
      JOIN locations l ON l.id = a.location_id
      WHERE a.status = 'available'
      ORDER BY a.created_at DESC
      LIMIT 100
    ";
    return $this->db->query($sql)->fetchAll();
  }

  public function filter(array $criteria = []): array {
    $where = [];
    $params = [];

    if (!empty($criteria['species_id'])) {
      $where[] = "a.species_id = ?";
      $params[] = $criteria['species_id'];
    }
    if (!empty($criteria['location_id'])) {
      $where[] = "a.location_id = ?";
      $params[] = $criteria['location_id'];
    }
    if (!empty($criteria['sex'])) {
      $where[] = "a.sex = ?";
      $params[] = $criteria['sex'];
    }

    $sql = "
      SELECT a.id, a.name, a.breed, a.sex, a.age_months, a.description,
             s.name AS species, l.name AS location, a.image
      FROM animals a
      JOIN species s ON s.id = a.species_id
      JOIN locations l ON l.id = a.location_id
      WHERE a.status = 'available'
    ";

    if ($where) {
      $sql .= " AND " . implode(" AND ", $where);
    }

    $sql .= " ORDER BY a.created_at DESC";

    $st = $this->db->prepare($sql);
    $st->execute($params);

    return $st->fetchAll();
  }

  public function findById($id): ?array {
    $stmt = $this->db->prepare("SELECT * FROM animals WHERE id = ?");
    $stmt->execute([$id]);
    $result = $stmt->fetch();
    return $result ?: null;
  }
}
