<div class="container">
  <h1><?= htmlspecialchars($title) ?></h1>

  <?php if (empty($stories)): ?>
    <p>Nu există încă povești fericite. Fii primul care povestește!</p>
  <?php else: ?>
    <div class="stories-list">
      <?php foreach ($stories as $s): ?>
        <article class="story-card">
          <div class="story-media">
            <?php if (!empty($s['animal_image'])): ?>
              <img class="thumb" src="<?= htmlspecialchars(upload_url($s['animal_image'])) ?>" alt="<?= htmlspecialchars($s['animal_name'] ?? 'Animal') ?>">
            <?php else: ?>
              <div class="placeholder"><?= htmlspecialchars($s['animal_name'] ?? 'Animal') ?></div>
            <?php endif; ?>
            </div>

            <style>
              /* Page-scoped override: make the white container behind this page wider/taller so it doesn't get visually cut */
              .container {
                max-width: 1000px;
                padding: 50px 40px;
              }

              /* Small tweak: allow stories-list to sit nicely with some extra top space */
              .stories-list { margin-top: 28px; }
            </style>
          <div class="story-body">
            <div class="story-meta">Animal: <strong><?= htmlspecialchars($s['animal_name'] ?? '') ?></strong> — Adoptator: <strong><?= htmlspecialchars($s['adopter_name'] ?? '') ?></strong></div>

            <?php if (!empty($s['story_content'])): ?>
              <div class="story-content"><?= nl2br(htmlspecialchars($s['story_content'])) ?></div>
            <?php else: ?>
              <div class="story-empty">Acest animal nu are încă o poveste adăugată.</div>
            <?php endif; ?>

            <?php
              // support multiple images stored as JSON array or a single filename
              $storyImages = [];
              if (!empty($s['story_image'])) {
                $decoded = json_decode($s['story_image'], true);
                if (is_array($decoded)) $storyImages = $decoded;
                else $storyImages = [$s['story_image']];
              }
            ?>
            <?php if (!empty($storyImages)): ?>
              <div class="story-image story-gallery">
                <?php foreach ($storyImages as $img): ?>
                  <img class="thumb story-photo" src="<?= htmlspecialchars(upload_url($img)) ?>" alt="Imagine poveste">
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
            
            <?php if (!empty($_SESSION['user']) && (($_SESSION['user']['role_id'] ?? 1) == 2)): ?>
              <form method="post" action="/site/public/index.php?controller=admin&action=deleteAnimal" onsubmit="return confirm('Sunteți sigur că doriți să ștergeți acest animal? Această operațiune nu poate fi anulată.');" style="margin-top:10px;">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">
                <input type="hidden" name="id" value="<?= (int)($s['animal_id'] ?? 0) ?>">
                <button type="submit" class="delete-btn">🗑️ Șterge animal</button>
              </form>
            <?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
