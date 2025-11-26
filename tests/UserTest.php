<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../app/Models/User.php';

class UserTest extends TestCase {
    public function testPasswordHashing() {
        $hash = password_hash("test123", PASSWORD_DEFAULT);
        $this->assertTrue(password_verify("test123", $hash));
    }
}
