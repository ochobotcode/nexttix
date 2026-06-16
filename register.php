<?php
session_start();
require_once 'config/database.php';
require_once 'config/app.php';

if (isset($_SESSION['user_id']))  redirect(APP_URL . '/public/index.php');
if (isset($_SESSION['admin_id'])) redirect(APP_URL . '/admin/index.php');

$errors = [];
$data   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['nama']     = clean($_POST['nama']     ?? '');
    $data['email']    = clean($_POST['email']    ?? '');
    $data['telepon']  = clean($_POST['telepon']  ?? '');
    $pass             =       $_POST['password'] ?? '';
    $pass2            =       $_POST['password2']?? '';

    if (!$data['nama'])  $errors[] = 'Nama lengkap wajib diisi.';
    if (!$data['email'] || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid.';
    if (strlen($pass) < 6) $errors[] = 'Password minimal 6 karakter.';
    if ($pass !== $pass2)  $errors[] = 'Konfirmasi password tidak cocok.';

    if (!$errors) {
        $exists = DB::val("SELECT COUNT(*) FROM users WHERE email = ?", [$data['email']]);
        if ($exists) {
            $errors[] = 'Email sudah terdaftar. Silahkan masuk.';
        } else {
            $id = DB::insert(
                "INSERT INTO users (nama, email, password, telepon) VALUES (?,?,?,?)",
                [$data['nama'], $data['email'], password_hash($pass, PASSWORD_DEFAULT), $data['telepon']]
            );
            $_SESSION['user_id']    = $id;
            $_SESSION['user_nama']  = $data['nama'];
            $_SESSION['user_email'] = $data['email'];
            flash('Selamat datang di NexTix, ' . $data['nama'] . '! 🎉');
            redirect(APP_URL . '/public/index.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar — NexTix</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
</head>
<body>
<main class="auth-page">
  <div class="auth-card animate-up">
    <div class="auth-logo">
      <div class="auth-logo-icon">🎵</div>
      <h1>Buat Akun</h1>
      <p>Mulai pesan tiket konser favoritmu</p>
    </div>

    <?php if ($errors): ?>
    <div class="alert alert-error">
      <div>
        <?php foreach ($errors as $e): ?>
          <div><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label class="form-label req">Nama Lengkap</label>
        <div class="input-wrap">
          <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <input type="text" name="nama" class="form-control has-icon"
                 placeholder="Nama lengkap kamu"
                 value="<?= htmlspecialchars($data['nama'] ?? '') ?>" required autofocus>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label req">Email</label>
        <div class="input-wrap">
          <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <input type="email" name="email" class="form-control has-icon"
                 placeholder="email@kamu.com"
                 value="<?= htmlspecialchars($data['email'] ?? '') ?>" required>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">No. Telepon</label>
        <div class="input-wrap">
          <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.65 3.09 2 2 0 0 1 3.62 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 8.91a16 16 0 0 0 6.29 6.29l.79-.79a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          <input type="tel" name="telepon" class="form-control has-icon"
                 placeholder="08xxxxxxxxxx"
                 value="<?= htmlspecialchars($data['telepon'] ?? '') ?>">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label req">Password</label>
        <div class="input-wrap">
          <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input type="password" name="password" class="form-control has-icon" placeholder="Min. 6 karakter" required>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label req">Konfirmasi Password</label>
        <div class="input-wrap">
          <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input type="password" name="password2" class="form-control has-icon" placeholder="Ulangi password" required>
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-full">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><line x1="11" y1="11" x2="17" y2="17"/><line x1="17" y1="11" x2="11" y2="17"/></svg>
        Buat Akun
      </button>
    </form>

    <div class="auth-footer" style="margin-top:20px">
      Sudah punya akun? <a href="<?= APP_URL ?>/login.php">Masuk di sini</a>
    </div>
  </div>
</main>
</body>
</html>
