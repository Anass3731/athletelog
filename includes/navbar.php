<?php

declare(strict_types=1);

// Nombre del fichero de la página actual (ej. index.php).
$paginaActual = basename($_SERVER['PHP_SELF']);

// Según haya sesión o no, usamos un menú u otro.
if (isset($_SESSION['user'])) {
    $items = APP_MENU_USER;
} else {
    $items = APP_MENU_GUEST;
}

$menu = '';

foreach ($items as $file => $label) {
    if ($file === $paginaActual) {
        $menu .= '<li><a href="' . e($file) . '" class="active">' . e($label) . '</a></li>';
    } else {
        $menu .= '<li><a href="' . e($file) . '">' . e($label) . '</a></li>';
    }
}

// Con sesión iniciada: saludo y enlace para cerrarla.
if (isset($_SESSION['user'])) {
    $menu .= '<li class="user-info">Hola, ' . e($_SESSION['user']['name']) . '</li>';
    $menu .= '<li><a href="logout.php">Cerrar sesión</a></li>';
}

echo <<<HTML
<header>
    <nav>
        <ul>
            {$menu}
        </ul>
    </nav>
</header>
HTML;