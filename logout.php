<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

// Eliminamos el token CSRF y vaciamos las variables de sesión, destruimos la sesión y volvemos al login.
unset($_SESSION['csrf_token']);
$_SESSION = [];
session_destroy();

header('Location: login.php');
exit();