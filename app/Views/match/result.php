<section class="match-result">
  <h2>🐾 Rezultatele potrivirii tale</h2>
  <p>Animalele care se potrivesc cel mai bine cu stilul tău de viață:</p>

  <div class="animal-list">
    <?php if (empty($results)): ?>
      <p>Nu s-au găsit potriviri pentru criteriile tale.</p>
    <?php else: ?>
      <?php foreach ($results as $res): ?>
        <?php $animal = $res['animal']; ?>
        <div class="animal-card">
          <img src="<?= htmlspecialchars(upload_url($animal['image'] ?? '')) ?>" alt="<?= htmlspecialchars($animal['name'] ?? 'Animal') ?>">
          <h3><?= htmlspecialchars($animal['name'] ?? 'Anonim') ?> — <span><?= htmlspecialchars((int)($res['compatibility'] ?? 0)) ?>%</span></h3>
          <?php
            // Compute age label from months (age_months is provided by the model)
            $ageLabel = '—';
            if (isset($animal['age_months'])) {
              $months = (int)$animal['age_months'];
              $years = intdiv($months, 12);
              $remMonths = $months % 12;
              if ($years > 0) {
                $ageLabel = $years . ' ani' . ($remMonths ? ' ' . $remMonths . ' luni' : '');
              } else {
                $ageLabel = $remMonths . ' luni';
              }
            }
          ?>
          <p><?= htmlspecialchars($animal['species'] ?? '') ?>, <?= htmlspecialchars($ageLabel) ?></p>
          <p><?= htmlspecialchars($animal['location'] ?? $animal['city'] ?? '') ?></p>
          <a href="/site/public/index.php?controller=animal&action=view&id=<?= htmlspecialchars($animal['id'] ?? '') ?>" class="button">Detalii</a>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>
