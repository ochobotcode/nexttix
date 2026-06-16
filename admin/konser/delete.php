<?php
require_once '../../auth/check_admin.php';
$id = (int)($_GET['id']??0);
$k  = DB::row("SELECT * FROM konser WHERE id=?",[$id]);
if ($k) {
    $hasOrders = DB::val("SELECT COUNT(*) FROM orders o JOIN tiket t ON o.tiket_id=t.id WHERE t.konser_id=?",[$id]);
    if ($hasOrders) {
        flash('Konser tidak bisa dihapus karena sudah memiliki riwayat pesanan. Ubah status menjadi "cancelled" jika ingin menonaktifkan.','error');
    } else {
        if ($k['poster'] && file_exists(POSTER_DIR.$k['poster'])) unlink(POSTER_DIR.$k['poster']);
        DB::run("DELETE FROM konser WHERE id=?",[$id]);
        flash('Konser berhasil dihapus.');
    }
}
redirect(APP_URL.'/admin/konser/');