<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/Contact.php';

/**
 * Primește mesajele din formularul de contact, aplică validări simple,
 * loghează informații despre utilizator și oferă interfața de administrare.
 */
class ContactController extends BaseController {

  public function index(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    $this->render('contact', [
      'title' => 'Contact',
      'flash' => $flash
    ]);
  }

  public function send(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->redirect('index.php?controller=contact&action=index');
      return;
    }

    try {
      $this->verifyCsrf();
    } catch (Exception $e) {
      $_SESSION['flash'] = 'Token CSRF invalid.';
      $this->redirect('index.php?controller=contact&action=index');
      return;
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $message === '') {
      $_SESSION['flash'] = 'Completează numele și mesajul.';
      $this->redirect('index.php?controller=contact&action=index');
      return;
    }

    // Persist in database
    try {
      $contactModel = new Contact();
      $contactModel->create([
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'message' => $message,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
      ]);
    } catch (Exception $e) {
      // Fallback to file log if DB unavailable
      $storageDir = __DIR__ . '/../../storage/';
      if (!is_dir($storageDir)) mkdir($storageDir, 0777, true);
      $logFile = $storageDir . 'contacts.log';
      $entry = [
        'time' => date('c'),
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'message' => $message,
        'error' => $e->getMessage()
      ];
      $line = json_encode($entry, JSON_UNESCAPED_UNICODE) . PHP_EOL;
      file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
    }

    $_SESSION['flash'] = 'Mesajul tău a fost înregistrat. Îți vom răspunde cât de curând.';
    $this->redirect('index.php?controller=contact&action=index');
  }

}
