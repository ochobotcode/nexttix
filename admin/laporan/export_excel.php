<?php
require_once '../../auth/check_admin.php';
require_once '_data.php';

$filename = 'laporan-' . $type . '-' . $from . '-sd-' . $to . '.xls';

header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// BOM for proper UTF-8 rendering in Excel
echo "\xEF\xBB\xBF";
?>
<html>
<head><meta charset="UTF-8"></head>
<body>
<table border="1">
  <tr><td colspan="10" style="font-size:16px;font-weight:bold;background:#8b5cf6;color:#fff;padding:8px"><?= htmlspecialchars($reportTitle) ?> — NexTix</td></tr>
  <tr><td colspan="10">Periode: <?= tglID($from) ?> s/d <?= tglID($to) ?></td></tr>
  <tr><td colspan="10"></td></tr>

  <?php if ($type==='orders'): ?>
  <tr style="background:#f0f0f0;font-weight:bold">
    <td>Kode</td><td>Nama Pembeli</td><td>Email</td><td>Konser</td><td>Tiket</td><td>Jumlah</td><td>Harga Satuan</td><td>Total</td><td>Status</td><td>Tanggal</td>
  </tr>
  <?php foreach ($data as $r): ?>
  <tr>
    <td><?= htmlspecialchars($r['kode']) ?></td>
    <td><?= htmlspecialchars($r['nama_pembeli']) ?></td>
    <td><?= htmlspecialchars($r['email']) ?></td>
    <td><?= htmlspecialchars($r['nama_konser']) ?></td>
    <td><?= htmlspecialchars($r['nama_tiket']) ?></td>
    <td><?= $r['jumlah'] ?></td>
    <td><?= $r['harga_satuan'] ?></td>
    <td><?= $r['total'] ?></td>
    <td><?= ucfirst($r['status']) ?></td>
    <td><?= date('d-m-Y H:i', strtotime($r['created_at'])) ?></td>
  </tr>
  <?php endforeach; ?>
  <tr></tr>
  <tr style="font-weight:bold"><td colspan="6"></td><td>Total Pendapatan</td><td colspan="3"><?= $summary['revenue'] ?></td></tr>

  <?php elseif ($type==='konser'): ?>
  <tr style="background:#f0f0f0;font-weight:bold">
    <td>Nama Konser</td><td>Artis</td><td>Tanggal</td><td>Kota</td><td>Jenis Tiket</td><td>Total Stok</td><td>Total Terjual</td><td>Pendapatan</td><td>Status</td>
  </tr>
  <?php foreach ($data as $r): ?>
  <tr>
    <td><?= htmlspecialchars($r['nama']) ?></td>
    <td><?= htmlspecialchars($r['artis']) ?></td>
    <td><?= date('d-m-Y', strtotime($r['tanggal'])) ?></td>
    <td><?= htmlspecialchars($r['kota']) ?></td>
    <td><?= $r['jml_tiket'] ?></td>
    <td><?= $r['total_stok'] ?></td>
    <td><?= $r['total_terjual'] ?></td>
    <td><?= $r['pendapatan'] ?: 0 ?></td>
    <td><?= ucfirst($r['status']) ?></td>
  </tr>
  <?php endforeach; ?>

  <?php else: ?>
  <tr style="background:#f0f0f0;font-weight:bold">
    <td>Konser</td><td>Artis</td><td>Nama Tiket</td><td>Harga</td><td>Stok</td><td>Terjual</td><td>Sisa</td><td>Pendapatan</td><td>Status</td>
  </tr>
  <?php foreach ($data as $r): ?>
  <tr>
    <td><?= htmlspecialchars($r['nama_konser']) ?></td>
    <td><?= htmlspecialchars($r['artis']) ?></td>
    <td><?= htmlspecialchars($r['nama']) ?></td>
    <td><?= $r['harga'] ?></td>
    <td><?= $r['stok'] ?></td>
    <td><?= $r['terjual'] ?></td>
    <td><?= $r['sisa'] ?></td>
    <td><?= $r['pendapatan'] ?: 0 ?></td>
    <td><?= $r['is_active']?'Aktif':'Nonaktif' ?></td>
  </tr>
  <?php endforeach; ?>
  <?php endif; ?>
</table>
</body>
</html>
