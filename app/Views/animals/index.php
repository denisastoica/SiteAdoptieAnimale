<section class="animal-list">
  <h2>🐶 Animale disponibile pentru adopție</h2>

  <div class="animals-grid">
    <?php foreach ($animals as $animal): ?>
      <div class="animal-card">
        <img 
          src="<?= htmlspecialchars(upload_url($animal['image'] ?? '')) ?>" 
          alt="<?= htmlspecialchars($animal['name']) ?>"
        >
        
        <h3><?= htmlspecialchars($animal['name']) ?></h3>
        <p><strong>Specie:</strong> <?= htmlspecialchars($animal['species']) ?></p>
  <p><strong>Vârstă:</strong> <?= htmlspecialchars($animal['age_months'] ?? 0) ?> luni</p>
  <p><strong>Locație:</strong> <?= htmlspecialchars($animal['location'] ?? '') ?></p>

        <div class="button-group">
          <a href="/site/public/index.php?controller=animal&action=view&id=<?= $animal['id'] ?>" class="button">
            🐾 Vezi detalii
          </a>

          <?php if (!empty($_SESSION['user']) && (($_SESSION['user']['role_id'] ?? 1) == 2)): ?>
            <a href="/site/public/index.php?controller=animal&action=edit&id=<?= $animal['id'] ?>" class="button-edit">
              ✏️ Editează
            </a>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <style>
    .animal-list {
      padding: 20px;
      text-align: center;
    }

    .animals-grid {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
      margin-top: 20px;
    }

    .animal-card {
      background-color: #fffaf0;
      border-radius: 16px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      padding: 15px;
      width: 240px;
      transition: transform 0.3s;
    }

    .animal-card:hover {
      transform: translateY(-5px);
    }

    .animal-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 12px;
      margin-bottom: 10px;
    }

    .animal-card h3 {
      color: #5b3b00;
      margin-bottom: 6px;
    }

    .animal-card p {
      margin: 2px 0;
      color: #6b4f1d;
      font-size: 0.95rem;
    }

    .button-group {
      margin-top: 10px;
      display: flex;
      justify-content: center;
      gap: 8px;
    }

    .button, .button-edit {
      display: inline-block;
      padding: 7px 12px;
      border-radius: 8px;
      color: #fff;
      font-weight: bold;
      text-decoration: none;
      font-size: 0.9rem;
      transition: background-color 0.3s;
    }

    .button {
      background-color: #fcbf49;
      color: #4a2e00;
    }

    .button:hover {
      background-color: #f9a825;
    }

    .button-edit {
      background-color: #6ab04c;
    }

    .button-edit:hover {
      background-color: #58a239;
    }
  </style>
</section>
