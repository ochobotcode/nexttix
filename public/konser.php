<?php
session_start();
require_once '../config/database.php';
require_once '../config/app.php';

$slug = clean($_GET['slug'] ?? '');
$k    = DB::row("SELECT * FROM konser WHERE slug = ?", [$slug]);
if (!$k) { http_response_code(404); die('<h2>Konser tidak ditemukan</h2>'); }

$tikets = DB::rows("SELECT *, (stok - terjual) as sisa FROM tiket WHERE konser_id = ? AND is_active = 1 ORDER BY harga ASC", [$k['id']]);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($k['nama']) ?> — NexTix</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<!-- DETAIL HERO -->
<div class="detail-hero">
  <div class="detail-inner">
    <div class="detail-poster">
      <?php if ($k['poster'] && file_exists(POSTER_DIR . $k['poster'])): ?>
        <img src="<?= APP_URL ?>/uploads/posters/<?= htmlspecialchars($k['poster']) ?>" alt="">
      <?php else: ?>
        <div class="detail-poster-placeholder"><span>🎵</span><p><?= htmlspecialchars($k['artis']) ?></p></div>
      <?php endif; ?>
    </div>
    <div class="detail-info">
      <div class="detail-artis"><?= htmlspecialchars($k['artis']) ?></div>
      <h1 class="detail-name"><?= htmlspecialchars($k['nama']) ?></h1>
      <?= statusBadge($k['status']) ?>
      <div class="detail-metas">
        <div class="detail-meta">
          <div class="detail-meta-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
          <div><div class="detail-meta-label">Tanggal & Waktu</div><div class="detail-meta-value"><?= tglID($k['tanggal']) ?> · <?= substr($k['jam'],0,5) ?> WIB</div></div>
        </div>
        <div class="detail-meta">
          <div class="detail-meta-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
          <div><div class="detail-meta-label">Venue</div><div class="detail-meta-value"><?= htmlspecialchars($k['venue']) ?></div></div>
        </div>
        <?php if ($k['alamat']): ?>
        <div class="detail-meta">
          <div class="detail-meta-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
          <div><div class="detail-meta-label">Alamat</div><div class="detail-meta-value" style="color:var(--text2);font-size:.875rem;font-weight:500"><?= htmlspecialchars($k['alamat']) ?></div></div>
        </div>
        <?php endif; ?>
      </div>
      <?php if ($k['deskripsi']): ?>
      <div class="detail-desc"><?= nl2br(htmlspecialchars($k['deskripsi'])) ?></div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- TIKET SECTION -->
<div class="ticket-section">
  <div class="ticket-inner">
    <h2 style="margin-bottom:20px">🎟️ Pilih Tiket</h2>

    <?php if (!isset($_SESSION['user_id'])): ?>
    <div class="alert alert-info" style="margin-bottom:24px">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <span>Kamu perlu <a href="<?= APP_URL ?>/login.php" style="color:var(--accent3);font-weight:700">masuk</a> atau <a href="<?= APP_URL ?>/register.php" style="color:var(--accent3);font-weight:700">daftar</a> dulu untuk membeli tiket.</span>
    </div>
    <?php endif; ?>

    <?php if ($tikets): ?>
    <form method="GET" action="<?= APP_URL ?>/public/checkout.php" id="ticketForm">
      <input type="hidden" name="konser_id" value="<?= $k['id'] ?>">
      <div class="ticket-grid">
        <?php foreach ($tikets as $t): ?>
        <?php $sisa = max(0, $t['sisa']); $soldOut = $sisa === 0; ?>
        <div class="ticket-item <?= $soldOut ? 'sold-out' : '' ?>">
          <div>
            <div class="ticket-name"><?= htmlspecialchars($t['nama']) ?></div>
            <?php if ($t['deskripsi']): ?>
            <div class="ticket-desc"><?= htmlspecialchars($t['deskripsi']) ?></div>
            <?php endif; ?>
            <div class="ticket-stok">
              <?php if ($soldOut): ?>
                <span style="color:var(--red)">Habis Terjual</span>
              <?php else: ?>
                Sisa <span><?= number_format($sisa) ?></span> tiket
              <?php endif; ?>
            </div>
          </div>
          <div class="ticket-price-col">
            <div class="ticket-harga"><?= rupiah($t['harga']) ?></div>
            <?php if (!$soldOut && isset($_SESSION['user_id'])): ?>
            <div class="qty-control" data-max="<?= min(10,$sisa) ?>">
              <button type="button" class="qty-btn qty-minus">−</button>
              <span class="qty-num">0</span>
              <button type="button" class="qty-btn qty-plus">+</button>
              <input type="hidden" name="tiket[<?= $t['id'] ?>]" class="qty-hidden" value="0">
            </div>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <?php if (isset($_SESSION['user_id'])): ?>
      <div style="margin-top:24px;text-align:right">
        <button type="submit" class="btn btn-primary btn-lg" id="checkoutBtn" disabled>
          🛒 Lanjut ke Pembayaran
        </button>
      </div>
      <?php endif; ?>
    </form>
    <?php else: ?>
    <div class="empty-state"><div class="icon">🎫</div><h3>Tiket belum tersedia</h3><p>Tiket untuk konser ini belum tersedia saat ini.</p></div>
    <?php endif; ?>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
<script>
// Enable checkout button when qty > 0
document.querySelectorAll('.qty-hidden').forEach(inp => {
    inp.addEventListener('change', checkAny);
});
function checkAny() {
    const any = [...document.querySelectorAll('.qty-hidden')].some(i => parseInt(i.value)>0);
    const btn = document.getElementById('checkoutBtn');
    if (btn) btn.disabled = !any;
}
// Prevent form submit if all 0
document.getElementById('ticketForm')?.addEventListener('submit', function(e) {
    const any = [...document.querySelectorAll('.qty-hidden')].some(i => parseInt(i.value)>0);
    if (!any) { e.preventDefault(); alert('Pilih minimal 1 tiket terlebih dahulu.'); }
});
// qty update triggers checkAny
document.querySelectorAll('.qty-btn').forEach(btn => btn.addEventListener('click', () => setTimeout(checkAny,50)));
</script>
</body>
</html>
