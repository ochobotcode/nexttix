<?php
require_once '../../auth/check_admin.php';
$errors=[]; $d=['status'=>'upcoming','featured'=>0,'jam'=>'19:00'];

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $d = [
        'nama'    => clean($_POST['nama']    ?? ''),
        'artis'   => clean($_POST['artis']   ?? ''),
        'deskripsi'=> clean($_POST['deskripsi']??''),
        'tanggal' => clean($_POST['tanggal'] ?? ''),
        'jam'     => clean($_POST['jam']     ?? ''),
        'venue'   => clean($_POST['venue']   ?? ''),
        'alamat'  => clean($_POST['alamat']  ?? ''),
        'kota'    => clean($_POST['kota']    ?? ''),
        'kapasitas'=> (int)($_POST['kapasitas']??0),
        'status'  => clean($_POST['status']  ?? 'upcoming'),
        'featured'=> isset($_POST['featured']) ? 1 : 0,
    ];
    if (!$d['nama'])    $errors[]='Nama konser wajib diisi.';
    if (!$d['artis'])   $errors[]='Nama artis wajib diisi.';
    if (!$d['tanggal']) $errors[]='Tanggal wajib diisi.';
    if (!$d['venue'])   $errors[]='Venue wajib diisi.';
    if (!$d['kota'])    $errors[]='Kota wajib diisi.';

    if (!$errors) {
        $slug = makeSlug($d['nama'].'-'.date('Y'));
        $base = $slug; $i = 1;
        while (DB::val("SELECT COUNT(*) FROM konser WHERE slug = ?", [$slug])) {
            $slug = $base.'-'.(++$i);
        }
        // Upload poster
        $poster = null;
        if (!empty($_FILES['poster']['name'])) {
            $ext = strtolower(pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION));
            if (in_array($ext,['jpg','jpeg','png','webp'])) {
                $poster = 'poster_'.uniqid().'.'.$ext;
                move_uploaded_file($_FILES['poster']['tmp_name'], POSTER_DIR.$poster);
            }
        }
        DB::insert("INSERT INTO konser (slug,nama,artis,deskripsi,tanggal,jam,venue,alamat,kota,kapasitas,poster,status,featured,created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$slug,$d['nama'],$d['artis'],$d['deskripsi'],$d['tanggal'],$d['jam'],$d['venue'],$d['alamat'],$d['kota'],$d['kapasitas'],$poster,$d['status'],$d['featured'],$_SESSION['admin_id']]);
        flash('Konser berhasil ditambahkan! 🎵');
        redirect(APP_URL.'/admin/konser/');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Tambah Konser — NexTix Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
<?php include '../../includes/sidebar.php'; ?>
<div class="admin-main">
  <div class="topbar">
    <button class="topbar-btn" id="sideToggle"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
    <div><div class="topbar-title">Tambah Konser</div></div>
    <div class="topbar-right"><a href="<?= APP_URL ?>/admin/konser/" class="btn btn-ghost btn-sm">← Kembali</a></div>
  </div>
  <div class="page-wrap">
    <div class="form-page">
      <?php if ($errors): ?><div class="alert alert-error"><?= implode('<br>',array_map('htmlspecialchars',$errors)) ?></div><?php endif; ?>
      <form method="POST" enctype="multipart/form-data">
        <div class="form-card">
          <div class="form-card-title">Informasi Konser</div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label req">Nama Konser</label>
              <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($d['nama']??'') ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label req">Nama Artis</label>
              <input type="text" name="artis" class="form-control" value="<?= htmlspecialchars($d['artis']??'') ?>" required>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control"><?= htmlspecialchars($d['deskripsi']??'') ?></textarea>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label req">Tanggal</label>
              <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($d['tanggal']??'') ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label req">Jam</label>
              <input type="time" name="jam" class="form-control" value="<?= htmlspecialchars($d['jam']??'19:00') ?>" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label req">Venue</label>
              <input type="text" name="venue" class="form-control" value="<?= htmlspecialchars($d['venue']??'') ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label req">Kota</label>
              <input type="text" name="kota" class="form-control" value="<?= htmlspecialchars($d['kota']??'') ?>" required>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Alamat Lengkap</label>
            <input type="text" name="alamat" class="form-control" value="<?= htmlspecialchars($d['alamat']??'') ?>">
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Kapasitas</label>
              <input type="number" name="kapasitas" class="form-control" value="<?= $d['kapasitas']??0 ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Status</label>
              <select name="status" class="form-control">
                <?php foreach (['upcoming','ongoing','completed','cancelled'] as $s): ?>
                <option value="<?= $s ?>" <?= ($d['status']??'')===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
            <input type="checkbox" name="featured" value="1" <?= ($d['featured']??0)?'checked':'' ?> style="accent-color:var(--accent)">
            <span style="font-size:.875rem">Tampilkan di halaman beranda (featured)</span>
          </label>
        </div>
        <div class="form-card">
          <div class="form-card-title">Poster Konser</div>
          <div class="upload-area" onclick="document.getElementById('posterInput').click()">
            <div style="font-size:2rem;margin-bottom:8px">🖼️</div>
            <div style="font-weight:600;margin-bottom:4px">Klik untuk upload poster</div>
            <div style="font-size:.8rem;color:var(--text3)">JPG, PNG, WEBP · Max 5MB</div>
          </div>
          <input type="file" name="poster" id="posterInput" accept="image/*" style="display:none">
          <img id="posterPreview" style="display:none;max-width:200px;border-radius:10px;margin-top:12px">
        </div>
        <div class="form-footer">
          <button type="submit" class="btn btn-primary">Simpan Konser</button>
          <a href="<?= APP_URL ?>/admin/konser/" class="btn btn-ghost">Batal</a>
        </div>
      </form>
    </div>
  </div>
</div>
</div>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body></html>
