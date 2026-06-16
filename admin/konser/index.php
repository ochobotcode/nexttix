<?php
require_once '../../auth/check_admin.php';
$page   = max(1,(int)($_GET['page']??1));
$search = clean($_GET['q']??'');
$status = clean($_GET['status']??'');
$per    = ITEMS_PER_PAGE;
$offset = ($page-1)*$per;

$w=[]; $p=[];
if ($search) { $w[]="(nama LIKE ? OR artis LIKE ? OR kota LIKE ?)"; $p=array_merge($p,["%$search%","%$search%","%$search%"]); }
if ($status) { $w[]="status=?"; $p[]=$status; }
$where = $w ? 'WHERE '.implode(' AND ',$w) : '';

$total = (int)DB::val("SELECT COUNT(*) FROM konser $where", $p);
$list  = DB::rows("SELECT k.*, (SELECT MIN(harga) FROM tiket WHERE konser_id=k.id AND is_active=1) as harga_min,
  (SELECT COUNT(*) FROM tiket WHERE konser_id=k.id) as jml_tiket
  FROM konser k $where ORDER BY k.tanggal DESC LIMIT $per OFFSET $offset", $p);

$base = APP_URL.'/admin/konser/?'.http_build_query(array_filter(['q'=>$search,'status'=>$status]));
$flash= getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Konser — NexTix Admin</title>
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
    <div><div class="topbar-title">Manajemen Konser</div></div>
    <div class="topbar-right">
      <a href="<?= APP_URL ?>/admin/konser/create.php" class="btn btn-primary btn-sm">+ Tambah Konser</a>
    </div>
  </div>
  <div class="page-wrap">
    <?php if ($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div><?php endif; ?>
    <div class="table-card">
      <div class="table-header">
        <div class="table-header-title">Daftar Konser (<?= $total ?>)</div>
        <form method="GET" class="table-toolbar">
          <input type="text" name="q" class="form-control" placeholder="Cari..." value="<?= htmlspecialchars($search) ?>" style="width:200px">
          <select name="status" class="form-control" style="width:auto">
            <option value="">Semua Status</option>
            <option value="upcoming"  <?= $status==='upcoming' ?'selected':'' ?>>Upcoming</option>
            <option value="ongoing"   <?= $status==='ongoing'  ?'selected':'' ?>>Ongoing</option>
            <option value="completed" <?= $status==='completed'?'selected':'' ?>>Completed</option>
            <option value="cancelled" <?= $status==='cancelled'?'selected':'' ?>>Cancelled</option>
          </select>
          <button type="submit" class="btn btn-ghost btn-sm">Filter</button>
        </form>
      </div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Konser</th><th>Tanggal</th><th>Venue</th><th>Tiket</th><th>Harga Mulai</th><th>Status</th><th>Aksi</th></tr></thead>
          <tbody>
          <?php if ($list): ?>
          <?php foreach ($list as $k): ?>
          <tr>
            <td>
              <div style="font-weight:700"><?= htmlspecialchars($k['nama']) ?></div>
              <div style="font-size:.78rem;color:var(--accent3)"><?= htmlspecialchars($k['artis']) ?></div>
            </td>
            <td style="font-size:.85rem"><?= tglID($k['tanggal']) ?><br><span style="color:var(--text3)"><?= substr($k['jam'],0,5) ?> WIB</span></td>
            <td style="font-size:.85rem"><?= htmlspecialchars($k['venue']) ?><br><span style="color:var(--text3)"><?= htmlspecialchars($k['kota']) ?></span></td>
            <td><?= $k['jml_tiket'] ?> kategori</td>
            <td><?= $k['harga_min'] ? rupiah($k['harga_min']) : '-' ?></td>
            <td><?= statusBadge($k['status']) ?></td>
            <td>
              <div class="action-btns">
                <a href="<?= APP_URL ?>/admin/tiket/?konser_id=<?= $k['id'] ?>" class="btn-icon btn-view" title="Kelola Tiket">🎟</a>
                <a href="<?= APP_URL ?>/admin/konser/edit.php?id=<?= $k['id'] ?>" class="btn-icon btn-edit" title="Edit">✏️</a>
                <button onclick="openDeleteModal('<?= APP_URL ?>/admin/konser/delete.php?id=<?= $k['id'] ?>','<?= htmlspecialchars(addslashes($k['nama'])) ?>')" class="btn-icon btn-delete" title="Hapus">🗑</button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php else: ?>
          <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text3)">Tidak ada data ditemukan</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="table-footer">
        <div class="table-info">Menampilkan <?= count($list) ?> dari <?= $total ?> data</div>
        <?= paginate($total,$page,$per,$base) ?>
      </div>
    </div>
  </div>
</div>
</div>

<!-- Delete Modal -->
<div class="modal-overlay" id="deleteModal">
  <div class="modal">
    <div class="modal-title">⚠️ Hapus Konser</div>
    <div class="modal-desc" id="deleteDesc"></div>
    <div class="modal-footer">
      <button onclick="closeModal('deleteModal')" class="btn btn-ghost">Batal</button>
      <a href="#" id="deleteBtn" class="btn btn-primary" style="background:var(--red);box-shadow:none">Hapus</a>
    </div>
  </div>
</div>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body></html>