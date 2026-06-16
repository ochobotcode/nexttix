<?php
require_once '../../auth/check_admin.php';
$id     = (int)($_GET['id']??0);
$status = clean($_GET['status']??'');
$valid  = ['paid','cancelled','refunded'];
if (!in_array($status,$valid)) redirect(APP_URL.'/admin/orders/');
$o = DB::row("SELECT * FROM orders WHERE id=?",[$id]);
if ($o && $o['status'] !== $status) {
    $tgl = $status==='paid' ? date('Y-m-d H:i:s') : $o['tgl_bayar'];
    DB::run("UPDATE orders SET status=?,tgl_bayar=?,processed_by=? WHERE id=?",[$status,$tgl,$_SESSION['admin_id'],$id]);

    // Keep tiket.terjual consistent with paid orders
    if ($o['status'] !== 'paid' && $status === 'paid') {
        DB::run("UPDATE tiket SET terjual=terjual+? WHERE id=?",[$o['jumlah'],$o['tiket_id']]);
    } elseif ($o['status'] === 'paid' && $status !== 'paid') {
        DB::run("UPDATE tiket SET terjual=terjual-? WHERE id=?",[$o['jumlah'],$o['tiket_id']]);
    }
    flash('Status pesanan berhasil diubah ke '.strtoupper($status).'.');
}
redirect(APP_URL.'/admin/orders/');