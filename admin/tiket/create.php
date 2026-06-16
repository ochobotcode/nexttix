<?php
require_once '../../auth/check_admin.php';
$konserID = (int)($_GET['konser_id']??0);
$konserList = DB::rows("SELECT id,nama,artis FROM konser ORDER BY tanggal DESC");
$errors=[]; $d=['stok'=>100,'is_active'=>1,'konser_id'=>$konserID];

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $d=[
        'konser_id' => (int)($_POST['konser_id']??0),
        'nama'      => clean($_POST['nama']??''),
        'deskripsi' => clean($_POST['deskripsi']??''),
        'harga'     => (float)($_POST['harga']??0),
        'stok'      => (int)($_POST['stok']??0),
        'is_active' => isset($_POST['is_active'])?1:0,
    ];
    if (!$d['konser_id']) $errors[]='Pilih konser.';
    if (!$d['nama'])      $errors[]='Nama tiket wajib diisi.';
    if ($d['harga']<=0)   $errors[]='Harga harus lebih dari 0.';
    if ($d['stok']<=0)    $errors[]='Stok harus lebih dari 0.';
    if (!$errors) {
        DB::insert("INSERT INTO tiket (konser_id,nama,deskripsi,harga,stok,is_active) VALUES (?,?,?,?,?,?)",
            [$d['konser_id'],$d['nama'],$d['deskripsi'],$d['harga'],$d['stok'],$d['is_active']]);
        flash('Tiket berhasil ditambahkan! 🎟️');
        redirect(APP_URL.'/admin/tiket/?konser_id='.$d['konser_id']);
    }
}
?>
<!DOCTYPE html><html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Tambah Tiket — NexTix Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/admin.css"></head>
<body><div class="admin-layout">
<?php include '../../includes/sidebar.php'; ?>
<div class="admin-main">
  <div class="topbar">
    <button class="topbar-btn" id="sideToggle"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
    <div><div class="topbar-title">Tambah Tiket</div></div>
    <div class="topbar-right"><a href="<?= APP_URL ?>/admin/tiket/" class="btn btn-ghost btn-sm">← Kembali</a></div>
  </div>
  <div class="page-wrap"><div class="form-page">
    <?php if ($errors): ?><div class="alert alert-error"><?= implode('<br>',array_map('htmlspecialchars',$errors)) ?></div><?php endif; ?>
    <form method="POST">
      <div class="form-card">
        <div class="form-card-title">Informasi Tiket</div>
        <div class="form-group"><label class="form-label req">Konser</label>
          <select name="konser_id" class="form-control" required>
            <option value="">-- Pilih Konser --</option>
            <?php foreach ($konserList as $k): ?>
            <option value="<?= $k['id'] ?>" <?= ($d['konser_id']??0)==$k['id']?'selected':'' ?>><?= htmlspecialchars($k['nama']) ?> — <?= htmlspecialchars($k['artis']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label req">Nama Kategori Tiket</label><input type="text" name="nama" class="form-control" placeholder="cth: VVIP, CAT 1, Regular" value="<?= htmlspecialchars($d['nama']??'') ?>" required></div>
          <div class="form-group"><label class="form-label req">Harga (Rp)</label><input type="number" name="harga" class="form-control" value="<?= $d['harga']??'' ?>" min="0" required></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label req">Stok</label><input type="number" name="stok" class="form-control" value="<?= $d['stok']??100 ?>" min="1" required></div>
          <div class="form-group"><label class="form-label">Deskripsi</label><input type="text" name="deskripsi" class="form-control" value="<?= htmlspecialchars($d['deskripsi']??'') ?>"></div>
        </div>
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
          <input type="checkbox" name="is_active" value="1" <?= ($d['is_active']??1)?'checked':'' ?> style="accent-color:var(--accent)">
          <span style="font-size:.875rem">Tiket aktif / tersedia untuk dibeli</span>
        </label>
      </div>
      <div class="form-footer">
        <button type="submit" class="btn btn-primary">Simpan Tiket</button>
        <a href="<?= APP_URL ?>/admin/tiket/" class="btn btn-ghost">Batal</a>
      </div>
    </form>
  </div></div>
</div></div>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body></html>
