<?php
require_once '../../auth/check_admin.php';
require_once '_data.php';
$qs = http_build_query(['from'=>$from,'to'=>$to,'type'=>$type]);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Laporan — NexTix Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
<?php include '../../includes/sidebar.php'; ?>
<div class="admin-main">
  <div class="topbar">
    <button class="topbar-btn" id="sideToggle"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
    <div><div class="topbar-title">Laporan & Statistik</div><div class="topbar-sub"><?= $reportTitle ?> · <?= tglID($from) ?> – <?= tglID($to) ?></div></div>
  </div>
  <div class="page-wrap">
    <!-- Filter -->
    <form method="GET" style="margin-bottom:24px">
      <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
        <div class="form-group mb-0"><label class="form-label">Dari Tanggal</label><input type="date" name="from" class="form-control" value="<?= $from ?>"></div>
        <div class="form-group mb-0"><label class="form-label">Sampai Tanggal</label><input type="date" name="to" class="form-control" value="<?= $to ?>"></div>
        <div class="form-group mb-0"><label class="form-label">Tipe Laporan</label>
          <select name="type" class="form-control">
            <option value="orders" <?= $type==='orders'?'selected':'' ?>>Transaksi</option>
            <option value="konser" <?= $type==='konser'?'selected':'' ?>>Per Konser</option>
            <option value="tiket"  <?= $type==='tiket' ?'selected':'' ?>>Per Tiket</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Tampilkan</button>
      </div>
    </form>

    <!-- Summary Cards -->
    <div class="stats-grid" style="margin-bottom:24px">
      <div class="stat-card"><div class="stat-card-icon stat-icon-gold">💰</div><div class="stat-card-num" style="font-size:1.3rem"><?= rupiah($summary['revenue']) ?></div><div class="stat-card-label">Total Pendapatan</div></div>
      <div class="stat-card"><div class="stat-card-icon stat-icon-purple">🛒</div><div class="stat-card-num"><?= number_format($summary['total']) ?></div><div class="stat-card-label">Total Transaksi</div></div>
      <div class="stat-card"><div class="stat-card-icon stat-icon-green">🎟️</div><div class="stat-card-num"><?= number_format($summary['tiket_terjual']) ?></div><div class="stat-card-label">Tiket Terjual</div></div>
      <div class="stat-card"><div class="stat-card-icon stat-icon-pink">⏳</div><div class="stat-card-num"><?= number_format($summary['pending']) ?></div><div class="stat-card-label">Pending</div></div>
    </div>

    <!-- Data Table -->
    <div class="table-card">
      <div class="table-header">
        <div class="table-header-title"><?= $reportTitle ?> · <?= count($data) ?> baris</div>
        <div class="table-toolbar">
          <a href="export_excel.php?<?= $qs ?>" class="btn btn-ghost btn-sm" target="_blank">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Export Excel
          </a>
          <a href="export_pdf.php?<?= $qs ?>" class="btn btn-ghost btn-sm" target="_blank">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Export PDF
          </a>
        </div>
      </div>
      <div class="table-wrap">
        <?php if (!$data): ?>
        <div class="table-empty"><div class="icon">📭</div><div>Tidak ada data pada rentang tanggal ini.</div></div>
        <?php elseif ($type==='orders'): ?>
        <table>
          <thead><tr><th>Kode</th><th>Pembeli</th><th>Konser</th><th>Tiket</th><th>Jml</th><th>Total</th><th>Status</th><th>Tgl</th></tr></thead>
          <tbody>
          <?php foreach ($data as $r): ?>
          <tr>
            <td style="font-family:monospace;font-size:.78rem"><?= htmlspecialchars($r['kode']) ?></td>
            <td><div style="font-size:.85rem"><?= htmlspecialchars($r['nama_pembeli']) ?></div><div style="font-size:.75rem;color:var(--text3)"><?= htmlspecialchars($r['email']) ?></div></td>
            <td style="font-size:.85rem"><?= htmlspecialchars($r['nama_konser']) ?></td>
            <td style="font-size:.85rem"><?= htmlspecialchars($r['nama_tiket']) ?></td>
            <td><?= $r['jumlah'] ?></td>
            <td style="font-weight:700"><?= rupiah($r['total']) ?></td>
            <td><?= statusBadge($r['status']) ?></td>
            <td style="font-size:.78rem"><?= date('d/m/y',strtotime($r['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <?php elseif ($type==='konser'): ?>
        <table>
          <thead><tr><th>Konser</th><th>Tanggal</th><th>Kota</th><th>Jenis Tiket</th><th>Terjual</th><th>Pendapatan</th><th>Status</th></tr></thead>
          <tbody>
          <?php foreach ($data as $r): ?>
          <tr>
            <td><div style="font-weight:700"><?= htmlspecialchars($r['nama']) ?></div><div style="font-size:.78rem;color:var(--accent3)"><?= htmlspecialchars($r['artis']) ?></div></td>
            <td style="font-size:.85rem"><?= tglID($r['tanggal']) ?></td>
            <td><?= htmlspecialchars($r['kota']) ?></td>
            <td><?= $r['jml_tiket'] ?></td>
            <td><?= number_format($r['total_terjual']) ?></td>
            <td style="font-weight:700;color:var(--accent3)"><?= rupiah($r['pendapatan']) ?></td>
            <td><?= statusBadge($r['status']) ?></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <?php else: ?>
        <table>
          <thead><tr><th>Konser</th><th>Tiket</th><th>Harga</th><th>Stok</th><th>Terjual</th><th>Sisa</th><th>Pendapatan</th></tr></thead>
          <tbody>
          <?php foreach ($data as $r): ?>
          <tr>
            <td><div style="font-size:.85rem"><?= htmlspecialchars($r['nama_konser']) ?></div></td>
            <td style="font-weight:700"><?= htmlspecialchars($r['nama']) ?></td>
            <td><?= rupiah($r['harga']) ?></td>
            <td><?= number_format($r['stok']) ?></td>
            <td><?= number_format($r['terjual']) ?></td>
            <td style="color:<?= $r['sisa']<=0?'var(--red)':'var(--green)' ?>;font-weight:700"><?= number_format($r['sisa']) ?></td>
            <td style="font-weight:700;color:var(--accent3)"><?= rupiah($r['pendapatan']) ?></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
</div>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body></html>
