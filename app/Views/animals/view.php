<?php
require_once __DIR__ . '/../../Models/Story.php';
$storyModel = new Story();
$story = $storyModel->getByAnimalId($animal['id']);
?>

<section class="animal-details">
  <div class="details-card">
    <img src="<?= htmlspecialchars(upload_url($animal['image'] ?? '')) ?>" 
      alt="<?= htmlspecialchars($animal['name']) ?>">

    <h2><?= htmlspecialchars($animal['name']) ?></h2>

    <ul>
      <li><strong>Specie:</strong> <?= htmlspecialchars($animal['species']) ?></li>
      <li><strong>Rasă:</strong> <?= htmlspecialchars($animal['breed']) ?></li>
      <li><strong>Sex:</strong> <?= htmlspecialchars($animal['sex']) ?></li>
      <li><strong>Vârstă:</strong> <?= htmlspecialchars($animal['age_months']) ?> luni</li>
      <li><strong>Locație:</strong> <?= htmlspecialchars($animal['location']) ?></li>
    </ul>

    <p class="desc"><?= nl2br(htmlspecialchars($animal['description'])) ?></p>

    <!-- ✅ Povestea animalului -->
    <hr style="margin: 25px 0; border: none; border-top: 2px solid #fcbf49;">

    <?php if ($story): ?>
      <section class="animal-story">
        <h3>🐾 Povestea mea: <?= htmlspecialchars($story['title']) ?></h3>
        <p><?= nl2br(htmlspecialchars($story['content'])) ?></p>

        <?php if (!empty($story['image'])): ?>
          <?php
            $imgs = [];
            $dec = json_decode($story['image'], true);
            if (is_array($dec)) $imgs = $dec;
            elseif (!empty($story['image'])) $imgs = [$story['image']];
          ?>
          <div class="story-gallery-full">
            <?php foreach ($imgs as $img): ?>
              <img src="<?= htmlspecialchars(upload_url($img)) ?>" 
                   alt="Povestea animalului" class="story-image" style="max-width:450px !important; width:auto !important; height:auto !important; display:block !important; margin:15px auto !important;">
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>
    <?php else: ?>
      <p class="no-story"><em>Acest animal nu are încă o poveste adăugată.</em></p>
    <?php endif; ?>

    <div class="buttons">
      <a href="/site/public/index.php?controller=animal&action=index" class="back-btn">⬅️ Înapoi la listă</a>

      <?php if (!empty($_SESSION['user']) && (($_SESSION['user']['role_id'] ?? 1) != 2) && (($animal['status'] ?? 'available') === 'available')): ?>
        <form method="post" action="/site/public/index.php?controller=animal&action=adopt" style="display:inline-block; margin-left:10px;">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '') ?>">
          <input type="hidden" name="id" value="<?= (int)$animal['id'] ?>">
          <button type="submit" class="adopt-btn">💛 Solicită adopție</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</section>

<style>
.animal-details {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 50px 20px;
}

.details-card {
  background: #fffaf0;
  border-radius: 20px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  padding: 30px;
  max-width: 650px;
  text-align: center;
}

.details-card img {
  width: 100%;
  max-height: 350px;
  object-fit: cover;
  border-radius: 15px;
  margin-bottom: 20px;
}

.details-card h2 {
  color: #4a2e00;
  font-size: 2rem;
  margin-bottom: 10px;
}

.details-card ul {
  list-style: none;
  padding: 0;
  color: #5b3b00;
  font-size: 1rem;
  line-height: 1.6;
}

.desc {
  color: #6b4f1d;
  margin-top: 20px;
}

/* 🔹 Povestea animalului */
.animal-story {
  background: #fff4e6;
  border-radius: 12px;
  padding: 20px;
  margin-top: 30px;
  color: #5a3d00;
  text-align: left;
}

.animal-story h3 {
  color: #b36b00;
  margin-bottom: 10px;
}

.story-image {
  width: 100%;
  max-width: 450px;
  border-radius: 10px;
  margin-top: 15px;
  display: block;
  margin-left: auto;
  margin-right: auto;
}

.no-story {
  color: #888;
  font-style: italic;
  margin-top: 20px;
}

.back-btn {
  display: inline-block;
  background-color: #fcbf49;
  color: #4a2e00;
  padding: 10px 18px;
  border-radius: 10px;
  text-decoration: none;
  font-weight: bold;
  transition: background 0.3s;
  margin-top: 25px;
}

.adopt-btn {
  background-color: #6ab04c;
  color: #fff;
  padding: 10px 18px;
  border-radius: 10px;
  border: none;
  font-weight: bold;
  cursor: pointer;
}

.adopt-btn:hover {
  background-color: #58a239;
}

.back-btn:hover {
  background-color: #f9a825;
}
</style>
