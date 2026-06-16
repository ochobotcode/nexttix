<?php
require_once '../auth/check_user.php';

$konserID = (int)($_GET['konser_id'] ?? $_POST['konser_id'] ?? 0);
$konser   = DB::row("SELECT * FROM konser WHERE id = ?", [$konserID]);
if (!$konser) redirect(APP_URL . '/public/katalog.php');

// Build cart from GET tiket[id]=qty
$cart   = [];
$total  = 0;
$tiketReq = $_GET['tiket'] ?? $_POST['tiket'] ?? [];
foreach ($tiketReq as $tid => $qty) {
    $qty = (int)$qty;
    if ($qty < 1) continue;
    $t = DB::row("SELECT * FROM tiket WHERE id = ? AND konser_id = ? AND is_active = 1", [(int)$tid, $konserID]);
    if (!$t) continue;
    $sisa = $t['stok'] - $t['terjual'];
    if ($qty > $sisa) $qty = $sisa;
    if ($qty < 1) continue;
    $cart[(int)$tid] = ['tiket' => $t, 'qty' => $qty, 'sub' => $t['harga'] * $qty];
    $total += $t['harga'] * $qty;
}
if (!$cart) { flash('Pilih tiket terlebih dahulu.', 'error'); redirect(APP_URL . '/public/konser.php?slug='.$konser['slug']); }

// Handle POST checkout
$errors = [];
$d      = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d['metode'] = clean($_POST['metode'] ?? '');
    if (!$d['metode']) $errors[] = 'Pilih metode pembayaran.';

    if (!$errors) {
        foreach ($cart as $tid => $item) {
            $kode = generateKode();
            DB::run("INSERT INTO orders (kode,user_id,tiket_id,jumlah,harga_satuan,total,status,metode_bayar,tgl_bayar) VALUES (?,?,?,?,?,?,'paid',?,NOW())",
                [$kode, $_SESSION['user_id'], $tid, $item['qty'], $item['tiket']['harga'], $item['sub'], $d['metode']]);
            DB::run("UPDATE tiket SET terjual = terjual + ? WHERE id = ?", [$item['qty'], $tid]);
        }
        flash('Pembayaran berhasil! Tiket kamu sudah siap. 🎉');
        redirect(APP_URL . '/public/tiket-saya.php');
    }
}

$user = DB::row("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Checkout — NexTix</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<section class="section">
  <div class="section-inner">
    <div class="page-title">🛒 Checkout</div>
    <div class="page-sub"><?= htmlspecialchars($konser['nama']) ?> · <?= tglID($konser['tanggal']) ?></div>

    <?php if ($errors): ?>
    <div class="alert alert-error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
    <?php endif; ?>

    <div class="checkout-grid">
      <!-- FORM -->
      <div>
        <form method="POST" action="">
          <input type="hidden" name="konser_id" value="<?= $konserID ?>">
          <?php foreach ($cart as $tid => $item): ?>
          <input type="hidden" name="tiket[<?= $tid ?>]" value="<?= $item['qty'] ?>">
          <?php endforeach; ?>

          <!-- Info Pembeli -->
          <div class="card" style="margin-bottom:20px">
            <div class="card-header" style="font-weight:700">👤 Informasi Pemesan</div>
            <div class="card-body">
              <div class="form-row">
                <div class="form-group mb-0">
                  <label class="form-label">Nama</label>
                  <input class="form-control" value="<?= htmlspecialchars($user['nama']) ?>" disabled>
                </div>
                <div class="form-group mb-0">
                  <label class="form-label">Email</label>
                  <input class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                </div>
              </div>
            </div>
          </div>

          <!-- Metode Bayar -->
          <div class="card">
            <div class="card-header" style="font-weight:700">💳 Metode Pembayaran</div>
            <div class="card-body">
              <?php
              $metodes = ['Transfer Bank BCA','Transfer Bank Mandiri','Transfer Bank BNI','GoPay','OVO','Dana','ShopeePay','QRIS'];
              foreach ($metodes as $m):
              ?>
              <label style="display:flex;align-items:center;gap:10px;padding:10px;border:1.5px solid var(--border);border-radius:10px;cursor:pointer;margin-bottom:8px;transition:border-color .2s" class="metode-label">
                <input type="radio" name="metode" value="<?= $m ?>" style="accent-color:var(--accent)" <?= ($d['metode']??'')===$m?'checked':'' ?>>
                <?= $m ?>
              </label>
              <?php endforeach; ?>
            </div>
          </div>

          <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top:20px">
            🎟️ Bayar <?= rupiah($total) ?>
          </button>
          <p style="text-align:center;font-size:.78rem;color:var(--text3);margin-top:10px">Dengan menekan bayar, kamu menyetujui syarat & ketentuan NexTix</p>
        </form>
      </div>

      <!-- SUMMARY -->
      <div>
        <div class="order-summary">
          <div class="order-summary-title">📋 Ringkasan Pesanan</div>
          <div style="font-size:.8rem;color:var(--text3);margin-bottom:14px"><?= htmlspecialchars($konser['nama']) ?></div>
          <?php foreach ($cart as $item): ?>
          <div class="order-row">
            <span><?= htmlspecialchars($item['tiket']['nama']) ?> ×<?= $item['qty'] ?></span>
            <span><?= rupiah($item['sub']) ?></span>
          </div>
          <?php endforeach; ?>
          <div class="order-row">
            <span>Biaya layanan</span>
            <span>Gratis</span>
          </div>
          <div class="order-row total">
            <span>Total</span>
            <span style="color:var(--accent3)"><?= rupiah($total) ?></span>
          </div>
          <div style="margin-top:16px;padding:12px;background:rgba(16,185,129,.08);border-radius:10px;border:1px solid rgba(16,185,129,.2);font-size:.8rem;color:#34d399">
            ✅ Pembelian aman &amp; terenkripsi
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include '../includes/footer.php'; ?>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
<script>
document.querySelectorAll('.metode-label').forEach(lbl => {
    lbl.addEventListener('click', () => {
        document.querySelectorAll('.metode-label').forEach(l => l.style.borderColor = 'var(--border)');
        lbl.style.borderColor = 'var(--accent)';
    });
});
</script>
</body>
</html>
