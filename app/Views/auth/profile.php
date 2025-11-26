<section class="profile-container">
  <div class="profile-card">
    <h2>👤 Profilul meu</h2>

    <p><strong>Nume:</strong> <?= htmlspecialchars($user['name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
<p><strong>Rol:</strong> <?= ucfirst($_SESSION['user']['role'] ?? 'Utilizator') ?></p>

    <div class="profile-actions">
      <?php if (($_SESSION['user']['role_id'] ?? 1) == 2): ?>
      <a href="/site/public/index.php?controller=admin&action=adoptions" class="profile-btn add">
       ➕  Cereri adoptie
      </a>
      <a href="/site/public/index.php?controller=admin&action=contacts" class="profile-btn add">
        ✉️ Mesaje utilizatori
      </a>
      <a href="/site/public/index.php?controller=volunteer&action=list" class="profile-btn add">
        🤝 Cereri voluntari
      </a>
      <?php endif; ?>
      <?php if (($_SESSION['user']['role_id'] ?? 1) != 2): ?>
      <a href="/site/public/index.php?controller=volunteer&action=form" class="profile-btn add">
        🤝 Vreau să fiu voluntar
      </a>
      <?php endif; ?>
      <a href="/site/public/index.php?controller=animal&action=create" class="profile-btn add">
        ➕ Adaugă un animal spre adopție
      </a>
      <?php if (($_SESSION['user']['role_id'] ?? 1) != 2): ?>
      <a href="/site/public/index.php?controller=user&action=adopted" class="profile-btn list">
        🏠 Animalele mele adoptate
      </a>
      <?php endif; ?>
      <a href="/site/public/index.php?controller=animal&action=index" class="profile-btn list">
        🐾 Vezi animalele disponibile
      </a>
      <a href="/site/public/index.php?controller=auth&action=logout" class="profile-btn logout">
        🚪 Deconectează-te
      </a>
    </div>
  </div>
</section>

  <!-- Removed inline adoptions list to keep profile clean. Use the "Animalele mele adoptate" page instead. -->

<style>
.profile-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 60px 20px;
}

.profile-card {
  background: #fffaf0;
  border-radius: 20px;
  padding: 40px;
  width: 100%;
  max-width: 450px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.profile-card h2 {
  color: #4a2e00;
  font-size: 1.8rem;
  margin-bottom: 20px;
}

.profile-card p {
  color: #5b3b00;
  font-size: 1rem;
  margin: 8px 0;
}

.profile-actions {
  margin-top: 25px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  align-items: center;
}

.profile-btn {
  display: inline-block;
  background-color: #fcbf49;
  color: #4a2e00;
  padding: 10px 18px;
  border-radius: 10px;
  text-decoration: none;
  font-weight: bold;
  width: 80%;
  transition: all 0.3s;
  box-shadow: 0 3px 6px rgba(0,0,0,0.1);
}

.profile-btn:hover {
  background-color: #f9a825;
  transform: scale(1.05);
}

.profile-btn.add {
  background-color: #ffd54f;
}

.profile-btn.list {
  background-color: #ffe082;
}

.profile-btn.logout {
  background-color: #f28b82;
  color: #fff;
}
.profile-btn.logout:hover {
  background-color: #e57373;
}
</style>
