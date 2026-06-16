<?php
session_start();
require_once '../config/database.php';
require_once '../config/app.php';

$search = clean($_GET['q']     ?? '');
$kota   = clean($_GET['kota']  ?? '');
$status = clean($_GET['status']?? '');
$page   = max(1,(int)($_GET['page']??1));
$per    = 12;
$offset = ($page-1)*$per;

$where  = "WHERE 1=1";
$params = [];
if ($search) { $where .= " AND (k.nama LIKE ? OR k.artis LIKE ? OR k.kota LIKE ?)"; $params=array_merge($params,["%$search%","%$search%","%$search%"]); }
if ($kota)   { $where .= " AND k.kota = ?";   $params[] = $kota; }
if ($status) { $where .= " AND k.status = ?"; $params[] = $status; }
else          { $where .= " AND k.status != 'cancelled'"; }

$total = (int)DB::val("SELECT COUNT(*) FROM konser k $where", $params);
$list  = DB::rows("SELECT k.*, MIN(t.harga) as harga_min FROM konser k LEFT JOIN tiket t ON t.konser_id=k.id AND t.is_active=1 $where GROUP BY k.id ORDER BY k.tanggal ASC LIMIT $per OFFSET $offset", $params);
$kotaList = DB::rows("SELECT DISTINCT kota FROM konser ORDER BY kota");

$baseUrl = APP_URL . '/public/katalog.php?' . http_build_query(array_filter(['q'=>$search,'kota'=>$kota,'status'=>$status]));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Katalog Konser — NexTix</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<section class="section">
  <div class="section-inner">
    <div class="page-title">🎵 Semua Konser</div>
    <div class="page-sub">Temukan konser terbaik dari <?= number_format($total) ?> pilihan</div>

    <!-- Search & Filter -->
    <form method="GET" action="">
      <div class="search-bar">
        <div class="input-wrap" style="flex:1;max-width:360px">
          <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" name="q" class="form-control has-icon" placeholder="Cari artis, konser, kota..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <select name="kota" class="form-control" style="width:auto">
          <option value="">Semua Kota</option>
          <?php foreach ($kotaList as $row): ?>
          <option value="<?= htmlspecialchars($row['kota']) ?>" <?= $kota===$row['kota']?'selected':'' ?>><?= htmlspecialchars($row['kota']) ?></option>
          <?php endforeach; ?>
        </select>
        <select name="status" class="form-control" style="width:auto">
          <option value="">Semua Status</option>
          <option value="upcoming"  <?= $status==='upcoming' ?'selected':''  ?>>Upcoming</option>
          <option value="ongoing"   <?= $status==='ongoing'  ?'selected':''  ?>>Berlangsung</option>
          <option value="completed" <?= $status==='completed'?'selected':'' ?>>Selesai</option>
        </select>
        <button type="submit" class="btn btn-primary">Cari</button>
        <?php if ($search||$kota||$status): ?>
        <a href="<?= APP_URL ?>/public/katalog.php" class="btn btn-ghost">Reset</a>
        <?php endif; ?>
      </div>
    </form>

    <?php if ($list): ?>
    <div class="concert-grid">
      <?php foreach ($list as $k): ?>
      <?php include 'partials/concert-card.php'; ?>
      <?php endforeach; ?>
    </div>
    <div style="margin-top:28px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
      <div style="font-size:.8rem;color:var(--text3)">Menampilkan <?= count($list) ?> dari <?= $total ?> konser</div>
      <?= paginate($total,$page,$per,$baseUrl) ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
      <div class="icon">🔍</div>
      <h3>Konser tidak ditemukan</h3>
      <p>Coba ubah kata kunci atau filter pencarian kamu</p>
      <a href="<?= APP_URL ?>/public/katalog.php" class="btn btn-primary btn-sm">Lihat Semua Konser</a>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php include '../includes/footer.php'; ?>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body>
</html>