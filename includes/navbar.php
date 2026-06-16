<?php
$BASE  = rtrim(APP_URL, '/');
$navPage  = basename($_SERVER['PHP_SELF'], '.php');
$isUser = isset($_SESSION['user_id']);
$userName = $_SESSION['user_nama'] ?? '';
$userInisial = $isUser ? strtoupper(substr($userName, 0, 1)) : '';
?>
<nav class="navbar">
  <div class="nav-inner">
    <a href="<?= $BASE ?>/public/index.php" class="nav-logo">
      <div class="nav-logo-icon">🎵</div>
      Nex<span style="color:var(--accent3)">Tix</span>
    </a>

    <div class="nav-links">
      <a href="<?= $BASE ?>/public/index.php"   class="nav-link <?= $navPage==='index'   ?'active':'' ?>">Beranda</a>
      <a href="<?= $BASE ?>/public/katalog.php" class="nav-link <?= $navPage==='katalog' ?'active':'' ?>">Konser</a>
      <?php if ($isUser): ?>
      <a href="<?= $BASE ?>/public/tiket-saya.php" class="nav-link <?= $navPage==='tiket-saya' ?'active':'' ?>">Tiket Saya</a>
      <?php endif; ?>
    </div>

    <div class="nav-actions">
      <?php if ($isUser): ?>
        <div class="nav-dropdown">
          <div class="nav-avatar" title="<?= htmlspecialchars($userName) ?>"><?= $userInisial ?></div>
          <div class="nav-dropdown-menu">
            <div style="padding:10px 14px 12px;border-bottom:1px solid var(--border);margin-bottom:6px;">
              <div style="font-weight:700;font-size:.875rem;"><?= htmlspecialchars($userName) ?></div>
              <div style="font-size:.75rem;color:var(--text3);"><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></div>
            </div>
            <a href="<?= $BASE ?>/public/tiket-saya.php" class="nav-dropdown-item">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/></svg>
              Tiket Saya
            </a>
            <a href="<?= $BASE ?>/public/profile.php" class="nav-dropdown-item">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              Profil
            </a>
            <div class="nav-divider"></div>
            <a href="<?= $BASE ?>/logout.php" class="nav-dropdown-item danger">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
              Keluar
            </a>
          </div>
        </div>
      <?php else: ?>
        <a href="<?= $BASE ?>/login.php" class="btn btn-ghost btn-sm">Masuk</a>
        <a href="<?= $BASE ?>/register.php" class="btn btn-primary btn-sm">Daftar</a>
      <?php endif; ?>
      <button class="hamburger" id="hamBtn" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</nav>

<div class="mobile-menu" id="mobileMenu">
  <div class="mobile-menu-inner">
    <button class="btn btn-ghost btn-sm mobile-menu-close" id="mobileClose">✕ Tutup</button>
    <a href="<?= $BASE ?>/public/index.php"   class="nav-link" style="display:block">Beranda</a>
    <a href="<?= $BASE ?>/public/katalog.php" class="nav-link" style="display:block">Konser</a>
    <?php if ($isUser): ?>
    <a href="<?= $BASE ?>/public/tiket-saya.php" class="nav-link" style="display:block">Tiket Saya</a>
    <a href="<?= $BASE ?>/public/profile.php"    class="nav-link" style="display:block">Profil</a>
    <div class="divider"></div>
    <a href="<?= $BASE ?>/logout.php" class="nav-link" style="display:block;color:var(--red)">Keluar</a>
    <?php else: ?>
    <div class="divider"></div>
    <a href="<?= $BASE ?>/login.php"    class="btn btn-ghost btn-full" style="margin-bottom:8px">Masuk</a>
    <a href="<?= $BASE ?>/register.php" class="btn btn-primary btn-full">Daftar</a>
    <?php endif; ?>
  </div>
</div>