<?php
require_once '../../auth/check_admin.php';
if (!isAdmin()) redirect(APP_URL.'/admin/index.php');
$id = (int)($_GET['id']??0);
if ($id && $id !== (int)$_SESSION["admin_id"]) {
    DB::run("DELETE FROM admins WHERE id=?",[$id]);
    flash('Pengguna berhasil dihapus.');
}
redirect(APP_URL.'/admin/users/');
