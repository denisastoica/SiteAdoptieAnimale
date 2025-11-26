<section class="animals-section">
  <h2>🐾 Animale disponibile pentru adopție</h2>

  <?php if (empty($animals)): ?>
    <p class="no-animals">Momentan nu sunt animale disponibile pentru adopție. ❤️</p>
  <?php else: ?>
    <div class="animal-list">
      <?php foreach ($animals as $animal): ?>
        <div class="animal-card">
          <img 
            src="<?= htmlspecialchars(upload_url($animal['image'] ?? '')) ?>" 
            alt="<?= htmlspecialchars($animal['name']) ?>"
          >

          <h3><?= htmlspecialchars($animal['name']) ?></h3>
          <p><strong>Specie:</strong> <?= htmlspecialchars($animal['species']) ?></p>
          <p><strong>Rasă:</strong> <?= htmlspecialchars($animal['breed']) ?></p>

          <div class="button-group">
            <a href="/site/public/index.php?controller=animal&action=view&id=<?= $animal['id'] ?>" class="details-btn">
              🐾 Vezi detalii
            </a>

            <?php if (!empty($_SESSION['user']) && (($_SESSION['user']['role_id'] ?? 1) == 2)): ?>
              <a href="/site/public/index.php?controller=animal&action=edit&id=<?= $animal['id'] ?>" class="edit-btn">
                ✏️ Editează
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<style>
.animals-section {
  text-align: center;
  padding: 50px 20px;
}

.animals-section h2 {
  color: #4a2e00;
  font-size: 1.8rem;
  margin-bottom: 30px;
}

.animal-list {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 25px;
}

.animal-card {
  background: #fffaf0;
  border-radius: 16px;
  padding: 20px;
  width: 250px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  transition: all 0.3s;
}

.animal-card:hover {
  transform: translateY(-5px);
}

.animal-card img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 12px;
}

.animal-card h3 {
  color: #5b3b00;
  margin-top: 10px;
  font-size: 1.3rem;
}

.animal-card p {
  color: #6b4f1d;
  font-size: 0.95rem;
  margin: 5px 0;
}

.button-group {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin-top: 10px;
}

.details-btn,
.edit-btn {
  display: inline-block;
  padding: 8px 14px;
  border-radius: 10px;
  text-decoration: none;
  font-weight: bold;
  transition: background-color 0.3s, transform 0.3s;
}

.details-btn {
  background-color: #fcbf49;
  color: #4a2e00;
}

.details-btn:hover {
  background-color: #f9a825;
  transform: scale(1.05);
}

.edit-btn {
  background-color: #6ab04c;
  color: #fff;
}

.edit-btn:hover {
  background-color: #4caf50;
  transform: scale(1.05);
}
</style>
