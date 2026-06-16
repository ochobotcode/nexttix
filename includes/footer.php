<?php $BASE = rtrim(APP_URL, '/'); ?>
<footer class="footer">
  <div class="footer-inner">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="nav-logo" style="justify-content:flex-start">
          <div class="nav-logo-icon">🎵</div>
          Nex<span style="color:var(--accent3)">Tix</span>
        </div>
        <p>Platform tiket konser terpercaya di Indonesia. Beli tiket konser favoritmu dengan mudah, aman, dan cepat.</p>
      </div>
      <div class="footer-col">
        <h4>Jelajahi</h4>
        <a href="<?= $BASE ?>/public/index.php">Beranda</a>
        <a href="<?= $BASE ?>/public/katalog.php">Semua Konser</a>
        <a href="<?= $BASE ?>/public/katalog.php?kota=Jakarta">Konser Jakarta</a>
        <a href="<?= $BASE ?>/public/katalog.php?kota=Bandung">Konser Bandung</a>
      </div>
      <div class="footer-col">
        <h4>Akun</h4>
        <a href="<?= $BASE ?>/login.php">Masuk</a>
        <a href="<?= $BASE ?>/register.php">Daftar</a>
        <a href="<?= $BASE ?>/public/tiket-saya.php">Tiket Saya</a>
        <a href="<?= $BASE ?>/public/profile.php">Profil</a>
      </div>
      <div class="footer-col">
        <h4>Bantuan</h4>
        <a href="#">FAQ</a>
        <a href="#">Cara Pembelian</a>
        <a href="#">Kebijakan Refund</a>
        <a href="#">Hubungi Kami</a>
      </div>
    </div>
    <div class="footer-bottom">
      <div>© <?= date('Y') ?> NexTix. All rights reserved.</div>
      <div style="display:flex;gap:16px">
        <a href="#">Privasi</a>
        <a href="#">Syarat & Ketentuan</a>
      </div>
    </div>
  </div>
</footer>
