<div class="container">
  <h1><?= htmlspecialchars($title) ?></h1>

  <?php if (empty($adoptions)): ?>
    <p>Nu ai nicio adopție aprobată încă.</p>
  <?php else: ?>
    <div class="animal-list">
      <?php foreach ($adoptions as $ad): ?>
        <div class="animal-card">
          <?php if (!empty($ad['image'])): ?>
            <img src="<?= htmlspecialchars(upload_url($ad['image'])) ?>" alt="<?= htmlspecialchars($ad['animal_name']) ?>">
          <?php endif; ?>

          <h3><?= htmlspecialchars($ad['animal_name']) ?></h3>
          <p><strong>Status:</strong> <?= htmlspecialchars($ad['status']) ?></p>
          <?php if (!empty($ad['adoption_date'])): ?>
            <p><small>Data adopției: <?= htmlspecialchars($ad['adoption_date']) ?></small></p>
          <?php endif; ?>

          <?php if (!empty($ad['story'])): ?>
            <a class="button" href="/site/public/index.php?controller=story&action=show&animal_id=<?= (int)$ad['animal_id'] ?>">📖 Vezi poveste</a>
            <a class="button" href="/site/public/index.php?controller=story&action=edit&animal_id=<?= (int)$ad['animal_id'] ?>">✏️ Editează povestea</a>
          <?php else: ?>
            <a class="button" href="/site/public/index.php?controller=story&action=create&animal_id=<?= (int)$ad['animal_id'] ?>">➕ Adaugă o poveste fericită</a>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<style>
  .animal-list { display:flex; flex-wrap:wrap; gap:20px; justify-content:center; margin-top:20px; }
  .animal-card { background:#fffaf0; border-radius:12px; padding:14px; width:220px; text-align:center; box-shadow:0 3px 8px rgba(0,0,0,0.08); }
  .animal-card img { width:100%; height:140px; object-fit:cover; border-radius:8px; }
  .animal-card h3 { margin:10px 0 6px; color:#5b3b00; }
  .animal-card p { margin:4px 0; color:#6b4f1d; }
  .animal-card .button { display:inline-block; background:#fcbf49; color:#4a2e00; padding:8px 12px; border-radius:8px; text-decoration:none; font-weight:700; }
</style>
