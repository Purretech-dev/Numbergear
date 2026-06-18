<?php
require_once __DIR__ . '/auth.php';
ng_logout();
header('Location: login.php');
exit;
