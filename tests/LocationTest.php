<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../app/Models/Location.php';

class LocationTest extends TestCase {
    public function testAllMethodExists() {
        $loc = new Location();
        $this->assertTrue(method_exists($loc, 'all'));
    }
}
