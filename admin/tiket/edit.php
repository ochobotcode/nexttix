<?php
require_once '../../auth/check_admin.php';
$id = (int)($_GET['id']??0);
$t  = DB::row("SELECT * FROM tiket WHERE id=?",[$id]);
if (!$t) { flash('Tiket tidak ditemukan.','error'); redirect(APP_URL.'/admin/tiket/'); }
$konserList = DB::rows("SELECT id,nama,artis FROM konser ORDER BY tanggal DESC");
$errors=[];

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $d=[
        'nama'      => clean($_POST['nama']??''),
        'deskripsi' => clean($_POST['deskripsi']??''),
        'harga'     => (float)($_POST['harga']??0),
        'stok'      => (int)($_POST['stok']??0),
        'is_active' => isset($_POST['is_active'])?1:0,
    ];
    if (!$d['nama'])    $errors[]='Nama tiket wajib.';
    if ($d['harga']<=0) $errors[]='Harga harus lebih dari 0.';
    if ($d['stok']<$t['terjual']) $errors[]='Stok tidak boleh kurang dari jumlah terjual ('.$t['terjual'].').';
    if (!$errors) {
        DB::run("UPDATE tiket SET nama=?,deskripsi=?,harga=?,stok=?,is_active=? WHERE id=?",
            [$d['nama'],$d['deskripsi'],$d['harga'],$d['stok'],$d['is_active'],$id]);
        flash('Tiket berhasil diperbarui! ✅');
        redirect(APP_URL.'/admin/tiket/?konser_id='.$t['konser_id']);
    }
    $t = array_merge($t,$d);
}
?>
<!DOCTYPE html><html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit Tiket — NexTix Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/admin.css"></head>
<body><div class="admin-layout">
<?php include '../../includes/sidebar.php'; ?>
<div class="admin-main">
  <div class="topbar">
    <button class="topbar-btn" id="sideToggle"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
    <div><div class="topbar-title">Edit Tiket</div></div>
    <div class="topbar-right"><a href="<?= APP_URL ?>/admin/tiket/?konser_id=<?= $t['konser_id'] ?>" class="btn btn-ghost btn-sm">← Kembali</a></div>
  </div>
  <div class="page-wrap"><div class="form-page">
    <?php if ($errors): ?><div class="alert alert-error"><?= implode('<br>',array_map('htmlspecialchars',$errors)) ?></div><?php endif; ?>
    <form method="POST">
      <div class="form-card">
        <div class="form-card-title">Edit Tiket</div>
        <div class="form-row">
          <div class="form-group"><label class="form-label req">Nama Kategori</label><input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($t['nama']) ?>" required></div>
          <div class="form-group"><label class="form-label req">Harga (Rp)</label><input type="number" name="harga" class="form-control" value="<?= $t['harga'] ?>" min="0" required></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label req">Stok (sudah terjual: <?= $t['terjual'] ?>)</label><input type="number" name="stok" class="form-control" value="<?= $t['stok'] ?>" min="<?= $t['terjual'] ?>" required></div>
          <div class="form-group"><label class="form-label">Deskripsi</label><input type="text" name="deskripsi" class="form-control" value="<?= htmlspecialchars($t['deskripsi']??'') ?>"></div>
        </div>
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
          <input type="checkbox" name="is_active" value="1" <?= $t['is_active']?'checked':'' ?> style="accent-color:var(--accent)">
          <span style="font-size:.875rem">Tiket aktif</span>
        </label>
      </div>
      <div class="form-footer">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= APP_URL ?>/admin/tiket/?konser_id=<?= $t['konser_id'] ?>" class="btn btn-ghost">Batal</a>
      </div>
    </form>
  </div></div>
</div></div>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body></html>
