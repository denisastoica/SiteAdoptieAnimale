<section class="auth-section">
  <div class="auth-card">
    <h2>Autentificare</h2>

    <?php if (!empty($error)): ?>
      <div class="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

<form method="post" action="/site/public/index.php?controller=auth&action=login" class="form">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">

      <label for="email">Email</label>
      <input type="email" id="email" name="email">

      <label for="password">Parolă</label>
      <input type="password" id="password" name="password" placeholder="Introdu parola" required>

      <button type="submit">Intră</button>
    </form>

    <p class="register-link">
      Nu ai cont? <a href="/site/public/index.php?controller=auth&action=registerForm">Înregistrează-te</a>
    </p>
  </div>
</section>
