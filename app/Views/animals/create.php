<section class="add-animal">
  <h2>🐾 Adaugă un animal pentru adopție</h2>
  <p>Completează formularul de mai jos pentru a adăuga un nou prieten necuvântător în platformă.</p>

  <form action="/site/public/index.php?controller=animal&action=store" method="POST" enctype="multipart/form-data" class="animal-form">

    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">

    <label for="name">Nume</label>
    <input type="text" id="name" name="name" placeholder="Ex: Bella" required>

    <label for="species_id">Specie</label>
    <select id="species_id" name="species_id" required>
      <option value="">Alege specie</option>
      <?php foreach ($species as $s): ?>
        <option value="<?= htmlspecialchars($s['id']) ?>"><?= htmlspecialchars($s['name']) ?></option>
      <?php endforeach; ?>
    </select>

    <label for="breed">Rasă</label>
    <input type="text" id="breed" name="breed" placeholder="Ex: Labrador, European etc.">

    <label for="sex">Sex</label>
    <select id="sex" name="sex" required>
      <option value="Mascul">Mascul</option>
      <option value="Femelă">Femelă</option>
    </select>

    <label for="age_months">Vârstă (în luni)</label>
    <input type="number" id="age_months" name="age_months" min="1" placeholder="Ex: 12">

    <label for="location_id">Locație</label>
    <select id="location_id" name="location_id" required>
      <option value="">Alege locația</option>
      <?php foreach ($locations as $loc): ?>
        <option value="<?= htmlspecialchars($loc['id']) ?>"><?= htmlspecialchars($loc['name']) ?></option>
      <?php endforeach; ?>
    </select>

    <label for="description">Descriere</label>
    <textarea id="description" name="description" rows="4" placeholder="Scrie câteva detalii despre animal..." required></textarea>

    <label for="image">Imagine</label>
    <input type="file" id="image" name="image" accept="image/*">

  <?php if (!empty($_SESSION['user'])): ?>
      <hr>
      <h3>Poveste (opțional, doar pentru administratori)</h3>
      <label for="story_content">Text poveste</label>
      <textarea id="story_content" name="story_content" rows="4" placeholder="Spune povestea animalului..."></textarea>

      <label for="story_images">Imagini poveste (opțional, poți selecta mai multe)</label>
      <input type="file" id="story_images" name="story_images[]" accept="image/*" multiple>
    <?php endif; ?>

    <button type="submit">💛 Adaugă animal</button>
  </form>
</section>

<style>
.add-animal {
  max-width: 600px;
  margin: 2rem auto;
  background: #fffaf3;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.add-animal h2 {
  text-align: center;
  color: #5b3d00;
  margin-bottom: 1rem;
}

.add-animal p {
  text-align: center;
  color: #6a4c1a;
  font-size: 1rem;
  margin-bottom: 1.5rem;
}

.animal-form {
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
}

.animal-form label {
  font-weight: bold;
  color: #4a2e00;
}

.animal-form input,
.animal-form select,
.animal-form textarea {
  padding: 0.6rem;
  border: 1px solid #d8b76b;
  border-radius: 10px;
  background: #fff;
  font-size: 1rem;
}

.animal-form button {
  background: #fcbf49;
  border: none;
  padding: 0.7rem;
  border-radius: 10px;
  font-size: 1.1rem;
  color: #4a2e00;
  font-weight: bold;
  margin-top: 1rem;
  cursor: pointer;
  transition: background 0.3s;
}

.animal-form button:hover {
  background: #f9a825;
}
</style>
