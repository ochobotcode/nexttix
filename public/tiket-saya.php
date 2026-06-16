<?php
require_once '../auth/check_user.php';
$flash   = getFlash();
$orders  = DB::rows("SELECT * FROM v_order_detail WHERE user_id = ? ORDER BY created_at DESC", [$_SESSION['user_id']]);
$pending = array_filter($orders, fn($o) => $o['status']==='pending');
$paid    = array_filter($orders, fn($o) => $o['status']==='paid');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Tiket Saya — NexTix</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<section class="section">
  <div class="section-inner" style="max-width:860px">
    <div class="page-title">🎟️ Tiket Saya</div>
    <div class="page-sub">Riwayat pembelian dan tiket aktif kamu</div>

    <?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <?php if (!$orders): ?>
    <div class="empty-state">
      <div class="icon">🎫</div>
      <h3>Belum ada tiket</h3>
      <p>Kamu belum pernah membeli tiket. Cari konser dan beli tiket sekarang!</p>
      <a href="<?= APP_URL ?>/public/katalog.php" class="btn btn-primary">Cari Konser</a>
    </div>
    <?php else: ?>

    <!-- Tabs -->
    <div style="display:flex;gap:4px;margin-bottom:24px;background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:4px;width:fit-content">
      <button class="tab-btn active" data-tab="all">Semua (<?= count($orders) ?>)</button>
      <button class="tab-btn" data-tab="paid">Lunas (<?= count($paid) ?>)</button>
      <button class="tab-btn" data-tab="pending">Pending (<?= count($pending) ?>)</button>
    </div>

    <div class="my-tickets-grid" id="ticketList">
      <?php foreach ($orders as $o): ?>
      <div class="my-ticket-card tab-item" data-status="<?= $o['status'] ?>">
        <div class="my-ticket-accent"></div>
        <div class="my-ticket-body">
          <div class="my-ticket-konser"><?= htmlspecialchars($o['nama_konser']) ?> · <?= htmlspecialchars($o['artis']) ?></div>
          <div class="my-ticket-name"><?= htmlspecialchars($o['nama_tiket']) ?></div>
          <div class="my-ticket-metas">
            <div class="my-ticket-meta">
              <strong><?= tglID($o['tanggal_konser']) ?></strong>
              Tanggal Konser
            </div>
            <div class="my-ticket-meta">
              <strong><?= htmlspecialchars($o['venue']) ?></strong>
              Venue
            </div>
            <div class="my-ticket-meta">
              <strong><?= $o['jumlah'] ?> tiket</strong>
              Jumlah
            </div>
            <div class="my-ticket-meta">
              <strong><?= rupiah($o['total']) ?></strong>
              Total Bayar
            </div>
          </div>
          <div style="margin-top:12px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">
            <?= statusBadge($o['status']) ?>
            <span style="font-size:.75rem;color:var(--text3)">#<?= htmlspecialchars($o['kode']) ?></span>
            <?php if ($o['metode_bayar']): ?>
            <span style="font-size:.75rem;color:var(--text3)">· <?= htmlspecialchars($o['metode_bayar']) ?></span>
            <?php endif; ?>
          </div>
        </div>
        <div class="my-ticket-actions">
          <?php if ($o['status'] === 'paid'): ?>
          <a href="<?= APP_URL ?>/public/e-tiket.php?kode=<?= urlencode($o['kode']) ?>" class="btn btn-primary btn-sm" target="_blank">E-Tiket</a>
          <?php endif; ?>
          <a href="<?= APP_URL ?>/public/konser.php?slug=<?= urlencode($o['slug'] ?? '') ?>" class="btn btn-ghost btn-sm">Detail</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php include '../includes/footer.php'; ?>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
<script>
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const tab = this.dataset.tab;
        document.querySelectorAll('.tab-item').forEach(item => {
            if (tab === 'all') { item.style.display=''; return; }
            item.style.display = item.dataset.status === tab ? '' : 'none';
        });
    });
});
</script>
<style>
.tab-btn{padding:7px 16px;border-radius:9px;border:none;background:none;color:var(--text2);font-size:.85rem;font-weight:600;cursor:pointer;transition:all .2s}
.tab-btn.active{background:var(--grad-1);color:#fff}
</style>
</body>
</html>
