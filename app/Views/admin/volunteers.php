<?php
// Admin listing for volunteer signups — improved layout
?>
<div class="admin-volunteers">
  <h2>Cereri voluntari</h2>

  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="flash"><?= htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?></div>
  <?php endif; ?>

  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr>
          <th>#</th>
          <th>Solicitant</th>
          <th>Contact</th>
          <th>Mesaj</th>
          <th>Trimis</th>
          <th>Status</th>
          <th>Acțiuni</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($volunteers as $v):
        // parse stored payload
        $payload = [];
        if (!empty($v['message'])) {
          $decoded = json_decode($v['message'], true);
          if (is_array($decoded)) $payload = $decoded;
        }
        $displayName = $payload['name'] ?? $v['user_name'] ?? '—';
        $displayEmail = $payload['email'] ?? $v['user_email'] ?? '—';
        $displayPhone = $payload['phone'] ?? ($v['phone'] ?? '—');
        $notes = $payload['notes'] ?? $payload['message'] ?? ($v['message'] ?? '');
        $status = $v['status'] ?? 'pending';
      ?>
        <tr class="row-<?php echo htmlspecialchars($status); ?>">
          <td class="id"><?php echo (int)$v['id']; ?></td>
          <td class="name">
            <strong><?php echo htmlspecialchars($displayName); ?></strong>
            <div class="small muted">ID user: <?php echo htmlspecialchars($v['user_id'] ?? '—'); ?></div>
          </td>
          <td class="contact">
            <div><?php echo htmlspecialchars($displayEmail); ?></div>
            <div class="small muted"><?php echo htmlspecialchars($displayPhone); ?></div>
          </td>
          <td class="message"><?php echo nl2br(htmlspecialchars($notes)); ?></td>
          <td class="created"><?php echo htmlspecialchars($v['created_at']); ?>
            <?php if (!empty($v['approved_at'])): ?>
              <div class="small muted">Rezolvat: <?php echo htmlspecialchars($v['approved_at']); ?></div>
            <?php endif; ?>
          </td>
          <td class="status">
            <?php if ($status === 'approved'): ?>
              <span class="badge approved">Aprobat</span>
            <?php elseif ($status === 'rejected'): ?>
              <span class="badge rejected">Respins</span>
            <?php else: ?>
              <span class="badge pending">În așteptare</span>
            <?php endif; ?>
          </td>
          <td class="actions">
            <?php if (empty($v['status']) || $status === 'pending'): ?>
              <a class="btn btn-approve" href="index.php?controller=volunteer&action=approve&id=<?php echo (int)$v['id']; ?>" onclick="return confirm('Aprobi această cerere?')">Aprobă</a>
              <a class="btn btn-reject" href="index.php?controller=volunteer&action=reject&id=<?php echo (int)$v['id']; ?>" onclick="return confirm('Respinge această cerere?')">Respinge</a>
            <?php endif; ?>
            <a class="btn btn-delete" href="index.php?controller=volunteer&action=delete&id=<?php echo (int)$v['id']; ?>" onclick="return confirm('Șterge?')">Șterge</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <style>
  .admin-volunteers { padding:24px; }
  .admin-volunteers h2 { color:#3e2a12; margin-bottom:12px; }
  .flash { margin-bottom:12px; padding:10px 12px; border-radius:8px; background:#e6f7e6; color:#1f5d21; }
  .table-wrap { overflow:auto; background:#fff; padding:12px; border-radius:12px; box-shadow:0 6px 18px rgba(0,0,0,0.05); }
  table.table { width:100%; border-collapse:collapse; min-width:800px; }
  table.table thead th { text-align:left; padding:10px 12px; background:#faf4ea; color:#4a2e00; font-weight:700; border-bottom:1px solid #f0e6da; }
  table.table tbody td { padding:12px; border-bottom:1px solid #f3efe5; vertical-align:top; }
  .small.muted { color:#7a5b3a; font-size:0.9rem; }
  .badge { display:inline-block; padding:6px 10px; border-radius:999px; font-weight:700; font-size:0.9rem; }
  .badge.approved { background:#e6f9ee; color:#167a3a; }
  .badge.rejected { background:#fdecea; color:#b33a2f; }
  .badge.pending { background:#fff8e6; color:#7a5b3a; }
  .actions .btn { display:inline-block; margin:3px 6px 3px 0; padding:8px 10px; border-radius:8px; text-decoration:none; font-weight:700; color:#fff; }
  .btn-approve { background:#2e8b57; }
  .btn-reject { background:#c94a4a; }
  .btn-delete { background:#777; }
  .btn:hover { opacity:0.92; }
  @media (max-width:900px) {
    table.table { min-width:680px; }
  }
  @media (max-width:640px) {
    .table-wrap { padding:8px; }
    table.table thead { display:none; }
    table.table, table.table tbody, table.table tr, table.table td { display:block; width:100%; }
    table.table tr { margin-bottom:12px; border-bottom:1px solid #eee; }
    table.table td { padding:8px; }
    .id { font-weight:700; }
    .actions { margin-top:8px; }
  }
  </style>

</div>
