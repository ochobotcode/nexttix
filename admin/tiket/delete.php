<?php
require_once '../../auth/check_admin.php';
$id = (int)($_GET['id']??0);
$t  = DB::row("SELECT * FROM tiket WHERE id=?",[$id]);
if ($t) {
    $hasOrders = DB::val("SELECT COUNT(*) FROM orders WHERE tiket_id=?",[$id]);
    if ($hasOrders) { flash('Tiket tidak bisa dihapus karena sudah ada pesanan.','error'); }
    else { DB::run("DELETE FROM tiket WHERE id=?",[$id]); flash('Tiket berhasil dihapus.'); }
    redirect(APP_URL.'/admin/tiket/?konser_id='.$t['konser_id']);
}
redirect(APP_URL.'/admin/tiket/');
