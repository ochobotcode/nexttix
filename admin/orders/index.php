<?php
require_once '../../auth/check_admin.php';
$page   = max(1,(int)($_GET['page']??1));
$search = clean($_GET['q']??'');
$status = clean($_GET['status']??'');
$per    = ITEMS_PER_PAGE;
$offset = ($page-1)*$per;

$w=[]; $p=[];
if ($search) { $w[]="(o.kode LIKE ? OR u.nama LIKE ? OR u.email LIKE ? OR k.nama LIKE ?)"; $s="%$search%"; $p=[$s,$s,$s,$s]; }
if ($status) { $w[]="o.status=?"; $p[]=$status; }
$where = $w ? 'WHERE '.implode(' AND ',$w) : '';

$total = (int)DB::val("SELECT COUNT(*) FROM orders o JOIN users u ON o.user_id=u.id JOIN tiket t ON o.tiket_id=t.id JOIN konser k ON t.konser_id=k.id $where", $p);
$list  = DB::rows("SELECT o.*,u.nama as nama_pembeli,u.email as email_pembeli,t.nama as nama_tiket,k.nama as nama_konser
  FROM orders o JOIN users u ON o.user_id=u.id JOIN tiket t ON o.tiket_id=t.id JOIN konser k ON t.konser_id=k.id
  $where ORDER BY o.created_at DESC LIMIT $per OFFSET $offset", $p);

$base  = APP_URL.'/admin/orders/?'.http_build_query(array_filter(['q'=>$search,'status'=>$status]));
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Pesanan — NexTix Admin</title>
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
    <div><div class="topbar-title">Manajemen Pesanan</div></div>
  </div>
  <div class="page-wrap">
    <?php if ($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div><?php endif; ?>
    <div class="table-card">
      <div class="table-header">
        <div class="table-header-title">Semua Pesanan (<?= $total ?>)</div>
        <form method="GET" class="table-toolbar">
          <input type="text" name="q" class="form-control" placeholder="Cari kode, pembeli..." value="<?= htmlspecialchars($search) ?>" style="width:220px">
          <select name="status" class="form-control" style="width:auto">
            <option value="">Semua Status</option>
            <option value="paid"      <?= $status==='paid'     ?'selected':'' ?>>Lunas</option>
            <option value="pending"   <?= $status==='pending'  ?'selected':'' ?>>Pending</option>
            <option value="cancelled" <?= $status==='cancelled'?'selected':'' ?>>Batal</option>
            <option value="refunded"  <?= $status==='refunded' ?'selected':'' ?>>Refund</option>
          </select>
          <button type="submit" class="btn btn-ghost btn-sm">Filter</button>
        </form>
      </div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Kode</th><th>Pembeli</th><th>Konser · Tiket</th><th>Jml</th><th>Total</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr></thead>
          <tbody>
          <?php if (!$list): ?>
          <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text3)">Tidak ada pesanan ditemukan</td></tr>
          <?php else: foreach ($list as $o): ?>
          <tr>
            <td style="font-family:monospace;font-size:.8rem"><?= htmlspecialchars($o['kode']) ?></td>
            <td>
              <div style="font-weight:600;font-size:.875rem"><?= htmlspecialchars($o['nama_pembeli']) ?></div>
              <div style="font-size:.75rem;color:var(--text3)"><?= htmlspecialchars($o['email_pembeli']) ?></div>
            </td>
            <td>
              <div style="font-size:.85rem;font-weight:600"><?= htmlspecialchars($o['nama_konser']) ?></div>
              <div style="font-size:.75rem;color:var(--accent3)"><?= htmlspecialchars($o['nama_tiket']) ?></div>
            </td>
            <td><?= $o['jumlah'] ?>×</td>
            <td style="font-weight:700;color:var(--accent3)"><?= rupiah($o['total']) ?></td>
            <td><?= statusBadge($o['status']) ?></td>
            <td style="font-size:.78rem;color:var(--text2)"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
            <td>
              <div class="action-btns">
                <a href="view.php?id=<?= $o['id'] ?>" class="btn-icon btn-view" title="Detail">👁</a>
                <?php if ($o['status']==='pending'): ?>
                <a href="update.php?id=<?= $o['id'] ?>&status=paid" class="btn-icon btn-edit" title="Konfirmasi" onclick="return confirm('Konfirmasi pembayaran?')">✅</a>
                <a href="update.php?id=<?= $o['id'] ?>&status=cancelled" class="btn-icon btn-delete" title="Batalkan" onclick="return confirm('Batalkan pesanan?')">✖</a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
      <div class="table-footer">
        <div class="table-info">Menampilkan <?= count($list) ?> dari <?= $total ?> pesanan</div>
        <?= paginate($total,$page,$per,$base) ?>
      </div>
    </div>
  </div>
</div>
</div>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body></html>