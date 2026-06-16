<?php
require_once '../../auth/check_admin.php';
if (!isAdmin()) { flash('Akses ditolak. Hanya Admin.','error'); redirect(APP_URL.'/admin/index.php'); }
$page   = max(1,(int)($_GET['page']??1));
$search = clean($_GET['q']??'');
$per    = ITEMS_PER_PAGE; $offset=($page-1)*$per;
$w=[]; $p=[];
if ($search) { $w[]="(nama LIKE ? OR email LIKE ?)"; $s="%$search%"; $p=[$s,$s]; }
$where = $w ? 'WHERE '.implode(' AND ',$w) : '';
$total = (int)DB::val("SELECT COUNT(*) FROM admins $where",$p);
$list  = DB::rows("SELECT * FROM admins $where ORDER BY created_at DESC LIMIT $per OFFSET $offset",$p);
$flash = getFlash();

// Handle create/delete
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['_action']??'')==='create') {
    $n=clean($_POST['nama']??''); $e=clean($_POST['email']??''); $u=clean($_POST['username']??'');
    $pw=$_POST['password']??''; $role=clean($_POST['role']??'operator');
    if ($n&&$e&&$u&&$pw) {
        try {
            DB::insert("INSERT INTO admins (username,email,password,nama,role) VALUES (?,?,?,?,?)",
                [$u,$e,password_hash($pw,PASSWORD_DEFAULT),$n,$role]);
            flash('Pengguna berhasil ditambahkan!');
        } catch(Exception $ex) { flash('Email/username sudah digunakan.','error'); }
    } else flash('Lengkapi semua field.','error');
    redirect(APP_URL.'/admin/users/');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Pengguna Admin — NexTix Admin</title>
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
    <div><div class="topbar-title">Pengguna Admin</div></div>
    <div class="topbar-right">
      <button onclick="document.getElementById('addModal').classList.add('open')" class="btn btn-primary btn-sm">+ Tambah</button>
    </div>
  </div>
  <div class="page-wrap">
    <?php if ($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div><?php endif; ?>
    <div class="table-card">
      <div class="table-header">
        <div class="table-header-title">Admin & Operator (<?= $total ?>)</div>
        <form method="GET"><input type="text" name="q" class="form-control" placeholder="Cari..." value="<?= htmlspecialchars($search) ?>" style="width:200px"><button type="submit" class="btn btn-ghost btn-sm" style="margin-left:6px">Cari</button></form>
      </div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Username</th><th>Nama</th><th>Email</th><th>Role</th><th>Last Login</th><th>Status</th><th>Aksi</th></tr></thead>
          <tbody>
          <?php foreach ($list as $u): ?>
          <tr>
            <td style="font-family:monospace"><?= htmlspecialchars($u['username']) ?></td>
            <td style="font-weight:700"><?= htmlspecialchars($u['nama']) ?></td>
            <td style="font-size:.85rem"><?= htmlspecialchars($u['email']) ?></td>
            <td><?= $u['role']==='admin'?'<span class="badge badge-purple">Admin</span>':'<span class="badge badge-info">Operator</span>' ?></td>
            <td style="font-size:.78rem;color:var(--text2)"><?= $u['last_login']?date('d M Y H:i',strtotime($u['last_login'])):'-' ?></td>
            <td><?= $u['is_active']?'<span class="badge badge-success">Aktif</span>':'<span class="badge badge-secondary">Nonaktif</span>' ?></td>
            <td>
              <?php if ((int)$u['id'] !== (int)$_SESSION['admin_id']): ?>
              <div class="action-btns">
                <a href="toggle.php?id=<?= $u['id'] ?>" class="btn-icon btn-view" title="Toggle status" onclick="return confirm('Ubah status?')">🔄</a>
                <a href="delete.php?id=<?= $u['id'] ?>" class="btn-icon btn-delete" title="Hapus" onclick="return confirm('Hapus pengguna ini?')">🗑</a>
              </div>
              <?php else: ?><span style="font-size:.75rem;color:var(--text3)">Anda</span><?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="table-footer">
        <div class="table-info"><?= count($list) ?> dari <?= $total ?></div>
        <?= paginate($total,$page,$per,APP_URL.'/admin/users/?q='.urlencode($search)) ?>
      </div>
    </div>
  </div>
</div>
</div>

<!-- Add Modal -->
<div class="modal-overlay" id="addModal">
  <div class="modal">
    <div class="modal-title">+ Tambah Pengguna Admin</div>
    <form method="POST" action="">
      <input type="hidden" name="_action" value="create">
      <div class="form-group"><label class="form-label req">Nama</label><input type="text" name="nama" class="form-control" required></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label req">Username</label><input type="text" name="username" class="form-control" required></div>
        <div class="form-group"><label class="form-label req">Role</label>
          <select name="role" class="form-control"><option value="operator">Operator</option><option value="admin">Admin</option></select>
        </div>
      </div>
      <div class="form-group"><label class="form-label req">Email</label><input type="email" name="email" class="form-control" required></div>
      <div class="form-group"><label class="form-label req">Password</label><input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required></div>
      <div class="modal-footer">
        <button type="button" onclick="closeModal('addModal')" class="btn btn-ghost">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body></html>