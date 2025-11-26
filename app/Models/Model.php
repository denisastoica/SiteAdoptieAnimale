<?php
require_once 'IModel.php';

/**
 * Clasă abstractă pentru toate modelele: se ocupă de inițializarea conexiunii
 * PDO folosind configurația aplicației și expune $db urmașilor.
 */
abstract class Model {
  protected PDO $db;

  public function __construct() {
    $cfg = require __DIR__ . '/../../config/database.php';
    $this->db = new PDO($cfg['dsn'], $cfg['user'], $cfg['pass'], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
  }
}
