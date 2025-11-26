<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/User.php';

/**
 * Răspunde de autentificare/înregistrare: afișează formularele, validează datele,
 * verifică token-ul CSRF și gestionează sesiunea utilizatorului.
 */
class AuthController extends BaseController {

public function loginForm(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    // 🔐 Generează token nou de fiecare dată când se încarcă pagina
    $_SESSION['csrf'] = bin2hex(random_bytes(32));

    $this->render('auth/login', [
        'title' => 'Autentificare',
        'csrf' => $_SESSION['csrf']
    ]);
}

  public function registerForm(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
    $this->render('auth/register', ['title' => 'Înregistrare', 'csrf' => $_SESSION['csrf']]);
  }

public function login(): void {
    // 💡 nu verificăm CSRF înainte să avem sesiunea pornită
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $this->render('auth/login', ['error' => 'Completează email și parolă']);
        return;
    }

    // 🔹 verificăm tokenul abia acum, după ce sesiunea e activă
    $this->verifyCsrf();

    $userModel = new User();
    $user = $userModel->verify($email, $password);

    if (!$user) {
        $this->render('auth/login', ['error' => 'Email sau parolă incorectă.']);
        return;
    }

    // 🔐 regenerează sesiunea doar după autentificare reușită
    session_regenerate_id(true);

    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role_id' => $user['role_id'] ?? 1,
      'role' => (($user['role_id'] ?? 1) == 2) ? 'admin' : 'user'
    ];

    header('Location: /site/public/index.php?controller=user&action=profile');
    exit;
}


  public function register(): void {
    $this->verifyCsrf();

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (!$name || !$email || !$password) {
      $this->render('auth/register', ['error' => 'Toate câmpurile sunt obligatorii.']);
      return;
    }

    if ($password !== $password2) {
      $this->render('auth/register', ['error' => 'Parolele nu coincid.']);
      return;
    }

    $userModel = new User();

    if ($userModel->findByEmail($email)) {
      $this->render('auth/register', ['error' => 'Acest email este deja înregistrat.']);
      return;
    }

    $userId = $userModel->create($name, $email, $password, 1);

    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }

    $_SESSION['user'] = [
      'id' => $userId,
      'name' => $name,
      'email' => $email,
      'role_id' => 1
    ];

    header('Location: /site/public/index.php?controller=user&action=profile');
    exit;
  }

  public function logout(): void {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
      );
    }

    session_destroy();
    header('Location: /site/public/index.php');
    exit;
  }
}
