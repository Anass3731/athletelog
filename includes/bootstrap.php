<?php

declare(strict_types=1);

// Cookie de sesión (PHPSESSID) con SameSite=Lax y HttpOnly, antes de iniciar la sesión.
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);

// La sesión se inicia una sola vez, aquí, para todas las páginas.
session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/data.php';
require_once __DIR__ . '/components.php';