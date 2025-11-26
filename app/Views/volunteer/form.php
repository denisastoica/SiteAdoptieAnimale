<?php
// Improved volunteer signup form (more spacing & responsive). Message is optional.
?>
<div class="volunteer-card">
  <div class="volunteer-inner">
    <h2>Înscrie-te ca voluntar</h2>

    <?php if (!empty($_SESSION['flash'])): ?>
      <div class="flash"><?php echo htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?></div>
    <?php endif; ?>

    <p class="lead">Mulțumim că vrei să ne ajuți! Completează câmpurile de mai jos.</p>

    <form method="post" class="volunteer-form" action="/site/public/index.php?controller=volunteer&action=submit">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">

      <div class="row">
        <div class="col">
          <label for="v-name">Nume <span class="required">*</span></label>
          <input id="v-name" type="text" name="name" required value="<?= htmlspecialchars($_SESSION['user']['name'] ?? '') ?>">
        </div>
        <div class="col">
          <label for="v-email">Email <span class="required">*</span></label>
          <input id="v-email" type="email" name="email" required value="<?= htmlspecialchars($_SESSION['user']['email'] ?? '') ?>">
        </div>
      </div>

      <div class="row">
        <div class="col">
          <label for="v-phone">Telefon <span class="required">*</span></label>
          <input id="v-phone" type="tel" name="phone" required value="<?= htmlspecialchars($_SESSION['user']['phone'] ?? '') ?>">
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <label for="v-message">Mesaj (opțional)</label>
          <textarea id="v-message" name="message" rows="6" placeholder="Spune-ne de ce vrei să fii voluntar, ce experiență ai sau când ești disponibil."></textarea>
        </div>
      </div>

      <div class="actions">
        <button class="primary" type="submit">Trimite cererea</button>
      </div>
    </form>
  </div>
</div>

<style>
.volunteer-card { display:flex; justify-content:center; padding:40px 18px; }
.volunteer-inner { background:#fffaf0; border-radius:14px; padding:28px; width:100%; max-width:820px; box-shadow:0 6px 18px rgba(0,0,0,0.08); }
.volunteer-inner h2 { margin-top:0; color:#4a2e00; }
.lead { color:#6b4f1d; margin-bottom:16px; }
.flash { background:#e6f7e6; border:1px solid #c6e8c6; padding:10px 12px; border-radius:8px; color:#245124; margin-bottom:12px; }
.volunteer-form .row { display:flex; gap:12px; margin-bottom:12px; flex-wrap:wrap; }
.volunteer-form .col { flex:1 1 260px; min-width:180px; }
.volunteer-form .col-12 { flex:1 1 100%; }
.volunteer-form label { display:block; font-weight:600; margin-bottom:6px; color:#4a2e00; }
.volunteer-form input[type="text"], .volunteer-form input[type="email"], .volunteer-form input[type="tel"], .volunteer-form textarea { width:100%; padding:10px 12px; border-radius:8px; border:1px solid #e0d4bf; background:#fff; box-sizing:border-box; }
.volunteer-form textarea { resize:vertical; }
.volunteer-form .actions { text-align:right; margin-top:14px; }
.primary { background:#fcbf49; color:#4a2e00; border:none; padding:10px 18px; border-radius:10px; font-weight:700; cursor:pointer; }
.primary:hover { background:#f7b12a; }
.required { color:#d9534f; }
.small-note p { margin:0; color:#7a5b3a; font-size:0.95rem; }
@media (max-width:600px) {
  .volunteer-form .row { flex-direction:column; }
  .volunteer-form .actions { text-align:center; }
}
</style>
