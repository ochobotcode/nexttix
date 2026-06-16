<?php
require_once '../auth/check_user.php';
$kode  = clean($_GET['kode'] ?? '');
$order = DB::row("SELECT * FROM v_order_detail WHERE kode = ? AND user_id = ? AND status = 'paid'", [$kode, $_SESSION['user_id']]);
if (!$order) { flash('Tiket tidak ditemukan.','error'); redirect(APP_URL.'/public/tiket-saya.php'); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>E-Tiket <?= htmlspecialchars($order['kode']) ?> — NexTix</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
  <style>
    @media print{
      .no-print{display:none!important}
      body{background:#fff!important;padding:0}
      .eticket{background:#fff!important;border:1px solid #ddd!important;box-shadow:none!important;margin:0 auto}
      .eticket-row{border-bottom:1px solid #eee!important}
      .eticket-row span:first-child{color:#888!important}
      .eticket-row span:last-child{color:#1a1a2e!important}
      .eticket-tear::before,.eticket-tear::after{background:#fff!important}
      .eticket-tear-line{border-top:2px dashed #ccc!important}
      .eticket-qr{border-top:1px solid #eee!important}
      .eticket-code{color:#888!important}
    }
    .eticket{max-width:520px;margin:40px auto;background:var(--card);border:1px solid var(--border);border-radius:20px;overflow:hidden}
    .eticket-top{background:linear-gradient(135deg,var(--accent),var(--pink));padding:28px 28px 20px;color:#fff}
    .eticket-logo{font-size:1.1rem;font-weight:800;margin-bottom:16px;opacity:.9}
    .eticket-nama{font-size:1.5rem;font-weight:800;line-height:1.2;margin-bottom:6px}
    .eticket-artis{font-size:.9rem;opacity:.85;margin-bottom:0}
    .eticket-tear{display:flex;align-items:center;gap:0;padding:0 0;position:relative;height:28px}
    .eticket-tear::before,.eticket-tear::after{content:'';width:20px;height:20px;border-radius:50%;background:var(--bg);flex-shrink:0}
    .eticket-tear-line{flex:1;border-top:2px dashed var(--border2)}
    .eticket-body{padding:24px 28px}
    .eticket-row{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:.875rem}
    .eticket-row:last-of-type{border-bottom:none}
    .eticket-row span:first-child{color:var(--text2)}
    .eticket-row span:last-child{font-weight:700;text-align:right}
    .eticket-qr{text-align:center;padding:20px 28px 28px;border-top:1px solid var(--border)}
    .qr-box{display:inline-block;background:#fff;padding:16px;border-radius:14px;margin-bottom:10px}
    .qr-pattern{width:120px;height:120px;background:repeating-conic-gradient(#333 0% 25%,#fff 0% 50%) 0 0/10px 10px}
    .eticket-code{font-size:.8rem;color:var(--text3);font-family:monospace;letter-spacing:.08em}
  </style>
</head>
<body style="background:var(--bg2);min-height:100vh;padding:20px">
<div style="text-align:center;margin-bottom:20px" class="no-print">
  <a href="<?= APP_URL ?>/public/tiket-saya.php" class="btn btn-ghost btn-sm">← Kembali</a>
  <button onclick="window.print()" class="btn btn-primary btn-sm" style="margin-left:8px">🖨️ Cetak / Simpan PDF</button>
</div>

<div class="eticket">
  <div class="eticket-top">
    <div class="eticket-logo">🎵 NexTix</div>
    <div class="eticket-nama"><?= htmlspecialchars($order['nama_konser']) ?></div>
    <div class="eticket-artis"><?= htmlspecialchars($order['artis']) ?></div>
  </div>
  <div class="eticket-tear">
    <div class="eticket-tear-line"></div>
  </div>
  <div class="eticket-body">
    <div class="eticket-row"><span>Nama Pemesan</span><span><?= htmlspecialchars($order['nama_pembeli']) ?></span></div>
    <div class="eticket-row"><span>Kategori Tiket</span><span><?= htmlspecialchars($order['nama_tiket']) ?></span></div>
    <div class="eticket-row"><span>Jumlah</span><span><?= $order['jumlah'] ?> tiket</span></div>
    <div class="eticket-row"><span>Tanggal Konser</span><span><?= tglID($order['tanggal_konser']) ?></span></div>
    <div class="eticket-row"><span>Waktu</span><span><?= substr($order['jam_konser'],0,5) ?> WIB</span></div>
    <div class="eticket-row"><span>Venue</span><span><?= htmlspecialchars($order['venue']) ?>, <?= htmlspecialchars($order['kota']) ?></span></div>
    <div class="eticket-row"><span>Total Bayar</span><span style="color:var(--accent3)"><?= rupiah($order['total']) ?></span></div>
    <div class="eticket-row"><span>Metode</span><span><?= htmlspecialchars($order['metode_bayar']) ?></span></div>
  </div>
  <div class="eticket-qr">
    <div class="qr-box">
      <div class="qr-pattern"></div>
    </div>
    <div class="eticket-code"><?= htmlspecialchars($order['kode']) ?></div>
    <div style="font-size:.75rem;color:var(--text3);margin-top:6px">Tunjukkan tiket ini saat masuk venue</div>
  </div>
</div>
</body>
</html>
