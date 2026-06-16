<?php /* $k = konser row */ ?>
<a href="<?= APP_URL ?>/public/konser.php?slug=<?= urlencode($k['slug']) ?>" class="concert-card" style="display:block">
  <div class="concert-poster">
    <?php if ($k['poster'] && file_exists(POSTER_DIR . $k['poster'])): ?>
      <img src="<?= APP_URL ?>/uploads/posters/<?= htmlspecialchars($k['poster']) ?>" alt="<?= htmlspecialchars($k['nama']) ?>" loading="lazy">
    <?php else: ?>
      <div class="concert-poster-placeholder"><span>🎵</span><p><?= htmlspecialchars($k['artis']) ?></p></div>
    <?php endif; ?>
    <div class="concert-status-overlay"><?= statusBadge($k['status']) ?></div>
  </div>
  <div class="concert-body">
    <div class="concert-artis"><?= htmlspecialchars($k['artis']) ?></div>
    <div class="concert-name"><?= htmlspecialchars($k['nama']) ?></div>
    <div class="concert-meta">
      <div class="concert-meta-item">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <?= tglID($k['tanggal']) ?> · <?= substr($k['jam'],0,5) ?> WIB
      </div>
      <div class="concert-meta-item">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        <?= htmlspecialchars($k['venue']) ?>, <?= htmlspecialchars($k['kota']) ?>
      </div>
    </div>
    <div class="concert-footer">
      <div class="concert-price">
        Mulai dari
        <strong><?= isset($k['harga_min']) && $k['harga_min'] ? rupiah($k['harga_min']) : 'Lihat harga' ?></strong>
      </div>
      <span class="btn btn-primary btn-sm">Beli Tiket</span>
    </div>
  </div>
</a>
