<section class="admin-panel">
  <h2>📥 Mesaje contact</h2>

  <?php if (empty($contacts)): ?>
    <div class="contacts-card">
      <p class="no-messages">Nu există mesaje înregistrate.</p>
    </div>
  <?php else: ?>
    <div class="contacts-card">
      <div class="table-wrap">
        <table class="contacts-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nume</th>
          <th>Email</th>
          <th>Telefon</th>
          <th>Mesaj</th>
          <th>Status</th>
          <th>Data</th>
          <th>Acțiuni</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($contacts as $c): ?>
          <tr>
            <td><?= htmlspecialchars($c['id']) ?></td>
            <td><?= htmlspecialchars($c['name'] ?? '') ?></td>
            <td><?= htmlspecialchars($c['email'] ?? '') ?></td>
            <td><?= htmlspecialchars($c['phone'] ?? '') ?></td>
            <td><div class="msg-snippet"><?= nl2br(htmlspecialchars(substr($c['message'],0,200))) ?><?= strlen($c['message'])>200 ? '...' : '' ?></div></td>
            <td><?= htmlspecialchars($c['status']) ?></td>
            <td><?= htmlspecialchars($c['created_at']) ?></td>
            <td class="actions">
              <!-- Mark as read only when new -->
              <?php if (($c['status'] ?? 'new') === 'new'): ?>
                <form method="post" action="/site/public/index.php?controller=admin&action=contactUpdateStatus">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                  <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                  <input type="hidden" name="status" value="read">
                  <button class="btn" type="submit">Marchează ca citit</button>
                </form>
              <?php else: ?>
                <span class="status-read">Citit</span>
              <?php endif; ?>

              <!-- Delete is always available -->
              <form method="post" action="/site/public/index.php?controller=admin&action=contactDelete" onsubmit="return confirm('Ștergi acest mesaj definitiv?');">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                <button class="btn btn-delete" type="submit">Șterge</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>

  <style>
    .contacts-card {
      max-width:1100px;
      margin: 18px auto;
      /* Make card background match table cell color so action buttons align visually */
      background: #fffaf0;
      border-radius: 12px;
      padding: 18px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }

    .contacts-card .no-messages {
      text-align: center;
      color: #666;
      padding: 30px 0;
      font-size: 1.05rem;
    }

  .table-wrap { overflow-x: auto; }

    .contacts-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 6px;
      background: transparent;
    }

    /* Make table cells transparent so the card background provides a uniform color */
    .contacts-table th, .contacts-table td {
      padding: 12px 14px;
      border-bottom: 1px solid #f0e7dd;
      text-align: left;
      vertical-align: top;
      background: transparent;
    }

    .contacts-table thead th {
      background: #f6ecd8;
      font-weight: 700;
      color: #4a2e00;
    }

    .msg-snippet {
      max-width:520px;
      white-space:normal;
      color:#4b2e05;
      line-height:1.35;
    }

    .btn {
      background:#fcbf49;
      color:#4a2e00;
      padding:8px 12px;
      border-radius:6px;
      border:none;
      cursor:pointer;
      font-weight:600;
    }

  .btn:hover { background:#f9a825; }

  .btn-delete { background:#ef6c6c; color:#fff; }
  .btn-delete:hover { background:#e24d4d; }

  /* Ensure action buttons are on the same row */
  .contacts-table td.actions { display:flex; gap:8px; align-items:center; }
  .contacts-table td.actions .status-read { color:#4a2e00; font-weight:700; margin-right:8px; }

    @media (max-width:700px) {
      .contacts-card { padding: 12px; }
      .contacts-table th, .contacts-table td { padding: 10px; }
      .msg-snippet { max-width: 260px; }
    }
  </style>
</section>
