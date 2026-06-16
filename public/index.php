<?php
session_start();
require_once '../config/database.php';
require_once '../config/app.php';

$featured = DB::rows("SELECT k.*, MIN(t.harga) as harga_min FROM konser k LEFT JOIN tiket t ON t.konser_id=k.id AND t.is_active=1 WHERE k.status='upcoming' AND k.featured=1 GROUP BY k.id ORDER BY k.tanggal ASC LIMIT 3");
$upcoming = DB::rows("SELECT k.*, MIN(t.harga) as harga_min FROM konser k LEFT JOIN tiket t ON t.konser_id=k.id AND t.is_active=1 WHERE k.status='upcoming' GROUP BY k.id ORDER BY k.tanggal ASC LIMIT 6");
$stats = DB::row("SELECT (SELECT COUNT(*) FROM konser WHERE status='upcoming') as konser, (SELECT COUNT(*) FROM users) as users, (SELECT COALESCE(SUM(total),0) FROM orders WHERE status='paid') as rev, (SELECT COUNT(*) FROM orders) as orders");
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>NexTix — Tiket Konser Terbaik Indonesia</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<?php if ($flash): ?>
<div style="max-width:900px;margin:16px auto;padding:0 24px">
  <div class="alert alert-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
</div>
<?php endif; ?>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg"><span></span><span></span><span></span></div>
  <div class="hero-content">
    <div class="hero-badge">🎵 #1 Platform Tiket Konser Indonesia</div>
    <h1>Rasakan <span class="grad-text">Konser Terbaik</span><br>Langsung di Depanmu</h1>
    <p>Temukan ribuan konser dari artis lokal hingga internasional. Beli tiket mudah, aman, dan cepat di NexTix.</p>
    <div class="hero-actions">
      <a href="<?= APP_URL ?>/public/katalog.php" class="btn btn-primary btn-lg">Cari Konser</a>
      <?php if (!isset($_SESSION['user_id'])): ?>
      <a href="<?= APP_URL ?>/register.php" class="btn btn-ghost btn-lg">Daftar Gratis</a>
      <?php endif; ?>
    </div>
    <div class="hero-stats">
      <div>
        <div class="hero-stat-num"><?= number_format($stats['konser']) ?>+</div>
        <div class="hero-stat-label">Konser Upcoming</div>
      </div>
      <div>
        <div class="hero-stat-num"><?= number_format($stats['users']) ?>+</div>
        <div class="hero-stat-label">Pengguna Aktif</div>
      </div>
      <div>
        <div class="hero-stat-num"><?= number_format($stats['orders']) ?>+</div>
        <div class="hero-stat-label">Tiket Terjual</div>
      </div>
    </div>
  </div>
</section>

<!-- FEATURED -->
<?php if ($featured): ?>
<section class="section">
  <div class="section-inner">
    <div class="section-head">
      <h2 class="section-title">🔥 Konser <span>Pilihan</span></h2>
      <a href="<?= APP_URL ?>/public/katalog.php" class="btn btn-ghost btn-sm">Lihat Semua →</a>
    </div>
    <div class="concert-grid">
      <?php foreach ($featured as $k): ?>
      <?php include 'partials/concert-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- UPCOMING -->
<section class="section" style="padding-top:0">
  <div class="section-inner">
    <div class="section-head">
      <h2 class="section-title">📅 Konser <span>Mendatang</span></h2>
      <a href="<?= APP_URL ?>/public/katalog.php" class="btn btn-ghost btn-sm">Lihat Semua →</a>
    </div>
    <?php if ($upcoming): ?>
    <div class="concert-grid">
      <?php foreach ($upcoming as $k): ?>
      <?php include 'partials/concert-card.php'; ?>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state"><div class="icon">🎵</div><h3>Belum ada konser</h3><p>Konser segera hadir. Pantau terus!</p></div>
    <?php endif; ?>
  </div>
</section>

<!-- CTA -->
<?php if (!isset($_SESSION['user_id'])): ?>
<section class="section" style="padding-top:0">
  <div class="section-inner">
    <div style="background:linear-gradient(135deg,rgba(124,58,237,.2),rgba(236,72,153,.15));border:1px solid rgba(124,58,237,.3);border-radius:20px;padding:52px 40px;text-align:center">
      <div style="font-size:2.5rem;margin-bottom:16px">🎟️</div>
      <h2 style="margin-bottom:10px">Siap Nonton Konser Favoritmu?</h2>
      <p style="color:var(--text2);margin-bottom:28px;max-width:440px;margin-left:auto;margin-right:auto">Daftar sekarang dan dapatkan akses ke semua konser terbaik. Gratis dan hanya butuh 1 menit!</p>
      <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
        <a href="<?= APP_URL ?>/register.php" class="btn btn-primary btn-lg">Daftar Sekarang</a>
        <a href="<?= APP_URL ?>/login.php" class="btn btn-ghost btn-lg">Sudah punya akun</a>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body>
</html>
