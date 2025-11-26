<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../app/Models/Adoption.php';

class AdoptionTest extends TestCase {
    public function testAdoptionDefaultStatus() {
        $adopt = new Adoption();
        $this->assertEquals('pending', $adopt->status);
    }
}
