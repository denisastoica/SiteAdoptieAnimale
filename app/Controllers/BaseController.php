<?php

/**
 * Excepție specială aruncată când tokenul CSRF este invalid sau lipsește.
 */
class CsrfException extends Exception {}

/**
 * Clasa de bază pentru toate controllerele: se ocupă de randarea view-urilor,
 * pregătirea tokenului CSRF și oferă utilitare comune (redirect, guarduri).
 */
class BaseController {

  protected function render(string $view, array $data = [], string $title = 'PawMatch') {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    if (empty($_SESSION['csrf'])) {
      $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

extract($data);
$csrf = $_SESSION['csrf'];

    // Make upload helper available in all views
    require_once __DIR__ . '/../Helpers/UploadHelper.php';

    $viewPath = __DIR__ . '/../Views/' . $view . '.php';
    require __DIR__ . '/../Views/layouts/main.php';
  }

  protected function verifyCsrf() {
    $ok = isset($_POST['csrf']) && hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf']);
    if (!$ok) {
      throw new CsrfException("Token CSRF invalid sau lipsă.");
    }
  }

  protected function redirect(string $url): void {
    header("Location: $url");
    exit;
  }

  public function home() {
    $this->render('home', ['title' => 'Acasă']);
  }
}
