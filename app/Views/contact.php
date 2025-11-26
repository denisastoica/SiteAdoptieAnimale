<div class="container">
  <h1>Contact</h1>

  <?php if (!empty($flash)): ?>
    <div class="flash-message"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <div class="contact-grid">
    <div class="contact-form-card">
      <h2>Trimite-ne un mesaj</h2>
      <form method="post" action="/site/public/index.php?controller=contact&action=send">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
        <label for="name">Nume</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email </label>
        <input type="email" id="email" name="email">

        <label for="phone">Telefon</label>
        <input type="text" id="phone" name="phone">

        <label for="message">Mesaj</label>
        <textarea id="message" name="message" rows="6" required></textarea>

        <div style="margin-top:12px;">
          <button type="submit" class="btn-primary">Trimite mesaj</button>
        </div>
      </form>
    </div>

    <div class="contact-info-card">
      <h2>Date de contact</h2>
  <p><strong>Email:</strong> <a href="mailto:<?= htmlspecialchars(defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'contact@example.com') ?>"><?= htmlspecialchars(defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'contact@example.com') ?></a></p>
      <p><strong>Telefon:</strong> <a href="tel:+40752573405">0752573405</a></p>

      <h3>Harta</h3>
      <div class="map-wrap">
        <iframe
          src="https://www.google.com/maps?q=Strada+Memorandului+32,+Brasov+500045,+Romania&output=embed"
          width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
      </div>

      <p style="margin-top:12px; color:#6b4f1d;">Ne bucurăm să colaborăm cu cabinete veterinare, voluntari și organizații locale. Trimite-ne un mesaj și revenim cu detalii.</p>
    </div>
  </div>
</div>

<style>
  .contact-grid { display:grid; grid-template-columns: 1fr 360px; gap: 20px; max-width: 1000px; margin: 20px auto; }
  .contact-form-card, .contact-info-card { background:#fffdf8; padding:18px; border-radius:10px; box-shadow:0 6px 18px rgba(0,0,0,0.06); text-align:left; }
  .contact-form-card h2, .contact-info-card h2 { margin-top:0; color:#4b2e05; }
  .contact-form-card label { display:block; margin-top:10px; font-weight:600; color:#4b2e05; }
  .contact-form-card input, .contact-form-card textarea { width:100%; padding:10px; border:1px solid #e0d7cc; border-radius:8px; box-sizing:border-box; }
  .map-wrap { width:100%; overflow:hidden; border-radius:8px; margin-top:8px; }
  .flash-message { background:#e6f9ed; border:1px solid #b8e6c4; padding:10px 12px; border-radius:8px; color:#1b6a2f; margin-bottom:12px; }

  @media (max-width: 900px) {
    .contact-grid { grid-template-columns: 1fr; padding: 0 12px; }
  }
</style>
