<section class="auth-section">
  <div class="auth-card">
    <h2>Înregistrare</h2>

    <?php if (!empty($error)): ?>
      <div class="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

<form method="post" action="/site/public/index.php?controller=auth&action=register" class="form">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">

      <label for="name">Nume</label>
      <input type="text" id="name" name="name">

      <label for="email">Email</label>
      <input type="email" id="email" name="email">

      <label for="password">Parolă</label>
      <input type="password" id="password" name="password" placeholder="Minim 6 caractere" required minlength="6">

      <label for="password2">Repetă parola</label>
      <input type="password" id="password2" name="password2" placeholder="Confirmă parola" required minlength="6">

      <button type="submit">Creează cont</button>
    </form>

    <p class="register-link">
      Ai deja cont? <a href="/site/public/index.php?controller=auth&action=loginForm">Autentifică-te</a>
    </p>
  </div>
</section>
