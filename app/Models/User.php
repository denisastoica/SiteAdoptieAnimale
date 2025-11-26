<?php
require_once __DIR__ . '/Model.php';

/**
 * Modelează utilizatorii (autentificare, roluri) și oferă operații de bază
 * pentru gestionarea conturilor și validarea credentialelor.
 */
class User extends Model {

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

public function create(string $name, string $email, string $password, int $roleId = 1): int {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $this->db->prepare("
        INSERT INTO users (role_id, email, password, name)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$roleId, $email, $hash, $name]);
    return (int)$this->db->lastInsertId();
}

public function verify($email, $password) {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        return null;
    }

    if (password_verify($password, $user['password'])) {
        return $user;
    }

    return null;
}


    public function read($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id) {
        $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$_POST['name'], $_POST['email'], $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }
}
