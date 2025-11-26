<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/Adoption.php';
require_once __DIR__ . '/../Models/Story.php';

/**
 * Controllerul profilului personal: verifică autentificarea, afișează adopțiile
 * utilizatorului și oferă vizualizări dedicate pentru animalele adoptate.
 */
class UserController extends BaseController {

  public function profile(): void {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    if (empty($_SESSION['user'])) {
      $this->redirect('index.php?controller=auth&action=loginForm');
      exit;
    }

    $user = $_SESSION['user'];

    // Preluăm adopțiile utilizatorului și atașăm eventualele povești existente
    $adoptionModel = new Adoption();
    $storyModel = new Story();
    $adoptions = $adoptionModel->getUserAdoptions((int)$user['id']);
    foreach ($adoptions as &$a) {
      $a['story'] = $storyModel->getByAnimalId((int)$a['animal_id']);
    }

    $this->render('auth/profile', [
      'user' => $user,
      'title' => 'Profilul meu',
      'adoptions' => $adoptions
    ]);
  }

  // Pagina separată cu animalele adoptate (doar cele aprobate)
  public function adopted(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['user'])) {
      $this->redirect('index.php?controller=auth&action=loginForm');
      return;
    }

    $user = $_SESSION['user'];

    $adoptionModel = new Adoption();
    $storyModel = new Story();

    $all = $adoptionModel->getUserAdoptions((int)$user['id']);
    // Păstrăm doar adopțiile aprobate
    $adoptions = array_values(array_filter($all, fn($a) => ($a['status'] ?? '') === 'approved'));

    foreach ($adoptions as &$a) {
      $a['story'] = $storyModel->getByAnimalId((int)$a['animal_id']);
    }

    $this->render('user/adopted', [
      'title' => 'Animalele mele adoptate',
      'adoptions' => $adoptions,
    ]);
  }
}
