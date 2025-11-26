<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'PawMatch') ?></title>
  <link rel="stylesheet" href="/site/public/assets/css/main.css">
</head>
<body>

  <header>
    <nav>
    <a href="/site/public/">Acasă</a>
    <a href="/site/public/index.php?controller=animal&action=index">Caută animale</a>
    <a href="/site/public/index.php?controller=match&action=form">Potrivire</a>
    <a href="/site/public/index.php?controller=story&action=index">Povești fericite</a>
    <a href="/site/public/index.php?controller=about&action=index">Despre noi</a>
    <a href="/site/public/index.php?controller=contact&action=index">Contact</a>

      <?php if (!empty($_SESSION['user'])): ?>
        <span>Bun venit, <?= htmlspecialchars($_SESSION['user']['name']) ?></span>
        <a href="/site/public/index.php?controller=user&action=profile">Profil</a>
        <a href="/site/public/index.php?controller=auth&action=logout">Logout</a>
      <?php else: ?>
        <a href="/site/public/index.php?controller=auth&action=loginForm">Login</a>
        <a href="/site/public/index.php?controller=auth&action=registerForm">Register</a>
      <?php endif; ?>
    </nav>
  </header>

<main>
  <?php require $viewPath; ?>
</main>

  <footer>
    <p>&copy; <?= date('Y') ?> PawMatch ❤️ — Găsește-ți prietenul perfect!</p>
  </footer>

</body>
</html>
