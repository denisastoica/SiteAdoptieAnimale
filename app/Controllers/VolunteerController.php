<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/Volunteer.php';

/**
 * Administrează formularul de voluntari: primește cererile publice și permite
 * administratorilor să aprobe sau să respingă înscrierile.
 */
class VolunteerController extends BaseController {

  public function form(): void {
    // show volunteer signup form
    $this->render('volunteer/form', ['title' => 'Fii voluntar']);
  }

  public function submit(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->redirect('index.php?controller=volunteer&action=form');
      return;
    }

    try { $this->verifyCsrf(); } catch (Exception $e) {
      if (session_status() === PHP_SESSION_NONE) session_start();
      $_SESSION['flash'] = 'Token CSRF invalid.';
      $this->redirect('index.php?controller=volunteer&action=form');
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    $v = new Volunteer();
    $userId = $_SESSION['user']['id'] ?? null;
    $v->createWithContact(['user_id' => $userId, 'name' => $name, 'email' => $email, 'phone' => $phone, 'message' => $message]);

    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['flash'] = 'Mulțumim! Ne vom contacta în curând.';
    $this->redirect('index.php?controller=volunteer&action=form');
  }

  // Admin list
  public function list(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['user']) || ($_SESSION['user']['role_id'] ?? 1) != 2) {
      http_response_code(403); exit('Acces interzis');
    }
    $v = new Volunteer();
    $rows = $v->all();
    $this->render('admin/volunteers', ['title' => 'Voluntari', 'volunteers' => $rows]);
  }

  // Admin approves a volunteer request
  public function approve(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['user']) || ($_SESSION['user']['role_id'] ?? 1) != 2) {
      http_response_code(403); exit('Acces interzis');
    }
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) { $this->redirect('/site/public/index.php?controller=volunteer&action=list'); }
    $v = new Volunteer();
    $adminId = $_SESSION['user']['id'] ?? null;
    $ok = $v->approve($id, $adminId);
    $_SESSION['flash'] = $ok ? 'Cerere aprobată.' : 'Eroare la aprobarea cererii.';
    $this->redirect('/site/public/index.php?controller=volunteer&action=list');
  }

  // Admin rejects a volunteer request
  public function reject(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['user']) || ($_SESSION['user']['role_id'] ?? 1) != 2) {
      http_response_code(403); exit('Acces interzis');
    }
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) { $this->redirect('/site/public/index.php?controller=volunteer&action=list'); }
    $v = new Volunteer();
    $adminId = $_SESSION['user']['id'] ?? null;
    $ok = $v->reject($id, $adminId);
    $_SESSION['flash'] = $ok ? 'Cerere respinsă.' : 'Eroare la respingerea cererii.';
    $this->redirect('/site/public/index.php?controller=volunteer&action=list');
  }

  public function delete(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['user']) || ($_SESSION['user']['role_id'] ?? 1) != 2) {
      http_response_code(403); exit('Acces interzis');
    }
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) {
      $this->redirect('/site/public/index.php?controller=volunteer&action=list');
    }
    $v = new Volunteer();
    $v->delete($id);
    $this->redirect('/site/public/index.php?controller=volunteer&action=list');
  }

}
