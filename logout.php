<?php
// logout.php
require_once __DIR__ . '/config/auth.php';
session_destroy();
header('Location: /patisserie_MVC/views/login.php');
exit;
