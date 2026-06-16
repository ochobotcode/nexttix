<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';

if (!isset($_SESSION['user_id'])) {
    redirect(APP_URL . '/login.php?next=' . urlencode($_SERVER['REQUEST_URI']));
}
