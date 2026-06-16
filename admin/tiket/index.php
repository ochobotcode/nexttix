<?php
require_once '../../auth/check_admin.php';
$konserID = (int)($_GET['konser_id']??0);
$konser   = $konserID ? DB::row("SELECT * FROM konser WHERE id=?",[$konserID]) : null;
$list     = $konserID
    ? DB::rows("SELECT *, (stok-terjual) as sisa FROM tiket WHERE konser_id=? ORDER BY harga ASC",[$konserID])
    : DB::rows("SELECT t.*, k.nama as nama_konser, k.artis, (t.stok-t.terjual) as sisa FROM tiket t JOIN konser k ON t.konser_id=k.id ORDER BY k.tanggal DESC, t.harga ASC");
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Tiket — NexTix Admin</title>
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
    <div>
      <div class="topbar-title">Manajemen Tiket<?= $konser ? ' — '.$konser['nama'] : '' ?></div>
    </div>
    <div class="topbar-right">
      <?php if ($konser): ?><a href="<?= APP_URL ?>/admin/konser/" class="btn btn-ghost btn-sm">← Konser</a><?php endif; ?>
      <a href="<?= APP_URL ?>/admin/tiket/create.php<?= $konserID ? '?konser_id='.$konserID : '' ?>" class="btn btn-primary btn-sm">+ Tambah Tiket</a>
    </div>
  </div>
  <div class="page-wrap">
    <?php if ($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div><?php endif; ?>
    <div class="table-card">
      <div class="table-header">
        <div class="table-header-title">Daftar Tiket (<?= count($list) ?>)</div>
        <input type="text" id="liveSearch" class="form-control" placeholder="Cari..." style="width:200px">
      </div>
      <div class="table-wrap">
        <table>
          <thead><tr>
            <?php if (!$konserID): ?><th>Konser</th><?php endif; ?>
            <th>Nama Tiket</th><th>Harga</th><th>Stok</th><th>Terjual</th><th>Sisa</th><th>Status</th><th>Aksi</th>
          </tr></thead>
          <tbody>
          <?php if (!$list): ?>
          <tr><td colspan="<?= $konserID?7:8 ?>" style="text-align:center;padding:40px;color:var(--text3)">Belum ada tiket. Klik "+ Tambah Tiket" untuk membuat kategori tiket baru.</td></tr>
          <?php else: foreach ($list as $t): ?>
          <tr>
            <?php if (!$konserID): ?><td><div style="font-weight:600;font-size:.85rem"><?= htmlspecialchars($t['nama_konser']) ?></div><div style="font-size:.75rem;color:var(--accent3)"><?= htmlspecialchars($t['artis']) ?></div></td><?php endif; ?>
            <td>
              <div style="font-weight:700"><?= htmlspecialchars($t['nama']) ?></div>
              <?php if ($t['deskripsi']): ?><div style="font-size:.75rem;color:var(--text3)"><?= htmlspecialchars(substr($t['deskripsi'],0,50)) ?>...</div><?php endif; ?>
            </td>
            <td style="font-weight:700;color:var(--accent3)"><?= rupiah($t['harga']) ?></td>
            <td><?= number_format($t['stok']) ?></td>
            <td><?= number_format($t['terjual']) ?></td>
            <td>
              <?php $pct = $t['stok']>0?round($t['terjual']/$t['stok']*100):0; ?>
              <div style="font-weight:700;color:<?= $t['sisa']<=0?'var(--red)':($pct>=80?'var(--gold)':'var(--green)') ?>"><?= number_format($t['sisa']) ?></div>
              <div style="font-size:.7rem;color:var(--text3)"><?= $pct ?>% terjual</div>
            </td>
            <td><?= $t['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>' ?></td>
            <td>
              <div class="action-btns">
                <a href="edit.php?id=<?= $t['id'] ?>" class="btn-icon btn-edit" title="Edit">✏️</a>
                <button onclick="openDeleteModal('delete.php?id=<?= $t['id'] ?>','<?= htmlspecialchars(addslashes($t['nama'])) ?>')" class="btn-icon btn-delete" title="Hapus">🗑</button>
              </div>
            </td>
          </tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</div>
<div class="modal-overlay" id="deleteModal">
  <div class="modal">
    <div class="modal-title">⚠️ Hapus Tiket</div>
    <div class="modal-desc" id="deleteDesc"></div>
    <div class="modal-footer">
      <button onclick="closeModal('deleteModal')" class="btn btn-ghost">Batal</button>
      <a href="#" id="deleteBtn" class="btn btn-primary" style="background:var(--red);box-shadow:none">Hapus</a>
    </div>
  </div>
</div>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body></html>
