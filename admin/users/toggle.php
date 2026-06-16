<?php
require_once '../../auth/check_admin.php';
if (!isAdmin()) redirect(APP_URL.'/admin/index.php');
$id = (int)($_GET['id']??0);
if ($id && $id !== (int)$_SESSION["admin_id"]) {
    DB::run("UPDATE admins SET is_active=1-is_active WHERE id=?",[$id]);
    flash('Status berhasil diubah.');
}
redirect(APP_URL.'/admin/users/');
