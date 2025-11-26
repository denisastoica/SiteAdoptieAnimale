<section class="admin-panel">
  <h2>📋 Cereri de adopție în așteptare</h2>

  <?php if (empty($pending)): ?>
    <p>Nicio cerere momentan.</p>
  <?php else: ?>
    <table class="admin-table">
      <tr>
        <th>ID</th>
        <th>Utilizator</th>
        <th>Animal</th>
        <th>Status</th>
        <th>Acțiuni</th>
      </tr>

      <?php foreach ($pending as $a): ?>
        <tr>
          <td><?= $a['id'] ?></td>
          <td><?= htmlspecialchars($a['user_name']) ?></td>
          <td><?= htmlspecialchars($a['animal_name']) ?></td>
          <td><?= htmlspecialchars($a['status']) ?></td>
          <td>
            <?php if (($a['status'] ?? 'pending') === 'pending'): ?>
              <form method="POST" action="index.php?controller=admin&action=approve" style="display:inline;">
                <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                <input type="hidden" name="id" value="<?= $a['id'] ?>">
                <button type="submit">Aproba</button>
              </form>

              <form method="POST" action="index.php?controller=admin&action=reject" style="display:inline;">
                <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                <input type="hidden" name="id" value="<?= $a['id'] ?>">
                <button type="submit">Respinge</button>
              </form>
            <?php else: ?>
              <span style="font-weight:600; color:#555;">—</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
</section>

<style>
.admin-panel { text-align: center; padding: 40px; }
.admin-table {
  width: 80%;
  margin: auto;
  border-collapse: collapse;
  background-color: #f5f5f5; 
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 4px 10px rgba(0,0,0,0.08);
}
.admin-table th, .admin-table td {
  padding: 12px 10px;
  border: 1px solid #ddd;
  background-color: #f5f5f5;
}
.admin-table th { background-color: #e9e9e9; }

.admin-table td form { display: inline-block; margin: 0 12px; }
.admin-table tr td:last-child {
  display: flex;
  justify-content: center;
  align-items: center;
}
button {
  background: #fcbf49;
  border: none;
  padding: 6px 12px;
  margin: 0; 
  border-radius: 6px;
  cursor: pointer;
}
button:hover { background: #f9a825; }
</style>
