<?php
$BASE    = rtrim(APP_URL, '/');
$curPath = $_SERVER['PHP_SELF'];
$isAdm   = ($_SESSION['admin_role'] ?? '') === 'admin';

function sideLink(string $href, string $icon, string $label, string $curPath, string $badge=''): string {
    $hrefPath = parse_url($href, PHP_URL_PATH);
    $active = $hrefPath && str_contains($curPath, $hrefPath) ? ' active' : '';
    $b = $badge ? '<span class="sidebar-badge">'.$badge.'</span>' : '';
    return '<a href="'.$href.'" class="sidebar-link'.$active.'">'.$icon.'<span>'.$label.'</span>'.$b.'</a>';
}

$nama = htmlspecialchars($_SESSION['admin_nama'] ?? '');
$role = htmlspecialchars($_SESSION['admin_role'] ?? '');
$inisial = strtoupper(substr($nama, 0, 1));
?>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <div class="sidebar-logo-icon">🎵</div>
    <div>
      <div class="sidebar-logo-text">NexTix <span class="sidebar-logo-badge">Admin</span></div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="sidebar-section">
      <div class="sidebar-section-label">Utama</div>
      <?= sideLink($BASE.'/admin/index.php', '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>', 'Dashboard', $curPath) ?>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Konten</div>
      <?= sideLink($BASE.'/admin/konser/', '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>', 'Konser', $curPath) ?>
      <?= sideLink($BASE.'/admin/tiket/', '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/></svg>', 'Tiket', $curPath) ?>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Transaksi</div>
      <?= sideLink($BASE.'/admin/orders/', '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>', 'Pesanan', $curPath) ?>
      <?= sideLink($BASE.'/admin/laporan/', '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>', 'Laporan', $curPath) ?>
    </div>

    <?php if ($isAdm): ?>
    <div class="sidebar-section">
      <div class="sidebar-section-label">Manajemen</div>
      <?= sideLink($BASE.'/admin/users/', '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>', 'Pengguna', $curPath) ?>
    </div>
    <?php endif; ?>

    <div class="sidebar-section" style="margin-top:auto">
      <div class="sidebar-section-label">Akun</div>
      <a href="<?= $BASE ?>/public/index.php" target="_blank" class="sidebar-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        <span>Lihat Website</span>
      </a>
      <a href="<?= $BASE ?>/admin/logout.php" class="sidebar-link" style="color:var(--red)">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        <span>Keluar</span>
      </a>
    </div>
  </nav>

  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="sidebar-user-avatar"><?= $inisial ?></div>
      <div>
        <div class="sidebar-user-name"><?= $nama ?></div>
        <div class="sidebar-user-role"><?= ucfirst($role) ?></div>
      </div>
    </div>
  </div>
</aside>
