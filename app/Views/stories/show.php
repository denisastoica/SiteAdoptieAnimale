<div class="container">
  <h1><?= htmlspecialchars($title) ?></h1>

  <div class="story-card">
    <?php if (!empty($story['image'])):
      $imgs = json_decode($story['image'], true);
      if (!is_array($imgs)) $imgs = [$story['image']];
    ?>
      <div class="story-gallery">
        <?php foreach ($imgs as $img): ?>
          <div class="story-photo">
            <img src="<?= htmlspecialchars(upload_url($img)) ?>" alt="imagine poveste">
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="story-content">
      <p><?= nl2br(htmlspecialchars($story['content'])) ?></p>
      <?php if (!empty($story['created_at'])): ?>
        <p class="muted"><small>Adăugată la: <?= htmlspecialchars($story['created_at']) ?></small></p>
      <?php endif; ?>
    </div>

    <div class="story-actions">
      <a class="button" href="/site/public/index.php?controller=story&action=edit&animal_id=<?= (int)$story['animal_id'] ?>">✏️ Editează povestea</a>
      <a class="button" href="/site/public/index.php?controller=story&action=index">⬅️ Înapoi la Povești fericite</a>
    </div>
  </div>
</div>

<style>
  .story-card { background:#fff; padding:18px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06); }
  .story-gallery { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:12px; }
  .story-photo img { width:200px; height:140px; object-fit:cover; border-radius:8px; }
  .story-content { margin-top:8px; }
  .story-actions { margin-top:14px; display:flex; gap:10px; }
</style>
