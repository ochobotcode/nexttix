<?php
require_once '../auth/check_user.php';
$user   = DB::row("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
$errors = []; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = clean($_POST['nama']    ?? '');
    $telepon = clean($_POST['telepon'] ?? '');
    $pass    =       $_POST['password']?? '';
    $pass2   =       $_POST['password2']??'';
    if (!$nama) $errors[] = 'Nama wajib diisi.';
    if ($pass && strlen($pass)<6) $errors[] = 'Password min. 6 karakter.';
    if ($pass && $pass!==$pass2)  $errors[] = 'Konfirmasi password tidak cocok.';
    if (!$errors) {
        if ($pass) {
            DB::run("UPDATE users SET nama=?,telepon=?,password=?,updated_at=NOW() WHERE id=?",[$nama,$telepon,password_hash($pass,PASSWORD_DEFAULT),$_SESSION['user_id']]);
        } else {
            DB::run("UPDATE users SET nama=?,telepon=?,updated_at=NOW() WHERE id=?",[$nama,$telepon,$_SESSION['user_id']]);
        }
        $_SESSION['user_nama'] = $nama;
        $user = DB::row("SELECT * FROM users WHERE id=?",[$_SESSION['user_id']]);
        flash('Profil berhasil diperbarui! ✅');
        redirect(APP_URL.'/public/profile.php');
    }
}

$totalOrders = DB::val("SELECT COUNT(*) FROM orders WHERE user_id=?",[$_SESSION['user_id']]);
$totalSpend  = DB::val("SELECT COALESCE(SUM(total),0) FROM orders WHERE user_id=? AND status='paid'",[$_SESSION['user_id']]);
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Profil — NexTix</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<section class="section">
  <div class="section-inner">
    <div class="page-title">👤 Profil Saya</div>
    <div class="page-sub">Kelola informasi akun kamu</div>
    <?php if ($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div><?php endif; ?>
    <?php if ($errors): ?><div class="alert alert-error"><?= implode('<br>',array_map('htmlspecialchars',$errors)) ?></div><?php endif; ?>

    <div class="profile-grid">
      <!-- Card kiri -->
      <div>
        <div class="profile-card">
          <div class="profile-avatar"><?= strtoupper(substr($user['nama'],0,1)) ?></div>
          <div class="profile-name"><?= htmlspecialchars($user['nama']) ?></div>
          <div class="profile-email"><?= htmlspecialchars($user['email']) ?></div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:20px">
            <div style="background:var(--bg3);border-radius:10px;padding:14px;text-align:center">
              <div style="font-size:1.4rem;font-weight:800"><?= $totalOrders ?></div>
              <div style="font-size:.75rem;color:var(--text3)">Total Pesanan</div>
            </div>
            <div style="background:var(--bg3);border-radius:10px;padding:14px;text-align:center">
              <div style="font-size:1rem;font-weight:800;color:var(--accent3)"><?= rupiah($totalSpend) ?></div>
              <div style="font-size:.75rem;color:var(--text3)">Total Belanja</div>
            </div>
          </div>
          <div style="margin-top:16px">
            <a href="<?= APP_URL ?>/public/tiket-saya.php" class="btn btn-ghost btn-full btn-sm">Lihat Tiket Saya</a>
          </div>
        </div>
      </div>

      <!-- Form kanan -->
      <div class="card">
        <div class="card-header" style="font-weight:700">Edit Profil</div>
        <div class="card-body">
          <form method="POST" action="">
            <div class="form-group">
              <label class="form-label req">Nama Lengkap</label>
              <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama']) ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
              <div class="form-hint">Email tidak dapat diubah</div>
            </div>
            <div class="form-group">
              <label class="form-label">No. Telepon</label>
              <input type="tel" name="telepon" class="form-control" value="<?= htmlspecialchars($user['telepon']??'') ?>">
            </div>
            <div class="divider"></div>
            <div style="font-weight:600;margin-bottom:14px;font-size:.9rem">Ganti Password <span style="font-weight:400;color:var(--text3)">(kosongkan jika tidak ingin diubah)</span></div>
            <div class="form-row">
              <div class="form-group mb-0">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter">
              </div>
              <div class="form-group mb-0">
                <label class="form-label">Konfirmasi</label>
                <input type="password" name="password2" class="form-control" placeholder="Ulangi password">
              </div>
            </div>
            <div style="margin-top:24px">
              <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<?php include '../includes/footer.php'; ?>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body></html>
