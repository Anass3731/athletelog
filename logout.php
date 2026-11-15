<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

// Vaciamos las variables de sesión, destruimos la sesión y volvemos al login.
$_SESSION = [];
session_destroy();

header('Location: login.php');
exit();