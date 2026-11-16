<?php

declare(strict_types=1);

// La sesión se inicia una sola vez, aquí, para todas las páginas.
session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/data.php';
require_once __DIR__ . '/components.php';
