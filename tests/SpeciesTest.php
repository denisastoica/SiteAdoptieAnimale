<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../app/Models/Species.php';

class SpeciesTest extends TestCase {
    public function testSpeciesClassExists() {
        $this->assertTrue(class_exists('Species'));
    }
}
