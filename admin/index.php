<?php
require_once '../auth/check_admin.php';

$stats = DB::row("SELECT
  (SELECT COUNT(*) FROM konser) as konser,
  (SELECT COUNT(*) FROM konser WHERE status='upcoming') as upcoming,
  (SELECT COUNT(*) FROM orders) as orders,
  (SELECT COUNT(*) FROM orders WHERE status='paid') as paid,
  (SELECT COALESCE(SUM(total),0) FROM orders WHERE status='paid') as revenue,
  (SELECT COUNT(*) FROM users) as users
");

$monthly = DB::rows("SELECT MONTH(created_at) as bln, SUM(total) as rev
  FROM orders WHERE status='paid' AND YEAR(created_at)=YEAR(NOW())
  GROUP BY MONTH(created_at) ORDER BY bln");

$recentOrders = DB::rows("SELECT o.*,u.nama as nama_pembeli,t.nama as nama_tiket,k.nama as nama_konser
  FROM orders o JOIN users u ON o.user_id=u.id JOIN tiket t ON o.tiket_id=t.id JOIN konser k ON t.konser_id=k.id
  ORDER BY o.created_at DESC LIMIT 8");

$topKonser = DB::rows("SELECT k.nama, k.artis, SUM(o.total) as rev, COUNT(o.id) as cnt
  FROM orders o JOIN tiket t ON o.tiket_id=t.id JOIN konser k ON t.konser_id=k.id
  WHERE o.status='paid' GROUP BY k.id ORDER BY rev DESC LIMIT 5");

$pending = DB::val("SELECT COUNT(*) FROM orders WHERE status='pending'");
$revData = array_fill(0,12,0);
foreach ($monthly as $m) $revData[$m['bln']-1] = (float)$m['rev'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard — NexTix Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/admin.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
<div class="admin-layout">
<?php include '../includes/sidebar.php'; ?>
<div class="admin-main">
  <!-- Topbar -->
  <div class="topbar">
    <button class="topbar-btn" id="sideToggle">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
    <div><div class="topbar-title">Dashboard</div><div class="topbar-sub">Selamat datang, <?= htmlspecialchars($_SESSION['admin_nama']) ?>!</div></div>
    <div class="topbar-right">
      <div style="font-size:.8rem;color:var(--text3)"><?= date('d M Y') ?></div>
      <a href="<?= APP_URL ?>/admin/logout.php" class="btn btn-ghost btn-sm">Keluar</a>
    </div>
  </div>

  <div class="page-wrap">
    <?php $f=getFlash(); if($f): ?><div class="alert alert-<?= $f['type'] ?>"><?= htmlspecialchars($f['msg']) ?></div><?php endif; ?>

    <!-- STATS -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-card-icon stat-icon-purple">🎵</div>
        <div class="stat-card-num"><?= number_format($stats['konser']) ?></div>
        <div class="stat-card-label">Total Konser</div>
        <div class="stat-card-change up">↑ <?= $stats['upcoming'] ?> upcoming</div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon stat-icon-pink">🎟️</div>
        <div class="stat-card-num"><?= number_format($stats['orders']) ?></div>
        <div class="stat-card-label">Total Pesanan</div>
        <div class="stat-card-change <?= $pending>0?'down':'up' ?>">⏳ <?= $pending ?> pending</div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon stat-icon-gold">💰</div>
        <div class="stat-card-num" style="font-size:1.3rem"><?= rupiah($stats['revenue']) ?></div>
        <div class="stat-card-label">Total Pendapatan</div>
        <div class="stat-card-change up">✅ <?= $stats['paid'] ?> lunas</div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon stat-icon-green">👥</div>
        <div class="stat-card-num"><?= number_format($stats['users']) ?></div>
        <div class="stat-card-label">Total Pelanggan</div>
      </div>
    </div>

    <!-- CHARTS -->
    <div class="chart-grid">
      <div class="chart-card">
        <div class="chart-header">
          <div><div class="chart-title">Pendapatan Bulanan <?= date('Y') ?></div><div class="chart-sub">Total revenue per bulan</div></div>
        </div>
        <canvas id="revenueChart" height="200"></canvas>
      </div>
      <div class="chart-card">
        <div class="chart-header"><div><div class="chart-title">Status Pesanan</div></div></div>
        <canvas id="statusChart" height="200"></canvas>
      </div>
    </div>

    <!-- TOP KONSER & RECENT ORDERS -->
    <div style="display:grid;grid-template-columns:1fr 1.6fr;gap:20px;margin-bottom:24px">
      <!-- Top Konser -->
      <div class="table-card">
        <div class="table-header"><div class="table-header-title">🏆 Top Konser</div></div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Konser</th><th>Revenue</th></tr></thead>
            <tbody>
            <?php foreach ($topKonser as $tk): ?>
            <tr>
              <td><div style="font-weight:600;font-size:.85rem"><?= htmlspecialchars($tk['nama']) ?></div><div style="font-size:.75rem;color:var(--text3)"><?= htmlspecialchars($tk['artis']) ?></div></td>
              <td style="font-weight:700;color:var(--accent3)"><?= rupiah($tk['rev']) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Recent Orders -->
      <div class="table-card">
        <div class="table-header">
          <div class="table-header-title">🛒 Pesanan Terbaru</div>
          <a href="<?= APP_URL ?>/admin/orders/" class="btn btn-ghost btn-sm">Lihat Semua</a>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Kode</th><th>Pembeli</th><th>Total</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($recentOrders as $o): ?>
            <tr>
              <td style="font-family:monospace;font-size:.78rem"><?= htmlspecialchars($o['kode']) ?></td>
              <td>
                <div style="font-size:.85rem;font-weight:600"><?= htmlspecialchars($o['nama_pembeli']) ?></div>
                <div style="font-size:.75rem;color:var(--text3)"><?= htmlspecialchars($o['nama_konser']) ?></div>
              </td>
              <td style="font-weight:700;font-size:.85rem"><?= rupiah($o['total']) ?></td>
              <td><?= statusBadge($o['status']) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<script src="<?= APP_URL ?>/assets/js/main.js"></script>
<script>
const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
const revData = <?= json_encode(array_values($revData)) ?>;

new Chart(document.getElementById('revenueChart'), {
  type: 'bar',
  data: {
    labels: months,
    datasets: [{
      label: 'Revenue',
      data: revData,
      backgroundColor: 'rgba(139,92,246,.55)',
      borderColor: 'rgba(139,92,246,1)',
      borderWidth: 2,
      borderRadius: 6,
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { color: 'rgba(255,255,255,.05)' }, ticks: { color: '#6f6c87' } },
      y: { grid: { color: 'rgba(255,255,255,.05)' }, ticks: { color: '#6f6c87', callback: v => 'Rp'+Number(v/1000000).toFixed(0)+'jt' } }
    }
  }
});

<?php
$statuses = DB::rows("SELECT status, COUNT(*) as n FROM orders GROUP BY status");
$slabels = []; $snums = []; $scolors = [];
$colorMap = ['paid'=>'#34d399','pending'=>'#fbbf24','cancelled'=>'#f87171','refunded'=>'#60a5fa'];
foreach ($statuses as $s) { $slabels[]=ucfirst($s['status']); $snums[]=(int)$s['n']; $scolors[]=$colorMap[$s['status']]??'#6f6c87'; }
?>
new Chart(document.getElementById('statusChart'), {
  type: 'doughnut',
  data: {
    labels: <?= json_encode($slabels) ?>,
    datasets: [{ data: <?= json_encode($snums) ?>, backgroundColor: <?= json_encode($scolors) ?>, borderWidth: 0 }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { position: 'bottom', labels: { color: '#aba8c2', padding: 12, font: { size: 12 } } }
    },
    cutout: '65%'
  }
});
</script>
</body>
</html>
