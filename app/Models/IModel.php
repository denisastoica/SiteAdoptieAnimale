<?php
/**
 * Contract minimal pentru modelele CRUD: definește semnăturile standard
 * pentru operațiile create/read/update/delete.
 */
interface IModel {
    public function create(): int;
    public function read($id): ?array;
    public function update($id, $data): void;
    public function delete($id): void;
}
