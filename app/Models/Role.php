<?php
require_once 'Model.php';

/**
 * Expune rolurile definite (user/admin); util pentru validări sau dropdown-uri.
 */
class Role extends Model {
    public function getAllRoles() {
        return ['user', 'admin'];
    }
}
