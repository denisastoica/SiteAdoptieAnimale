<div class="container">
  <h1><?= htmlspecialchars($title) ?></h1>

  <div class="story-create-card">
    <form class="story-form" method="post" action="/site/public/index.php?controller=story&action=update" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
      <input type="hidden" name="animal_id" value="<?= (int)$story['animal_id'] ?>">

      <div class="form-row">
        <div class="field">
          <label for="content">Poveste</label>
          <textarea id="content" name="content" rows="8" required><?= htmlspecialchars($story['content']) ?></textarea>
        </div>
      </div>

      <?php if (!empty($story['image'])):
        $imgs = json_decode($story['image'], true);
        if (!is_array($imgs)) $imgs = [$story['image']];
      ?>
        <div class="form-row current-images">
          <label>Imagini curente (debifează pentru a le șterge)</label>
          <div class="current-images-list">
            <?php foreach ($imgs as $img): ?>
              <div class="current-thumb">
                <img src="<?= htmlspecialchars(upload_url($img)) ?>" alt="imagine">
                <div class="keep-checkbox">
                  <label>
                    <input type="checkbox" name="existing_images[]" value="<?= htmlspecialchars($img) ?>" checked>
                    Păstrează
                  </label>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <p class="hint">Bifează imaginile pe care dorești să le păstrezi. Încărcând fișiere noi le vei adăuga la lista existentă.</p>
        </div>
      <?php endif; ?>

      <div class="form-row form-row-inline">
        <div class="field file-field">
          <label for="images">Încarcă imagini noi (opțional, înlocuiesc pe cele existente)</label>
          <input type="file" id="images" name="images[]" accept="image/*" multiple>
        </div>
      </div>

      <div class="form-row actions">
        <button type="submit" class="btn-primary">Salvează modificările</button>
        <a class="button" href="/site/public/index.php?controller=story&action=show&animal_id=<?= (int)$story['animal_id'] ?>">Anulează</a>
      </div>
    </form>
  </div>
</div>

<style>
  .current-images-list { display:flex; gap:10px; margin:8px 0; }
  .current-thumb img { width:120px; height:90px; object-fit:cover; border-radius:6px; }
</style>
