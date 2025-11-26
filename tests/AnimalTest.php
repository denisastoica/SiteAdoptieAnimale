<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../app/Models/Animal.php';

class AnimalTest extends TestCase {

    public function testCreateAnimal() {
        $animal = new Animal();
        $this->assertInstanceOf(Animal::class, $animal);
    }

    public function testHasCorrectProperties() {
        $animal = new Animal();
        $this->assertObjectHasProperty('name', $animal);
        $this->assertObjectHasProperty('species_id', $animal);
    }
}
