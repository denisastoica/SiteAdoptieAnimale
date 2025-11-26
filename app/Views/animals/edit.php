<section class="edit-animal">
  <h2>✏️ Editează animalul <?= htmlspecialchars($animal['name']) ?></h2>

  <form action="/site/public/index.php?controller=animal&action=edit&id=<?= $animal['id'] ?>" 
        method="POST" enctype="multipart/form-data" class="edit-form">

    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?? '' ?>">

    <div class="form-group">
      <label for="name">Nume:</label>
      <input type="text" name="name" id="name" value="<?= htmlspecialchars($animal['name']) ?>" required>
    </div>

    <div class="form-group">
      <label for="breed">Rasă:</label>
      <input type="text" name="breed" id="breed" value="<?= htmlspecialchars($animal['breed']) ?>" required>
    </div>

    <div class="form-group">
      <label for="sex">Sex:</label>
      <select name="sex" id="sex">
        <option value="Mascul" <?= $animal['sex'] === 'Mascul' ? 'selected' : '' ?>>Mascul</option>
        <option value="Femelă" <?= $animal['sex'] === 'Femelă' ? 'selected' : '' ?>>Femelă</option>
      </select>
    </div>

    <div class="form-group">
      <label for="age_months">Vârstă (luni):</label>
      <input type="number" name="age_months" id="age_months" min="0" 
             value="<?= htmlspecialchars($animal['age_months']) ?>">
    </div>

    <div class="form-group">
      <label for="species_id">Specie:</label>
      <select name="species_id" id="species_id">
        <?php foreach ($species as $sp): ?>
          <option value="<?= $sp['id'] ?>" <?= $animal['species_id'] == $sp['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($sp['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="location_id">Oraș:</label>
      <select name="location_id" id="location_id">
        <?php foreach ($locations as $loc): ?>
          <option value="<?= $loc['id'] ?>" <?= $animal['location_id'] == $loc['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($loc['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="description">Descriere:</label>
      <textarea name="description" id="description" rows="4"><?= htmlspecialchars($animal['description']) ?></textarea>
    </div>

    <div class="form-group">
      <label>Imagine actuală:</label><br>
      <?php if (!empty($animal['image'])): ?>
        <img src="<?= htmlspecialchars(upload_url($animal['image'])) ?>" 
             alt="Imagine animal" class="preview-image">
      <?php else: ?>
        <p><em>Fără imagine</em></p>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label for="image">Încarcă o imagine nouă:</label>
      <input type="file" name="image" id="image" accept="image/*">
    </div>

    <?php
      if (!empty($_SESSION['user']) && (($_SESSION['user']['role_id'] ?? 1) == 2)):
        require_once __DIR__ . '/../../Models/Story.php';
        $storyModel = new Story();
        $existingStory = $storyModel->getByAnimalId($animal['id']);
    ?>
      <hr>
      <h3>Poveste animal (opțional)</h3>
      <label for="story_content">Text poveste</label>
      <textarea id="story_content" name="story_content" rows="4"><?= htmlspecialchars($existingStory['content'] ?? '') ?></textarea>

      <?php if (!empty($existingStory['image'])):
        $imgs = json_decode($existingStory['image'], true);
        if (!is_array($imgs)) $imgs = [$existingStory['image']];
      ?>
        <div class="current-admin-images">
          <label>Imagini curente poveste</label>
          <div style="display:flex; gap:8px; margin:8px 0;">
            <?php foreach ($imgs as $img): ?>
              <div style="width:120px"><img src="<?= htmlspecialchars(upload_url($img)) ?>" style="width:100%; height:80px; object-fit:cover; border-radius:6px;"></div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <label for="story_images">Încarcă imagini poveste (opțional, multiple; se adaugă la cele existente)</label>
      <input type="file" id="story_images" name="story_images[]" accept="image/*" multiple>
    <?php endif; ?>

    </form>

    <div class="action-row">
      <form id="save-animal-form" action="/site/public/index.php?controller=animal&action=edit&id=<?= $animal['id'] ?>" method="POST" enctype="multipart/form-data" style="display:none;">
        <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?? '' ?>">
      </form>

      <button type="submit" form="" class="save-btn" onclick="document.querySelector('.edit-form').submit();">💾 Salvează modificările</button>

      <form id="delete-animal-form" action="/site/public/index.php?controller=animal&action=delete" method="POST" onsubmit="return confirm('Sigur vrei să ștergi acest animal? Această acțiune este ireversibilă.');" style="display:inline-block; margin-left:12px;">
        <input type="hidden" name="id" value="<?= $animal['id'] ?>">
        <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?? '' ?>">
        <button type="submit" class="delete-btn">🗑️ Șterge animalul</button>
      </form>
    </div>
</section>

<style>
.edit-animal {
  max-width: 600px;
  margin: 40px auto;
  background: #fffaf0;
  padding: 25px;
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.edit-animal h2 {
  text-align: center;
  color: #4a2e00;
  margin-bottom: 20px;
}

.edit-form .form-group {
  margin-bottom: 15px;
  text-align: left;
}

.edit-form label {
  font-weight: bold;
  color: #5b3b00;
}

.edit-form input,
.edit-form select,
.edit-form textarea {
  width: 100%;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 8px;
  margin-top: 5px;
  font-size: 1rem;
}

.preview-image {
  width: 100%;
  max-height: 250px;
  object-fit: cover;
  border-radius: 10px;
  margin-top: 10px;
}

.action-row { margin-top: 18px; display:flex; gap:12px; justify-content:flex-start; }
.save-btn {
  display: inline-block;
  padding: 10px 18px;
  border: none;
  border-radius: 10px;
  background-color: #fcbf49;
  color: #4a2e00;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.2s;
}
.save-btn:hover { background-color: #f9a825; }
.delete-btn {
  display: inline-block;
  padding: 10px 14px;
  border: none;
  border-radius: 10px;
  background-color: #e74c3c;
  color: #fff;
  font-weight: bold;
  cursor: pointer;
}
.delete-btn:hover { background-color: #c0392b; }
</style>
