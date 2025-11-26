<div class="container">
  <h1><?= htmlspecialchars($title) ?></h1>

  <div class="story-create-card">
    <form class="story-form" method="post" action="/site/public/index.php?controller=story&action=store" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
      <input type="hidden" name="animal_id" value="<?= (int)$animal_id ?>">

      <!-- Title removed: stories will contain only the content to keep things simple -->

      <div class="form-row">
        <div class="field">
          <label for="content">Poveste</label>
          <textarea id="content" name="content" rows="8" required></textarea>
        </div>
      </div>

      <div class="form-row form-row-inline">
        <div class="field file-field">
          <label for="images">Imagini (opțional, poți selecta mai multe)</label>
          <input type="file" id="images" name="images[]" accept="image/*" multiple>
        </div>
        <div class="field note-field">
          <p class="hint">Poți adăuga o fotografie pentru a-ți ilustra povestea. Max 3MB.</p>
        </div>
      </div>

      <div class="form-row actions">
        <button type="submit" class="btn-primary">Trimite povestea</button>
      </div>
    </form>
  </div>
</div>
