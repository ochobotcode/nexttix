<?php
require_once '../../auth/check_admin.php';
require_once '_data.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($reportTitle) ?> — NexTix</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; }
  body { color: #1a1a2e; padding: 32px; }
  .head { display:flex; align-items:center; justify-content:space-between; border-bottom: 3px solid #8b5cf6; padding-bottom: 16px; margin-bottom: 20px; }
  .brand { display:flex; align-items:center; gap:10px; }
  .brand-icon { width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#8b5cf6,#f472b6);display:flex;align-items:center;justify-content:center;font-size:18px; }
  .brand-name { font-size: 20px; font-weight: 800; color:#1a1a2e; }
  .head-meta { text-align:right; font-size: 11px; color:#666; }
  h1 { font-size: 18px; font-weight: 800; margin-bottom: 4px; }
  .period { font-size: 12px; color: #666; margin-bottom: 18px; }
  .summary { display:flex; gap:14px; margin-bottom: 22px; flex-wrap: wrap; }
  .summary div { flex:1; min-width:120px; border:1px solid #e5e5f0; border-radius:10px; padding:12px 14px; }
  .summary .num { font-size: 16px; font-weight: 800; color:#7c3aed; }
  .summary .lbl { font-size: 10px; color:#888; margin-top:2px; text-transform:uppercase; letter-spacing:.04em; }
  table { width: 100%; border-collapse: collapse; font-size: 11px; }
  th { background: #8b5cf6; color: #fff; text-align:left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; letter-spacing: .04em; }
  td { padding: 7px 10px; border-bottom: 1px solid #eee; }
  tr:nth-child(even) td { background: #faf9ff; }
  .status { display:inline-block; padding:2px 8px; border-radius:99px; font-size:10px; font-weight:700; }
  .status-paid { background:#d1fae5; color:#059669; }
  .status-pending { background:#fef3c7; color:#b45309; }
  .status-cancelled { background:#fee2e2; color:#dc2626; }
  .status-refunded { background:#dbeafe; color:#2563eb; }
  .status-upcoming { background:#dbeafe; color:#2563eb; }
  .status-ongoing { background:#d1fae5; color:#059669; }
  .status-completed { background:#f3f4f6; color:#555; }
  .foot { margin-top: 24px; font-size: 10px; color: #999; text-align:center; }
  .print-btn {
    position: fixed; top: 16px; right: 16px; z-index: 99;
    background: #8b5cf6; color:#fff; border:none; padding: 10px 18px;
    border-radius: 10px; font-size: 13px; font-weight: 700; cursor:pointer;
    box-shadow: 0 6px 18px rgba(139,92,246,.4);
  }
  @media print { .print-btn { display: none; } body { padding: 0; } }
</style>
</head>
<body>
<button class="print-btn" onclick="window.print()">🖨️ Cetak / Simpan sebagai PDF</button>

<div class="head">
  <div class="brand">
    <div class="brand-icon">🎵</div>
    <div class="brand-name">NexTix</div>
  </div>
  <div class="head-meta">
    Dicetak: <?= date('d M Y H:i') ?> WIB<br>
    Oleh: <?= htmlspecialchars($_SESSION['admin_nama']) ?>
  </div>
</div>

<h1><?= htmlspecialchars($reportTitle) ?></h1>
<div class="period">Periode: <?= tglID($from) ?> — <?= tglID($to) ?></div>

<div class="summary">
  <div><div class="num"><?= rupiah($summary['revenue']) ?></div><div class="lbl">Total Pendapatan</div></div>
  <div><div class="num"><?= number_format($summary['total']) ?></div><div class="lbl">Total Transaksi</div></div>
  <div><div class="num"><?= number_format($summary['tiket_terjual']) ?></div><div class="lbl">Tiket Terjual</div></div>
  <div><div class="num"><?= number_format($summary['pending']) ?></div><div class="lbl">Pending</div></div>
</div>

<?php if (!$data): ?>
<p style="text-align:center;color:#999;padding:30px 0">Tidak ada data pada rentang tanggal ini.</p>
<?php elseif ($type==='orders'): ?>
<table>
  <thead><tr><th>Kode</th><th>Pembeli</th><th>Konser</th><th>Tiket</th><th>Jml</th><th>Total</th><th>Status</th><th>Tanggal</th></tr></thead>
  <tbody>
  <?php foreach ($data as $r): ?>
  <tr>
    <td><?= htmlspecialchars($r['kode']) ?></td>
    <td><?= htmlspecialchars($r['nama_pembeli']) ?><br><span style="color:#999"><?= htmlspecialchars($r['email']) ?></span></td>
    <td><?= htmlspecialchars($r['nama_konser']) ?></td>
    <td><?= htmlspecialchars($r['nama_tiket']) ?></td>
    <td><?= $r['jumlah'] ?></td>
    <td><?= rupiah($r['total']) ?></td>
    <td><span class="status status-<?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
    <td><?= date('d/m/Y',strtotime($r['created_at'])) ?></td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php elseif ($type==='konser'): ?>
<table>
  <thead><tr><th>Konser</th><th>Artis</th><th>Tanggal</th><th>Kota</th><th>Jenis Tiket</th><th>Terjual</th><th>Pendapatan</th><th>Status</th></tr></thead>
  <tbody>
  <?php foreach ($data as $r): ?>
  <tr>
    <td><?= htmlspecialchars($r['nama']) ?></td>
    <td><?= htmlspecialchars($r['artis']) ?></td>
    <td><?= tglID($r['tanggal']) ?></td>
    <td><?= htmlspecialchars($r['kota']) ?></td>
    <td><?= $r['jml_tiket'] ?></td>
    <td><?= number_format($r['total_terjual']) ?></td>
    <td><?= rupiah($r['pendapatan']) ?></td>
    <td><span class="status status-<?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
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
    <td><?= htmlspecialchars($r['nama_konser']) ?></td>
    <td><?= htmlspecialchars($r['nama']) ?></td>
    <td><?= rupiah($r['harga']) ?></td>
    <td><?= number_format($r['stok']) ?></td>
    <td><?= number_format($r['terjual']) ?></td>
    <td><?= number_format($r['sisa']) ?></td>
    <td><?= rupiah($r['pendapatan']) ?></td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>

<div class="foot">NexTix &copy; <?= date('Y') ?> — Laporan ini dibuat otomatis oleh sistem.</div>

<script>
  window.onload = function() { setTimeout(() => window.print(), 400); };
</script>
</body>
</html>
