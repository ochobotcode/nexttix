<?php
require_once '../../auth/check_admin.php';
$id = (int)($_GET['id']??0);
$o  = DB::row("SELECT * FROM v_order_detail WHERE id=?",[$id]);
if (!$o) { flash('Pesanan tidak ditemukan.','error'); redirect(APP_URL.'/admin/orders/'); }
?>
<!DOCTYPE html><html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Detail Pesanan — NexTix Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/admin.css"></head>
<body><div class="admin-layout">
<?php include '../../includes/sidebar.php'; ?>
<div class="admin-main">
  <div class="topbar">
    <button class="topbar-btn" id="sideToggle"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
    <div><div class="topbar-title">Detail Pesanan</div></div>
    <div class="topbar-right"><a href="<?= APP_URL ?>/admin/orders/" class="btn btn-ghost btn-sm">← Kembali</a></div>
  </div>
  <div class="page-wrap"><div class="form-page" style="max-width:620px">
    <div class="form-card">
      <div class="form-card-title">Info Pesanan — <?= htmlspecialchars($o['kode']) ?> &nbsp; <?= statusBadge($o['status']) ?></div>
      <div style="display:grid;gap:12px">
        <?php foreach ([
          'Nama Pembeli'=>$o['nama_pembeli'],'Email'=>$o['email_pembeli'],'Telepon'=>$o['telepon'],
          'Konser'=>$o['nama_konser'],'Artis'=>$o['artis'],'Tiket'=>$o['nama_tiket'],
          'Tanggal Konser'=>tglID($o['tanggal_konser']).' · '.substr($o['jam_konser'],0,5).' WIB',
          'Venue'=>$o['venue'].', '.$o['kota'],
          'Jumlah'=>$o['jumlah'].' tiket','Harga Satuan'=>rupiah($o['harga_satuan']),'Total'=>rupiah($o['total']),
          'Metode Bayar'=>$o['metode_bayar']??'-','Tgl Bayar'=>$o['tgl_bayar']?date('d M Y H:i',strtotime($o['tgl_bayar'])):'-',
          'Tgl Pesan'=>date('d M Y H:i',strtotime($o['created_at'])),
        ] as $label=>$val): ?>
        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);font-size:.875rem">
          <span style="color:var(--text2)"><?= $label ?></span>
          <span style="font-weight:600;text-align:right"><?= htmlspecialchars($val??'-') ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php if ($o['status']==='pending'): ?>
    <div style="display:flex;gap:10px">
      <a href="update.php?id=<?= $o['id'] ?>&status=paid" class="btn btn-primary" onclick="return confirm('Konfirmasi pembayaran?')">✅ Konfirmasi Lunas</a>
      <a href="update.php?id=<?= $o['id'] ?>&status=cancelled" class="btn btn-ghost" style="color:var(--red);border-color:var(--red)" onclick="return confirm('Batalkan pesanan ini?')">✖ Batalkan</a>
    </div>
    <?php endif; ?>
  </div></div>
</div></div>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body></html>
