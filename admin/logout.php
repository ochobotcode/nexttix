<?php
session_start();
require_once '../config/database.php';
require_once '../config/app.php';
session_destroy();
redirect(APP_URL . '/login.php');
