<?php
session_start();
require_once 'config/database.php';
require_once 'config/app.php';

// Already logged in?
if (isset($_SESSION['admin_id'])) redirect(APP_URL . '/admin/index.php');
if (isset($_SESSION['user_id']))  redirect(APP_URL . '/public/index.php');

$error = '';
$next  = clean($_GET['next'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = clean($_POST['email']    ?? '');
    $password =       $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Email dan password wajib diisi.';
    } else {
        // ── Cek Admin/Operator ──────────────────────────────
        $admin = DB::row("SELECT * FROM admins WHERE email = ? AND is_active = 1", [$email]);
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_nama'] = $admin['nama'];
            $_SESSION['admin_role'] = $admin['role'];
            DB::run("UPDATE admins SET last_login = NOW() WHERE id = ?", [$admin['id']]);
            DB::run("INSERT INTO activity_log (admin_id, aksi, detail, ip) VALUES (?,?,?,?)",
                    [$admin['id'], 'login', 'Login berhasil', $_SERVER['REMOTE_ADDR']]);
            redirect(APP_URL . '/admin/index.php');
        }

        // ── Cek User/Pelanggan ──────────────────────────────
        $user = DB::row("SELECT * FROM users WHERE email = ? AND is_active = 1", [$email]);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_nama']  = $user['nama'];
            $_SESSION['user_email'] = $user['email'];
            DB::run("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']]);
            $dest = ($next && str_starts_with($next, '/')) ? APP_URL . $next : APP_URL . '/public/index.php';
            redirect($dest);
        }

        $error = 'Email atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk — NexTix</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
</head>
<body>

<main class="auth-page">
  <div class="auth-card animate-up">
    <div class="auth-logo">
      <div class="auth-logo-icon">🎵</div>
      <h1>Selamat Datang</h1>
      <p>Masuk ke akun NexTix kamu</p>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <?php $f = getFlash(); if ($f): ?>
    <div class="alert alert-<?= $f['type'] ?>"><?= htmlspecialchars($f['msg']) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <?php if ($next): ?>
      <input type="hidden" name="next" value="<?= htmlspecialchars($next) ?>">
      <?php endif; ?>

      <div class="form-group">
        <label class="form-label req">Email</label>
        <div class="input-wrap">
          <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <input type="email" name="email" class="form-control has-icon"
                 placeholder="email@kamu.com"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                 required autofocus>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label req">Password</label>
        <div class="input-wrap" style="position:relative">
          <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input type="password" name="password" id="pwInput" class="form-control has-icon"
                 placeholder="Password kamu" required>
          <button type="button" id="pwToggle"
            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text3)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-full" style="margin-top:8px">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        Masuk
      </button>
    </form>

    <div class="auth-footer" style="margin-top:24px">
      Belum punya akun? <a href="<?= APP_URL ?>/register.php">Daftar gratis</a>
    </div>
  </div>
</main>

<script>
document.getElementById('pwToggle')?.addEventListener('click', function() {
    const inp = document.getElementById('pwInput');
    inp.type = inp.type === 'password' ? 'text' : 'password';
});
</script>
</body>
</html>
